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
            ['name' => 'Makanan & Minuman', 'icon' => '🍔', 'color' => '#FF6B6B', 'type' => 'expense'],
            ['name' => 'Transport', 'icon' => '🚗', 'color' => '#4ECDC4', 'type' => 'expense'],
            ['name' => 'Belanja', 'icon' => '🛍️', 'color' => '#FFE66D', 'type' => 'expense'],
            ['name' => 'Hiburan', 'icon' => '🎮', 'color' => '#95E1D3', 'type' => 'expense'],
            ['name' => 'Kesehatan', 'icon' => '💊', 'color' => '#F38181', 'type' => 'expense'],
            ['name' => 'Pendidikan', 'icon' => '📚', 'color' => '#6C5CE7', 'type' => 'expense'],
            ['name' => 'Tagihan', 'icon' => '💳', 'color' => '#FD79A8', 'type' => 'expense'],
            ['name' => 'Investasi', 'icon' => '📈', 'color' => '#00B894', 'type' => 'expense'],
            ['name' => 'Keluarga', 'icon' => '👨‍👩‍👧‍👦', 'color' => '#FDCB6E', 'type' => 'expense'],
            ['name' => 'Donasi', 'icon' => '💝', 'color' => '#E17055', 'type' => 'expense'],
            ['name' => 'Lainnya', 'icon' => '📦', 'color' => '#B2BEC3', 'type' => 'expense'],
            
            // Income Categories
            ['name' => 'Gaji', 'icon' => '💰', 'color' => '#00B894', 'type' => 'income'],
            ['name' => 'Freelance', 'icon' => '💼', 'color' => '#0984E3', 'type' => 'income'],
            ['name' => 'Bisnis', 'icon' => '🏢', 'color' => '#6C5CE7', 'type' => 'income'],
            ['name' => 'Investasi', 'icon' => '📊', 'color' => '#FDCB6E', 'type' => 'income'],
            ['name' => 'Hadiah', 'icon' => '🎁', 'color' => '#FD79A8', 'type' => 'income'],
            ['name' => 'Bonus', 'icon' => '🎉', 'color' => '#55EFC4', 'type' => 'income'],
            ['name' => 'Lainnya', 'icon' => '💵', 'color' => '#DFE6E9', 'type' => 'income'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
