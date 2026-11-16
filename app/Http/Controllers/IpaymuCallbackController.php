<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\IpaymuSignatureVerifier;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class IpaymuCallbackController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $secret = (string) config('ipaymu.va');
        $receivedSignature = (string) $request->header('X-Signature', '');

        if ($secret === '' || $receivedSignature === '') {
            return response()->json(['message' => 'Callback verification is not configured.'], 503);
        }

        /** @var IpaymuSignatureVerifier $signatureVerifier */
        $signatureVerifier = app(IpaymuSignatureVerifier::class);
        if (!$signatureVerifier->verify($request->all(), $receivedSignature, $secret)) {
            Log::warning('Rejected iPaymu callback with an invalid signature.');
            return response()->json(['message' => 'Invalid signature.'], 401);
        }

        $payload = $signatureVerifier->normalize($request->all());
        $referenceId = $payload['reference_id'] ?? $payload['referenceId'] ?? null;
        $sessionId = $payload['sid'] ?? null;

        if (!$referenceId && !$sessionId) {
            return response()->json(['message' => 'Missing payment reference.'], 422);
        }

        $callbackAmount = $payload['amount'] ?? $payload['total'] ?? null;
        if ($callbackAmount === null || !is_numeric($callbackAmount)) {
            return response()->json(['message' => 'Payment amount is required.'], 422);
        }

        $statusText = strtolower((string) ($payload['status'] ?? ''));
        $statusCode = isset($payload['status_code']) ? (int) $payload['status_code'] : null;
        $transactionStatusCode = isset($payload['transaction_status_code'])
            ? (int) $payload['transaction_status_code']
            : null;

        $nextStatus = null;
        $successfulText = in_array($statusText, ['berhasil', 'success', 'completed'], true);
        if (($statusCode === 1 && $successfulText) || ($transactionStatusCode === 6 && $successfulText)) {
            $nextStatus = 'completed';
        } elseif ($statusCode === -2 || in_array($statusText, ['expired', 'failed', 'cancelled', 'canceled'], true)) {
            $nextStatus = 'canceled';
        } elseif (in_array($statusText, ['pending', 'waiting'], true) || $statusCode === 0) {
            $nextStatus = 'pending';
        } else {
            // Unknown provider states are acknowledged but never used to mutate an order.
            return response()->json(['message' => 'Callback status ignored.'], 200);
        }

        $result = DB::transaction(function () use ($referenceId, $sessionId, $callbackAmount, $nextStatus) {
            $query = Order::query();

            if ($referenceId) {
                $query->where('trx_id', (string) $referenceId);
            } elseif ($sessionId) {
                $query->where('session_id', (string) $sessionId);
            }

            $order = $query->lockForUpdate()->first();
            if (!$order && $sessionId && $referenceId) {
                $order = Order::where('session_id', (string) $sessionId)
                    ->lockForUpdate()
                    ->first();
            }

            if (!$order) {
                return 'not_found';
            }

            if ($callbackAmount !== null && is_numeric($callbackAmount)) {
                $expected = number_format((float) $order->total, 2, '.', '');
                $received = number_format((float) $callbackAmount, 2, '.', '');
                if ($expected !== $received) {
                    return 'amount_mismatch';
                }
            }

            // Idempotency: completed orders must never be downgraded by retries
            // or a delayed pending/cancelled notification.
            if ($order->status === 'completed') {
                return 'already_completed';
            }

            $order->status = $nextStatus;
            $order->save();

            return 'updated';
        });

        if ($result === 'not_found') {
            return response()->json(['message' => 'Order reference not found.'], 404);
        }

        if ($result === 'amount_mismatch') {
            Log::warning('Rejected iPaymu callback because the amount did not match the order.');
            return response()->json(['message' => 'Payment amount does not match the order.'], 409);
        }

        return response()->json([
            'message' => $result === 'already_completed' ? 'Callback already processed.' : 'Callback processed.',
        ], 200);
    }

}
