<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('budgets', 'is_protected')) {
            Schema::table('budgets', function (Blueprint $table) {
                $table->boolean('is_protected')->default(false)->after('limit_amount');
            });
        }

        if (!Schema::hasColumn('transactions', 'is_protected')) {
            Schema::table('transactions', function (Blueprint $table) {
                $table->boolean('is_protected')->default(false)->after('notes');
            });
        }

        // Mark existing seeded demo budgets and demo incomes as protected
        $demoEmails = ['user@budgetingme.com', 'siti@budgetingme.com'];
        $demoUserIds = DB::table('users')->whereIn('email', $demoEmails)->pluck('id');

        if ($demoUserIds->isNotEmpty()) {
            DB::table('budgets')
                ->whereIn('user_id', $demoUserIds)
                ->update(['is_protected' => true]);

            DB::table('transactions')
                ->whereIn('user_id', $demoUserIds)
                ->where('type', 'income')
                ->update(['is_protected' => true]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('budgets', 'is_protected')) {
            Schema::table('budgets', function (Blueprint $table) {
                $table->dropColumn('is_protected');
            });
        }

        if (Schema::hasColumn('transactions', 'is_protected')) {
            Schema::table('transactions', function (Blueprint $table) {
                $table->dropColumn('is_protected');
            });
        }
    }
};
