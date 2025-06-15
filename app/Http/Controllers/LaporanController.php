<?php

namespace App\Http\Controllers;

use App\Models\FinancialEntry;
use App\Models\Transaction;
use App\Models\TransactionItem; // [BARU] Tambahkan ini
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // [BARU] Tambahkan ini
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class LaporanController extends Controller
{
    public function index()
    {
        return view('laporan.index');
    }

    public function show(Request $request, $jenis)
    {
        $tglMulai = Carbon::parse($request->query('tanggal_mulai', Carbon::now()->startOfMonth()))->startOfDay();
        $tglSelesai = Carbon::parse($request->query('tanggal_selesai', Carbon::now()->endOfMonth()))->endOfDay();
        
        $viewName = 'laporan.show_lainnya';
        $data = [];
        $judulLaporan = 'Laporan';

        switch ($jenis) {
            case 'keuangan':
                // ... (logika laporan keuangan yang sudah ada)
                $viewName = 'laporan.show_keuangan';
                $judulLaporan = 'Laporan Keuangan';
                $pemasukanKasir = Transaction::whereBetween('transaction_date', [$tglMulai, $tglSelesai])->get();
                $pemasukanLain = FinancialEntry::where('tipe', 'pemasukan')->whereBetween('tanggal', [$tglMulai, $tglSelesai])->get();
                $pengeluaran = FinancialEntry::where('tipe', 'pengeluaran')->whereBetween('tanggal', [$tglMulai, $tglSelesai])->get();
                $laporanGabungan = $pemasukanKasir->map(function ($trx) { return (object) ['tanggal' => $trx->transaction_date, 'keterangan' => 'Penjualan Kasir - Invoice #' . $trx->invoice_number, 'tipe' => 'pemasukan', 'jumlah' => $trx->total_amount,]; })->merge($pemasukanLain)->merge($pengeluaran)->sortByDesc('tanggal');
                $totalPemasukan = $pemasukanKasir->sum('total_amount') + $pemasukanLain->sum('jumlah');
                $totalPengeluaran = $pengeluaran->sum('jumlah');
                $labaRugi = $totalPemasukan - $totalPengeluaran;
                $data = ['laporanGabungan' => $laporanGabungan, 'totalPemasukan' => $totalPemasukan, 'totalPengeluaran' => $totalPengeluaran, 'labaRugi' => $labaRugi];
                break;
            
            case 'penjualan':
                // ... (logika laporan penjualan yang sudah ada)
                $viewName = 'laporan.show_penjualan';
                $judulLaporan = 'Laporan Penjualan';
                $transaksi = Transaction::with(['items', 'user'])->whereBetween('transaction_date', [$tglMulai, $tglSelesai])->orderBy('transaction_date', 'desc')->get();
                $totalPenjualan = $transaksi->sum('total_amount');
                $jumlahTransaksi = $transaksi->count();
                $rataRataTransaksi = ($jumlahTransaksi > 0) ? $totalPenjualan / $jumlahTransaksi : 0;
                $data = ['transaksi' => $transaksi, 'totalPenjualan' => $totalPenjualan, 'jumlahTransaksi' => $jumlahTransaksi, 'rataRataTransaksi' => $rataRataTransaksi];
                break;
            
            case 'produk_terlaris':
                $viewName = 'laporan.show_produk_terlaris'; // View baru
                $judulLaporan = 'Laporan Produk Terlaris';

                // Query untuk mendapatkan produk terlaris berdasarkan KUANTITAS
                $produkByQuantity = TransactionItem::whereHas('transaction', function ($query) use ($tglMulai, $tglSelesai) {
                        $query->whereBetween('transaction_date', [$tglMulai, $tglSelesai]);
                    })
                    ->select('item_name', DB::raw('SUM(quantity) as total_quantity'))
                    ->groupBy('item_name')
                    ->orderBy('total_quantity', 'desc')
                    ->take(10) // Ambil 10 teratas
                    ->get();
                
                // Query untuk mendapatkan produk terlaris berdasarkan PENDAPATAN
                $produkByRevenue = TransactionItem::whereHas('transaction', function ($query) use ($tglMulai, $tglSelesai) {
                        $query->whereBetween('transaction_date', [$tglMulai, $tglSelesai]);
                    })
                    ->select('item_name', DB::raw('SUM(quantity * item_price) as total_revenue'))
                    ->groupBy('item_name')
                    ->orderBy('total_revenue', 'desc')
                    ->take(10) // Ambil 10 teratas
                    ->get();

                $data = [
                    'produkByQuantity' => $produkByQuantity,
                    'produkByRevenue' => $produkByRevenue,
                ];
                break;
        }

        return view($viewName, [
            'data' => $data,
            'jenisLaporan' => $jenis,
            'judulLaporan' => $judulLaporan,
            'tglMulai' => $tglMulai->format('Y-m-d'),
            'tglSelesai' => $tglSelesai->format('Y-m-d'),
        ]);
    }

    public function generate(Request $request)
    {
        // ... (validasi request tidak berubah)
        $request->validate([ 'jenis_laporan' => 'required|string', 'tanggal_mulai' => 'required|date', 'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai', ]);

        $jenis = $request->jenis_laporan;
        $tglMulai = Carbon::parse($request->tanggal_mulai)->startOfDay();
        $tglSelesai = Carbon::parse($request->tanggal_selesai)->endOfDay();
        $data = [];

        switch ($jenis) {
            case 'keuangan':
                // ... (logika PDF keuangan tidak berubah)
                $pemasukanKasir = Transaction::whereBetween('transaction_date', [$tglMulai, $tglSelesai])->get();
                $pemasukanLain = FinancialEntry::where('tipe', 'pemasukan')->whereBetween('tanggal', [$tglMulai, $tglSelesai])->get();
                $pengeluaran = FinancialEntry::where('tipe', 'pengeluaran')->whereBetween('tanggal', [$tglMulai, $tglSelesai])->get();
                $laporanGabungan = $pemasukanKasir->map(function ($trx) { return (object) ['tanggal' => $trx->transaction_date, 'keterangan' => 'Penjualan Kasir - Invoice #' . $trx->invoice_number, 'tipe' => 'pemasukan', 'jumlah' => $trx->total_amount,]; })->merge($pemasukanLain)->merge($pengeluaran)->sortBy('tanggal');
                $totalPemasukan = $pemasukanKasir->sum('total_amount') + $pemasukanLain->sum('jumlah');
                $totalPengeluaran = $pengeluaran->sum('jumlah');
                $labaRugi = $totalPemasukan - $totalPengeluaran;
                $data = ['laporanGabungan' => $laporanGabungan, 'totalPemasukan' => $totalPemasukan, 'totalPengeluaran' => $totalPengeluaran, 'labaRugi' => $labaRugi];
                $pdf = Pdf::loadView('laporan.pdf.keuangan', ['data' => $data, 'tglMulai' => $tglMulai->format('d M Y'), 'tglSelesai' => $tglSelesai->format('d M Y'),]);
                return $pdf->stream('laporan-keuangan-' . $tglMulai->format('dmY') . '-' . $tglSelesai->format('dmY') . '.pdf');

            case 'penjualan':
                // ... (logika PDF penjualan tidak berubah)
                $transaksi = Transaction::with(['items', 'user'])->whereBetween('transaction_date', [$tglMulai, $tglSelesai])->orderBy('transaction_date', 'desc')->get();
                $totalPenjualan = $transaksi->sum('total_amount');
                $jumlahTransaksi = $transaksi->count();
                $data = ['transaksi' => $transaksi, 'totalPenjualan' => $totalPenjualan, 'jumlahTransaksi' => $jumlahTransaksi];
                $pdf = Pdf::loadView('laporan.pdf.penjualan', ['data' => $data, 'tglMulai' => $tglMulai->format('d M Y'), 'tglSelesai' => $tglSelesai->format('d M Y'),]);
                return $pdf->stream('laporan-penjualan-' . $tglMulai->format('dmY') . '-' . $tglSelesai->format('dmY') . '.pdf');
            
            case 'produk_terlaris':
                // Logika yang sama dengan di method show
                $produkByQuantity = TransactionItem::whereHas('transaction', function ($query) use ($tglMulai, $tglSelesai) { $query->whereBetween('transaction_date', [$tglMulai, $tglSelesai]); })->select('item_name', DB::raw('SUM(quantity) as total_quantity'))->groupBy('item_name')->orderBy('total_quantity', 'desc')->take(10)->get();
                $produkByRevenue = TransactionItem::whereHas('transaction', function ($query) use ($tglMulai, $tglSelesai) { $query->whereBetween('transaction_date', [$tglMulai, $tglSelesai]); })->select('item_name', DB::raw('SUM(quantity * item_price) as total_revenue'))->groupBy('item_name')->orderBy('total_revenue', 'desc')->take(10)->get();

                $data = [
                    'produkByQuantity' => $produkByQuantity,
                    'produkByRevenue' => $produkByRevenue,
                ];

                $pdf = Pdf::loadView('laporan.pdf.produk_terlaris', [
                    'data' => $data,
                    'tglMulai' => $tglMulai->format('d M Y'),
                    'tglSelesai' => $tglSelesai->format('d M Y'),
                ]);

                return $pdf->stream('laporan-produk-terlaris-' . $tglMulai->format('dmY') . '-' . $tglSelesai->format('dmY') . '.pdf');
        }

        return back()->with('error', 'Fitur PDF untuk laporan ini belum tersedia.');
    }
}