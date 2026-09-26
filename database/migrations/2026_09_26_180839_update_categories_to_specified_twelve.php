<?php

use App\Models\Budget;
use App\Models\Category;
use App\Models\Transaction;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $approvedCategories = [
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

        // 1. Create or ensure the 12 approved categories exist for both expense and income
        $newExpenseCategoryMap = [];
        $newIncomeCategoryMap = [];

        foreach ($approvedCategories as $cat) {
            $expCat = Category::firstOrCreate(
                ['name' => $cat['name'], 'type' => 'expense'],
                ['icon' => $cat['icon'], 'color' => $cat['color']]
            );
            $newExpenseCategoryMap[$cat['name']] = $expCat->id;

            $incCat = Category::firstOrCreate(
                ['name' => $cat['name'], 'type' => 'income'],
                ['icon' => $cat['icon'], 'color' => $cat['color']]
            );
            $newIncomeCategoryMap[$cat['name']] = $incCat->id;
        }

        // 2. Map old category names to new category names
        $legacyNameMapping = [
            'Makanan & Minuman' => 'Biaya Makan',
            'Tagihan (listrik/air/internet/pulsa)' => 'Biaya Tagihan',
            'Belanja Kebutuhan' => 'Lain Lain',
            'Kesehatan' => 'Lain Lain',
            'Biaya Kehidupan' => 'Biaya Makan',
            'Bayar Utang/Cicilan' => 'Utang',
            'Transfer ke Orang Tua/Keluarga' => 'Transfer Orangtua/Keluarga',
            'Transfer dari Orang Tua/Keluarga' => 'Transfer Orangtua/Keluarga',
            'Tabungan/Investasi' => 'Tabungan',
            'Pinjaman Diterima' => 'Pinjaman',
            'Gaji' => 'Lain Lain',
            'Bonus/THR' => 'Lain Lain',
            'Usaha/Bisnis' => 'Lain Lain',
            'Hasil Investasi' => 'Tabungan',
            'Hadiah/Pemberian' => 'Lain Lain',
            'Lain-lain' => 'Lain Lain',
            'Transportasi' => 'Transportasi',
            'Hiburan' => 'Hiburan',
            'Pendidikan' => 'Pendidikan',
            'Biaya Kost' => 'Biaya Kost',
        ];

        // 3. Migrate all existing transactions and budgets to new categories
        $allOldCategories = Category::all();
        foreach ($allOldCategories as $oldCat) {
            $targetName = $legacyNameMapping[$oldCat->name] ?? (in_array($oldCat->name, array_column($approvedCategories, 'name')) ? $oldCat->name : 'Lain Lain');
            $newCatId = $oldCat->type === 'expense'
                ? ($newExpenseCategoryMap[$targetName] ?? $newExpenseCategoryMap['Lain Lain'])
                : ($newIncomeCategoryMap[$targetName] ?? $newIncomeCategoryMap['Lain Lain']);

            if ($oldCat->id !== $newCatId) {
                DB::table('transactions')
                    ->where('category_id', $oldCat->id)
                    ->update(['category_id' => $newCatId]);

                DB::table('budgets')
                    ->where('category_id', $oldCat->id)
                    ->update(['category_id' => $newCatId]);
            }
        }

        // 4. Delete any categories that are not among the 12 approved names
        $approvedNames = array_column($approvedCategories, 'name');
        Category::whereNotIn('name', $approvedNames)->delete();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No reverse needed as we keep data intact
    }
};
