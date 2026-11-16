<?php

namespace App\Http\Livewire;

use App\Mail\OrderReceived;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Services\InvoiceService;
use Livewire\Component;
use App\Http\Livewire\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Checkout extends Component
{
    public function initiatePayment()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $cart = session()->get('cart', []);
        if (empty($cart)) {
            session()->flash('error', 'Keranjang kosong');
            return redirect()->route('cart');
        }

        try {
            $order = DB::transaction(function () use ($cart, $user) {
                $products = [];
                $prices = [];
                $quantities = [];
                $descriptions = [];
                $totalAmount = 0;

                foreach ($cart as $item) {
                    $productModel = $item['model'] ?? $item['product'] ?? null;
                    $productId = $productModel?->id ?? $item['id'] ?? null;
                    $quantity = (int) ($item['qty'] ?? $item['quantity'] ?? 0);

                    if (!$productId || $quantity < 1) {
                        throw new \RuntimeException('Item keranjang tidak valid.');
                    }

                    $product = Product::query()->lockForUpdate()->find($productId);
                    if (!$product || (int) $product->stock < $quantity) {
                        throw new \RuntimeException('Stok produk tidak mencukupi.');
                    }

                    $unitPrice = (float) $product->price;
                    $products[] = $product->name;
                    $prices[] = (int) round($unitPrice);
                    $quantities[] = $quantity;
                    $descriptions[] = $product->description ?: 'Produk ' . $product->name;
                    $totalAmount += $unitPrice * $quantity;
                }

                if (empty($products)) {
                    throw new \RuntimeException('Tidak ada produk valid dalam keranjang.');
                }

                $trxId = 'TRX-' . now()->format('YmdHis') . '-' . strtoupper(\Illuminate\Support\Str::random(8));
                $order = Order::create([
                    'user_id' => $user->id,
                    'total' => $totalAmount,
                    'status' => 'pending',
                    'trx_id' => $trxId,
                ]);

                foreach ($cart as $item) {
                    $productModel = $item['model'] ?? $item['product'] ?? null;
                    $productId = $productModel?->id ?? $item['id'] ?? null;
                    $quantity = (int) ($item['qty'] ?? $item['quantity'] ?? 0);
                    $product = Product::find($productId);

                    if ($product && $quantity > 0) {
                        OrderItem::create([
                            'order_id' => $order->id,
                            'product_id' => $product->id,
                            'quantity' => $quantity,
                            'price' => $product->price,
                        ]);
                    }
                }

                return [
                    'order' => $order,
                    'products' => $products,
                    'prices' => $prices,
                    'quantities' => $quantities,
                    'descriptions' => $descriptions,
                    'total' => $totalAmount,
                ];
            });

            $order = $order['order'];
            $payload = [
                'product' => $orderDataProducts = $order ? $orderDataProducts ?? [] : [],
                'price' => [],
                'qty' => [],
                'description' => [],
            ];

            // Rebuild the provider payload from persisted order items to avoid
            // relying on mutable client-side cart values after order creation.
            $orderItems = $order->orderItems()->with('product')->get();
            $payload = [
                'product' => $orderItems->map(fn ($item) => $item->product?->name ?? 'Product')->all(),
                'price' => $orderItems->map(fn ($item) => (int) round($item->price))->all(),
                'qty' => $orderItems->pluck('quantity')->map(fn ($qty) => (int) $qty)->all(),
                'description' => $orderItems->map(fn ($item) => $item->product?->description ?: 'Product')->all(),
                'buyerName' => $user->name,
                'buyerEmail' => $user->email,
                'buyerPhone' => $user->phone ?? '',
                'returnUrl' => config('ipaymu.url.return') ?: route('checkout.success'),
                'notifyUrl' => config('ipaymu.url.notify') ?: route('ipaymu.callback'),
                'cancelUrl' => config('ipaymu.url.cancel') ?: route('checkout.cancel'),
                'referenceId' => $order->trx_id,
            ];

            $returnUrl = $payload['returnUrl'];
            $payload['returnUrl'] = $returnUrl . (str_contains($returnUrl, '?') ? '&' : '?') . 'trx_id=' . urlencode($order->trx_id);

            Log::info('Starting iPaymu checkout request.', [
                'order_id' => $order->id,
                'item_count' => count($payload['product']),
                'total_amount' => $order->total,
            ]);

            $jsonBody = json_encode($payload, JSON_UNESCAPED_SLASHES);
            if ($jsonBody === false) {
                throw new \RuntimeException('Gagal menyiapkan data pembayaran.');
            }

            $requestBody = strtolower(hash('sha256', $jsonBody));
            $stringToSign = 'POST:' . config('ipaymu.va') . ':' . $requestBody . ':' . config('ipaymu.api_key');
            $signature = hash_hmac('sha256', $stringToSign, config('ipaymu.api_key'));

            $response = Http::timeout(30)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'signature' => $signature,
                    'va' => config('ipaymu.va'),
                    'timestamp' => now()->format('YmdHis'),
                ])
                ->post(config('ipaymu.api_url'), $payload);

            $result = $response->json();

            if (!$response->successful() || !isset($result['Status']) || (int) $result['Status'] !== 200) {
                $order->update(['status' => 'canceled']);
                Log::warning('iPaymu rejected checkout initialization.', [
                    'order_id' => $order->id,
                    'http_status' => $response->status(),
                ]);
                session()->flash('error', 'Gagal memulai pembayaran. Silakan coba kembali.');
                return redirect()->route('cart');
            }

            if (empty($result['Data']['Url'])) {
                $order->update(['status' => 'canceled']);
                Log::warning('iPaymu response did not include a payment URL.', ['order_id' => $order->id]);
                session()->flash('error', 'Respons payment gateway tidak lengkap.');
                return redirect()->route('cart');
            }

            if (!empty($result['Data']['SessionID'])) {
                // Keep the merchant reference (trx_id) stable; store provider SID separately.
                $order->update(['session_id' => (string) $result['Data']['SessionID']]);
            }

            return redirect()->away($result['Data']['Url']);
        } catch (\Throwable $exception) {
            if (isset($order) && $order instanceof Order && $order->status === 'pending') {
                $order->update(['status' => 'canceled']);
            }

            Log::error('iPaymu checkout initialization failed.', [
                'exception' => get_class($exception),
                'message' => $exception->getMessage(),
            ]);

            session()->flash('error', 'Terjadi kesalahan saat menyiapkan pembayaran. Silakan coba kembali.');
            return redirect()->route('cart');
        }
    }

        public function success(Request $request)
    {
        $user = Auth::user();
        $trxId = (string) $request->query('trx_id', '');

        if (!$user || $trxId === '') {
            return redirect()->route('cart')->with('error', 'Referensi pembayaran tidak ditemukan.');
        }

        $order = Order::where('trx_id', $trxId)
            ->where('user_id', $user->id)
            ->firstOrFail();

        if ($order->status !== 'completed') {
            return redirect()->route('cart')->with('warning', 'Pembayaran belum terkonfirmasi. Keranjang tidak dikosongkan.');
        }

        Session::forget('cart');
        return view('livewire/success')->with('success', 'Pembayaran berhasil. Terima kasih.');
    }

    public function cancel(Request $request)
    {
        $trxId = (string) $request->query('trx_id', '');
        if ($trxId !== '' && Auth::check()) {
            Order::where('trx_id', $trxId)
                ->where('user_id', Auth::id())
                ->where('status', 'pending')
                ->update(['status' => 'canceled']);
        }

        return redirect()->route('cart')->with('warning', 'Pembayaran dibatalkan. Pesanan belum dibayar.');
    }

}