<?php

namespace App\Services;

use LaravelDaily\Invoices\Invoice;
use LaravelDaily\Invoices\Classes\Party;
use LaravelDaily\Invoices\Classes\InvoiceItem;

class InvoiceService
{
    public function createInvoice($order)
    {
        $client = new Party([
            'name' => 'PureGreens',
            'custom_fields' => [
                'seller' => 'PureGreens',
                'email'  => config('mail.from.address'),
            ],
        ]);

        $customer = new Party([
            'name' => $order->user->name,
            'code' => '#00A' . $order->id,
            'custom_fields' => [
                'email' => $order->user->email,
                'order number' => '#' . $order->id,
            ],
        ]);

        $items = [];
        foreach ($order->orderItems as $item) {
            $items[] = (new InvoiceItem())
                ->title($item->product->name)
                ->pricePerUnit($item->price) // Pastikan $item->price sudah numeric tanpa simbol
                ->quantity($item->quantity);
        }

        $invoice = Invoice::make('receipt')
            ->status(__('invoices::invoice.paid'))
            ->sequence(667)
            ->serialNumberFormat('{SEQUENCE}/{SERIES}')
            ->series('BIG')
            ->seller($client)
            ->buyer($customer)
            ->date($order->created_at)
            ->dateFormat('d/m/Y') // format tanggal Indonesia (optional)
            ->payUntilDays(14)
            ->currencySymbol('Rp. ')           // Simbol Rupiah tanpa titik di belakang
            ->currencyCode('IDR')            // Kode mata uang IDR
            ->currencyFormat('{SYMBOL} {VALUE}') // Format dengan spasi antara simbol dan nilai
            ->currencyThousandsSeparator('.')    // Pemisah ribuan sesuai format Indonesia
            ->currencyDecimalPoint(',')            // Pemisah desimal sesuai format Indonesia
            ->filename($client->name . '-' . $customer->name)
            ->addItems($items)
            ->save('public');

        // Mendapatkan link file invoice yang sudah disimpan
        $link = $invoice->url();

        return $invoice;
    }
}
