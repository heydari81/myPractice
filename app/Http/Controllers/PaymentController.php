<?php

namespace App\Http\Controllers;

use App\Http\Services\PaymentService;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use Illuminate\Http\Request;
use Shetabit\Multipay\Invoice;
use Shetabit\Payment\Facade\Payment;

class PaymentController extends ApiController
{
    public function payment(Request $request,Order $order,OrderDetail $orderDetail)
    {
       $total_price = PaymentService::calculateTotalPrice($request);
       $order = $order->newOrder($request,$total_price);
       $orderDetail->newOrderDetail($request,$order);

        $invoice = (new Invoice)->amount($total_price*1000);
        $result = Payment::purchase($invoice,function($driver, $transactionId)use($order) {
            $order->update(['transaction_id' => $transactionId]);
        })->pay()->toJson();
        return json_decode($result);
    }

    public function callback(Request $request)
    {
        $transaction_id = $request->get('token');
        $order = Order::query()->where('transaction_id',$transaction_id)->first();
        $orderDetails = OrderDetail::query()->where('order_id',$order->id)->get();
        if ($request->get('status') == 'success') {
            $order->update(['status' => 'success']);
            foreach ($orderDetails as $orderDetail) {
                $product = Product::query()->find($orderDetail->product_id);
                $product->update(['quantity'=>$product->quantity - $orderDetail->count]);
                $orderDetail->update(['status' => 'success']);
            }
            return $this->successResponse(201,$order, 'payment success');

        }else{
            $order->update(['status' => 'fail']);
            foreach ($orderDetails as $orderDetail) {
                $orderDetail->update(['status' => 'fail']);
        }
            return $this->successResponse(201,$order, 'payment failed');
        }
    }
}
