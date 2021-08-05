<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Course;
use App\Models\UserCourse;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Transformers\BaseTransformer;

class OrderController extends Controller
{
    public static $model = Order::class;

    public function create(Request $request)
    {
        $request->validate([
            'course_id' => 'required'
        ]);

        $course = Course::findOrFail($request->course_id)->with('category')->first();

        $user = Auth::user();

        $order = Order::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'amount' => $course->price
        ]);

        if($course->price === 0){
            return UserCourse::create([
                'user_id' => $user->id,
                'course_id' => $course->id,
                'status' => 'ACTIVE'
            ]);
        }else{
            $transactionDetails = [
                'order_id' => $order->id,
                'gross_amount' => $course->price
            ];


            $itemDetails = [
                [
                    'id' => $course->id,
                    'price' => $course->price,
                    'quantity' => 1,
                    'name' => $course->name,
                    'brand' => env('APP_NAME'),
                    'category' => $course->category->name
                ]
            ];

            $customerDetails = [
                'first_name' => $user->name,
                'email' => $user->email
            ];

            $midtransParams = [
                'transaction_details' => $transactionDetails,
                'item_details' => $itemDetails,
                'customer_details' => $customerDetails
            ];

            $order->snap_id = $this->getMidtransSnapToken($midtransParams);

            $order->metadata = [
                'course_id' => $course->id,
                'course_price' => $course->price,
                'course_name' => $course->name,
                'course_thumbnail' => $course->thumbnail,
                'course_level' => $course->level,
                'course_type' => $course->type,
            ];

            $order->save();

            return $this->response->item($order, new BaseTransformer);
        }
    }


    private function getMidtransSnapToken($params)
    {
        \Midtrans\Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        \Midtrans\Config::$isProduction = (bool) env('MIDTRANS_PRODUCTION');
        \Midtrans\Config::$is3ds = (bool) env('MIDTRANS_3DS');

        return \Midtrans\Snap::getSnapToken($params);
    }
}
