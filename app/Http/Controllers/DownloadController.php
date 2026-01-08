<?php

namespace App\Http\Controllers;

use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DownloadController extends Controller
{
    public function download(OrderItem $orderItem)
    {
        // Check if user owns this order item (admins can bypass)
        if ($orderItem->order->user_id !== auth()->id() && !auth()->user()->is_admin) {
            abort(403, 'This download belongs to a different account. Please log in with the account you used to make the purchase.');
        }

        // Use transaction with locking to prevent race conditions on download limits
        return DB::transaction(function () use ($orderItem) {
            // Lock the order item row to prevent concurrent downloads bypassing limit
            $orderItem = OrderItem::where('id', $orderItem->id)->lockForUpdate()->first();

            // Check download limit with fresh data
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
        });
    }
}
