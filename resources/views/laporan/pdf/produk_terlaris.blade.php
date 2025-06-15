<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Produk Terlaris</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 11px; color: #333; }
        .container { width: 100%; margin: 0 auto; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 22px; }
        .header p { margin: 5px 0; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        th, td { border: 1px solid #ddd; padding: 7px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        h3 { border-bottom: 1px solid #ccc; padding-bottom: 5px; margin-top: 25px; font-size: 14px;}
        .text-right { text-align: right; }
        .footer { text-align: center; font-size: 9px; color: #777; position: fixed; bottom: 0; width: 100%; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Laporan Produk Terlaris</h1>
            <p><strong>{{ config('app.name', 'KasPlus') }}</strong></p>
            <p>Periode: {{ $tglMulai }} - {{ $tglSelesai }}</p>
        </div>

        <h3>Peringkat Berdasarkan Jumlah Terjual</h3>
        <table>
            <thead>
                <tr>
                    <th style="width:10%;">Peringkat</th>
                    <th>Nama Produk</th>
                    <th class="text-right" style="width:25%;">Jumlah Terjual</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data['produkByQuantity'] as $index => $produk)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $produk->item_name }}</td>
                        <td class="text-right">{{ $produk->total_quantity }}</td>
                    </tr>
                @empty
                    <tr><td colspan="3" style="text-align:center;">Tidak ada data penjualan pada periode ini.</td></tr>
                @endforelse
            </tbody>
        </table>

        <h3>Peringkat Berdasarkan Pendapatan</h3>
        <table>
            <thead>
                <tr>
                    <th style="width:10%;">Peringkat</th>
                    <th>Nama Produk</th>
                    <th class="text-right" style="width:25%;">Total Pendapatan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data['produkByRevenue'] as $index => $produk)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $produk->item_name }}</td>
                        <td class="text-right">Rp {{ number_format($produk->total_revenue, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="3" style="text-align:center;">Tidak ada data penjualan pada periode ini.</td></tr>
                @endforelse
            </tbody>
        </table>

        <div class="footer">
            Laporan ini dibuat secara otomatis oleh sistem pada {{ date('d M Y, H:i') }}.
        </div>
    </div>
</body>
</html>