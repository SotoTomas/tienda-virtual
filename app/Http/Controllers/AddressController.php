<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class AddressController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Account/Addresses', [
            'addresses' => auth()->user()
                ->addresses()
                ->get()
                ->map(fn ($a) => [
                    'id'          => $a->id,
                    'name'        => $a->name,
                    'phone'       => $a->phone,
                    'street'      => $a->street,
                    'number'      => $a->number,
                    'floor'       => $a->floor,
                    'apartment'   => $a->apartment,
                    'city'        => $a->city,
                    'state'       => $a->state,
                    'postal_code' => $a->postal_code,
                    'country'     => $a->country,
                    'is_default'  => $a->is_default,
                ]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'phone'       => 'nullable|string|max:20',
            'street'      => 'required|string|max:255',
            'number'      => 'required|string|max:20',
            'floor'       => 'nullable|string|max:10',
            'apartment'   => 'nullable|string|max:10',
            'city'        => 'required|string|max:100',
            'state'       => 'required|string|max:100',
            'postal_code' => 'required|string|max:10',
            'country'     => 'required|string|max:5',
            'is_default'  => 'boolean',
        ]);

        // Si es default, sacar default a las demás
        if (! empty($validated['is_default'])) {
            auth()->user()->addresses()->update(['is_default' => false]);
        }

        // Primera dirección es default automáticamente
        if (! auth()->user()->addresses()->exists()) {
            $validated['is_default'] = true;
        }

        auth()->user()->addresses()->create($validated);

        return back()->with('success', 'Dirección agregada.');
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $address = auth()->user()->addresses()->findOrFail($id);

        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'phone'       => 'nullable|string|max:20',
            'street'      => 'required|string|max:255',
            'number'      => 'required|string|max:20',
            'floor'       => 'nullable|string|max:10',
            'apartment'   => 'nullable|string|max:10',
            'city'        => 'required|string|max:100',
            'state'       => 'required|string|max:100',
            'postal_code' => 'required|string|max:10',
            'country'     => 'required|string|max:5',
            'is_default'  => 'boolean',
        ]);

        if (! empty($validated['is_default'])) {
            auth()->user()->addresses()->update(['is_default' => false]);
        }

        $address->update($validated);

        return back()->with('success', 'Dirección actualizada.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $address = auth()->user()->addresses()->findOrFail($id);
        $address->delete();

        // Si era default, asignar default a la siguiente
        if ($address->is_default) {
            auth()->user()->addresses()->first()?->update(['is_default' => true]);
        }

        return back()->with('success', 'Dirección eliminada.');
    }
}