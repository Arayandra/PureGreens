<?php

namespace App\Http\Controllers;

use App\Models\Order;
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

        $payload = $this->normalizePayload($request->all());
        unset($payload['signature']);
        ksort($payload, SORT_STRING);

        $jsonPayload = json_encode($payload);
        if ($jsonPayload === false) {
            return response()->json(['message' => 'Invalid callback payload.'], 400);
        }

        // iPaymu callback signatures use the Merchant VA number as HMAC secret.
        // PHP json_encode escapes forward slashes by default, as required by its guide.
        $expectedSignature = hash_hmac('sha256', $jsonPayload, $secret);
        if (!hash_equals($expectedSignature, $receivedSignature)) {
            Log::warning('Rejected iPaymu callback with an invalid signature.');
            return response()->json(['message' => 'Invalid signature.'], 401);
        }

        $referenceId = $payload['reference_id'] ?? $payload['referenceId'] ?? null;
        $sessionId = $payload['sid'] ?? null;

        if (!$referenceId && !$sessionId) {
            return response()->json(['message' => 'Missing payment reference.'], 422);
        }

        $callbackAmount = $payload['amount'] ?? $payload['total'] ?? null;
        $statusText = strtolower((string) ($payload['status'] ?? ''));
        $statusCode = isset($payload['status_code']) ? (int) $payload['status_code'] : null;

        $nextStatus = null;
        if ($statusCode === 1 && in_array($statusText, ['berhasil', 'success', 'completed'], true)) {
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

    private function normalizePayload(array $raw): array
    {
        $normalized = [];

        foreach ($raw as $key => $value) {
            if ($key === 'additional_info') {
                if ($value === null || $value === '[]') {
                    $normalized[$key] = [];
                } elseif (is_string($value)) {
                    $decoded = json_decode($value, true);
                    $normalized[$key] = is_array($decoded) ? $decoded : $value;
                } else {
                    $normalized[$key] = $value;
                }

                continue;
            }

            if (in_array($key, ['trx_id', 'status_code', 'transaction_status_code', 'paid_off'], true)) {
                $normalized[$key] = is_numeric($value) ? (int) $value : $value;
                continue;
            }

            if ($key === 'is_escrow') {
                $normalized[$key] = in_array($value, [true, 1, '1', 'true'], true);
                continue;
            }

            $normalized[$key] = is_array($value) ? $value : (string) $value;
        }

        if (!array_key_exists('additional_info', $normalized)) {
            $normalized['additional_info'] = [];
        }

        return $normalized;
    }
}
