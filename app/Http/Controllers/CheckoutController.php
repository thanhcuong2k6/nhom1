<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Discount;
use App\Models\UserAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function index()
    {
        $cartItems = auth()->user()->cartItems()->with('product')->get();

        if ($cartItems->isEmpty()) {
            return redirect('/cart')->with('error', 'Giỏ hàng trống');
        }

        $addresses = auth()->user()->addresses;
        $subtotal = $cartItems->sum(function ($item) {
            $price = $item->product->discount_price ?? $item->product->price;
            return $price * $item->quantity;
        });

        return view('checkout.index', compact('cartItems', 'addresses', 'subtotal'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'required|string',
            'phone' => 'required|string',
            'address' => 'required|string',
            'city' => 'required|string',
            'province' => 'required|string',
            'postal_code' => 'required|string',
            'payment_method' => 'required|in:cod,bank_transfer,ewallet',
            'discount_code' => 'nullable|string',
        ]);

        $cartItems = auth()->user()->cartItems()->with('product')->get();

        if ($cartItems->isEmpty()) {
            return back()->with('error', 'Giỏ hàng trống');
        }

        $subtotal = $cartItems->sum(function ($item) {
            $price = $item->product->discount_price ?? $item->product->price;
            return $price * $item->quantity;
        });

        $discountAmount = 0;
        if ($request->discount_code) {
            $discount = Discount::where('code', $request->discount_code)
                ->where('is_active', true)
                ->first();

            if ($discount && $discount->isValid() && $subtotal >= $discount->min_order_value) {
                if ($discount->discount_type == 1) { // percentage
                    $discountAmount = ($subtotal * $discount->discount_value) / 100;
                    if ($discount->max_discount) {
                        $discountAmount = min($discountAmount, $discount->max_discount);
                    }
                } else { // fixed amount
                    $discountAmount = $discount->discount_value;
                }

                $discount->increment('used_count');
            }
        }

        $total = $subtotal - $discountAmount + 0; // 0 = shipping fee

        $order = Order::create([
            'user_id' => auth()->id(),
            'order_number' => 'ORD-' . Str::random(10),
            'status' => 'pending',
            'subtotal' => $subtotal,
            'shipping_fee' => 0,
            'discount_amount' => $discountAmount,
            'total' => $total,
            'payment_method' => $validated['payment_method'],
            'payment_status' => 'unpaid',
            'full_name' => $validated['full_name'],
            'phone' => $validated['phone'],
            'address' => $validated['address'],
            'city' => $validated['city'],
            'province' => $validated['province'],
            'postal_code' => $validated['postal_code'],
        ]);

        foreach ($cartItems as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'price' => $item->product->discount_price ?? $item->product->price,
            ]);

            $item->product->decrement('stock', $item->quantity);
        }

        Payment::create([
            'order_id' => $order->id,
            'payment_method' => $validated['payment_method'],
            'payment_status' => 'pending',
            'amount' => $total,
        ]);

        CartItem::whereIn('id', $cartItems->pluck('id'))->delete();

        return redirect("/order/{$order->id}")->with('success', 'Đơn hàng đã được tạo thành công');
    }

    public function applyDiscount(Request $request)
    {
        $code = $request->code;
        $discount = Discount::where('code', $code)
            ->where('is_active', true)
            ->first();

        if (!$discount) {
            return response()->json(['error' => 'Mã giảm giá không hợp lệ'], 422);
        }

        if (!$discount->isValid()) {
            return response()->json(['error' => 'Mã giảm giá đã hết hạn hoặc hết lượt sử dụng'], 422);
        }

        return response()->json([
            'discount_type' => $discount->discount_type,
            'discount_value' => $discount->discount_value,
            'max_discount' => $discount->max_discount,
            'min_order_value' => $discount->min_order_value,
        ]);
    }
}
