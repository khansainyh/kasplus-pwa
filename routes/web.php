<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KasirController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\KeuanganController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LlamaController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return view('landing'); // ganti arahkan ke landing page
});

Route::get('/landing', function () {
    return view('landing');
});

// Semua rute yang butuh login kita kelompokkan di sini
Route::middleware(['auth', 'verified'])->group(function () {
    
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/data', [DashboardController::class, 'fetchData'])->name('dashboard.data');
    
    // Produk
    Route::get('/produk', [ProdukController::class, 'index'])->name('produk.index');
    Route::get('/produk/create', [ProdukController::class, 'create'])->name('produk.create');
    Route::post('/produk', [ProdukController::class, 'store'])->name('produk.store');
    Route::get('/produk/{produk}/edit', [ProdukController::class, 'edit'])->name('produk.edit');
    Route::put('/produk/{produk}', [ProdukController::class, 'update'])->name('produk.update');
    Route::delete('/produk/{produk}', [ProdukController::class, 'destroy'])->name('produk.destroy');
    
    // Kategori
    Route::resource('kategori', KategoriController::class)->only(['index', 'store', 'destroy']);
    
    // Kasir (Path controller dirapikan)
    Route::get('/kasir', [KasirController::class, 'index'])->name('kasir.index');
    Route::post('/kasir/checkout', [KasirController::class, 'checkout'])->name('kasir.checkout');
    Route::get('/kasir/receipt/{invoice_number}', [KasirController::class, 'showReceipt'])->name('kasir.receipt');
    
    // Keuangan (Path controller dirapikan)
    Route::get('/keuangan', [KeuanganController::class, 'index'])->name('keuangan.index');
    Route::get('/keuangan/pemasukan/create', [KeuanganController::class, 'createPemasukan'])->name('keuangan.pemasukan.create');
    Route::post('/keuangan/pemasukan', [KeuanganController::class, 'storePemasukan'])->name('keuangan.pemasukan.store');
    Route::get('/keuangan/pengeluaran/create', [KeuanganController::class, 'createPengeluaran'])->name('keuangan.pengeluaran.create');
    Route::post('/keuangan/pengeluaran', [KeuanganController::class, 'storePengeluaran'])->name('keuangan.pengeluaran.store');

    // Laporan 
    Route::prefix('laporan')->name('laporan.')->group(function () {
        // Rute untuk halaman utama "Pusat Laporan"
        Route::get('/', [LaporanController::class, 'index'])->name('index'); 
        
        // [BARU] Rute untuk MENAMPILKAN laporan di web
        Route::get('/show/{jenis}', [LaporanController::class, 'show'])->name('show');
        
        // Rute untuk men-generate PDF (tetap dibutuhkan untuk tombol download)
        Route::post('/generate', [LaporanController::class, 'generate'])->name('generate');
    });

    
    // Chatbot
    Route::get('/chatbot', [LlamaController::class, 'showChatbot'])->name('chatbot.show');
    Route::post('/ask-llama', [LlamaController::class, 'askWeb'])->name('chatbot.ask');
});

require __DIR__.'/auth.php';