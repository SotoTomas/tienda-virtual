<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ReviewController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Reviews/Index', [
            'reviews' => Review::with('user', 'product')
                ->latest()
                ->paginate(20)
                ->through(fn ($r) => [
                    'id'           => $r->id,
                    'rating'       => $r->rating,
                    'title'        => $r->title,
                    'body'         => $r->body,
                    'is_approved'  => $r->is_approved,
                    'user_name'    => $r->user->name,
                    'product_name' => $r->product->name,
                    'created_at'   => $r->created_at->diffForHumans(),
                ]),
        ]);
    }

    public function approve(Review $review): RedirectResponse
    {
        $review->update(['is_approved' => ! $review->is_approved]);

        return back()->with('success', $review->is_approved
            ? 'Reseña aprobada.'
            : 'Reseña desaprobada.'
        );
    }

    public function destroy(Review $review): RedirectResponse
    {
        $review->delete();

        return back()->with('success', 'Reseña eliminada.');
    }
}