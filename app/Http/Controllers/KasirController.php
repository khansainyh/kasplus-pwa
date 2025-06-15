<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\Variasi;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
// use Maatwebsite\Excel\Facades\Excel; // Untuk fungsionalitas Excel jika diperlukan

class KasirController extends Controller
{
    /**
     * Menampilkan halaman kasir dengan daftar produk.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $produks = Produk::with('variasis', 'kategori')->get();
        return view('kasir.index', compact('produks'));
    }

    /**
     * Memproses checkout transaksi dari kasir.
     * Ini akan menyimpan transaksi ke database dan mengurangi stok.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkout(Request $request)
    {
        // ... (kode validasi yang sudah ada) ...
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.produk_id' => 'required|exists:produks,id',
            'items.*.variasi_id' => 'nullable|exists:variasis,id',
            'items.*.qty' => 'required|integer|min:1',
            'total' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,qris',
            'amount_paid' => 'nullable|numeric|min:0',
        ]);


        try {
            DB::beginTransaction();

            $totalAmount = $request->total;
            $amountPaid = $request->amount_paid;
            $changeAmount = $request->payment_method === 'cash' ? ($amountPaid - $totalAmount) : 0;

            if ($request->payment_method === 'cash' && $amountPaid < $totalAmount) {
                DB::rollBack();
                return response()->json(['message' => 'Uang yang diterima kurang dari total pembayaran.'], 400);
            }

            // 1. Buat transaksi utama
            $transaction = Transaction::create([
                'invoice_number' => 'INV-' . Str::upper(Str::random(8)) . Carbon::now()->format('YmdHis'),
                'user_id' => auth()->id(), // ID user yang sedang login (kasir)
                'total_amount' => $totalAmount,
                'payment_method' => $request->payment_method,
                'amount_paid' => $amountPaid,
                'change_amount' => $changeAmount,
                'transaction_date' => Carbon::now(), // Waktu transaksi saat ini
            ]);

            // 2. Simpan setiap item transaksi dan update stok
            foreach ($request->items as $itemData) {
                $produk = Produk::find($itemData['produk_id']);
                if (!$produk) {
                    DB::rollBack();
                    return response()->json(['message' => 'Produk tidak ditemukan.'], 404);
                }

                $itemPrice = 0;
                $itemName = $produk->nama;
                
                // Pastikan harga produk default tersedia jika tanpa variasi
                // if (empty($itemData['variasi_id']) && is_null($produk->harga)) { // Ini hanya jika kolom harga produk bisa null
                //     DB::rollBack();
                //     return response()->json(['message' => 'Harga produk utama tidak ditemukan untuk produk ' . $produk->nama . '.'], 400);
                // }

                if (!empty($itemData['variasi_id'])) {
                    $variasi = Variasi::find($itemData['variasi_id']);
                    if (!$variasi) {
                        DB::rollBack();
                        return response()->json(['message' => 'Variasi produk tidak ditemukan.'], 404);
                    }
                    if ($variasi->stok < $itemData['qty']) {
                        DB::rollBack();
                        return response()->json(['message' => 'Stok variasi "' . $variasi->nama . '" tidak mencukupi. Tersedia: ' . $variasi->stok], 400);
                    }
                    $itemPrice = $variasi->harga;
                    $itemName .= ' (' . $variasi->nama . ')';
                    
                    $variasi->decrement('stok', $itemData['qty']);
                } else {
                    if ($produk->stok < $itemData['qty']) {
                        DB::rollBack();
                        return response()->json(['message' => 'Stok produk "' . $produk->nama . '" tidak mencukupi. Tersedia: ' . $produk->stok], 400);
                    }
                    $itemPrice = $produk->harga; // Ambil harga dari produk jika tidak ada variasi
                    
                    $produk->decrement('stok', $itemData['qty']);
                }

                TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'produk_id' => $itemData['produk_id'],
                    'variasi_id' => $itemData['variasi_id'] ?? null,
                    'item_name' => $itemName,
                    'item_price' => $itemPrice,
                    'quantity' => $itemData['qty'],
                    'subtotal' => $itemPrice * $itemData['qty'],
                ]);
            }

            DB::commit();

            // Berikan respons sukses ke frontend, sertakan nomor invoice
            return response()->json([
                'message' => 'Transaksi berhasil!',
                'invoice_number' => $transaction->invoice_number,
                'redirect_url' => route('kasir.receipt', $transaction->invoice_number) // Rute ke halaman struk
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json(['message' => 'Validasi gagal: ' . $e->getMessage(), 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error saat checkout kasir: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['message' => 'Terjadi kesalahan server saat memproses transaksi.'], 500);
        }
    }

    /**
     * Menampilkan detail struk transaksi.
     *
     * @param  string  $invoiceNumber
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function showReceipt(string $invoiceNumber)
    {
        // Ambil transaksi beserta item-itemnya dan user yang melakukan transaksi
        $transaction = Transaction::where('invoice_number', $invoiceNumber)
                                  ->with('items', 'user')
                                  ->first();

        if (!$transaction) {
            // Jika transaksi tidak ditemukan, redirect atau tampilkan error
            return redirect()->route('kasir.index')->with('error', 'Transaksi tidak ditemukan.');
        }

        return view('kasir.receipt', compact('transaction'));
    }

    // ... (metode lain seperti exportTransactions jika ada) ...
}