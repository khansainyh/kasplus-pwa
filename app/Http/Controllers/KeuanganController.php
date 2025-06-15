<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request; // Pastikan ini ada
use App\Models\Transaction;
use App\Models\FinancialEntry;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator;

class KeuanganController extends Controller
{
    /**
     * [FINAL] Menampilkan laporan keuangan dengan fungsionalitas filter.
     */
    public function index(Request $request)
    {
        // 1. Ambil nilai filter dari URL, defaultnya adalah 'semua'
        $filter = $request->query('filter', 'semua');

        // Inisialisasi collection kosong
        $transaksiKasir = collect();
        $entriLain = collect();

        // 2. Logika pengambilan data berdasarkan filter
        if ($filter == 'semua' || $filter == 'pemasukan') {
            // Ambil data dari Transaksi Kasir (semua adalah pemasukan)
            $transaksiKasir = Transaction::with('user')->get()->map(function ($trx) {
                return (object) [
                    'id' => 'kasir-' . $trx->id,
                    'tanggal' => $trx->transaction_date,
                    'tipe' => 'pemasukan_kasir',
                    'keterangan' => 'Penjualan Kasir - Invoice #' . $trx->invoice_number,
                    'jumlah' => $trx->total_amount,
                    'user_name' => $trx->user ? $trx->user->name : 'N/A',
                    'original_data' => $trx
                ];
            });
        }

        if ($filter == 'semua') {
            // Ambil SEMUA data dari Pemasukan/Pengeluaran Lain
            $entriLain = FinancialEntry::with('user')->get()->map(function ($entry) {
                return (object) [
                    'id' => 'entry-' . $entry->id,
                    'tanggal' => $entry->tanggal . ' ' . $entry->created_at->format('H:i:s'),
                    'tipe' => $entry->tipe,
                    'keterangan' => $entry->keterangan,
                    'jumlah' => $entry->jumlah,
                    'user_name' => $entry->user ? $entry->user->name : 'N/A',
                    'original_data' => null
                ];
            });
        } else {
            // Jika filter bukan 'semua', ambil HANYA yang sesuai dengan filter
            $entriLain = FinancialEntry::with('user')->where('tipe', $filter)->get()->map(function ($entry) {
                return (object) [
                    'id' => 'entry-' . $entry->id,
                    'tanggal' => $entry->tanggal . ' ' . $entry->created_at->format('H:i:s'),
                    'tipe' => $entry->tipe,
                    'keterangan' => $entry->keterangan,
                    'jumlah' => $entry->jumlah,
                    'user_name' => $entry->user ? $entry->user->name : 'N/A',
                    'original_data' => null
                ];
            });
        }

        // 3. Gabungkan kedua sumber data, lalu urutkan berdasarkan tanggal terbaru
        $laporanGabungan = $transaksiKasir->merge($entriLain)->sortByDesc('tanggal');

        // 4. Buat Paginasi Secara Manual
        $perPage = 20;
        $currentPage = request()->input('page', 1);
        $pagedData = $laporanGabungan->slice(($currentPage - 1) * $perPage, $perPage);
        $laporanKeuangan = new LengthAwarePaginator(
            $pagedData->values(),
            $laporanGabungan->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );
        
        // 5. Hitung total untuk kartu ringkasan (tetap menunjukkan total keseluruhan)
        $pemasukanKasir = Transaction::where('payment_method', '!=', 'refund')->sum('total_amount');
        $pemasukanLain = FinancialEntry::where('tipe', 'pemasukan')->sum('jumlah');
        $totalPengeluaran = FinancialEntry::where('tipe', 'pengeluaran')->sum('jumlah');
        $totalPemasukan = $pemasukanKasir + $pemasukanLain;
        $saldoAkhir = $totalPemasukan - $totalPengeluaran;

        // 6. Kirim data yang sudah difilter dan variabel filter ke view
        return view('keuangan.index', compact(
            'laporanKeuangan',
            'totalPemasukan',
            'totalPengeluaran',
            'pemasukanKasir',
            'saldoAkhir',
            'filter' // Kirim variabel filter ke view
        ));
    }

    /**
     * Menampilkan form untuk menambah data Pemasukan baru.
     */
    public function createPemasukan()
    {
        return view('keuangan.create', ['tipe' => 'pemasukan']);
    }

    /**
     * Menampilkan form untuk menambah data Pengeluaran baru.
     */
    public function createPengeluaran()
    {
        return view('keuangan.create', ['tipe' => 'pengeluaran']);
    }

    /**
     * Menyimpan data Pemasukan baru dari form ke database.
     */
    public function storePemasukan(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'jumlah' => 'required|numeric|min:0',
            'keterangan' => 'required|string|max:255',
        ]);

        FinancialEntry::create([
            'tanggal' => $request->tanggal,
            'tipe' => 'pemasukan',
            'jumlah' => $request->jumlah,
            'keterangan' => $request->keterangan,
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('keuangan.index')->with('success', 'Data pemasukan berhasil ditambahkan.');
    }

    /**
     * Menyimpan data Pengeluaran baru dari form ke database.
     */
    public function storePengeluaran(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'jumlah' => 'required|numeric|min:0',
            'keterangan' => 'required|string|max:255',
        ]);

        FinancialEntry::create([
            'tanggal' => $request->tanggal,
            'tipe' => 'pengeluaran',
            'jumlah' => $request->jumlah,
            'keterangan' => $request->keterangan,
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('keuangan.index')->with('success', 'Data pengeluaran berhasil ditambahkan.');
    }
}