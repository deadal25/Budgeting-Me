<?php

namespace Tests\Feature;

use App\Models\Budget;
use App\Models\Category;
use App\Models\Debt;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class BudgetingMeTest extends TestCase
{
    public function test_landing_page_can_be_rendered(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Budgeting-Me');
        $response->assertSee('Form Transaksi Cepat');
    }

    public function test_ajax_email_check_works_properly(): void
    {
        $user = User::where('email', 'user@budgetingme.com')->first();

        // 1. Registered email
        $response = $this->getJson('/check-email?email=user@budgetingme.com');
        $response->assertStatus(200);
        $response->assertJson([
            'registered' => true,
            'name' => $user->name,
        ]);

        // 2. Unregistered email
        $response = $this->getJson('/check-email?email=bukanuser@budgetingme.com');
        $response->assertStatus(200);
        $response->assertJson([
            'registered' => false,
        ]);
    }

    public function test_public_form_stores_transaction_for_registered_user(): void
    {
        $user = User::where('email', 'user@budgetingme.com')->first();
        $category = Category::where('type', 'expense')->first();

        $initialCount = Transaction::where('user_id', $user->id)->count();

        $response = $this->post('/public-transaction', [
            'email' => 'user@budgetingme.com',
            'type' => 'expense',
            'category_id' => $category->id,
            'amount' => 85000,
            'date' => date('Y-m-d'),
            'notes' => 'Beli bensin lewat form publik',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('public_success');

        $this->assertEquals($initialCount + 1, Transaction::where('user_id', $user->id)->count());

        $this->assertDatabaseHas('transactions', [
            'user_id' => $user->id,
            'category_id' => $category->id,
            'amount' => 85000,
            'notes' => 'Beli bensin lewat form publik',
        ]);
    }

    public function test_authenticated_user_can_access_dashboard(): void
    {
        $user = User::where('email', 'user@budgetingme.com')->first();

        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Budgetinku');
        $response->assertSee('Pemasukan Bulan Ini');
        $response->assertSee('Pengeluaran Bulan Ini');
    }

    public function test_user_can_perform_transaction_crud(): void
    {
        $user = User::where('email', 'user@budgetingme.com')->first();
        $category = Category::where('type', 'income')->first();

        // 1. Create
        $response = $this->actingAs($user)->post('/transactions', [
            'type' => 'income',
            'category_id' => $category->id,
            'amount' => 2000000,
            'date' => date('Y-m-d'),
            'notes' => 'Bonus project sampingan',
        ]);
        $response->assertRedirect();
        $response->assertSessionHas('success');

        $transaction = Transaction::where('user_id', $user->id)
            ->where('notes', 'Bonus project sampingan')
            ->first();
        $this->assertNotNull($transaction);

        // 2. Read Index
        $indexResponse = $this->actingAs($user)->get('/transactions');
        $indexResponse->assertStatus(200);
        $indexResponse->assertSee('Bonus project sampingan');

        // 3. Update
        $editResponse = $this->actingAs($user)->get("/transactions/{$transaction->id}/edit");
        $editResponse->assertStatus(200);

        $updateResponse = $this->actingAs($user)->put("/transactions/{$transaction->id}", [
            'type' => 'income',
            'category_id' => $category->id,
            'amount' => 2500000,
            'date' => date('Y-m-d'),
            'notes' => 'Bonus project sampingan revised',
        ]);
        $updateResponse->assertRedirect(route('transactions.index'));

        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'amount' => 2500000,
            'notes' => 'Bonus project sampingan revised',
        ]);

        // 4. Delete
        $deleteResponse = $this->actingAs($user)->delete("/transactions/{$transaction->id}");
        $deleteResponse->assertRedirect();
        $this->assertDatabaseMissing('transactions', [
            'id' => $transaction->id,
        ]);
    }

    public function test_user_can_export_transactions_pdf(): void
    {
        $user = User::where('email', 'user@budgetingme.com')->first();

        $response = $this->actingAs($user)->get('/transactions/export/pdf');
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_budgetinku_calculations_and_crud(): void
    {
        $user = User::factory()->create([
            'name' => 'Regular User',
            'email' => 'regular_user_budget@example.com',
            'role' => 'user',
        ]);
        $now = Carbon::now();

        // 1. Store/update budget limit
        $response = $this->actingAs($user)->post('/budgets', [
            'category_id' => null,
            'month' => $now->month,
            'year' => $now->year,
            'limit_amount' => 6000000,
        ]);
        $response->assertRedirect();
        $response->assertSessionHas('success');

        $budget = Budget::where('user_id', $user->id)
            ->whereNull('category_id')
            ->where('month', $now->month)
            ->where('year', $now->year)
            ->first();

        $this->assertNotNull($budget);
        $this->assertEquals(6000000, (int) $budget->limit_amount);

        // 2. Budget page view
        $budgetIndex = $this->actingAs($user)->get('/budgets');
        $budgetIndex->assertStatus(200);
        $budgetIndex->assertSee('Budgetinku');

        // Check badge calculations
        $this->assertContains($budget->status, ['safe', 'warning', 'danger']);
    }

    public function test_admin_authorization(): void
    {
        $user = User::where('email', 'user@budgetingme.com')->first();
        $admin = User::where('email', 'alqad.ri2505@gmail.com')->first();

        // 1. Regular user is forbidden from admin panel (403)
        $userForbidden = $this->actingAs($user)->get('/admin');
        $userForbidden->assertStatus(403);

        // 2. Admin can access admin dashboard
        $adminAccess = $this->actingAs($admin)->get('/admin');
        $adminAccess->assertStatus(200);
        $adminAccess->assertSee('Panel Administrator');
        $adminAccess->assertSee($user->name);

        // 3. Admin can view user detail (Read-Only)
        $showUser = $this->actingAs($admin)->get("/admin/users/{$user->id}");
        $showUser->assertStatus(200);
        $showUser->assertSee("Detail Pengguna: {$user->name}");
        $showUser->assertSee('Read-Only');
    }

    public function test_only_specified_twelve_categories_exist(): void
    {
        $expectedCategories = [
            'Biaya Kost',
            'Biaya Makan',
            'Biaya Tagihan',
            'Hiburan',
            'Tabungan',
            'Sedekah',
            'Transfer Orangtua/Keluarga',
            'Pinjaman',
            'Utang',
            'Pendidikan',
            'Transportasi',
            'Lain Lain',
        ];

        $distinctCategories = Category::pluck('name')->unique()->values()->all();
        sort($expectedCategories);
        sort($distinctCategories);

        $this->assertEquals($expectedCategories, $distinctCategories);
    }

    public function test_regular_user_and_public_form_only_have_four_payment_methods(): void
    {
        $user = User::where('email', 'user@budgetingme.com')->first();
        $admin = User::where('email', 'alqad.ri2505@gmail.com')->first();

        // 1. Regular user gets only 4 payment methods
        $userMethods = Transaction::getAvailablePaymentMethods($user);
        $this->assertCount(4, $userMethods);
        $this->assertEquals(['E-Wallet', 'M-Banking', 'Cash/Tunai', 'Rekening Tabungan'], array_keys($userMethods));

        // 2. Guest/Public gets only 4 payment methods
        $publicMethods = Transaction::getAvailablePaymentMethods(null);
        $this->assertCount(4, $publicMethods);
        $this->assertEquals(['E-Wallet', 'M-Banking', 'Cash/Tunai', 'Rekening Tabungan'], array_keys($publicMethods));

        // 3. Admin gets full detailed methods (OVO, SeaBank, BRI, etc.)
        $adminMethods = Transaction::getAvailablePaymentMethods($admin);
        $this->assertGreaterThan(4, count($adminMethods));
        $this->assertArrayHasKey('SeaBank', $adminMethods);
        $this->assertArrayHasKey('OVO', $adminMethods);
        $this->assertArrayHasKey('BRI', $adminMethods);
    }

    public function test_public_form_stores_transaction_with_selected_payment_method(): void
    {
        $user = User::where('email', 'user@budgetingme.com')->first();
        $kostCategory = Category::where('name', 'Biaya Kost')->firstOrFail();

        $response = $this->post('/public-transaction', [
            'email' => 'user@budgetingme.com',
            'type' => 'expense',
            'category_id' => $kostCategory->id,
            'payment_method' => 'M-Banking',
            'amount' => 1200000,
            'date' => date('Y-m-d'),
            'notes' => 'Bayar kost via M-Banking transfer',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('public_success');

        $this->assertDatabaseHas('transactions', [
            'user_id' => $user->id,
            'category_id' => $kostCategory->id,
            'payment_method' => 'M-Banking',
            'amount' => 1200000,
            'notes' => 'Bayar kost via M-Banking transfer',
        ]);
    }

    public function test_user_transaction_crud_with_payment_methods(): void
    {
        $user = User::where('email', 'user@budgetingme.com')->first();
        $makanCategory = Category::where('name', 'Biaya Makan')->firstOrFail();

        // 1. Create with E-Wallet
        $response = $this->actingAs($user)->post('/transactions', [
            'type' => 'expense',
            'category_id' => $makanCategory->id,
            'payment_method' => 'E-Wallet',
            'amount' => 150000,
            'date' => date('Y-m-d'),
            'notes' => 'Makan malam keluarga',
        ]);
        $response->assertRedirect();

        $transaction = Transaction::where('user_id', $user->id)
            ->where('notes', 'Makan malam keluarga')
            ->first();

        $this->assertNotNull($transaction);
        $this->assertEquals('E-Wallet', $transaction->payment_method);
        $this->assertEquals('E-Wallet', $transaction->payment_method_label);
        $this->assertEquals('E-Wallet', $transaction->asal_dana);

        // 2. Update to Cash/Tunai
        $updateResponse = $this->actingAs($user)->put("/transactions/{$transaction->id}", [
            'type' => 'expense',
            'category_id' => $makanCategory->id,
            'payment_method' => 'Cash/Tunai',
            'amount' => 175000,
            'date' => date('Y-m-d'),
            'notes' => 'Makan malam keluarga (update)',
        ]);
        $updateResponse->assertRedirect(route('transactions.index'));

        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'payment_method' => 'Cash/Tunai',
            'amount' => 175000,
        ]);
    }

    public function test_user_can_filter_transactions_by_payment_method(): void
    {
        $user = User::where('email', 'user@budgetingme.com')->first();
        $category = Category::where('type', 'expense')->first();

        // Create transaction with M-Banking
        Transaction::create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'type' => 'expense',
            'payment_method' => 'M-Banking',
            'amount' => 500000,
            'date' => date('Y-m-d'),
            'notes' => 'Transaksi Unik M-Banking Test',
        ]);

        // Create transaction with E-Wallet
        Transaction::create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'type' => 'expense',
            'payment_method' => 'E-Wallet',
            'amount' => 75000,
            'date' => date('Y-m-d'),
            'notes' => 'Transaksi Unik E-Wallet Test',
        ]);

        // Filter by M-Banking
        $responseMbanking = $this->actingAs($user)->get('/transactions?payment_method=M-Banking');
        $responseMbanking->assertStatus(200);
        $responseMbanking->assertSee('Transaksi Unik M-Banking Test');
        $responseMbanking->assertDontSee('Transaksi Unik E-Wallet Test');

        // Filter by E-Wallet
        $responseEwallet = $this->actingAs($user)->get('/transactions?payment_method=E-Wallet');
        $responseEwallet->assertStatus(200);
        $responseEwallet->assertSee('Transaksi Unik E-Wallet Test');
        $responseEwallet->assertDontSee('Transaksi Unik M-Banking Test');
    }

    public function test_user_account_balances_calculation_and_dashboard_display(): void
    {
        $user = User::where('email', 'user@budgetingme.com')->first();
        $incomeCat = Category::where('type', 'income')->first();
        $expenseCat = Category::where('type', 'expense')->first();

        $testUser = User::create([
            'name' => 'Test Balance User',
            'email' => 'balance_test_' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
            'role' => 'user',
        ]);

        // 1. Add Income to SeaBank: 2.000.000
        Transaction::create([
            'user_id' => $testUser->id,
            'category_id' => $incomeCat->id,
            'type' => 'income',
            'payment_method' => 'SeaBank',
            'amount' => 2000000,
            'date' => date('Y-m-d'),
            'notes' => 'Transfer SeaBank Masuk',
        ]);

        // 2. Add Expense from SeaBank: 500.000
        Transaction::create([
            'user_id' => $testUser->id,
            'category_id' => $expenseCat->id,
            'type' => 'expense',
            'payment_method' => 'SeaBank',
            'amount' => 500000,
            'date' => date('Y-m-d'),
            'notes' => 'Beli token via SeaBank',
        ]);

        // Check getAccountBalances model method
        $balances = $testUser->getAccountBalances();
        $this->assertArrayHasKey('SeaBank', $balances);
        $this->assertEquals(2000000, $balances['SeaBank']['income']);
        $this->assertEquals(500000, $balances['SeaBank']['expense']);
        $this->assertEquals(1500000, $balances['SeaBank']['balance']);
        $this->assertTrue($balances['SeaBank']['has_activity']);

        // Check User Dashboard display (Regular users see 4 grouped categories: Cash/Tunai, E-Wallet, M-Banking, Rekening Tabungan)
        $userDisplayBalances = $testUser->getDisplayAccountBalances();
        $this->assertCount(4, $userDisplayBalances);
        $this->assertArrayHasKey('M-Banking', $userDisplayBalances);
        $this->assertArrayHasKey('E-Wallet', $userDisplayBalances);
        $this->assertArrayHasKey('Cash/Tunai', $userDisplayBalances);
        $this->assertArrayHasKey('Rekening Tabungan', $userDisplayBalances);
        $this->assertEquals(1500000, $userDisplayBalances['M-Banking']['balance']);

        $response = $this->actingAs($testUser)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Saldo Rekening &amp; Dompet Digital Saya', false);
        $response->assertSee('M-Banking');
        $response->assertSee('1.500.000');

        // Check Admin display (Admin sees all individual wallets/accounts)
        $admin = User::where('email', 'alqad.ri2505@gmail.com')->first();
        $adminDisplayBalances = $admin->getDisplayAccountBalances();
        $this->assertGreaterThan(4, count($adminDisplayBalances));
        $this->assertArrayHasKey('SeaBank', $adminDisplayBalances);
    }

    public function test_admin_can_view_platform_and_user_account_balances(): void
    {
        $user = User::where('email', 'user@budgetingme.com')->first();
        $admin = User::where('email', 'alqad.ri2505@gmail.com')->first();

        // Admin platform dashboard
        $platformResponse = $this->actingAs($admin)->get('/admin');
        $platformResponse->assertStatus(200);
        $platformResponse->assertSee('Total Peredaran Saldo Platform per Rekening &amp; Dompet', false);

        // Admin user detail page
        $userShowResponse = $this->actingAs($admin)->get("/admin/users/{$user->id}");
        $userShowResponse->assertStatus(200);
        $userShowResponse->assertSee('Saldo Rekening Bank &amp; Dompet Digital Pengguna', false);
        $userShowResponse->assertSee('SeaBank');
    }

    public function test_user_can_create_view_and_manage_debts(): void
    {
        $user = User::where('email', 'user@budgetingme.com')->first();

        // 1. Create debt with "pay_on_salary" checked
        $response = $this->actingAs($user)->post('/debts', [
            'title' => 'Pinjaman Laptop Kantor',
            'creditor' => 'Budi Sudarsono',
            'amount' => 1500000,
            'paid_amount' => 0,
            'due_date' => date('Y-m-d', strtotime('+30 days')),
            'pay_on_salary' => 1,
            'notes' => 'Harus dibayar setelah gajian cair',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('debts', [
            'user_id' => $user->id,
            'title' => 'Pinjaman Laptop Kantor',
            'creditor' => 'Budi Sudarsono',
            'amount' => 1500000,
            'paid_amount' => 0,
            'pay_on_salary' => 1,
            'status' => 'unpaid',
        ]);

        // 2. Access debts index
        $indexResponse = $this->actingAs($user)->get('/debts');
        $indexResponse->assertStatus(200);
        $indexResponse->assertSee('Catatan Utang');
        $indexResponse->assertSee('Bayar Saat Gajian');
        $indexResponse->assertSee('Pinjaman Laptop Kantor');
        $indexResponse->assertSee('1.500.000');
    }

    public function test_user_can_pay_debt_installment_and_auto_record_transaction(): void
    {
        $user = User::where('email', 'user@budgetingme.com')->first();
        $debt = \App\Models\Debt::create([
            'user_id' => $user->id,
            'title' => 'Cicilan HP',
            'creditor' => 'Toko Elektronik',
            'amount' => 2000000,
            'paid_amount' => 0,
            'status' => 'unpaid',
            'pay_on_salary' => true,
        ]);

        // 1. Pay partial installment (500.000) using SeaBank
        $payResponse = $this->actingAs($user)->post("/debts/{$debt->id}/pay", [
            'payment_amount' => 500000,
            'payment_method' => 'SeaBank',
            'create_transaction' => 1,
            'payment_date' => date('Y-m-d'),
            'notes' => 'Cicilan bulan 1',
        ]);

        $payResponse->assertRedirect();
        $payResponse->assertSessionHas('success');

        $debt->refresh();
        $this->assertEquals(500000, (float) $debt->paid_amount);
        $this->assertEquals(1500000, (float) $debt->remaining_amount);
        $this->assertEquals('partial', $debt->status);

        // Verify transaction is recorded automatically with category Bayar Utang/Cicilan
        $this->assertDatabaseHas('transactions', [
            'user_id' => $user->id,
            'type' => 'expense',
            'payment_method' => 'SeaBank',
            'amount' => 500000,
        ]);

        // 2. Pay remaining (1.500.000) using Mandiri to fully settle debt
        $finalPayResponse = $this->actingAs($user)->post("/debts/{$debt->id}/pay", [
            'payment_amount' => 1500000,
            'payment_method' => 'Mandiri',
            'create_transaction' => 1,
        ]);

        $finalPayResponse->assertRedirect();
        $debt->refresh();
        $this->assertEquals(2000000, (float) $debt->paid_amount);
        $this->assertEquals(0, (float) $debt->remaining_amount);
        $this->assertEquals('paid', $debt->status);
        $this->assertTrue($debt->is_paid);
    }

    public function test_dashboard_displays_kotak_catatan_utang_and_gajian_popup_reminder(): void
    {
        $user = User::where('email', 'user@budgetingme.com')->first();

        // Ensure user has at least one active debt
        \App\Models\Debt::create([
            'user_id' => $user->id,
            'title' => 'Pinjaman Teman Kuliah',
            'creditor' => 'Rian',
            'amount' => 750000,
            'paid_amount' => 0,
            'status' => 'unpaid',
            'pay_on_salary' => true,
        ]);

        $dashboard = $this->actingAs($user)->get('/dashboard');
        $dashboard->assertStatus(200);

        // Kotak Total Utang Saya
        $dashboard->assertSee('Total Utang Saya');
        $dashboard->assertSee('Bayar Saat Gajian!');
        $dashboard->assertSee('Catat Utang');

        // Popup Modal Pengingat Utang Saat Gajian
        $dashboard->assertSee('debtReminderModal');
        $dashboard->assertSee('Jangan Lupa Bayar Utang!');
        $dashboard->assertSee('Pesan Penting: Bayar Saat Gajian!');
        $dashboard->assertSee('jangan lupa bayar utang jika ada saat menerima gaji');
    }

    public function test_theme_toggle_feature_is_available_across_pages(): void
    {
        $user = User::where('email', 'user@budgetingme.com')->first();

        // 1. Landing Page
        $landing = $this->get('/');
        $landing->assertStatus(200);
        $landing->assertSee('theme-toggle-btn');
        $landing->assertSee('localStorage.getItem(\'theme\')', false);

        // 2. Auth Page (Login)
        $login = $this->get('/login');
        $login->assertStatus(200);
        $login->assertSee('theme-toggle-btn');
        $login->assertSee('localStorage.getItem(\'theme\')', false);

        // 3. Authenticated Dashboard Page
        $dashboard = $this->actingAs($user)->get('/dashboard');
        $dashboard->assertStatus(200);
        $dashboard->assertSee('theme-toggle-btn');
        $dashboard->assertSee('localStorage.getItem(\'theme\')', false);
        $dashboard->assertSee('toggleTheme');
    }

    public function test_pdf_export_shows_four_boxes_when_unpaid_debt_exists_and_three_boxes_when_paid(): void
    {
        $user = User::where('email', 'user@budgetingme.com')->first();

        // 1. When NO unpaid debts exist (3 boxes)
        \App\Models\Debt::where('user_id', $user->id)->delete();

        $viewThreeBoxes = view('transactions.pdf', [
            'transactions' => Transaction::where('user_id', $user->id)->get(),
            'user' => $user,
            'totalIncome' => 5000000,
            'totalExpense' => 2000000,
            'netBalance' => 3000000,
            'filterLabel' => 'Semua Transaksi',
            'unpaidDebts' => collect([]),
            'totalUnpaidDebt' => 0,
            'hasUnpaidDebt' => false,
        ])->render();

        $this->assertStringContainsString('col-3', $viewThreeBoxes);
        $this->assertStringNotContainsString('Total Utang Belum Lunas', $viewThreeBoxes);

        // 2. When UNPAID debt exists (4 boxes)
        $debt = \App\Models\Debt::create([
            'user_id' => $user->id,
            'title' => 'Pinjaman Darurat Medis',
            'creditor' => 'Kakak',
            'amount' => 1200000,
            'paid_amount' => 200000,
            'status' => 'partial',
            'pay_on_salary' => true,
        ]);

        $viewFourBoxes = view('transactions.pdf', [
            'transactions' => Transaction::where('user_id', $user->id)->get(),
            'user' => $user,
            'totalIncome' => 5000000,
            'totalExpense' => 2000000,
            'netBalance' => 3000000,
            'filterLabel' => 'Semua Transaksi',
            'unpaidDebts' => collect([$debt]),
            'totalUnpaidDebt' => 1000000,
            'hasUnpaidDebt' => true,
        ])->render();

        $this->assertStringContainsString('col-4', $viewFourBoxes);
        $this->assertStringContainsString('Total Utang Belum Lunas', $viewFourBoxes);
        $this->assertStringContainsString('1.000.000', $viewFourBoxes);
        $this->assertStringContainsString('Bayar saat gajian', $viewFourBoxes);
        $this->assertStringContainsString('Pinjaman Darurat Medis', $viewFourBoxes);

        // 3. Test actual controller download with unpaid debt
        $response = $this->actingAs($user)->get('/transactions/export/pdf');
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_registration_code_generation_and_auto_weekly_rotation(): void
    {
        // 1. Initial code generation
        $code1 = \App\Models\AppSetting::getActiveRegistrationCode();
        $this->assertEquals(4, strlen($code1));
        $this->assertMatchesRegularExpression('/^[2-9A-HJ-NP-Z]{4}$/', $code1);

        // Subsequent call before expiration returns the same code
        $code1Repeat = \App\Models\AppSetting::getActiveRegistrationCode();
        $this->assertEquals($code1, $code1Repeat);

        // Validation test
        $this->assertTrue(\App\Models\AppSetting::validateRegistrationCode($code1));
        $this->assertTrue(\App\Models\AppSetting::validateRegistrationCode(strtolower($code1)));
        $this->assertFalse(\App\Models\AppSetting::validateRegistrationCode('WRON'));

        // 2. Simulate 7 days passing (expire the code)
        $setting = \App\Models\AppSetting::where('key', \App\Models\AppSetting::KEY_REGISTRATION_CODE)->first();
        $setting->expires_at = now()->subMinutes(5);
        $setting->save();

        // 3. Next retrieval must automatically generate a new code and set expires_at 7 days ahead
        $code2 = \App\Models\AppSetting::getActiveRegistrationCode();
        $this->assertEquals(4, strlen($code2));
        
        $updatedSetting = \App\Models\AppSetting::where('key', \App\Models\AppSetting::KEY_REGISTRATION_CODE)->first();
        $this->assertTrue($updatedSetting->expires_at->isFuture());
        $this->assertGreaterThan(5, now()->diffInDays($updatedSetting->expires_at));
    }

    public function test_admin_can_regenerate_and_customize_registration_code(): void
    {
        $admin = User::where('email', 'alqad.ri2505@gmail.com')->firstOrFail();
        $normalUser = User::where('email', 'user@budgetingme.com')->firstOrFail();

        // 1. Non-admin is forbidden
        $this->actingAs($normalUser)
            ->post('/admin/registration-code/regenerate')
            ->assertStatus(403);

        $this->actingAs($normalUser)
            ->post('/admin/registration-code/update', ['custom_code' => 'TEST'])
            ->assertStatus(403);

        // 2. Admin can manually regenerate code
        $oldCode = \App\Models\AppSetting::getActiveRegistrationCode();
        $response = $this->actingAs($admin)
            ->post('/admin/registration-code/regenerate');

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // 3. Admin can set custom 4-character code
        $customResponse = $this->actingAs($admin)
            ->post('/admin/registration-code/update', ['custom_code' => 'vip7']);

        $customResponse->assertRedirect();
        $customResponse->assertSessionHas('success');
        $this->assertEquals('VIP7', \App\Models\AppSetting::getActiveRegistrationCode());

        // 4. Validation error if custom code is not 4 chars
        $failResponse = $this->actingAs($admin)
            ->post('/admin/registration-code/update', ['custom_code' => 'ABC']);
        $failResponse->assertSessionHasErrors('code');
    }

    public function test_registration_code_displayed_on_admin_index_and_admin_profile(): void
    {
        $admin = User::where('email', 'alqad.ri2505@gmail.com')->firstOrFail();
        $normalUser = User::where('email', 'user@budgetingme.com')->firstOrFail();

        $activeCode = \App\Models\AppSetting::getActiveRegistrationCode();

        // 1. Admin Index renders registration code card
        $adminIndex = $this->actingAs($admin)->get('/admin');
        $adminIndex->assertStatus(200);
        $adminIndex->assertSee('Kode Akses Registrasi User Baru');
        $adminIndex->assertSee($activeCode);
        $adminIndex->assertSee('Rotasi Otomatis Setiap 7 Hari');

        // 2. Admin Profile renders registration code card
        $adminProfile = $this->actingAs($admin)->get('/profile');
        $adminProfile->assertStatus(200);
        $adminProfile->assertSee('Kode Akses Registrasi User Baru');
        $adminProfile->assertSee($activeCode);

        // 3. Normal User Profile does NOT render registration code card
        $normalUserProfile = $this->actingAs($normalUser)->get('/profile');
        $normalUserProfile->assertStatus(200);
        $normalUserProfile->assertDontSee('Kode Akses Registrasi User Baru');
    }

    public function test_demo_account_protected_budgets_and_incomes_restrictions(): void
    {
        $demoUser = User::where('email', 'user@budgetingme.com')->firstOrFail();
        $this->assertTrue($demoUser->isDemo());

        // 1. Ensure initial demo budget has is_protected = true
        $defaultBudget = Budget::where('user_id', $demoUser->id)->firstOrFail();
        $defaultBudget->update(['is_protected' => true]);
        $originalLimit = $defaultBudget->limit_amount;

        // Demo user tries to update default budget -> blocked!
        $updateBudgetRes = $this->actingAs($demoUser)->put("/budgets/{$defaultBudget->id}", [
            'limit_amount' => 9999999,
        ]);
        $updateBudgetRes->assertRedirect();
        $updateBudgetRes->assertSessionHas('error');
        $this->assertEquals($originalLimit, $defaultBudget->fresh()->limit_amount);

        // Demo user tries to delete default budget -> blocked!
        $deleteBudgetRes = $this->actingAs($demoUser)->delete("/budgets/{$defaultBudget->id}");
        $deleteBudgetRes->assertRedirect();
        $deleteBudgetRes->assertSessionHas('error');
        $this->assertDatabaseHas('budgets', ['id' => $defaultBudget->id]);

        // 2. Demo user ADDS a new budget -> SUCCESS!
        $hiburanCategory = Category::where('name', 'Hiburan')->firstOrFail();
        $addBudgetRes = $this->actingAs($demoUser)->post('/budgets', [
            'category_id' => $hiburanCategory->id,
            'month' => 12,
            'year' => 2026,
            'limit_amount' => 500000,
        ]);
        $addBudgetRes->assertRedirect();
        $newBudget = Budget::where('user_id', $demoUser->id)
            ->where('category_id', $hiburanCategory->id)
            ->where('month', 12)
            ->where('year', 2026)
            ->firstOrFail();

        $this->assertFalse((bool)$newBudget->is_protected);
        $this->assertFalse($newBudget->isLockedFor($demoUser));

        // Newly added budget CAN be edited and deleted!
        $updateNewBudgetRes = $this->actingAs($demoUser)->put("/budgets/{$newBudget->id}", [
            'limit_amount' => 750000,
        ]);
        $updateNewBudgetRes->assertRedirect();
        $updateNewBudgetRes->assertSessionHas('success');
        $this->assertEquals(750000, $newBudget->fresh()->limit_amount);

        $deleteNewBudgetRes = $this->actingAs($demoUser)->delete("/budgets/{$newBudget->id}");
        $deleteNewBudgetRes->assertRedirect();
        $deleteNewBudgetRes->assertSessionHas('success');
        $this->assertDatabaseMissing('budgets', ['id' => $newBudget->id]);

        // 3. Ensure initial demo income has is_protected = true
        $defaultIncome = Transaction::where('user_id', $demoUser->id)->where('type', 'income')->firstOrFail();
        $defaultIncome->update(['is_protected' => true]);
        $originalIncomeAmount = $defaultIncome->amount;

        // Demo user tries to access edit form of default income -> redirected with error!
        $editIncomePageRes = $this->actingAs($demoUser)->get("/transactions/{$defaultIncome->id}/edit");
        $editIncomePageRes->assertRedirect(route('transactions.index'));
        $editIncomePageRes->assertSessionHas('error');

        // Demo user tries to update default income -> blocked!
        $updateIncomeRes = $this->actingAs($demoUser)->put("/transactions/{$defaultIncome->id}", [
            'type' => 'income',
            'category_id' => $defaultIncome->category_id,
            'amount' => 9999999,
            'payment_method' => $defaultIncome->payment_method,
            'date' => $defaultIncome->date->format('Y-m-d'),
            'notes' => 'Diubah demo',
        ]);
        $updateIncomeRes->assertRedirect(route('transactions.index'));
        $updateIncomeRes->assertSessionHas('error');
        $this->assertEquals($originalIncomeAmount, $defaultIncome->fresh()->amount);

        // Demo user tries to delete default income -> blocked!
        $deleteIncomeRes = $this->actingAs($demoUser)->delete("/transactions/{$defaultIncome->id}");
        $deleteIncomeRes->assertRedirect();
        $deleteIncomeRes->assertSessionHas('error');
        $this->assertDatabaseHas('transactions', ['id' => $defaultIncome->id]);

        // 4. Demo user ADDS a new income transaction -> SUCCESS!
        $gajiCat = Category::where('type', 'income')->firstOrFail();
        $addIncomeRes = $this->actingAs($demoUser)->post('/transactions', [
            'type' => 'income',
            'category_id' => $gajiCat->id,
            'amount' => 3000000,
            'payment_method' => 'E-Wallet',
            'date' => date('Y-m-d'),
            'notes' => 'Pemasukan bonus baru demo',
        ]);
        $addIncomeRes->assertRedirect();
        $newIncome = Transaction::where('user_id', $demoUser->id)
            ->where('notes', 'Pemasukan bonus baru demo')
            ->firstOrFail();

        $this->assertFalse((bool)$newIncome->is_protected);
        $this->assertFalse($newIncome->isLockedFor($demoUser));

        // Newly added income CAN be edited!
        $editNewIncomePageRes = $this->actingAs($demoUser)->get("/transactions/{$newIncome->id}/edit");
        $editNewIncomePageRes->assertStatus(200);

        $updateNewIncomeRes = $this->actingAs($demoUser)->put("/transactions/{$newIncome->id}", [
            'type' => 'income',
            'category_id' => $gajiCat->id,
            'amount' => 3500000,
            'payment_method' => 'E-Wallet',
            'date' => date('Y-m-d'),
            'notes' => 'Pemasukan bonus baru demo diperbarui',
        ]);
        $updateNewIncomeRes->assertRedirect(route('transactions.index'));
        $updateNewIncomeRes->assertSessionHas('success');
        $this->assertEquals(3500000, $newIncome->fresh()->amount);

        // Newly added income CAN be deleted!
        $deleteNewIncomeRes = $this->actingAs($demoUser)->delete("/transactions/{$newIncome->id}");
        $deleteNewIncomeRes->assertRedirect();
        $deleteNewIncomeRes->assertSessionHas('success');
        $this->assertDatabaseMissing('transactions', ['id' => $newIncome->id]);

        // 5. Test UI renders locked badges
        $budgetsPageRes = $this->actingAs($demoUser)->get('/budgets');
        $budgetsPageRes->assertStatus(200);
        $budgetsPageRes->assertSee('Terkunci');

        $transactionsPageRes = $this->actingAs($demoUser)->get('/transactions');
        $transactionsPageRes->assertStatus(200);
        $transactionsPageRes->assertSee('Terkunci (Demo)');
    }

    public function test_admin_can_update_user_password_and_view_it(): void
    {
        $admin = User::where('email', 'alqad.ri2505@gmail.com')->first();
        $targetUser = User::create([
            'name' => 'Target Reset User',
            'email' => 'target_reset_' . uniqid() . '@example.com',
            'password' => Hash::make('OldPassword123'),
            'role' => 'user',
        ]);

        $newSecret = 'NewPass987#';
        $response = $this->actingAs($admin)->put("/admin/users/{$targetUser->id}/password", [
            'password' => $newSecret,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $response->assertSessionHas('new_password_info', [
            'user_name' => $targetUser->name,
            'email' => $targetUser->email,
            'password' => $newSecret,
        ]);

        $this->assertTrue(Hash::check($newSecret, $targetUser->fresh()->password));
    }

    public function test_admin_can_send_password_reset_link_to_user_email(): void
    {
        Notification::fake();

        $admin = User::where('email', 'alqad.ri2505@gmail.com')->first();
        $targetUser = User::create([
            'name' => 'Target Email Reset User',
            'email' => 'target_email_' . uniqid() . '@example.com',
            'password' => Hash::make('password123'),
            'role' => 'user',
        ]);

        $response = $this->actingAs($admin)->post("/admin/users/{$targetUser->id}/send-reset-link");
        $response->assertRedirect();
        $response->assertSessionHas('success');

        Notification::assertSentTo($targetUser, ResetPassword::class);
    }

    public function test_user_can_request_password_reset_link_from_forgot_password_form(): void
    {
        Notification::fake();

        $user = User::create([
            'name' => 'Forgot Pass User',
            'email' => 'forgot_pass_' . uniqid() . '@example.com',
            'password' => Hash::make('password123'),
            'role' => 'user',
        ]);

        $response = $this->get('/forgot-password');
        $response->assertStatus(200);
        $response->assertSee('Lupa Kata Sandi?');

        $postResponse = $this->post('/forgot-password', [
            'email' => $user->email,
        ]);
        $postResponse->assertSessionHas('status');

        Notification::assertSentTo($user, ResetPassword::class, function ($notification) use ($user) {
            $mail = $notification->toMail($user);
            return $mail->subject === 'Permintaan Reset Kata Sandi - BudgetingMe';
        });
    }

    public function test_admin_can_delete_user_and_related_financial_data(): void
    {
        $admin = User::where('email', 'alqad.ri2505@gmail.com')->first();
        $userToDelete = User::create([
            'name' => 'User To Delete',
            'email' => 'delete_me_' . uniqid() . '@example.com',
            'password' => Hash::make('password123'),
            'role' => 'user',
        ]);

        $category = Category::where('type', 'expense')->first();

        // Give the user a transaction, budget, and debt
        $trx = Transaction::create([
            'user_id' => $userToDelete->id,
            'category_id' => $category->id,
            'type' => 'expense',
            'payment_method' => 'Cash',
            'amount' => 50000,
            'date' => date('Y-m-d'),
            'notes' => 'Catatan belanja sebelum dihapus',
        ]);

        $budget = Budget::create([
            'user_id' => $userToDelete->id,
            'category_id' => $category->id,
            'limit_amount' => 50000,
            'month' => date('n'),
            'year' => date('Y'),
        ]);

        $debt = Debt::create([
            'user_id' => $userToDelete->id,
            'title' => 'Utang sebelum dihapus',
            'creditor' => 'Teman Kampus',
            'amount' => 100000,
            'paid_amount' => 0,
            'status' => 'unpaid',
            'due_date' => date('Y-m-d', strtotime('+7 days')),
        ]);

        $response = $this->actingAs($admin)->delete("/admin/users/{$userToDelete->id}");
        $response->assertRedirect(route('admin.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('users', ['id' => $userToDelete->id]);
        $this->assertDatabaseMissing('transactions', ['id' => $trx->id]);
        $this->assertDatabaseMissing('budgets', ['id' => $budget->id]);
        $this->assertDatabaseMissing('debts', ['id' => $debt->id]);
    }

    public function test_admin_cannot_delete_themselves_or_main_admin(): void
    {
        $admin = User::where('email', 'alqad.ri2505@gmail.com')->first();

        // 1. Admin deleting themselves
        $selfDeleteResponse = $this->actingAs($admin)->delete("/admin/users/{$admin->id}");
        $selfDeleteResponse->assertSessionHas('error');
        $this->assertDatabaseHas('users', ['id' => $admin->id]);

        // 2. Admin cannot delete another user with role admin
        $anotherAdmin = User::create([
            'name' => 'Secondary Admin',
            'email' => 'admin2_' . uniqid() . '@example.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        $adminDeleteResponse = $this->actingAs($admin)->delete("/admin/users/{$anotherAdmin->id}");
        $adminDeleteResponse->assertSessionHas('error');
        $this->assertDatabaseHas('users', ['id' => $anotherAdmin->id]);
    }
}


