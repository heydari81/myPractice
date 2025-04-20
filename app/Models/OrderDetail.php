<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class OrderDetail extends Model
{
    protected $guarded = [];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function newOrderDetail(Request $request,$order)
    {
        foreach ($request->items as $item) {
            $product = Product::find($item['product_id']);
            $orderDetail = OrderDetail::query()->create([
                'order_id' => $order->id,
                'product_id' => $item['product_id'],
                'count' => $item['count'],
                'price' => $product->price,
                'discount' => $product->discount,
            ]);
        }
    }
}
