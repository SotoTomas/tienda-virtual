<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request, Product $product): RedirectResponse
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'title'  => 'nullable|string|max:255',
            'body'   => 'nullable|string|max:1000',
        ]);

        // Un usuario solo puede reseñar el mismo producto una vez
        $exists = Review::where('product_id', $product->id)
            ->where('user_id', auth()->id())
            ->exists();

        if ($exists) {
            return back()->with('error', 'Ya dejaste una reseña para este producto.');
        }

        Review::create([
            'product_id'  => $product->id,
            'user_id'     => auth()->id(),
            'rating'      => $request->rating,
            'title'       => $request->title,
            'body'        => $request->body,
            'is_approved' => false,
        ]);

        return back()->with('success', 'Reseña enviada. Será revisada antes de publicarse.');
    }
}