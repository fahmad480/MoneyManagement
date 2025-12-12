<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            // Expense Categories
            ['name' => 'Makanan & Minuman', 'icon' => '🍔', 'color' => '#FF6B6B', 'type' => 'expense', 'description' => 'Pengeluaran untuk makanan dan minuman sehari-hari'],
            ['name' => 'Transport', 'icon' => '🚗', 'color' => '#4ECDC4', 'type' => 'expense', 'description' => 'Biaya transportasi dan bahan bakar'],
            ['name' => 'Belanja', 'icon' => '🛍️', 'color' => '#FFE66D', 'type' => 'expense', 'description' => 'Belanja kebutuhan dan barang-barang'],
            ['name' => 'Hiburan', 'icon' => '🎮', 'color' => '#95E1D3', 'type' => 'expense', 'description' => 'Hiburan, rekreasi, dan hobi'],
            ['name' => 'Kesehatan', 'icon' => '💊', 'color' => '#F38181', 'type' => 'expense', 'description' => 'Biaya kesehatan dan medis'],
            ['name' => 'Pendidikan', 'icon' => '📚', 'color' => '#6C5CE7', 'type' => 'expense', 'description' => 'Biaya pendidikan dan kursus'],
            ['name' => 'Tagihan', 'icon' => '💳', 'color' => '#FD79A8', 'type' => 'expense', 'description' => 'Tagihan bulanan seperti listrik, air, internet'],
            ['name' => 'Investasi', 'icon' => '📈', 'color' => '#00B894', 'type' => 'expense', 'description' => 'Pengeluaran untuk investasi'],
            ['name' => 'Keluarga', 'icon' => '👨‍👩‍👧‍👦', 'color' => '#FDCB6E', 'type' => 'expense', 'description' => 'Pengeluaran untuk kebutuhan keluarga'],
            ['name' => 'Donasi', 'icon' => '💝', 'color' => '#E17055', 'type' => 'expense', 'description' => 'Sumbangan dan donasi'],
            ['name' => 'Rumah Tangga', 'icon' => '🏠', 'color' => '#A29BFE', 'type' => 'expense', 'description' => 'Kebutuhan rumah tangga'],
            ['name' => 'Pakaian', 'icon' => '👔', 'color' => '#FF7675', 'type' => 'expense', 'description' => 'Pembelian pakaian dan aksesoris'],
            ['name' => 'Lainnya', 'icon' => '📦', 'color' => '#B2BEC3', 'type' => 'expense', 'description' => 'Pengeluaran lainnya'],
            
            // Income Categories
            ['name' => 'Gaji', 'icon' => '💰', 'color' => '#00B894', 'type' => 'income', 'description' => 'Pendapatan dari gaji tetap'],
            ['name' => 'Freelance', 'icon' => '💼', 'color' => '#0984E3', 'type' => 'income', 'description' => 'Pendapatan dari pekerjaan freelance'],
            ['name' => 'Bisnis', 'icon' => '🏢', 'color' => '#6C5CE7', 'type' => 'income', 'description' => 'Pendapatan dari bisnis'],
            ['name' => 'Investasi', 'icon' => '📊', 'color' => '#FDCB6E', 'type' => 'income', 'description' => 'Pendapatan dari hasil investasi'],
            ['name' => 'Hadiah', 'icon' => '🎁', 'color' => '#FD79A8', 'type' => 'income', 'description' => 'Hadiah atau pemberian'],
            ['name' => 'Bonus', 'icon' => '🎉', 'color' => '#55EFC4', 'type' => 'income', 'description' => 'Bonus dari pekerjaan'],
            ['name' => 'Lainnya', 'icon' => '💵', 'color' => '#DFE6E9', 'type' => 'income', 'description' => 'Pendapatan lainnya'],
        ];

        // Create default categories (user_id = null)
        foreach ($categories as $category) {
            Category::firstOrCreate(
                [
                    'name' => $category['name'],
                    'type' => $category['type'],
                    'user_id' => null
                ],
                [
                    'icon' => $category['icon'],
                    'color' => $category['color'],
                    'description' => $category['description'],
                    'is_active' => true,
                ]
            );
        }

        // Optionally create categories for the first user if exists
        $firstUser = \App\Models\User::first();
        if ($firstUser) {
            // You can optionally seed some categories for the first user
            // This is just an example, remove if not needed
            $userCategories = [
                ['name' => 'Kustom 1', 'icon' => '⭐', 'color' => '#FF6B6B', 'type' => 'expense', 'description' => 'Kategori kustom user'],
            ];
            
            foreach ($userCategories as $category) {
                $category['user_id'] = $firstUser->id;
                Category::firstOrCreate(
                    [
                        'name' => $category['name'],
                        'type' => $category['type'],
                        'user_id' => $firstUser->id
                    ],
                    $category
                );
            }
        }
    }
}
