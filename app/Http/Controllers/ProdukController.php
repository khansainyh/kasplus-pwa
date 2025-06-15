<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\Kategori;
use App\Models\Variasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProdukController extends Controller
{

    public function index()
    {
        $produks = Produk::with('variasis', 'kategori')->latest()->get();
        return view('produk.index', compact('produks'));
    }

    public function create()
    {
        $kategoris = Kategori::all();
        return view('produk.create', compact('kategoris'));
    }

    public function store(Request $request)
    {
        // 1. Validasi semua input dari form
        $validatedData = $request->validate([
            'nama' => 'required|string|max:255',
            'kategori_id' => 'required|exists:kategoris,id',
            'deskripsi' => 'nullable|string',
            'variasis' => 'required|array|min:1',
            'variasis.*.nama' => 'required|string|max:255',
            'variasis.*.harga' => 'required|numeric|min:0',
            'variasis.*.stok' => 'required|integer|min:0',
        ]);

        try {
            // 2. Mulai transaksi database
            DB::beginTransaction();

            // 3. Simpan data produk utama
            $produk = Produk::create([
                'nama' => $validatedData['nama'],
                'kategori_id' => $validatedData['kategori_id'],
                'deskripsi' => $validatedData['deskripsi'],
            ]);

            // 4. Looping untuk menyimpan setiap variasi
            foreach ($validatedData['variasis'] as $variasiData) {
                Variasi::create([
                    'produk_id' => $produk->id,
                    'nama' => $variasiData['nama'],
                    'harga' => $variasiData['harga'],
                    'stok' => $variasiData['stok'],
                ]);
            }

            // 5. Jika semua berhasil, commit (simpan permanen) transaksi
            DB::commit();

        } catch (\Exception $e) {
            // 6. Jika ada error di tengah jalan, batalkan semua yang sudah disimpan
            DB::rollBack();
            return back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan produk: ' . $e->getMessage());
        }

        // 7. Jika berhasil, kembali ke halaman daftar produk dengan pesan sukses
        return redirect()->route('produk.index')->with('success', 'Produk berhasil ditambahkan!');
    }

    public function edit(Produk $produk)
    {
        $kategoris = Kategori::all();
        // Menggunakan with('variasis') untuk memastikan variasi produk juga diambil
        $produk->load('variasis'); 
        return view('produk.edit', compact('produk', 'kategoris'));
    }

    public function update(Request $request, Produk $produk)
    {
        // 1. Validasi input
        $validatedData = $request->validate([
            'nama' => 'required|string|max:255',
            'kategori_id' => 'required|exists:kategoris,id',
            'deskripsi' => 'nullable|string',
            'variasis' => 'required|array|min:1',
            'variasis.*.id' => 'nullable|exists:variasis,id', // ID variasi yang sudah ada
            'variasis.*.nama' => 'required|string|max:255',
            'variasis.*.harga' => 'required|numeric|min:0',
            'variasis.*.stok' => 'required|integer|min:0',
        ]);

        try {
            DB::beginTransaction();

            // 2. Update data produk utama
            $produk->update([
                'nama' => $validatedData['nama'],
                'kategori_id' => $validatedData['kategori_id'],
                'deskripsi' => $validatedData['deskripsi'],
            ]);

            // 3. Update atau buat variasi baru, dan hapus variasi yang tidak lagi ada
            $existingVariasiIds = $produk->variasis->pluck('id')->toArray();
            $updatedVariasiIds = [];

            foreach ($validatedData['variasis'] as $variasiData) {
                if (isset($variasiData['id']) && $variasiData['id']) {
                    // Update variasi yang sudah ada
                    $variasi = Variasi::find($variasiData['id']);
                    if ($variasi) {
                        $variasi->update([
                            'nama' => $variasiData['nama'],
                            'harga' => $variasiData['harga'],
                            'stok' => $variasiData['stok'],
                        ]);
                        $updatedVariasiIds[] = $variasi->id;
                    }
                } else {
                    // Buat variasi baru
                    $newVariasi = Variasi::create([
                        'produk_id' => $produk->id,
                        'nama' => $variasiData['nama'],
                        'harga' => $variasiData['harga'],
                        'stok' => $variasiData['stok'],
                    ]);
                    $updatedVariasiIds[] = $newVariasi->id;
                }
            }

            // Hapus variasi yang tidak dikirimkan di form (berarti dihapus)
            $variasisToDelete = array_diff($existingVariasiIds, $updatedVariasiIds);
            Variasi::whereIn('id', $variasisToDelete)->delete();

            DB::commit();

            return redirect()->route('produk.index')->with('success', 'Produk berhasil diperbarui!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Terjadi kesalahan saat memperbarui produk: ' . $e->getMessage());
        }
    }

    public function destroy(Produk $produk)
    {
        try {
            $produk->delete(); // Ini juga akan menghapus variasi terkait karena onDelete('cascade') di migrasi
            return redirect()->route('produk.index')->with('success', 'Produk berhasil dihapus!');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat menghapus produk: ' . $e->getMessage());
        }
    }

    // ... (metode export/import jika ada) ...
}