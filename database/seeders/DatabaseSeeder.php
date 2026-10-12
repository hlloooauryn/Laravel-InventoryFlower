<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Users & Profiles
        $admin = User::firstOrCreate(
            ['username' => 'admin'],
            [
                'password' => Hash::make('admin123'),
                'role' => 'admin',
            ]
        );

        UserProfile::updateOrCreate(
            ['user_id' => $admin->id],
            ['full_name' => 'Administrator']
        );

        $staff = User::firstOrCreate(
            ['username' => 'staff'],
            [
                'password' => Hash::make('staff123'),
                'role' => 'staff',
            ]
        );

        UserProfile::updateOrCreate(
            ['user_id' => $staff->id],
            ['full_name' => 'Staff Toko']
        );

        // 2. Seed Categories
        $categoriesData = [
            'Bunga Potong',
            'Bunga Hias',
            'Tanaman Hias',
            'Buket',
            'Aksesori',
        ];

        $categories = [];
        foreach ($categoriesData as $catName) {
            $categories[$catName] = Category::firstOrCreate(['name' => $catName]);
        }

        // 3. Seed Suppliers
        $sup1 = Supplier::firstOrCreate(
            ['name' => 'CV Florist Nusantara'],
            ['contact' => '081234567890', 'address' => 'Jl. Kebon Jeruk No. 12, Bandung']
        );

        $sup2 = Supplier::firstOrCreate(
            ['name' => 'PT Agrindo Mega Flora'],
            ['contact' => '089876543210', 'address' => 'Jl. Merdeka Raya No. 45, Malang']
        );

        // 4. Seed Products
        $p1 = Product::firstOrCreate(
            ['sku' => 'FL-ROSERED'],
            [
                'category_id' => $categories['Bunga Potong']->id,
                'name' => 'Mawar Merah Premium (Stem)',
                'price' => 15000.00,
                'stock' => 50,
                'minimum_stock' => 10,
            ]
        );

        $p2 = Product::firstOrCreate(
            ['sku' => 'FL-LILYWHT'],
            [
                'category_id' => $categories['Bunga Potong']->id,
                'name' => 'Bunga Lily Putih Casablanca',
                'price' => 35000.00,
                'stock' => 3,
                'minimum_stock' => 5,
            ]
        );

        $p3 = Product::firstOrCreate(
            ['sku' => 'FL-BKWDDNG'],
            [
                'category_id' => $categories['Buket']->id,
                'name' => 'Buket Bunga Pengantin Pastel',
                'price' => 250000.00,
                'stock' => 8,
                'minimum_stock' => 3,
            ]
        );

        // Attach suppliers
        $p1->suppliers()->syncWithoutDetaching([
            $sup1->id => ['supplier_price' => 9000.00],
            $sup2->id => ['supplier_price' => 8500.00],
        ]);

        $p2->suppliers()->syncWithoutDetaching([
            $sup2->id => ['supplier_price' => 22000.00],
        ]);

        // 5. Seed Initial Stock Movements
        StockMovement::firstOrCreate(
            ['product_id' => $p1->id, 'type' => 'IN', 'quantity' => 50],
            ['user_id' => $admin->id, 'note' => 'Stok awal setup sistem']
        );

        StockMovement::firstOrCreate(
            ['product_id' => $p2->id, 'type' => 'IN', 'quantity' => 3],
            ['user_id' => $admin->id, 'note' => 'Stok awal setup sistem']
        );
    }
}
