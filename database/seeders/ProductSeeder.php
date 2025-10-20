<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Product::create([
            'name' => 'Sample Product',
            'slug' => 'sample-product',
            'description' => 'This is a sample product for seeding demonstration.',
            'price' => 49.99,
            'stock' => 25,
            'status' => 'active', // if applicable
            'category_id' => 1,   // adjust according to your schema
            'image' => 'products/sample.jpg', // optional
        ]);
    }
}
