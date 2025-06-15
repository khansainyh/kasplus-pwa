<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Penjualan</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 11px; color: #333; }
        .container { width: 100%; margin: 0 auto; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 22px; }
        .header p { margin: 5px 0; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 7px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .text-right { text-align: right; }
        .footer { text-align: center; font-size: 9px; color: #777; position: fixed; bottom: 0; width: 100%; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Laporan Penjualan</h1>
            <p><strong>{{ config('app.name', 'KasPlus') }}</strong></p>
            <p>Periode: {{ $tglMulai }} - {{ $tglSelesai }}</p>
        </div>

        <h3>Ringkasan</h3>
        <table style="width: 50%;">
            <tr>
                <th style="width: 60%;">Total Penjualan</th>
                <td class="text-right">Rp {{ number_format($data['totalPenjualan'], 0, ',', '.') }}</td>
            </tr>
            <tr>
                <th>Jumlah Transaksi</th>
                <td class="text-right">{{ $data['jumlahTransaksi'] }}</td>
            </tr>
        </table>

        <h3>Detail Transaksi</h3>
        <table>
            <thead>
                <tr>
                    <th style="width:20%;">Tanggal</th>
                    <th>Invoice #</th>
                    <th>Kasir</th>
                    <th class="text-right" style="width:25%;">Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data['transaksi'] as $trx)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($trx->transaction_date)->format('d/m/Y H:i') }}</td>
                        <td>{{ $trx->invoice_number }}</td>
                        <td>{{ $trx->user->name ?? 'N/A' }}</td>
                        <td class="text-right">Rp {{ number_format($trx->total_amount, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" style="text-align:center;">Tidak ada data penjualan pada periode ini.</td></tr>
                @endforelse
            </tbody>
        </table>

        <div class="footer">
            Laporan ini dibuat secara otomatis oleh sistem pada {{ date('d M Y, H:i') }}.
        </div>
    </div>
</body>
</html>