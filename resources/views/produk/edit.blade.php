<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800">
            Edit Produk: {{ $produk->nama }}
        </h2>
    </x-slot>

    <div class="w-full pb-10">
        <div class="bg-white p-4 rounded-lg shadow-md">
            @if ($errors->any())
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <strong class="font-bold">Oops! Terjadi kesalahan.</strong>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('produk.update', $produk->id) }}" method="POST">
                @csrf
                @method('PUT') {{-- Penting: Menentukan metode HTTP PUT untuk update --}}

                <div class="mb-4">
                    <label for="nama" class="block text-sm font-medium text-gray-700">Nama Produk</label>
                    <input type="text" name="nama" id="nama" value="{{ old('nama', $produk->nama) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm @error('nama') border-red-500 @enderror" required>
                    @error('nama')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <div class="flex justify-between items-center">
                        <label for="kategori_id" class="block text-sm font-medium text-gray-700">Kategori</label>
                        <a href="{{ route('kategori.index') }}" class="text-xs text-blue-600 hover:underline">(+ Kelola Kategori)</a>
                    </div>
                    <select name="kategori_id" id="kategori_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm @error('kategori_id') border-red-500 @enderror" required>
                        <option value="">Pilih Kategori</option>
                        @foreach ($kategoris as $kategori)
                            <option value="{{ $kategori->id }}" {{ old('kategori_id', $produk->kategori_id) == $kategori->id ? 'selected' : '' }}>
                                {{ $kategori->nama_kategori }}
                            </option>
                        @endforeach
                    </select>
                    @error('kategori_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="deskripsi" class="block text-sm font-medium text-gray-700">Deskripsi</label>
                    <textarea name="deskripsi" id="deskripsi" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ old('deskripsi', $produk->deskripsi) }}</textarea>
                </div>

                <div x-data="{ variasis: {{ json_encode(old('variasis', $produk->variasis->map(function($v){ return ['id' => $v->id, 'nama' => $v->nama, 'harga' => $v->harga, 'stok' => $v->stok]; }))) }} }">
                    <h3 class="text-lg font-semibold border-t pt-4 mt-4 mb-2">Variasi Produk</h3>
                    @error('variasis')
                        <p class="text-red-500 text-xs mb-2">{{ $message }}</p>
                    @enderror
                    <template x-for="(variasi, index) in variasis" :key="index">
                        <div class="grid grid-cols-12 gap-2 items-center mb-2 p-2 border rounded-md">
                            <div class="col-span-4">
                                <label class="block text-xs font-medium text-gray-700">Nama Variasi</label>
                                {{-- Input hidden untuk ID variasi yang sudah ada --}}
                                <input type="hidden" :name="`variasis[${index}][id]`" x-model="variasi.id">
                                <input x-model="variasi.nama" :name="`variasis[${index}][nama]`" type="text" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm" placeholder="Contoh: Pedas" required>
                            </div>
                            <div class="col-span-3">
                                <label class="block text-xs font-medium text-gray-700">Harga</label>
                                <input x-model="variasi.harga" :name="`variasis[${index}][harga]`" type="number" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm" placeholder="15000" required>
                            </div>
                            <div class="col-span-3">
                                <label class="block text-xs font-medium text-gray-700">Stok</label>
                                <input x-model="variasi.stok" :name="`variasis[${index}][stok]`" type="number" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm" placeholder="50" required>
                            </div>
                            <div class="col-span-2 text-right">
                                <button x-show="index > 0" @click.prevent="variasis.splice(index, 1)" class="text-red-500 hover:text-red-700 mt-5">&#10005;</button>
                            </div>
                        </div>
                    </template>
                    <button @click.prevent="variasis.push({ id: '', nama: '', harga: '', stok: '' })" class="text-sm text-blue-600 hover:underline">+ Tambah Variasi</button>
                </div>

                <div class="mt-6 border-t pt-4">
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">
                        Update Produk
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>