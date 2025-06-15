<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\FinancialEntry;
use App\Models\TransactionItem;
use App\Models\Variasi;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use DatePeriod;
use DateInterval;

class DashboardController extends Controller
{
    public function index()
    {
        $defaultData = $this->getDashboardData('week');
        return view('dashboard', $defaultData);
    }

    public function fetchData(Request $request)
    {
        $period = $request->input('period', 'week');
        $data = $this->getDashboardData($period);
        return response()->json($data);
    }

    private function getDashboardData($period)
    {
        // 1. Tentukan rentang tanggal
        switch ($period) {
            case 'today':
                $startDate = Carbon::today()->startOfDay();
                $endDate = Carbon::today()->endOfDay();
                $format = 'H:00';
                break;
            case 'month':
                $startDate = Carbon::now()->startOfMonth();
                $endDate = Carbon::now()->endOfMonth();
                $format = 'd M';
                break;
            case 'year':
                $startDate = Carbon::now()->startOfYear();
                $endDate = Carbon::now()->endOfYear();
                $format = 'M Y';
                break;
            case 'week':
            default:
                $startDate = Carbon::now()->startOfWeek(Carbon::MONDAY);
                $endDate = Carbon::now()->endOfWeek(Carbon::SUNDAY);
                $format = 'd M';
                break;
        }

        // 2. Hitung Data untuk Kartu KPI
        $pemasukanKasir = Transaction::whereBetween('transaction_date', [$startDate, $endDate])->sum('total_amount');
        $pemasukanLain = FinancialEntry::where('tipe', 'pemasukan')->whereBetween('tanggal', [$startDate, $endDate])->sum('jumlah');
        $totalPemasukan = $pemasukanKasir + $pemasukanLain;
        $totalPengeluaran = FinancialEntry::where('tipe', 'pengeluaran')->whereBetween('tanggal', [$startDate, $endDate])->sum('jumlah');
        $keuntunganBersih = $totalPemasukan - $totalPengeluaran;
        $jumlahTransaksi = Transaction::whereBetween('transaction_date', [$startDate, $endDate])->count();
        $saldoKasTotal = (Transaction::sum('total_amount') + FinancialEntry::where('tipe', 'pemasukan')->sum('jumlah')) - FinancialEntry::where('tipe', 'pengeluaran')->sum('jumlah');

        // 3. Siapkan data untuk semua grafik
        // Logika untuk label berdasarkan periode
        if ($period === 'today') {
            $dateRange = new DatePeriod($startDate, new DateInterval('PT1H'), $endDate);
            $labels = [];
            foreach ($dateRange as $date) {
                $labels[] = $date->format($format);
            }
        } else if ($period === 'year') {
            $dateRange = new DatePeriod($startDate, new DateInterval('P1M'), $endDate);
             $labels = [];
            foreach ($dateRange as $date) {
                $labels[] = $date->format('M'); // Hanya bulan
            }
        }
        else {
            $dateRange = new DatePeriod($startDate, new DateInterval('P1D'), $endDate->copy()->addDay());
            $labels = [];
            foreach ($dateRange as $date) {
                $labels[] = $date->format($format);
            }
        }


        // 3a. Grafik Tren Pendapatan
        $pendapatanPerHari = Transaction::whereBetween('transaction_date', [$startDate, $endDate])
            ->selectRaw('DATE(transaction_date) as tanggal, SUM(total_amount) as total')
            ->groupBy('tanggal')->orderBy('tanggal')->pluck('total', 'tanggal');
        
        $dataPendapatan = array_fill(0, count($labels), 0);
        foreach (new DatePeriod($startDate, new DateInterval('P1D'), $endDate->copy()->addDay()) as $date) {
            $formattedDate = $date->format($format);
            $dateKey = $date->format('Y-m-d');
            $index = array_search($formattedDate, $labels);
            if ($index !== false) {
                 $dataPendapatan[$index] += $pendapatanPerHari->get($dateKey, 0);
            }
        }

        // 3b. Grafik Pemasukan vs Pengeluaran
        $pemasukanLainPerHari = FinancialEntry::where('tipe', 'pemasukan')->whereBetween('tanggal', [$startDate, $endDate])
            ->selectRaw('tanggal, SUM(jumlah) as total')->groupBy('tanggal')->pluck('total', 'tanggal');
            
        $pengeluaranTotalPerHari = FinancialEntry::where('tipe', 'pengeluaran')->whereBetween('tanggal', [$startDate, $endDate])
            ->selectRaw('tanggal, SUM(jumlah) as total')->groupBy('tanggal')->pluck('total', 'tanggal');

        $dataTotalPemasukan = array_fill(0, count($labels), 0);
        $dataTotalPengeluaran = array_fill(0, count($labels), 0);
        foreach (new DatePeriod($startDate, new DateInterval('P1D'), $endDate->copy()->addDay()) as $date) {
            $formattedDate = $date->format($format);
            $dateKey = $date->format('Y-m-d');
            $index = array_search($formattedDate, $labels);

            if($index !== false) {
                $pemasukanKasirHarian = $pendapatanPerHari->get($dateKey, 0);
                $pemasukanLainHarian = $pemasukanLainPerHari->get($dateKey, 0);
                $dataTotalPemasukan[$index] += $pemasukanKasirHarian + $pemasukanLainHarian;
                $dataTotalPengeluaran[$index] += $pengeluaranTotalPerHari->get($dateKey, 0);
            }
        }

        // 3c. Grafik Top 3 Produk Terlaris
        $topProduk = TransactionItem::join('transactions', 'transaction_items.transaction_id', '=', 'transactions.id')
            ->whereBetween('transactions.transaction_date', [$startDate, $endDate])
            ->select('transaction_items.item_name', DB::raw('SUM(transaction_items.quantity) as total_terjual'))
            ->groupBy('transaction_items.item_name')
            ->orderByDesc('total_terjual')
            ->limit(3)
            ->get();
        
        // 3d. Kartu Stok Segera Habis (tidak terpengaruh filter waktu)
        $stokMenipis = Variasi::with('produk')->where('stok', '<', 10)->orderBy('stok', 'asc')->limit(5)->get();

        // 4. Kembalikan semua data
        return [
            'kpi' => ['pendapatan' => $totalPemasukan, 'pengeluaran' => $totalPengeluaran, 'keuntungan' => $keuntunganBersih, 'jml_transaksi' => $jumlahTransaksi, 'saldo_kas' => $saldoKasTotal],
            'grafik_pendapatan' => ['labels' => $labels, 'data' => $dataPendapatan],
            'grafik_pemasukan_pengeluaran' => ['labels' => $labels, 'pemasukan' => $dataTotalPemasukan, 'pengeluaran' => $dataTotalPengeluaran],
            'top_produk' => ['labels' => $topProduk->pluck('item_name'), 'data' => $topProduk->pluck('total_terjual')],
            'stok_menipis' => $stokMenipis,
            'periode' => $period,
        ];
    }
}