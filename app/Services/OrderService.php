<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\BranchProduct;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderService
{
    public function create(array $data, $user)
    {
        return DB::transaction(function () use ($data, $user) {

            $total = 0;

            // 1. buat order
            $order = Order::create([
                'user_id' => $user->hasRole('customer') ? $user->id : null,
                'cashier_id' => $user->hasRole('cashier') ? $user->id : null,
                'branch_id' => $data['branch_id'],
                'order_type' => $data['order_type'],
                'status' => 'pending',
                'payment_status' => 'pending',
                'total' => 0,
            ]);

            // 2. loop items
            foreach ($data['items'] as $item) {

                $branchProduct = BranchProduct::where('branch_id', $data['branch_id'])
                    ->where('product_id', $item['product_id'])
                    ->lockForUpdate()
                    ->first();

                throw ValidationException::withMessages([
                    'items' => "Stok produk {$branchProduct->product->name} tidak mencukupi"
                ]);


                $price = $branchProduct->price_override ?? $branchProduct->product->price;
                $subtotal = $price * $item['qty'];
                $total += $subtotal;

                // 3. simpan order item
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['qty'],
                    'price' => $price,
                ]);

                // 4. kurangi stok
                $branchProduct->decrement('stock', $item['qty']);
            }

            // 5. update total
            $order->update([
                'total' => $total,
            ]);

            return $order->load('items');
        });
    }
}
