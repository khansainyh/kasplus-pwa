<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800">
            Pusat Laporan
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-6 text-center">Pilih Laporan yang Ingin Anda Lihat</h3>
                
                {{-- Grid untuk Kartu Laporan --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    
                    {{-- Kartu 1: Laporan Keuangan --}}
                    <a href="{{ route('laporan.show', ['jenis' => 'keuangan']) }}"
                       class="block p-6 bg-white border rounded-lg text-center shadow-sm hover:shadow-xl hover:border-blue-500 hover:-translate-y-1 transition-all duration-200">
                        
                        {{-- Ikon --}}
                        <div class="flex items-center justify-center h-16 w-16 mx-auto mb-4 bg-blue-100 rounded-full">
                            <svg class="w-8 h-8 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                            </svg>
                        </div>
                        
                        {{-- Judul & Deskripsi --}}
                        <h4 class="font-semibold text-gray-800 text-lg">Laporan Keuangan</h4>
                        <p class="text-xs text-gray-500 mt-1">Analisis pemasukan, pengeluaran, dan laba rugi.</p>
                    </a>

                    {{-- Kartu 2: Laporan Penjualan --}}
                    <a href="{{ route('laporan.show', ['jenis' => 'penjualan']) }}"
                       class="block p-6 bg-white border rounded-lg text-center shadow-sm hover:shadow-xl hover:border-green-500 hover:-translate-y-1 transition-all duration-200">
                        
                        <div class="flex items-center justify-center h-16 w-16 mx-auto mb-4 bg-green-100 rounded-full">
                            <svg class="w-8 h-8 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c.51 0 .962-.328 1.093-.829l2.87-6.641a.562.562 0 0 0-.16- .602l-2.26-1.507a.562.562 0 0 0-.64.045l-2.29 1.527a.562.562 0 0 1-.64.045l-2.29-1.527a.562.562 0 0 0-.64.045l-2.29 1.527a.562.562 0 0 1-.64.045l-2.29-1.527a.562.562 0 0 0-.64.045L3 6.75Z" />
                            </svg>
                        </div>

                        <h4 class="font-semibold text-gray-800 text-lg">Laporan Penjualan</h4>
                        <p class="text-xs text-gray-500 mt-1">Detail transaksi penjualan dari kasir.</p>
                    </a>

                    {{-- Kartu 3: Produk Terlaris --}}
                    <a href="{{ route('laporan.show', ['jenis' => 'produk_terlaris']) }}"
                       class="block p-6 bg-white border rounded-lg text-center shadow-sm hover:shadow-xl hover:border-yellow-500 hover:-translate-y-1 transition-all duration-200">

                        <div class="flex items-center justify-center h-16 w-16 mx-auto mb-4 bg-yellow-100 rounded-full">
                             <svg class="w-8 h-8 text-yellow-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z" />
                            </svg>
                        </div>

                        <h4 class="font-semibold text-gray-800 text-lg">Produk Terlaris</h4>
                        <p class="text-xs text-gray-500 mt-1">Peringkat produk berdasarkan jumlah penjualan.</p>
                    </a>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>