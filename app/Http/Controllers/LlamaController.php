<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Exception;
use App\Models\Produk;
use App\Models\Transaction;
use App\Models\Kategori;
use App\Models\Variasi;
use App\Models\FinancialEntry; // [MODIFIKASI] Ganti Expense dengan FinancialEntry
use Carbon\Carbon;

class LlamaController extends Controller
{
    // ... (property $appKnowledgeBase tidak perlu diubah) ...
    private array $appKnowledgeBase = [
        'dashboard' => [ 'description' => "Modul Dashboard adalah ringkasan utama aktivitas bisnis Anda...", 'features' => [ "Melihat ringkasan penjualan harian", "Melihat jumlah transaksi", "Melihat produk-produk terlaris", "Melihat grafik pemasukan", "Akses cepat ke modul lain" ], 'route_name' => 'dashboard', 'link_text' => 'Pergi ke Dashboard' ],
        'kasir' => [ 'description' => "Modul Kasir adalah tempat Anda memproses penjualan...", 'features' => [ "Menampilkan daftar produk", "Mencari produk", "Memilih variasi produk", "Menambahkan produk ke keranjang", "Mengubah kuantitas", "Menghitung total pembayaran", "Memilih metode pembayaran (Tunai/QRIS)", "Memproses transaksi", "Melihat struk transaksi" ], 'route_name' => 'kasir.index', 'link_text' => 'Pergi ke Kasir' ],
        'produk' => [ 'description' => "Modul Produk adalah tempat Anda mengelola daftar barang atau jasa...", 'features' => [ "Melihat daftar semua produk", "Menambahkan produk baru", "Mengedit detail produk", "Mengelola variasi produk (nama, harga, stok)", "Menghapus produk", "Mengelola kategori produk" ], 'route_name' => 'produk.index', 'link_text' => 'Lihat Produk' ],
        'keuangan' => [ 'description' => "Modul Keuangan memberikan ringkasan dan riwayat aktivitas finansial Anda...", 'features' => [ "Melihat saldo akhir", "Melihat total pemasukan", "Melihat total pengeluaran", "Melihat pemasukan dari kasir", "Memfilter riwayat transaksi (harian, bulanan, dll)", "Melihat struk transaksi lama" ], 'route_name' => 'keuangan.index', 'link_text' => 'Pergi ke Keuangan' ],
        'laporan' => [ 'description' => "Modul Laporan adalah tempat Anda dapat menghasilkan berbagai laporan bisnis...", 'features' => [ "Membuat laporan penjualan", "Membuat laporan keuangan", "Menganalisis performa bisnis", "Mengekspor laporan" ], 'route_name' => 'laporan.index', 'link_text' => 'Lihat Laporan' ]
    ];


    // ... (fungsi askWeb, getLlamaResponse, dan showChatbot tidak perlu diubah) ...
    public function askWeb(Request $request)
    {
        $request->validate(['prompt' => 'required|string', 'history' => 'nullable|array']);
        try {
            $userPromptText = $request->prompt;
            $conversationHistory = $request->input('history', []);
            $systemMessage = [ 'role' => 'system', 'content' => "Anda adalah chatbot Customer Service untuk aplikasi KasPlus. Anda dapat membantu pengguna menavigasi aplikasi dan memberikan informasi tentang keuangan, produk, dan laporan berdasarkan data yang diberikan atau pengetahuan internal aplikasi. Jawablah pertanyaan dengan sopan, informatif, dan ringkas. Jangan membuat informasi palsu. Jika Anda tidak memiliki data atau informasi yang relevan untuk menjawab, katakan 'Maaf, saya tidak memiliki informasi yang Anda cari.' atau 'Maaf, saya tidak dapat melakukan itu saat ini.'." ];
            $contextData = '';
            $appFeatureContext = '';
            foreach ($this->appKnowledgeBase as $key => $module) {
                if (stripos($userPromptText, $key) !== false || stripos($userPromptText, 'menu ' . $key) !== false || stripos($userPromptText, 'fitur ' . $key) !== false) {
                    $appFeatureContext = "Modul " . ucfirst($key) . " (" . $module['link_text'] . "):\n" . $module['description'] . "\nFungsi utamanya meliputi:\n- " . implode("\n- ", $module['features']) . "\n";
                    break;
                }
            }
            if (stripos($userPromptText, 'pemasukan') !== false || stripos($userPromptText, 'keuangan') !== false || stripos($userPromptText, 'saldo') !== false || stripos($userPromptText, 'pengeluaran') !== false || stripos($userPromptText, 'laporan') !== false) {
                $contextData .= $this->getFinancialSummaryContext($userPromptText);
            }
            if (stripos($userPromptText, 'produk') !== false || stripos($userPromptText, 'barang') !== false || stripos($userPromptText, 'stok') !== false || stripos($userPromptText, 'harga') !== false) {
                 $contextData .= $this->getProductsSummaryContext($userPromptText);
            }
            $messagesForLlama = [$systemMessage];
            if (!empty($contextData)) {
                $messagesForLlama[] = ['role' => 'system', 'content' => "Berikut adalah data konkret dari aplikasi yang relevan:\n" . $contextData];
            }
            if (!empty($appFeatureContext)) {
                $messagesForLlama[] = ['role' => 'system', 'content' => "Berikut adalah deskripsi fungsionalitas modul aplikasi:\n" . $appFeatureContext];
            }
            foreach ($conversationHistory as $msg) {
                if ($msg['role'] === 'user' || $msg['role'] === 'assistant') {
                    $messagesForLlama[] = ['role' => $msg['role'], 'content' => $msg['content']];
                }
            }
            $messagesForLlama[] = ['role' => 'user', 'content' => $userPromptText];
            $responseContent = $this->getLlamaResponse($messagesForLlama);
            return response()->json(['input' => $request->prompt, 'response' => $responseContent]);
        } catch (Exception $e) {
            return response()->json(['error' => 'Gagal terhubung ke LLaMA API: ' . $e->getMessage()], 500);
        }
    }
    private function getLlamaResponse(array $messages)
    {
        $response = Http::withHeaders([ 'Authorization' => 'Bearer ' . env('GROQ_API_KEY'), 'Content-Type' => 'application/json', ])->post('https://api.groq.com/openai/v1/chat/completions', [ 'model' => 'llama3-8b-8192', 'messages' => $messages, 'temperature' => 0.7, ]);
        if ($response->failed()) { throw new Exception('API request failed: ' . $response->body()); }
        return $response->json()['choices'][0]['message']['content'] ?? 'Maaf, saya tidak mendapat jawaban.';
    }
    public function showChatbot() { return view('chatbot'); }


    /**
     * [MODIFIKASI] Mengambil ringkasan data keuangan menggunakan FinancialEntry.
     */
    private function getFinancialSummaryContext(string $userPrompt): string
    {
        $timeframe = 'all_time';
        if (stripos($userPrompt, 'hari ini') !== false) {
            $timeframe = 'today';
        } elseif (stripos($userPrompt, 'bulan ini') !== false) {
            $timeframe = 'monthly';
        }
        
        // Pemasukan dari kasir
        $totalIncomeFromKasir = Transaction::sum('total_amount');
        // Pemasukan dari entri manual
        $totalIncomeFromEntry = FinancialEntry::where('tipe', 'pemasukan')->sum('jumlah');
        // Pengeluaran dari entri manual
        $totalExpensesFromEntry = FinancialEntry::where('tipe', 'pengeluaran')->sum('jumlah');

        // Kalkulasi Total
        $totalIncomeAllTime = $totalIncomeFromKasir + $totalIncomeFromEntry;
        $trueBalance = $totalIncomeAllTime - $totalExpensesFromEntry;
        
        $context = "Data Keuangan:\n";

        switch ($timeframe) {
            case 'today':
                $today = Carbon::today();
                $incomeKasirToday = Transaction::whereDate('transaction_date', $today)->sum('total_amount');
                $incomeEntryToday = FinancialEntry::where('tipe', 'pemasukan')->whereDate('tanggal', $today)->sum('jumlah');
                $expensesEntryToday = FinancialEntry::where('tipe', 'pengeluaran')->whereDate('tanggal', $today)->sum('jumlah');
                $incomeToday = $incomeKasirToday + $incomeEntryToday;

                $context .= "- Pemasukan hari ini (" . $today->format('d M Y') . "): Rp " . number_format($incomeToday, 0, ',', '.');
                $context .= "\n- Pengeluaran hari ini (" . $today->format('d M Y') . "): Rp " . number_format($expensesEntryToday, 0, ',', '.');
                $context .= "\n\n- Saldo akhir (keseluruhan): Rp " . number_format($trueBalance, 0, ',', '.');
                break;

            case 'monthly':
                $startOfMonth = Carbon::now()->startOfMonth();
                $endOfMonth = Carbon::now()->endOfMonth();
                $incomeKasirMonth = Transaction::whereBetween('transaction_date', [$startOfMonth, $endOfMonth])->sum('total_amount');
                $incomeEntryMonth = FinancialEntry::where('tipe', 'pemasukan')->whereBetween('tanggal', [$startOfMonth, $endOfMonth])->sum('jumlah');
                $expensesEntryMonth = FinancialEntry::where('tipe', 'pengeluaran')->whereBetween('tanggal', [$startOfMonth, $endOfMonth])->sum('jumlah');
                $incomeThisMonth = $incomeKasirMonth + $incomeEntryMonth;

                $context .= "- Pemasukan bulan ini (" . $startOfMonth->format('M Y') . "): Rp " . number_format($incomeThisMonth, 0, ',', '.');
                $context .= "\n- Pengeluaran bulan ini (" . $startOfMonth->format('M Y') . "): Rp " . number_format($expensesEntryMonth, 0, ',', '.');
                $context .= "\n\n- Saldo akhir (keseluruhan): Rp " . number_format($trueBalance, 0, ',', '.');
                break;

            default: // case 'all_time'
                $context .= "- Total pemasukan (sepanjang waktu): Rp " . number_format($totalIncomeAllTime, 0, ',', '.');
                $context .= "\n- Total pengeluaran (sepanjang waktu): Rp " . number_format($totalExpensesFromEntry, 0, ',', '.');
                $context .= "\n- Saldo akhir saat ini: Rp " . number_format($trueBalance, 0, ',', '.');
                break;
        }
        
        return $context;
    }

    // ... (fungsi getProductsSummaryContext tidak perlu diubah) ...
    private function getProductsSummaryContext(string $userPrompt): string
    {
        $context = "Data Produk:\n";
        $productsLowStock = Produk::whereHas('variasis', function ($query) { $query->where('stok', '<=', 5); })->get();
        $totalProducts = Produk::count();
        $context .= "- Saat ini ada " . $totalProducts . " jenis produk terdaftar.";
        if ($productsLowStock->count() > 0) {
            $context .= "\n- Ada " . $productsLowStock->count() . " produk dengan variasi yang stoknya rendah (kurang dari 5). Beberapa di antaranya: ";
            $context .= $productsLowStock->take(3)->pluck('nama')->implode(', ') . ".";
        } else {
            $context .= "\n- Tidak ada produk dengan variasi yang stoknya rendah.";
        }
        if (preg_match('/(stok|harga|info|detail|produk|barang|cari)\s+(.+)/i', $userPrompt, $matches) && isset($matches[2])) {
            $specificProductName = trim($matches[2]);
            $specificProductName = preg_replace('/\s+(ya|yaa|dong|kak|nya)$/i', '', $specificProductName);
            if (!empty($specificProductName)) {
                $product = Produk::with('variasis')->where('nama', 'like', '%' . $specificProductName . '%')->first();
                if ($product) {
                    $context .= "\n\n- Informasi detail untuk produk '" . $product->nama . "':";
                    if ($product->variasis->count() > 0) {
                        $context .= " memiliki variasi sebagai berikut:";
                        foreach ($product->variasis as $variasi) {
                            $context .= "\n  - Variasi '" . $variasi->nama . "': Stok " . $variasi->stok . ", Harga Rp " . number_format($variasi->harga, 0, ',', '.') . ".";
                        }
                    } else {
                        if (Schema::hasColumn('produks', 'harga') && Schema::hasColumn('produks', 'stok')) {
                            $context .= " Stok: " . $product->stok . ", Harga: Rp " . number_format($product->harga, 0, ',', '.') . ".";
                        } else {
                            $context .= " Produk ini tidak memiliki variasi dan detail stok/harga tidak tersedia secara langsung.";
                        }
                    }
                } else {
                    $context .= "\n\n- Maaf, produk dengan nama yang mirip '" . $specificProductName . "' tidak dapat saya temukan di database.";
                }
            }
        }
        return $context;
    }
}