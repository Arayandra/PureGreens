<?php

namespace App\Http\Livewire;

use App\Mail\OrderReceived;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\InvoiceService;
use Livewire\Component;
use App\Http\Livewire\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Checkout extends Component
{
    public function initiatePayment()
    {
        $user = Auth::user();
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            session()->flash('error', 'Keranjang kosong');
            return redirect()->route('cart');
        }

        $products = [];
        $prices = [];
        $quantities = [];
        $descriptions = [];
        $totalAmount = 0;

        foreach ($cart as $item) {
            // Pastikan struktur data cart konsisten
            $product = $item['model'] ?? $item['product'] ?? null;
            $qty = (int) ($item['qty'] ?? $item['quantity'] ?? 1);
            
            if (!$product) {
                Log::error('Product not found in cart item:', $item);
                continue;
            }

            $products[] = $product->name;
            $prices[] = (int) $product->price; // Pastikan integer
            $quantities[] = $qty;
            $descriptions[] = $product->description ?? 'Produk ' . $product->name;
            $totalAmount += $product->price * $qty;
        }

        // Validasi apakah ada produk
        if (empty($products)) {
            session()->flash('error', 'Tidak ada produk valid dalam keranjang');
            return redirect()->route('cart');
        }

        // Generate unique transaction ID
        $trxId = 'TRX-' . time() . '-' . uniqid();

        // Simpan transaksi ke database
        $order = Order::create([
            'user_id' => $user->id,
            'total' => $totalAmount,
            'status' => 'pending',
            'trx_id' => $trxId,
        ]);

        // Simpan order items
        foreach ($cart as $item) {
            $product = $item['model'] ?? $item['product'] ?? null;
            $qty = (int) ($item['qty'] ?? $item['quantity'] ?? 1);
            
            if ($product) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $qty,
                    'price' => $product->price,
                ]);
            }
        }

        // Data transaksi untuk iPaymu
        $payload = [
            'product' => $products,
            'price' => $prices,
            'qty' => $quantities,
            'description' => $descriptions, // Pastikan jumlah deskripsi sama dengan produk
            'buyerName' => $user->name, // Gunakan camelCase
            'buyerEmail' => $user->email,
            'buyerPhone' => $user->phone ?? '08123456789', // Ambil dari user atau default
            'returnUrl' => config('ipaymu.url.return') . '?trx_id=' . $order->trx_id,
            'notifyUrl' => config('ipaymu.url.notify') ?: url('/payment/notify'),
            'cancelUrl' => config('ipaymu.url.cancel'),
            'referenceId' => $order->trx_id,
        ];

        // Log payload untuk debugging
        Log::info('iPaymu Payload:', $payload);

        // Generate signature
        $jsonBody = json_encode($payload, JSON_UNESCAPED_SLASHES);
        $requestBody = strtolower(hash('sha256', $jsonBody));
        $stringToSign = strtoupper('POST') . ':' . config('ipaymu.va') . ':' . $requestBody . ':' . config('ipaymu.api_key');
        $signature = hash_hmac('sha256', $stringToSign, config('ipaymu.api_key'));
        $timestamp = now()->format('YmdHis');

        Log::info('Payment Debug Info:', [
            'products_count' => count($products),
            'prices_count' => count($prices),
            'quantities_count' => count($quantities),
            'descriptions_count' => count($descriptions),
            'total_amount' => $totalAmount,
            'payload' => $payload,
            'signature' => $signature
        ]);

        try {
            $response = Http::timeout(30) // Increase timeout
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'signature' => $signature,
                    'va' => config('ipaymu.va'),
                    'timestamp' => $timestamp,
                ])
                ->post(config('ipaymu.api_url'), $payload);

            $result = $response->json();

            Log::info('iPaymu Response:', [
                'status_code' => $response->status(),
                'response_body' => $result
            ]);

            // Cek response status
            if (!$response->successful()) {
                Log::error('iPaymu HTTP Error:', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                session()->flash('error', 'Gagal terhubung ke payment gateway');
                return redirect()->route('cart');
            }

            if ($result && isset($result['Status']) && $result['Status'] == 200) {
                if (isset($result['Data']['Url'])) {
                    // Update order dengan session ID dari iPaymu
                    if (isset($result['Data']['SessionID'])) {
                        $order->update(['trx_id' => $result['Data']['SessionID']]);
                    }
                    
                    // Redirect ke halaman pembayaran
                    return redirect()->away($result['Data']['Url']);
                } else {
                    Log::error('iPaymu Response missing URL:', $result);
                    session()->flash('error', 'Response dari payment gateway tidak lengkap');
                    return redirect()->route('cart');
                }
            } else {
                $errorMessage = $result['Message'] ?? 'Gagal memulai transaksi';
                Log::error('iPaymu Error Response:', $result);
                session()->flash('error', $errorMessage);
                return redirect()->route('cart');
            }
        } catch (\Exception $e) {
            Log::error('iPaymu Exception:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            session()->flash('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
            return redirect()->route('cart');
        }
    }

    public function handleNotify(Request $request)
    {
        // Log semua data masuk buat debug
        Log::info('Notify Callback Headers:', $request->headers->all());
        Log::info('Notify Callback Body:', $request->all());

        $trxId = $request->input('reference_id') 
               ?? $request->input('trx_id') 
               ?? $request->input('sid')
               ?? $request->input('sessionId');
        
        $status = $request->input('status') 
                ?? $request->input('Status')
                ?? $request->input('transaction_status');

        if (!$trxId) {
            Log::error('Missing transaction ID in notify callback');
            return response()->json(['error' => 'Missing transaction ID'], 400);
        }

        $order = Order::where('trx_id', $trxId)->first();

        if (!$order) {
            Log::error('Order not found for trx_id: ' . $trxId);
            return response()->json(['error' => 'Order not found'], 404);
        }

        // Update status berdasarkan response dari iPaymu
        if (in_array(strtolower($status), ['berhasil', 'success', 'completed']) || $status == 1) {
            $order->status = 'completed';
            $order->save();

            // Clear cart setelah pembayaran berhasil
            Session::forget('cart');

            Log::info("Order #{$order->id} marked as completed.");
        } elseif (in_array(strtolower($status), ['pending', 'waiting'])) {
            $order->status = 'pending';
            $order->save();
            
            Log::info("Order #{$order->id} still pending.");
        } elseif (in_array(strtolower($status), ['failed', 'cancelled', 'expired'])) {
            $order->status = 'canceled';
            $order->save();
            
            Log::info("Order #{$order->id} marked as failed.");
        }

        return response()->json(['message' => 'Notification processed']);
    }

    public function success()
    {
        // Kosongkan session cart
        Session::forget('cart');

        // Tampilkan view sukses
        return view('livewire/success')->with('success', 'Pembayaran berhasil! Terima kasih.');
    }
}