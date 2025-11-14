<?php

namespace App\Http\View\Composers;

use Illuminate\View\View;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class CartComposer
{
    public function compose(View $view)
    {
        $latestOrder = null;

        if (Auth::check()) {
            $latestOrder = Order::where('user_id', Auth::id())
                ->where('status', 'pending')
                ->with('items.product')
                ->latest()
                ->first();
        }

        $view->with('latestOrder', $latestOrder);
    }
}
