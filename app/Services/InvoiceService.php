<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class InvoiceService
{
    public function generateInvoice(Order $order): string
    {
        $order->load(['user', 'items.product', 'items.seller']);

        $data = [
            'order' => $order,
            'company' => [
                'name' => Setting::get('site_name', config('app.name')),
                'email' => Setting::get('contact_email', 'support@example.com'),
                'address' => Setting::get('company_address', ''),
                'phone' => Setting::get('company_phone', ''),
                'logo' => Setting::get('company_logo', ''),
            ],
            'invoice_number' => 'INV-' . str_pad($order->id, 6, '0', STR_PAD_LEFT),
            'invoice_date' => $order->created_at->format('F j, Y'),
            'due_date' => $order->created_at->format('F j, Y'),
        ];

        $pdf = Pdf::loadView('invoices.order', $data);

        $filename = "invoice-{$order->order_number}.pdf";
        $path = "invoices/{$filename}";

        Storage::disk('public')->put($path, $pdf->output());

        return $path;
    }

    public function downloadInvoice(Order $order)
    {
        $order->load(['user', 'items.product', 'items.seller']);

        $data = [
            'order' => $order,
            'company' => [
                'name' => Setting::get('site_name', config('app.name')),
                'email' => Setting::get('contact_email', 'support@example.com'),
                'address' => Setting::get('company_address', ''),
                'phone' => Setting::get('company_phone', ''),
                'logo' => Setting::get('company_logo', ''),
            ],
            'invoice_number' => 'INV-' . str_pad($order->id, 6, '0', STR_PAD_LEFT),
            'invoice_date' => $order->created_at->format('F j, Y'),
            'due_date' => $order->created_at->format('F j, Y'),
        ];

        $pdf = Pdf::loadView('invoices.order', $data);

        return $pdf->download("invoice-{$order->order_number}.pdf");
    }

    public function streamInvoice(Order $order)
    {
        $order->load(['user', 'items.product', 'items.seller']);

        $data = [
            'order' => $order,
            'company' => [
                'name' => Setting::get('site_name', config('app.name')),
                'email' => Setting::get('contact_email', 'support@example.com'),
                'address' => Setting::get('company_address', ''),
                'phone' => Setting::get('company_phone', ''),
                'logo' => Setting::get('company_logo', ''),
            ],
            'invoice_number' => 'INV-' . str_pad($order->id, 6, '0', STR_PAD_LEFT),
            'invoice_date' => $order->created_at->format('F j, Y'),
            'due_date' => $order->created_at->format('F j, Y'),
        ];

        $pdf = Pdf::loadView('invoices.order', $data);

        return $pdf->stream("invoice-{$order->order_number}.pdf");
    }
}
