<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class CategoryController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Categories/Index', [
            'categories' => Category::with('parent')
                ->ordered()
                ->get()
                ->map(fn ($c) => [
                    'id'        => $c->id,
                    'name'      => $c->name,
                    'slug'      => $c->slug,
                    'is_active' => $c->is_active,
                    'parent_id' => $c->parent_id,
                    'parent'    => $c->parent ? ['name' => $c->parent->name] : null,
                ]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'slug'      => 'required|string|unique:categories,slug',
            'parent_id' => 'nullable|exists:categories,id',
            'is_active' => 'boolean',
        ]);

        Category::create($request->only('name', 'slug', 'parent_id', 'is_active'));

        return back()->with('success', 'Categoría creada.');
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'slug'      => 'required|string|unique:categories,slug,' . $category->id,
            'parent_id' => 'nullable|exists:categories,id',
            'is_active' => 'boolean',
        ]);

        $category->update($request->only('name', 'slug', 'parent_id', 'is_active'));

        return back()->with('success', 'Categoría actualizada.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        // Si tiene productos asociados no se puede eliminar
        if ($category->products()->exists()) {
            return back()->with('error', 'No podés eliminar una categoría con productos asociados.');
        }

        $category->delete();

        return back()->with('success', 'Categoría eliminada.');
    }
}