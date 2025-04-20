<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Order extends Model
{
    use HasFactory;
    protected $table = 'orders';
    protected $guarded = [];

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function newOrder(Request $request,$total_price)
    {
        $order = Order::query()->create([
            'total_price' => $total_price,
            'status' => 'pending',
            'address'=> $request->address,
            'user_id'=>auth()->user()->id,
            'code'=>rand(1111,9999),
        ]);
        return $order;
    }
}
