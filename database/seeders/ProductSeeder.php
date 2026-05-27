<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Categorías hijas donde van los productos
        $remeras   = Category::where('slug', 'remeras')->first();
        $zapatillas = Category::where('slug', 'zapatillas')->first();
        $bolsos    = Category::where('slug', 'bolsos')->first();

        $products = [
            // ── Remeras ──────────────────────────────────────
            [
                'category_id'       => $remeras->id,
                'name'              => 'Remera Básica Algodón',
                'short_description' => 'Remera 100% algodón, corte regular.',
                'description'       => 'Remera de algodón peinado de alta calidad. Disponible en múltiples colores y talles. Ideal para el uso diario.',
                'price'             => 8500.00,
                'compare_price'     => 11000.00,
                'sku'               => 'REM-001',
                'stock'             => 50,
                'is_featured'       => true,
                'variants' => [
                    ['name' => 'Blanco - S',  'options' => ['color' => 'Blanco', 'talle' => 'S'],  'stock' => 10],
                    ['name' => 'Blanco - M',  'options' => ['color' => 'Blanco', 'talle' => 'M'],  'stock' => 15],
                    ['name' => 'Blanco - L',  'options' => ['color' => 'Blanco', 'talle' => 'L'],  'stock' => 10],
                    ['name' => 'Negro - S',   'options' => ['color' => 'Negro',  'talle' => 'S'],  'stock' => 5],
                    ['name' => 'Negro - M',   'options' => ['color' => 'Negro',  'talle' => 'M'],  'stock' => 10],
                ],
            ],
            [
                'category_id'       => $remeras->id,
                'name'              => 'Remera Estampada Oversize',
                'short_description' => 'Remera oversize con estampa frontal.',
                'description'       => 'Diseño urbano con corte holgado. Tela de alta gramaje.',
                'price'             => 12000.00,
                'compare_price'     => null,
                'sku'               => 'REM-002',
                'stock'             => 30,
                'is_featured'       => false,
                'variants' => [
                    ['name' => 'Gris - M',  'options' => ['color' => 'Gris', 'talle' => 'M'],  'stock' => 10],
                    ['name' => 'Gris - L',  'options' => ['color' => 'Gris', 'talle' => 'L'],  'stock' => 10],
                    ['name' => 'Gris - XL', 'options' => ['color' => 'Gris', 'talle' => 'XL'], 'stock' => 10],
                ],
            ],

            // ── Zapatillas ───────────────────────────────────
            [
                'category_id'       => $zapatillas->id,
                'name'              => 'Zapatilla Running Pro',
                'short_description' => 'Zapatilla deportiva para running.',
                'description'       => 'Suela de amortiguación máxima, upper de mesh transpirable. Ideal para largas distancias.',
                'price'             => 65000.00,
                'compare_price'     => 80000.00,
                'sku'               => 'ZAP-001',
                'stock'             => 20,
                'is_featured'       => true,
                'variants' => [
                    ['name' => 'Blanco - 39', 'options' => ['color' => 'Blanco', 'talle' => '39'], 'stock' => 3],
                    ['name' => 'Blanco - 40', 'options' => ['color' => 'Blanco', 'talle' => '40'], 'stock' => 4],
                    ['name' => 'Blanco - 41', 'options' => ['color' => 'Blanco', 'talle' => '41'], 'stock' => 5],
                    ['name' => 'Negro - 40',  'options' => ['color' => 'Negro',  'talle' => '40'], 'stock' => 4],
                    ['name' => 'Negro - 41',  'options' => ['color' => 'Negro',  'talle' => '41'], 'stock' => 4],
                ],
            ],
            [
                'category_id'       => $zapatillas->id,
                'name'              => 'Zapatilla Urbana Classic',
                'short_description' => 'Zapatilla casual para el día a día.',
                'description'       => 'Diseño minimalista con suela vulcanizada. Combinable con cualquier outfit.',
                'price'             => 45000.00,
                'compare_price'     => null,
                'sku'               => 'ZAP-002',
                'stock'             => 15,
                'is_featured'       => false,
                'variants' => [
                    ['name' => 'Crema - 38', 'options' => ['color' => 'Crema', 'talle' => '38'], 'stock' => 5],
                    ['name' => 'Crema - 39', 'options' => ['color' => 'Crema', 'talle' => '39'], 'stock' => 5],
                    ['name' => 'Crema - 40', 'options' => ['color' => 'Crema', 'talle' => '40'], 'stock' => 5],
                ],
            ],

            // ── Bolsos ───────────────────────────────────────
            [
                'category_id'       => $bolsos->id,
                'name'              => 'Bolso Tote de Cuero',
                'short_description' => 'Bolso tote en cuero ecológico.',
                'description'       => 'Amplio interior con bolsillo organizador. Asa de mano y bandolera removible.',
                'price'             => 28000.00,
                'compare_price'     => 35000.00,
                'sku'               => 'BOL-001',
                'stock'             => 10,
                'is_featured'       => true,
                'variants' => [
                    ['name' => 'Negro',   'options' => ['color' => 'Negro'],   'stock' => 4],
                    ['name' => 'Camel',   'options' => ['color' => 'Camel'],   'stock' => 3],
                    ['name' => 'Bordó',   'options' => ['color' => 'Bordó'],   'stock' => 3],
                ],
            ],
        ];

        foreach ($products as $data) {
            $variants = $data['variants'];
            unset($data['variants']);

            $product = Product::create([
                ...$data,
                'slug'      => Str::slug($data['name']),
                'is_active' => true,
                'weight'    => 0.5,
            ]);

            foreach ($variants as $variantData) {
                $product->variants()->create([
                    ...$variantData,
                    'is_active' => true,
                ]);
            }
        }
    }
}