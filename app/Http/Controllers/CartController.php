<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cartItems = $this->getCartItems();
        $total = $cartItems->sum(function ($item) {
            $price = $item->product->discount_price ?? $item->product->price;
            return $price * $item->quantity;
        });

        return view('cart.index', compact('cartItems', 'total'));
    }

    public function add(Request $request)
    {
        $product = Product::where('is_active', true)->findOrFail($request->product_id);

        if ($product->stock <= 0) {
            return response()->json(['error' => 'Sản phẩm hết hàng'], 422);
        }

        $sessionId = session()->getId();
        $userId = auth()->id();

        $cartItem = CartItem::where(function ($q) use ($userId, $sessionId) {
            $q->where('user_id', $userId)->orWhere('session_id', $sessionId);
        })->where('product_id', $product->id)->first();

        if ($cartItem) {
            $cartItem->increment('quantity', $request->quantity ?? 1);
        } else {
            CartItem::create([
                'user_id' => $userId,
                'session_id' => $sessionId,
                'product_id' => $product->id,
                'quantity' => $request->quantity ?? 1,
            ]);
        }

        return response()->json(['success' => 'Thêm vào giỏ hàng thành công']);
    }

    public function update(Request $request, $id)
    {
        $cartItem = CartItem::findOrFail($id);
        $quantity = $request->quantity;

        if ($quantity <= 0) {
            $cartItem->delete();
        } else {
            if ($cartItem->product->stock < $quantity) {
                return response()->json(['error' => 'Số lượng không đủ'], 422);
            }
            $cartItem->update(['quantity' => $quantity]);
        }

        return response()->json(['success' => 'Cập nhật giỏ hàng thành công']);
    }

    public function remove($id)
    {
        $cartItem = CartItem::findOrFail($id);
        $cartItem->delete();

        return response()->json(['success' => 'Xóa khỏi giỏ hàng thành công']);
    }

    public function clear()
    {
        $sessionId = session()->getId();
        $userId = auth()->id();

        CartItem::where(function ($q) use ($userId, $sessionId) {
            $q->where('user_id', $userId)->orWhere('session_id', $sessionId);
        })->delete();

        return response()->json(['success' => 'Xóa tất cả thành công']);
    }

    private function getCartItems()
    {
        $sessionId = session()->getId();
        $userId = auth()->id();

        return CartItem::with('product')
            ->where(function ($q) use ($userId, $sessionId) {
                $q->where('user_id', $userId)->orWhere('session_id', $sessionId);
            })
            ->get();
    }
}
