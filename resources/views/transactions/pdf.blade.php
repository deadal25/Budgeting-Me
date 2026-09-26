<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Keuangan & Catatan Utang — Budgeting-Me</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #334155;
            font-size: 11px;
            line-height: 1.4;
            margin: 0;
            padding: 20px;
        }
        .header {
            width: 100%;
            border-bottom: 2px solid #059669;
            padding-bottom: 12px;
            margin-bottom: 16px;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
        }
        .app-title {
            font-size: 20px;
            font-weight: bold;
            color: #059669;
            margin: 0;
        }
        .app-subtitle {
            font-size: 9px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 2px;
        }
        .doc-title {
            font-size: 13px;
            font-weight: bold;
            text-align: right;
            color: #1e293b;
            margin: 0;
        }
        .doc-date {
            font-size: 9px;
            color: #64748b;
            text-align: right;
            margin-top: 2px;
        }
        .meta-box {
            width: 100%;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 10px 14px;
            margin-bottom: 16px;
        }
        .meta-table {
            width: 100%;
            border-collapse: collapse;
        }
        .meta-table td {
            font-size: 10px;
            padding: 2px 4px;
        }
        .meta-label {
            color: #64748b;
            font-weight: bold;
            width: 110px;
        }
        .meta-value {
            color: #0f172a;
        }
        .summary-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
        }
        .summary-table td {
            padding: 8px 10px;
            border: 1px solid #cbd5e1;
            text-align: center;
            vertical-align: middle;
        }
        .col-3 {
            width: 33.33%;
        }
        .col-4 {
            width: 25%;
        }
        .summary-income {
            background-color: #ecfdf5;
            color: #065f46;
        }
        .summary-expense {
            background-color: #fff1f2;
            color: #9f1239;
        }
        .summary-balance {
            background-color: #f0fdfa;
            color: #115e59;
        }
        .summary-debt {
            background-color: #fff1f2;
            border: 2px solid #f43f5e !important;
            color: #9f1239;
        }
        .summary-title {
            font-size: 8.5px;
            text-transform: uppercase;
            font-weight: bold;
            letter-spacing: 0.5px;
        }
        .summary-amount {
            font-size: 12px;
            font-weight: bold;
            margin-top: 3px;
        }
        .summary-note {
            font-size: 8px;
            margin-top: 3px;
            font-weight: bold;
        }
        .section-title {
            font-size: 11px;
            font-weight: bold;
            color: #1e293b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 14px;
            margin-bottom: 6px;
            padding-bottom: 3px;
            border-bottom: 1.5px solid #cbd5e1;
        }
        .section-title-debt {
            font-size: 11px;
            font-weight: bold;
            color: #be123c;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 20px;
            margin-bottom: 6px;
            padding-bottom: 3px;
            border-bottom: 1.5px solid #fecdd3;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4px;
        }
        .data-table th {
            background-color: #f1f5f9;
            color: #475569;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border: 1px solid #cbd5e1;
            padding: 6px 8px;
            text-align: left;
        }
        .data-table td {
            border: 1px solid #e2e8f0;
            padding: 5px 8px;
            font-size: 9.5px;
        }
        .data-table tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .badge-income {
            color: #047857;
            font-weight: bold;
        }
        .badge-expense {
            color: #b91c1c;
            font-weight: bold;
        }
        .footer {
            margin-top: 24px;
            border-top: 1px solid #e2e8f0;
            padding-top: 8px;
            font-size: 8px;
            color: #94a3b8;
            text-align: center;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <table class="header-table">
            <tr>
                <td style="width: 50%;">
                    <div class="app-title">Budgeting-Me</div>
                    <div class="app-subtitle">Aplikasi Pencatatan Keuangan Pribadi Cerdas</div>
                </td>
                <td style="width: 50%;">
                    <div class="doc-title">LAPORAN KEUANGAN & CATATAN TRANSAKSI</div>
                    <div class="doc-date">Dicetak pada: {{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM Y HH:mm') }}</div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Metadata Box -->
    <div class="meta-box">
        <table class="meta-table">
            <tr>
                <td class="meta-label">Nama Pengguna:</td>
                <td class="meta-value">{{ $user->name }}</td>
                <td class="meta-label">Periode Laporan:</td>
                <td class="meta-value"><strong>{{ $filterLabel }}</strong></td>
            </tr>
            <tr>
                <td class="meta-label">Email Terdaftar:</td>
                <td class="meta-value">{{ $user->email }}</td>
                <td class="meta-label">Status Utang:</td>
                <td class="meta-value">
                    @if(!empty($hasUnpaidDebt))
                        <strong style="color: #be123c;">Ada {{ $unpaidDebts->count() }} Utang Belum Lunas (Sisa: Rp {{ number_format($totalUnpaidDebt, 0, ',', '.') }})</strong>
                    @else
                        <strong style="color: #047857;">Semua Utang Lunas / Tidak Ada Tanggungan Utang</strong>
                    @endif
                </td>
            </tr>
        </table>
    </div>

    <!-- Summary Boxes: 4 Kotak jika ada utang belum bayar, 3 Kotak jika tidak ada utang / sudah lunas -->
    <table class="summary-table">
        <tr>
            <!-- Kotak 1: Pemasukan -->
            <td class="summary-income {{ !empty($hasUnpaidDebt) ? 'col-4' : 'col-3' }}">
                <div class="summary-title">Total Pemasukan</div>
                <div class="summary-amount">Rp {{ number_format($totalIncome, 0, ',', '.') }}</div>
            </td>

            <!-- Kotak 2: Pengeluaran -->
            <td class="summary-expense {{ !empty($hasUnpaidDebt) ? 'col-4' : 'col-3' }}">
                <div class="summary-title">Total Pengeluaran</div>
                <div class="summary-amount">Rp {{ number_format($totalExpense, 0, ',', '.') }}</div>
            </td>

            <!-- Kotak 3: Selisih Bersih (Saldo) -->
            <td class="summary-balance {{ !empty($hasUnpaidDebt) ? 'col-4' : 'col-3' }}">
                <div class="summary-title">Selisih Bersih (Saldo)</div>
                <div class="summary-amount">Rp {{ number_format($netBalance, 0, ',', '.') }}</div>
            </td>

            <!-- Kotak 4: Khusus jika ADA utang belum bayar / belum lunas -->
            @if(!empty($hasUnpaidDebt))
                <td class="summary-debt col-4">
                    <div class="summary-title" style="color: #be123c;">Total Utang Belum Lunas</div>
                    <div class="summary-amount" style="color: #be123c;">Rp {{ number_format($totalUnpaidDebt, 0, ',', '.') }}</div>
                    <div class="summary-note" style="color: #9f1239;">
                        {{ $unpaidDebts->count() }} utang &bull; Bayar saat gajian!
                    </div>
                </td>
            @endif
        </tr>
    </table>

    <!-- Section 1: Itemized Transactions Table -->
    <div class="section-title">Riwayat Transaksi Keuangan ({{ $transactions->count() }} Data)</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 20px;" class="text-center">No</th>
                <th style="width: 65px;">Tanggal</th>
                <th style="width: 65px;">Jenis</th>
                <th style="width: 110px;">Kategori</th>
                <th style="width: 80px;">Sumber Dana</th>
                <th>Catatan</th>
                <th style="width: 90px;" class="text-right">Nominal (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $index => $trx)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ \Carbon\Carbon::parse($trx->date)->format('d/m/Y') }}</td>
                    <td>
                        @if($trx->type === 'income')
                            <span class="badge-income">Pemasukan</span>
                        @else
                            <span class="badge-expense">Pengeluaran</span>
                        @endif
                    </td>
                    <td><strong>{{ $trx->category->name }}</strong></td>
                    <td>{{ $trx->payment_method_label }}</td>
                    <td>{{ $trx->notes ?: '-' }}</td>
                    <td class="text-right {{ $trx->type === 'income' ? 'badge-income' : 'badge-expense' }}">
                        {{ $trx->type === 'income' ? '+' : '-' }} {{ number_format($trx->amount, 0, ',', '.') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center" style="padding: 18px; color: #94a3b8;">
                        Tidak ada transaksi yang tercatat pada periode ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Section 2: Catatan Utang Belum Lunas (Hanya jika ADA utang belum bayar) -->
    @if(!empty($hasUnpaidDebt) && $unpaidDebts->count() > 0)
        <div class="section-title-debt">
            Daftar Utang Yang Belum Lunas (Pengingat: Bayar Saat Gajian!)
        </div>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 20px;" class="text-center">No</th>
                    <th>Keperluan / Nama Utang</th>
                    <th style="width: 110px;">Pemberi Pinjaman</th>
                    <th style="width: 75px;">Jatuh Tempo</th>
                    <th style="width: 90px;" class="text-right">Total Utang</th>
                    <th style="width: 90px;" class="text-right">Sisa Belum Bayar</th>
                    <th style="width: 75px;" class="text-center">Kewajiban</th>
                </tr>
            </thead>
            <tbody>
                @foreach($unpaidDebts as $uIdx => $debtItem)
                    <tr>
                        <td class="text-center">{{ $uIdx + 1 }}</td>
                        <td>
                            <strong>{{ $debtItem->title }}</strong>
                            @if($debtItem->pay_on_salary)
                                <span style="font-size: 8px; color: #b45309; font-weight: bold;">[Bayar saat gajian]</span>
                            @endif
                            @if($debtItem->notes)
                                <div style="font-size: 8.5px; color: #64748b;">{{ $debtItem->notes }}</div>
                            @endif
                        </td>
                        <td>{{ $debtItem->creditor }}</td>
                        <td>{{ $debtItem->due_date ? \Carbon\Carbon::parse($debtItem->due_date)->format('d/m/Y') : '-' }}</td>
                        <td class="text-right">Rp {{ number_format($debtItem->amount, 0, ',', '.') }}</td>
                        <td class="text-right" style="color: #be123c; font-weight: bold;">
                            Rp {{ number_format($debtItem->remaining_amount, 0, ',', '.') }}
                        </td>
                        <td class="text-center">
                            <span style="color: #be123c; font-weight: bold; font-size: 8.5px;">
                                Belum Lunas
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <!-- Footer -->
    <div class="footer">
        Dokumen ini diterbitkan secara otomatis oleh sistem Budgeting-Me sebagai bukti catatan keuangan pribadi pengguna yang sah. &bull; Hak Cipta &copy; {{ date('Y') }} Budgeting-Me.
    </div>
</body>
</html>
