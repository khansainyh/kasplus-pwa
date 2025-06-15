<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Keuangan</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 11px; color: #333; }
        .container { width: 100%; margin: 0 auto; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 22px; }
        .header p { margin: 5px 0; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 7px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .summary-table th, .summary-table td { text-align: right; }
        .summary-table tr:last-child { font-weight: bold; border-top: 2px solid #333; }
        h3 { border-bottom: 1px solid #ccc; padding-bottom: 5px; margin-top: 25px; font-size: 14px;}
        .text-right { text-align: right; }
        .text-green { color: #28a745; }
        .text-red { color: #dc3545; }
        .footer { text-align: center; font-size: 9px; color: #777; position: fixed; bottom: 0; width: 100%; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Laporan Keuangan</h1>
            <p><strong>{{ config('app.name', 'KasPlus') }}</strong></p>
            <p>Periode: {{ $tglMulai }} - {{ $tglSelesai }}</p>
        </div>

        <h3>Ringkasan Keuangan</h3>
        <table class="summary-table">
            <tr>
                <td style="width:70%;">Total Pemasukan</td>
                <td class="text-green">Rp {{ number_format($data['totalPemasukan'], 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Total Pengeluaran</td>
                <td class="text-red">Rp {{ number_format($data['totalPengeluaran'], 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Laba / Rugi Bersih</td>
                <td>Rp {{ number_format($data['labaRugi'], 0, ',', '.') }}</td>
            </tr>
        </table>

        <h3>Detail Transaksi</h3>
        <table>
            <thead>
                <tr>
                    <th style="width:20%;">Tanggal</th>
                    <th>Keterangan</th>
                    <th class="text-right" style="width:25%;">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data['laporanGabungan'] as $item)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y H:i') }}</td>
                        <td>{{ $item->keterangan }}</td>
                        <td class="text-right {{ $item->tipe == 'pengeluaran' ? 'text-red' : 'text-green' }}">
                           {{ $item->tipe == 'pengeluaran' ? '-' : '+' }} Rp {{ number_format($item->jumlah, 0, ',', '.') }}
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="3" style="text-align:center;">Tidak ada data pada periode ini.</td></tr>
                @endforelse
            </tbody>
        </table>

        <div class="footer">
            Laporan ini dibuat secara otomatis oleh sistem pada {{ date('d M Y, H:i') }}.
        </div>
    </div>
</body>
</html>