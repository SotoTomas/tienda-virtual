<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'total_orders'    => Order::count(),
                'pending_orders'  => Order::byStatus(OrderStatus::Pending)->count(),
                'total_revenue'   => Order::paid()->sum('total'),
                'total_products'  => Product::active()->count(),
                'low_stock'       => Product::where('stock', '<=', 5)->where('manage_stock', true)->count(),
                'total_users'     => User::count(),
            ],
            'recentOrders' => Order::with('user')
                ->latest()
                ->take(10)
                ->get()
                ->map(fn ($o) => [
                    'id'             => $o->id,
                    'order_number'   => $o->order_number,
                    'customer'       => $o->user?->name ?? 'Guest',
                    'total'          => $o->total,
                    'status'         => $o->status,
                    'payment_status' => $o->payment_status,
                    'created_at'     => $o->created_at->format('d/m/Y H:i'),
                ]),
        ]);
    }
}