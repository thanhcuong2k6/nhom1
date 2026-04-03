<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class AdminOrderController extends Controller
{
    public function index()
    {
        $orders = Order::with('user')
            ->when(request('status'), function ($q) {
                return $q->where('status', request('status'));
            })
            ->latest()
            ->paginate(20);

        return view('admin.orders.index', compact('orders'));
    }

    public function show($id)
    {
        $order = Order::with('items.product', 'user', 'payment', 'shipping')->findOrFail($id);
        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled',
        ]);

        $statusUpdates = [
            'shipped' => ['shipped_at' => now()],
            'delivered' => ['delivered_at' => now()],
        ];

        $order->update(array_merge(
            $validated,
            $statusUpdates[$validated['status']] ?? []
        ));

        return back()->with('success', 'Trạng thái đã được cập nhật');
    }

    public function updatePayment(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $validated = $request->validate([
            'payment_status' => 'required|in:paid,unpaid',
        ]);

        $order->update($validated);
        $order->payment()->update($validated);

        return back()->with('success', 'Trạng thái thanh toán đã được cập nhật');
    }
}
