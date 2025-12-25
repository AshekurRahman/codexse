<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $orderItems = auth()->user()->seller->orderItems()
            ->with(['order.user', 'product', 'license'])
            ->latest()
            ->paginate(20);

        return view('seller.orders.index', compact('orderItems'));
    }
}
