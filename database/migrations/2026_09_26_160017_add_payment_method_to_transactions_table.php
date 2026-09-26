<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('payment_method')->default('Cash')->after('amount');
        });

        // Add new categories requested by user
        $newCategories = [
            ['name' => 'Biaya Kehidupan', 'type' => 'expense', 'icon' => 'sparkles', 'color' => '#6366F1'],
            ['name' => 'Biaya Kost', 'type' => 'expense', 'icon' => 'home', 'color' => '#0EA5E9'],
        ];

        foreach ($newCategories as $cat) {
            \App\Models\Category::firstOrCreate(
                ['name' => $cat['name'], 'type' => $cat['type']],
                ['icon' => $cat['icon'], 'color' => $cat['color']]
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('payment_method');
        });

        \App\Models\Category::whereIn('name', ['Biaya Kehidupan', 'Biaya Kost'])->where('type', 'expense')->delete();
    }
};
