<?php

namespace App\Http\Controllers;

use App\Models\OrderItem;
use Illuminate\Http\Request;

class DownloadController extends Controller
{
    public function download(OrderItem $orderItem)
    {
        // Check if user owns this order item
        if ($orderItem->order->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        // Check download limit
        $downloadCount = $orderItem->downloads()->count();
        if ($orderItem->download_limit && $downloadCount >= $orderItem->download_limit) {
            return redirect()->back()->with('error', 'Download limit reached for this product.');
        }

        // Get file from product
        $product = $orderItem->product;
        $file = $product->getFirstMedia('files');

        if (!$file) {
            return redirect()->back()->with('error', 'File not available.');
        }

        // Create download record
        $orderItem->downloads()->create([
            'user_id' => auth()->id(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        // Increment download count
        $orderItem->increment('download_count');

        return response()->download($file->getPath(), $product->slug . '.zip');
    }
}
