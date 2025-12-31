<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class InvoiceController extends Controller
{
    /**
     * Download invoice PDF for an order
     */
    public function download(Order $order)
    {
        // Verify user can access this order
        if ($order->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403);
        }

        $pdf = $this->generatePdf($order);

        return $pdf->download("invoice-{$order->order_number}.pdf");
    }

    /**
     * View invoice PDF in browser
     */
    public function view(Order $order)
    {
        // Verify user can access this order
        if ($order->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403);
        }

        $pdf = $this->generatePdf($order);

        return $pdf->stream("invoice-{$order->order_number}.pdf");
    }

    /**
     * Download invoice for seller
     */
    public function sellerDownload(Order $order)
    {
        $seller = auth()->user()->seller;

        if (!$seller) {
            abort(403);
        }

        // Check if seller has items in this order
        $hasItems = $order->items()->where('seller_id', $seller->id)->exists();

        if (!$hasItems) {
            abort(403);
        }

        $pdf = $this->generateSellerPdf($order, $seller);

        return $pdf->download("seller-invoice-{$order->order_number}.pdf");
    }

    /**
     * Generate PDF for customer invoice
     */
    protected function generatePdf(Order $order)
    {
        $order->load(['user', 'items.product', 'items.seller']);

        $data = [
            'order' => $order,
            'invoiceNumber' => 'INV-' . str_pad($order->id, 6, '0', STR_PAD_LEFT),
            'invoiceDate' => $order->created_at,
            'dueDate' => $order->created_at,
            'company' => [
                'name' => config('app.name', 'Codexse'),
                'address' => \App\Models\Setting::get('company_address', '123 Business Street'),
                'city' => \App\Models\Setting::get('company_city', 'New York, NY 10001'),
                'country' => \App\Models\Setting::get('company_country', 'United States'),
                'email' => \App\Models\Setting::get('company_email', 'support@codexse.com'),
                'phone' => \App\Models\Setting::get('company_phone', '+1 (555) 123-4567'),
                'website' => config('app.url'),
                'tax_id' => \App\Models\Setting::get('company_tax_id', ''),
            ],
            'customer' => [
                'name' => $order->user->name,
                'email' => $order->user->email,
                'address' => $order->billing_address ?? '',
            ],
        ];

        $pdf = Pdf::loadView('invoices.customer', $data);
        $pdf->setPaper('a4');

        return $pdf;
    }

    /**
     * Generate PDF for seller invoice/statement
     */
    protected function generateSellerPdf(Order $order, $seller)
    {
        $order->load(['user', 'items' => function ($query) use ($seller) {
            $query->where('seller_id', $seller->id)->with('product');
        }]);

        $data = [
            'order' => $order,
            'seller' => $seller,
            'invoiceNumber' => 'SINV-' . str_pad($order->id, 6, '0', STR_PAD_LEFT),
            'invoiceDate' => $order->created_at,
            'items' => $order->items,
            'subtotal' => $order->items->sum('seller_amount'),
            'company' => [
                'name' => config('app.name', 'Codexse'),
                'email' => \App\Models\Setting::get('company_email', 'support@codexse.com'),
            ],
        ];

        $pdf = Pdf::loadView('invoices.seller', $data);
        $pdf->setPaper('a4');

        return $pdf;
    }
}
