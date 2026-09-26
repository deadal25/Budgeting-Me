<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Biaya Kost', 'icon' => 'home', 'color' => '#0EA5E9'],
            ['name' => 'Biaya Makan', 'icon' => 'cake', 'color' => '#EF4444'],
            ['name' => 'Biaya Tagihan', 'icon' => 'receipt-tax', 'color' => '#F59E0B'],
            ['name' => 'Hiburan', 'icon' => 'film', 'color' => '#8B5CF6'],
            ['name' => 'Tabungan', 'icon' => 'arrow-trending-up', 'color' => '#10B981'],
            ['name' => 'Sedekah', 'icon' => 'heart', 'color' => '#14B8A6'],
            ['name' => 'Transfer Orangtua/Keluarga', 'icon' => 'user-group', 'color' => '#6366F1'],
            ['name' => 'Pinjaman', 'icon' => 'credit-card', 'color' => '#EC4899'],
            ['name' => 'Utang', 'icon' => 'shield-exclamation', 'color' => '#DC2626'],
            ['name' => 'Pendidikan', 'icon' => 'academic-cap', 'color' => '#3B82F6'],
            ['name' => 'Transportasi', 'icon' => 'truck', 'color' => '#F97316'],
            ['name' => 'Lain Lain', 'icon' => 'ellipsis-horizontal', 'color' => '#64748B'],
        ];

        foreach ($categories as $cat) {
            Category::firstOrCreate(
                ['name' => $cat['name'], 'type' => 'expense'],
                ['icon' => $cat['icon'], 'color' => $cat['color']]
            );
            Category::firstOrCreate(
                ['name' => $cat['name'], 'type' => 'income'],
                ['icon' => $cat['icon'], 'color' => $cat['color']]
            );
        }
    }
}
