<?php

namespace Database\Seeders;

use App\Models\Budget;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed categories
        $this->call(CategorySeeder::class);

        // 2. Seed Admin user (Alqadri)
        User::firstOrCreate(
            ['email' => 'alqad.ri2505@gmail.com'],
            [
                'name' => 'Alqadri (Admin)',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );
        User::firstOrCreate(
            ['email' => 'alqadri2505@gmail.com'],
            [
                'name' => 'Alqadri (Admin)',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        // 3. Seed Demo regular user
        $user = User::firstOrCreate(
            ['email' => 'user@budgetingme.com'],
            [
                'name' => 'Budi Santoso',
                'password' => Hash::make('password'),
                'role' => 'user',
                'email_verified_at' => now(),
            ]
        );

        // 4. Seed demo user 2
        $user2 = User::firstOrCreate(
            ['email' => 'siti@budgetingme.com'],
            [
                'name' => 'Siti Rahma',
                'password' => Hash::make('password'),
                'role' => 'user',
                'email_verified_at' => now(),
            ]
        );

        // 5. Seed some initial transactions for Budi Santoso
        $incomeCat = Category::where('name', 'Lain Lain')->where('type', 'income')->first();
        $transferCat = Category::where('name', 'Transfer Orangtua/Keluarga')->where('type', 'income')->first();
        $makananCat = Category::where('name', 'Biaya Makan')->where('type', 'expense')->first();
        $tagihanCat = Category::where('name', 'Biaya Tagihan')->where('type', 'expense')->first();
        $transportCat = Category::where('name', 'Transportasi')->where('type', 'expense')->first();
        $kostCat = Category::where('name', 'Biaya Kost')->where('type', 'expense')->first();

        $now = Carbon::now();

        // Income (Protected for Demo user)
        if ($incomeCat) {
            Transaction::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'notes' => 'Pemasukan bulanan kantor',
                ],
                [
                    'category_id' => $incomeCat->id,
                    'type' => 'income',
                    'amount' => 7500000,
                    'payment_method' => 'M-Banking',
                    'date' => $now->copy()->startOfMonth()->toDateString(),
                    'is_protected' => true,
                ]
            );
        }
        if ($transferCat) {
            Transaction::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'notes' => 'Transfer dari keluarga',
                ],
                [
                    'category_id' => $transferCat->id,
                    'type' => 'income',
                    'amount' => 1200000,
                    'payment_method' => 'E-Wallet',
                    'date' => $now->copy()->subDays(10)->toDateString(),
                    'is_protected' => true,
                ]
            );
        }

        // Expenses
        if ($tagihanCat) {
            Transaction::firstOrCreate([
                'user_id' => $user->id,
                'category_id' => $tagihanCat->id,
                'type' => 'expense',
                'amount' => 650000,
                'payment_method' => 'M-Banking',
                'date' => $now->copy()->subDays(12)->toDateString(),
                'notes' => 'Listrik PLN & WiFi Indihome',
            ]);
        }
        if ($makananCat) {
            Transaction::firstOrCreate([
                'user_id' => $user->id,
                'category_id' => $makananCat->id,
                'type' => 'expense',
                'amount' => 1250000,
                'payment_method' => 'Cash/Tunai',
                'date' => $now->copy()->subDays(5)->toDateString(),
                'notes' => 'Makan sehari-hari & groceries mingguan',
            ]);
        }
        if ($transportCat) {
            Transaction::firstOrCreate([
                'user_id' => $user->id,
                'category_id' => $transportCat->id,
                'type' => 'expense',
                'amount' => 350000,
                'payment_method' => 'E-Wallet',
                'date' => $now->copy()->subDays(3)->toDateString(),
                'notes' => 'Bensin & e-toll bulanan',
            ]);
        }
        if ($kostCat) {
            Transaction::firstOrCreate([
                'user_id' => $user->id,
                'category_id' => $kostCat->id,
                'type' => 'expense',
                'amount' => 1200000,
                'payment_method' => 'Rekening Tabungan',
                'date' => $now->copy()->subDays(1)->toDateString(),
                'notes' => 'Biaya sewa kost bulanan',
            ]);
        }

        // 6. Seed demo Budget for Budi Santoso (Protected for Demo user)
        Budget::firstOrCreate(
            [
                'user_id' => $user->id,
                'category_id' => null, // Total Monthly Limit
                'month' => $now->month,
                'year' => $now->year,
            ],
            [
                'limit_amount' => 4500000,
                'is_protected' => true,
            ]
        );

        if ($makananCat) {
            Budget::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'category_id' => $makananCat->id,
                    'month' => $now->month,
                    'year' => $now->year,
                ],
                [
                    'limit_amount' => 1800000,
                    'is_protected' => true,
                ]
            );
        }

        // Seed some for Siti
        if ($incomeCat) {
            Transaction::firstOrCreate(
                [
                    'user_id' => $user2->id,
                    'notes' => 'Pemasukan freelance',
                ],
                [
                    'category_id' => $incomeCat->id,
                    'type' => 'income',
                    'amount' => 5000000,
                    'payment_method' => 'M-Banking',
                    'date' => $now->copy()->startOfMonth()->toDateString(),
                    'is_protected' => true,
                ]
            );
        }
        if ($makananCat) {
            Transaction::firstOrCreate([
                'user_id' => $user2->id,
                'category_id' => $makananCat->id,
                'type' => 'expense',
                'amount' => 1500000,
                'payment_method' => 'Cash/Tunai',
                'date' => $now->copy()->subDays(8)->toDateString(),
                'notes' => 'Kuliner & makan siang',
            ]);
        }
    }
}
