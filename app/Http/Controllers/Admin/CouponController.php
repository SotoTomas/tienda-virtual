<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Enums\CouponType;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class CouponController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Coupons/Index', [
            'coupons' => Coupon::latest()->get()->map(fn ($c) => [
                'id'             => $c->id,
                'code'           => $c->code,
                'type'           => $c->type->value,
                'value'          => $c->value,
                'minimum_amount' => $c->minimum_amount,
                'usage_limit'    => $c->usage_limit,
                'used_count'     => $c->used_count,
                'is_active'      => $c->is_active,
                'expires_at'     => $c->expires_at?->format('d/m/Y H:i'),
            ]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'code'           => 'required|string|unique:coupons,code',
            'type'           => 'required|in:percentage,fixed',
            'value'          => 'required|numeric|min:0',
            'minimum_amount' => 'nullable|numeric|min:0',
            'usage_limit'    => 'nullable|integer|min:1',
            'is_active'      => 'boolean',
            'expires_at'     => 'nullable|date|after:now',
        ]);

        Coupon::create([
            ...$request->only('code', 'type', 'value', 'minimum_amount', 'usage_limit', 'is_active', 'expires_at'),
            'code' => strtoupper($request->code),
        ]);

        return back()->with('success', 'Cupón creado.');
    }

    public function update(Request $request, Coupon $coupon): RedirectResponse
    {
        $request->validate([
            'is_active' => 'required|boolean',
        ]);

        $coupon->update(['is_active' => $request->is_active]);

        return back()->with('success', 'Cupón actualizado.');
    }

    public function destroy(Coupon $coupon): RedirectResponse
    {
        $coupon->delete();

        return back()->with('success', 'Cupón eliminado.');
    }
}