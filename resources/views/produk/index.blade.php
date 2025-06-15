<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800">
            Daftar Produk
        </h2>
    </x-slot>

    {{-- Menggunakan layout container yang sama seperti Dashboard --}}
    <div class="py-2">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Pesan Sukses atau Error --}}
            @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded mb-6" role="alert">
                    <p>{{ session('success') }}</p>
                </div>
            @endif
            @if(session('error'))
                 <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded mb-6" role="alert">
                    <p>{{ session('error') }}</p>
                </div>
            @endif

            {{-- Alpine.js untuk fungsionalitas pencarian live --}}
            <div x-data="{ 
                search: '',
                products: {{ $produks->toJson() }},
                get filteredProducts() {
                    if (this.search === '') {
                        return this.products;
                    }
                    return this.products.filter(
                        product => product.nama.toLowerCase().includes(this.search.toLowerCase()) || 
                                 product.kategori.nama_kategori.toLowerCase().includes(this.search.toLowerCase())
                    );
                }
            }">

                <div class="mb-6">
                    <input x-model="search" type="text" placeholder="Cari nama produk atau kategori..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <template x-for="produk in filteredProducts" :key="produk.id">
                        <div class="bg-white rounded-xl shadow-md overflow-hidden transition-transform duration-300 hover:scale-105">
                            <div class="p-5">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="text-sm font-medium text-gray" x-text="produk.kategori.nama_kategori"></p>
                                        <p class="text-xl font-bold text-navy_blue" x-text="produk.nama"></p>
                                    </div>
                                    {{-- Tombol Aksi (Edit & Hapus) --}}
                                    <div class="flex space-x-2 flex-shrink-0">
                                        
                                        {{-- Tombol Edit (Ikon Solid) --}}
                                        {{-- Penjelasan:
                                            - Class diubah agar tombol memiliki border biru dan ikon berwarna biru.
                                            - Saat di-hover, background menjadi biru dan ikon menjadi putih.
                                            - Atribut SVG diubah ke fill="currentColor" dan path diganti ke versi solid.
                                        --}}
                                        <a :href="`/produk/${produk.id}/edit`" 
                                        class="bg-white border border-blue text-blue-500 hover:bg-white hover:text-blue p-2 rounded-full h-8 w-8 flex items-center justify-center transition-colors duration-200">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="blue">
                                                <path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z" />
                                                <path fill-rule="evenodd" d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" clip-rule="evenodd" />
                                            </svg>
                                        </a>
                                        
                                        {{-- Tombol Hapus (Ikon Solid) --}}
                                        {{-- Penjelasan:
                                            - Class diubah agar tombol memiliki border oranye dan ikon berwarna oranye.
                                            - Saat di-hover, background menjadi oranye dan ikon menjadi putih.
                                            - Atribut SVG diubah ke fill="currentColor" dan path diganti ke versi solid.
                                        --}}
                                        <form :action="`/produk/${produk.id}`" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="bg-white border border-dark_orange text-dark_orange hover:bg-white hover:text-dark_orange p-2 rounded-full h-8 w-8 flex items-center justify-center transition-colors duration-200" 
                                                    onclick="return confirm('Apakah Anda yakin ingin menghapus produk ini?')">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="dark_orange">
                                                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm4 0a1 1 0 012 0v6a1 1 0 11-2 0V8z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>

                                <div class="mt-4 border-t border-navy_blue pt-4">
                                    <p class="text-sm font-semibold mb-2 text-gray-600">Variasi:</p>
                                    <ul class="space-y-2">
                                        <template x-for="variasi in produk.variasis" :key="variasi.id">
                                            <li class="flex justify-between items-center text-sm">
                                                <span class="text-gray-700" x-text="variasi.nama"></span>
                                                <div class="text-right">
                                                    <p class="font-semibold text-gray-800" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(variasi.harga)"></p>
                                                    <p class="text-xs text-gray-500" x-text="'Stok: ' + variasi.stok"></p>
                                                </div>
                                            </li>
                                        </template>
                                         <template x-if="produk.variasis.length === 0">
                                            <li class="text-sm text-gray-400">Belum ada variasi.</li>
                                        </template>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                 {{-- Pesan jika tidak ada produk sama sekali atau tidak ditemukan --}}
                <div x-show="filteredProducts.length === 0" class="text-center py-16">
                    <p class="text-gray-500" x-text="products.length === 0 ? 'Belum ada produk yang ditambahkan.' : 'Produk tidak ditemukan.'"></p>
                </div>

            </div>
        </div>
    </div>

    {{-- Tombol Tambah Produk (Plus) - Warna disamakan dengan KPI utama dashboard --}}
    <div class="absolute bottom-20 right-4">
        <a href="{{ route('produk.create') }}" class="bg-blue hover:bg-blue-800 text-white font-bold h-14 w-14 flex items-center justify-center rounded-full shadow-lg transition">
            <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
        </a>
    </div>
    
    {{-- Pastikan Alpine.js di-load jika belum ada di layout utama --}}
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</x-app-layout>