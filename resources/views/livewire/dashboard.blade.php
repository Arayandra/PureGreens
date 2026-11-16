<x-app-layout>
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-2xl font-bold mb-6 text-gray-800">Your Orders</h1>

        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border border-gray-200 rounded-lg shadow-sm">
                <thead>
                    <tr class="bg-gray-100 text-left text-sm font-semibold text-gray-600 uppercase tracking-wider">
                        <th class="px-6 py-3">Products</th>
                        <th class="px-6 py-3">Product Names</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">Total</th>
                        <th class="px-6 py-3">Order Date</th>
                        <th class="px-6 py-3">Quantity</th>
                        <th class="px-6 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($orders as $order)
                        @php
                            $quantity = $order->orderItems->sum('quantity');
                        @endphp
                        <tr class="border-b hover:bg-gray-50 transition-colors duration-200">
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-2 items-center">
                                    @foreach ($order->orderItems as $item)
                                        <a href="{{ route('product.details', $item->product->id) }}">
                                            <img src="{{ asset('storage/' . $item->product->image) }}" 
                                                 alt="{{ $item->product->name }}" 
                                                 class="w-16 h-16 object-cover rounded-md">
                                        </a>
                                    @endforeach
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <ul class="list-disc list-inside text-sm text-gray-700">
                                    @foreach ($order->orderItems as $item)
                                        <li>
                                            <a href="{{ route('product.details', $item->product->id) }}" 
                                               class="text-blue-600 hover:underline">
                                                {{ $item->product->name }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700">{{ $order->status }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700">Rp. {{ number_format($order->total, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700">{{ $order->created_at->format('F j, Y') }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700">{{ $quantity }}</td>
                            <td class="px-6 py-4">
                                <a href="{{ route('invoice', $order) }}" 
                                   class="inline-block bg-orange-500 text-white px-4 py-2 text-sm font-semibold rounded-lg hover:bg-orange-600 transition-colors duration-200 shadow-sm">
                                    View Details
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $orders->links('pagination::tailwind') }}
        </div>
    </div>

    <style>
        /* Tabel */
        table {
            border-collapse: collapse;
            width: 100%;
        }

        th, td {
            text-align: left;
            vertical-align: middle;
        }

        th {
            background-color: #f3f4f6;
            color: #4b5563;
            font-weight: 600;
        }

        tr {
            transition: background-color 0.2s;
        }

        tr:hover {
            background-color: #f9fafb;
        }

        /* Tombol */
        a.bg-orange-500 {
            display: inline-block;
            padding: 8px 16px;
            font-size: 0.875rem;
            font-weight: 600;
            line-height: 1.5;
            text-align: center;
            white-space: nowrap;
            border-radius: 0.5rem;
            background-color: #f97316;
            color: #ffffff;
            transition: background-color 0.2s ease-in-out;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        }

        a.bg-orange-500:hover {
            background-color: #ea580c;
        }

        a.bg-orange-500:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.3);
        }

        /* Responsivitas */
        @media (max-width: 640px) {
            table {
                font-size: 0.75rem;
            }

            th, td {
                padding: 0.5rem;
            }

            a.bg-orange-500 {
                padding: 6px 12px;
                font-size: 0.75rem;
            }

            img.w-16 {
                width: 2.5rem;
                height: 2.5rem;
            }
        }
    </style>
</x-app-layout>