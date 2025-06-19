<x-app-layout>
    <x-slot name="header">
        {{-- UBAH INI: Ukuran font header halaman Edit Produk dikecilkan dari text-2xl menjadi text-xl --}}
        <h2 class="font-bold text-xl text-[#131951] text-center tracking-tight py-2">
            Edit Produk: {{ $produk->nama }}
        </h2>
    </x-slot>

    {{-- Main container untuk halaman, dengan padding dan font Satoshi --}}
    <div class="w-full max-w-2xl mx-auto py-8 px-4 font-satoshi">
        {{-- Pesan Error Validasi (jika ada) --}}
        @if ($errors->any())
            <div class="mb-6 p-4 bg-red-100 text-red-700 rounded-xl shadow-md font-medium tracking-wide">
                <strong class="font-bold">Oops! Terjadi kesalahan.</strong>
                <ul class="mt-2 list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('produk.update', $produk->id) }}" method="POST">
            @csrf
            @method('PUT') {{-- Penting: Menentukan metode HTTP PUT untuk update --}}

            {{-- Nama Produk --}}
            <div class="mb-4 p-6 bg-white rounded-2xl shadow-md">
                <label for="nama" class="block text-sm font-medium text-gray-700 mb-2 tracking-wide">Nama Produk</label>
                <input type="text" name="nama" id="nama" value="{{ old('nama', $produk->nama) }}"
                       class="mt-1 block w-full border-[#D3D3D3] rounded-lg shadow-sm focus:border-[#2D5AF7] focus:ring-[#2D5AF7] text-gray-800 p-3 text-base placeholder-gray-400 transition-all duration-200 @error('nama') border-red-500 @enderror"
                       placeholder="Contoh: Kopi Susu Aren" required>
                @error('nama')
                    <p class="text-red-500 text-xs mt-2 font-medium">{{ $message }}</p>
                @enderror
            </div>

            {{-- Kategori --}}
            <div class="mb-4 p-6 bg-white rounded-2xl shadow-md mt-3">
                <div class="flex justify-between items-center mb-2">
                    <label for="kategori_id" class="block text-sm font-medium text-gray-700 tracking-wide">Kategori</label>
                    <a href="{{ route('kategori.index') }}"
                       class="text-sm text-[#2D5AF7] hover:text-[#1f42b3] font-medium transition-colors duration-200 flex items-center gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        Kelola Kategori
                    </a>
                </div>
                <select name="kategori_id" id="kategori_id"
                        class="mt-1 block w-full border-[#D3D3D3] rounded-lg shadow-sm focus:border-[#2D5AF7] focus:ring-[#2D5AF7] text-gray-800 p-3 text-base transition-all duration-200 @error('kategori_id') border-red-500 @enderror" required>
                    <option value="">Pilih Kategori</option>
                    @foreach ($kategoris as $kategori)
                        <option value="{{ $kategori->id }}" {{ old('kategori_id', $produk->kategori_id) == $kategori->id ? 'selected' : '' }}>
                            {{ $kategori->nama_kategori }}
                        </option>
                    @endforeach
                </select>
                @error('kategori_id')
                    <p class="text-red-500 text-xs mt-2 font-medium">{{ $message }}</p>
                @enderror
            </div>

            {{-- Deskripsi --}}
            <div class="mb-4 p-6 bg-white rounded-2xl shadow-md mt-3">
                <label for="deskripsi" class="block text-sm font-medium text-gray-700 mb-2 tracking-wide">Deskripsi</label>
                <textarea name="deskripsi" id="deskripsi" rows="3"
                          class="mt-1 block w-full border-[#D3D3D3] rounded-lg shadow-sm focus:border-[#2D5AF7] focus:ring-[#2D5AF7] text-gray-800 p-3 text-base placeholder-gray-400 transition-all duration-200">{{ old('deskripsi', $produk->deskripsi) }}</textarea>
            </div>

            {{-- Variasi Produk (menggunakan Alpine.js) --}}
            <div x-data="{ variasis: {{ json_encode(old('variasis', $produk->variasis->map(function($v){ return ['id' => $v->id, 'nama' => $v->nama, 'harga' => $v->harga, 'stok' => $v->stok]; }))) }} }"
                 class="mt-4 p-6 bg-white rounded-2xl shadow-md">
                <h3 class="text-xl font-bold text-[#131951] mb-4 tracking-tight">Variasi Produk</h3>
                @error('variasis')
                    <p class="text-red-500 text-sm mb-4 font-medium">{{ $message }}</p>
                @enderror
                <template x-for="(variasi, index) in variasis" :key="index">
                    <div class="grid grid-cols-12 gap-3 items-end mb-4 p-4 bg-gray-50 rounded-lg border border-gray-100 shadow-sm">
                        <div class="col-span-12 sm:col-span-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1 tracking-wide">Nama Variasi</label>
                            {{-- Input hidden untuk ID variasi yang sudah ada --}}
                            <input type="hidden" :name="`variasis[${index}][id]`" x-model="variasi.id">
                            <input x-model="variasi.nama" :name="`variasis[${index}][nama]`" type="text"
                                   class="mt-1 block w-full border-[#D3D3D3] rounded-lg shadow-sm focus:border-[#2D5AF7] focus:ring-[#2D5AF7] text-gray-800 p-2 text-sm placeholder-gray-400"
                                   placeholder="Contoh: Pedas" required>
                        </div>
                        <div class="col-span-6 sm:col-span-3">
                            <label class="block text-sm font-medium text-gray-700 mb-1 tracking-wide">Harga</label>
                            <input x-model="variasi.harga" :name="`variasis[${index}][harga]`" type="number"
                                   class="mt-1 block w-full border-[#D3D3D3] rounded-lg shadow-sm focus:border-[#2D5AF7] focus:ring-[#2D5AF7] text-gray-800 p-2 text-sm placeholder-gray-400"
                                   placeholder="15000" required>
                        </div>
                        <div class="col-span-6 sm:col-span-3">
                            <label class="block text-sm font-medium text-gray-700 mb-1 tracking-wide">Stok</label>
                            <input x-model="variasi.stok" :name="`variasis[${index}][stok]`" type="number"
                                   class="mt-1 block w-full border-[#D3D3D3] rounded-lg shadow-sm focus:border-[#2D5AF7] focus:ring-[#2D5AF7] text-gray-800 p-2 text-sm placeholder-gray-400"
                                   placeholder="50" required>
                        </div>
                        <div class="col-span-12 sm:col-span-2 flex justify-end">
                            <button x-show="variasis.length > 1" @click.prevent="variasis.splice(index, 1)"
                                    class="bg-red-100 text-red-600 hover:bg-red-200 p-2 rounded-lg transition-colors duration-200">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </div>
                    </div>
                </template>
                <button @click.prevent="variasis.push({ id: '', nama: '', harga: '', stok: '' })"
                        class="bg-gray-100 text-gray-700 hover:bg-gray-200 px-4 py-2 rounded-lg font-medium transition-colors duration-200 text-sm mt-2">
                    + Tambah Variasi
                </button>
            </div>

            {{-- Tombol Update Produk --}}
            <div class="mt-4 p-6 bg-white rounded-2xl shadow-md">
                <button type="submit" class="w-full bg-[#2D5AF7] hover:bg-[#1f42b3] text-white font-bold py-3 rounded-xl shadow-md transition-colors duration-200">
                    Update Produk
                </button>
            </div>
        </form>
    </div>

    {{-- Custom Font Styling --}}
    <style>
        /* Memuat Font Satoshi */
        @font-face {
            font-family: 'Satoshi';
            src: url('https://cdn.fontshare.com/wf/E3E8C349-EBAB-4148-A951-404B67DFD0C6/4U3B674513E020A8FA45B2556515A97D/Satoshi-Variable.woff2') format('woff2');
            font-weight: 300 900;
            font-display: swap;
        }

        .font-satoshi {
            font-family: 'Satoshi', sans-serif;
        }
    </style>
</x-app-layout>
