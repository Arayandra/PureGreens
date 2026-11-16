<?php

namespace App\Http\Livewire;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Component;

class ThankYou extends Component
{
    public function render()
    {
        $customer = Auth::user();
        $trxId = request()->query('trx_id') ?? request()->query('sid');

        if (!$customer) {
            return redirect()->route('home')->withErrors(['error' => 'Silakan login terlebih dahulu']);
        }

        $order = $trxId ? Order::where('trx_id', $trxId)->where('user_id', $customer->id)->first() : null;
        if ($trxId && (!$order || $order->status !== 'completed')) {
            return redirect()->route('home')->withErrors(['error' => 'Transaksi tidak valid']);
        }
        Session::forget('cart');
        return view('livewire/success', compact('customer'));
    }
}
