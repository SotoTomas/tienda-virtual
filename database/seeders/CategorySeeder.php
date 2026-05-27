<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name'        => 'Ropa',
                'description' => 'Indumentaria para hombre y mujer',
                'children'    => ['Remeras', 'Pantalones', 'Camperas', 'Vestidos'],
            ],
            [
                'name'        => 'Calzado',
                'description' => 'Zapatillas, zapatos y sandalias',
                'children'    => ['Zapatillas', 'Zapatos', 'Sandalias', 'Botas'],
            ],
            [
                'name'        => 'Accesorios',
                'description' => 'Bolsos, carteras y complementos',
                'children'    => ['Bolsos', 'Carteras', 'Cinturones', 'Gorros'],
            ],
        ];

        foreach ($categories as $data) {
            $parent = Category::create([
                'name'        => $data['name'],
                'slug'        => Str::slug($data['name']),
                'description' => $data['description'],
                'is_active'   => true,
                'sort_order'  => 0,
            ]);

            foreach ($data['children'] as $index => $childName) {
                Category::create([
                    'name'       => $childName,
                    'slug'       => Str::slug($childName),
                    'parent_id'  => $parent->id,
                    'is_active'  => true,
                    'sort_order' => $index,
                ]);
            }
        }
    }
}