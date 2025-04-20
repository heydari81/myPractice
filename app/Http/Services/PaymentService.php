<?php

namespace App\Http\Services;

use App\Models\Product;
use Illuminate\Http\Request;

class PaymentService
{
    public static function calculateTotalPrice(Request $request)
    {
        $total_price = 0;

        foreach ($request->items as $item) {
            $product = Product::find($item['product_id']);
            if ($product->quantity < $item['count']) {
                return response()->json([
                    'data' => [],
                    'message' => "not enough stock",
                    'success' => false,
                ], 200);
            }
            if (!$product) continue;

            $pricePerItem = $product->discount > 0
                ? $product->price * (1 - $product->discount / 100)
                : $product->price;

            $total_price += $pricePerItem * $item['count'];
        }

        return $total_price;
    }


}
