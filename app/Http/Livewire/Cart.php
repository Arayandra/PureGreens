<?php

namespace App\Http\Livewire;

use App\Models\Product;
use Livewire\Component;
use Illuminate\Http\Request;

class Cart extends Component
{
    public function addToCart(Request $request)
    {
        $p = Product::find($request->input('product_id'));

        if (!$p) {
            return redirect()->back()->withErrors(['msg' => 'Produk tidak ditemukan']);
        }

        $cart = session()->get('cart', []);
        $cart[$p->id] = [
            'id' => $p->id,
            'name' => $p->name,
            'price' => $p->price,
            'qty' => 1,
            'model' => $p, // Simpan model untuk kompatibilitas dengan blade
        ];

        session()->put('cart', $cart);

        // CartFacade::add($p->id, $p->name, 1, $p->price)->associate('App\Models\Product');
        session()->flash('success', 'Item added in Cart');
        return redirect()->route('cart');
    }
    public function incQty(Request $req)
    {
        $rowId = $req->row_id;
        $cart = session()->get('cart', []);

        if (isset($cart[$rowId])) {
            $cart[$rowId]['qty'] += 1;
            session()->put('cart', $cart);
        }
        return redirect()->route('cart');
    }
    public function decQty(Request $req)
    {
        $rowId = $req->row_id;
        $cart = session()->get('cart', []);

        if (isset($cart[$rowId]) && $cart[$rowId]['qty'] > 1) {
            $cart[$rowId]['qty'] -= 1;
            session()->put('cart', $cart);
        } elseif (isset($cart[$rowId])) {
            unset($cart[$rowId]);
            session()->put('cart', $cart);
        }
        return redirect()->route('cart');
    }
    public function destroyItem(Request $req)
    {
        $rowId = $req->row_id;
        $cart = session()->get('cart', []);

        if (isset($cart[$rowId])) {
            unset($cart[$rowId]);
            session()->put('cart', $cart);
            session()->flash('success', 'Item has been removed from cart');
        }
        return redirect()->back();
    }
    public function destroyCart(Request $req)
    {
        session()->forget('cart');
        session()->flash('success', 'Cart has been cleared');
        return redirect()->back();
    }
    public function render()
    {
        $cart = session()->get('cart', []);
        return view('livewire.cart', ['cart' => $cart]);
    }
}
