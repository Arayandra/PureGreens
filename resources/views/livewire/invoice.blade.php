<x-app-layout>
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-end mb-4">
            <a href="{{ route('invoice.pdf', $order) }}" target="__blank" class="btn">
                Download Invoice</a>
        </div>
        <div class="bg-orange-100/40 p-8 rounded-3xl">
            <header class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-5xl font-bold text-orange-600">Invoice</h1>
                    <p class="text-md">Invoice #{{ $order->id }}</p>
                </div>
                <div>
                    <p class="text-orange-600 font-semibold">PureGreens</p>
                    <p>Tangerang, Cisauk</p>
                    <p>Tangerang</p>
                    <p>{{ config('mail.from.address') }}</p>
                </div>
            </header>
            <table class="w-full text-left table-auto bg-white/60">
                <thead>
                    <tr>
                        <th class="px-4 py-2 text-orange-600">Product image</th>
                        <th class="px-4 py-2 text-orange-600">Product name</th>
                        <th class="px-4 py-2 text-orange-600">Quantity</th>
                        <th class="px-4 py-2 text-orange-600">Price</th>
                        <th class="px-4 py-2 text-orange-600">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($order->orderItems as $item)
                        <tr class="text-lg">
                            <td class="px-4 py-2">
                                <a href="{{ route('product.details', $item->product->id) }}">
                                    <img src="{{ asset('storage/' . $item->product->image) }}"
                                        class="w-40 h-40 mr-4 rounded">
                                </a>
                            </td>
                            <td class="px-4 py-2">
                                <p class="font-medium text-lg mb-2">&bullet; {{ $item->product->name }}</p>
                                <p>{{ $item->product->brief_description }}</p>
                            </td>
                            <td class="px-4 py-2">{{ $item->quantity }}</td>
                            <td class="px-4 py-2">Rp. {{ number_format($item->product->price, 0, ',', '.') }}</td>
                            <td class="px-4 py-2">Rp.
                                {{ number_format($item->product->price * $item->quantity, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="text-lg">
                    <tr class="font-semibold">
                        <td colspan="4" class="px-4 py-2 text-right">Subtotal:</td>
                        <td class="px-4 py-2">Rp. {{ number_format($order->total, 0, ',', '.') }}</td>
                    </tr>
                    <tr class="font-semibold text-orange-600">
                        <td colspan="4" class="px-4 py-2 text-right">Total:</td>
                        <td class="px-4 py-2">Rp. {{ number_format($order->total, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</x-app-layout>
