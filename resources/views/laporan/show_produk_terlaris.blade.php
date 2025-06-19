<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('laporan.index') }}" class="text-gray-500 hover:text-gray-700">
                <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" /></svg>
            </a>
            <h2 class="font-bold text-xl text-gray-800">
                Laporan Produk Terlaris
            </h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Form Filter Tanggal & Tombol Aksi --}}
            <div class="bg-white p-4 rounded-lg shadow mb-6">
                <div class="flex flex-wrap items-end gap-4">
                    <form action="{{ route('laporan.show', ['jenis' => 'produk_terlaris']) }}" method="GET" class="flex flex-wrap items-end gap-4 flex-grow">
                        <div>
                            <label for="tanggal_mulai" class="block text-sm font-medium text-gray-700">Tanggal Mulai</label>
                            <input type="date" name="tanggal_mulai" id="tanggal_mulai" value="{{ $tglMulai }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm">
                        </div>
                        <div>
                            <label for="tanggal_selesai" class="block text-sm font-medium text-gray-700">Tanggal Selesai</label>
                            <input type="date" name="tanggal_selesai" id="tanggal_selesai" value="{{ $tglSelesai }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm">
                        </div>
                        <button type="submit" class="px-4 py-2 bg-blue text-white text-sm font-semibold rounded-lg shadow hover:bg-blue">Terapkan</button>
                    </form>

                    <form action="{{ route('laporan.generate') }}" method="POST" target="_blank">
                        @csrf
                        <input type="hidden" name="jenis_laporan" value="produk_terlaris">
                        <input type="hidden" name="tanggal_mulai" value="{{ $tglMulai }}">
                        <input type="hidden" name="tanggal_selesai" value="{{ $tglSelesai }}">
                        <button type="submit" class="px-4 py-2 bg-green-600 text-white text-sm font-semibold rounded-lg shadow hover:bg-green-700 flex items-center gap-2">
                            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
                            Download PDF
                        </button>
                    </form>
                </div>
            </div>

            {{-- Dua Kolom Peringkat --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                
                {{-- Kolom 1: Berdasarkan Jumlah Terjual --}}
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="font-bold text-lg text-gray-800 mb-4">Top 10: Jumlah Terjual</h3>
                    <ol class="list-decimal list-inside space-y-3">
                        @forelse ($data['produkByQuantity'] as $produk)
                            <li class="flex justify-between items-center text-sm">
                                <span class="text-gray-700">{{ $produk->item_name }}</span>
                                <span class="font-bold text-blue-600 bg-blue-100 px-2 py-1 rounded-full">{{ $produk->total_quantity }} Terjual</span>
                            </li>
                        @empty
                            <p class="text-gray-500 text-sm">Tidak ada data penjualan.</p>
                        @endforelse
                    </ol>
                </div>

                {{-- Kolom 2: Berdasarkan Pendapatan --}}
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="font-bold text-lg text-gray-800 mb-4">Top 10: Pendapatan Tertinggi</h3>
                    <ol class="list-decimal list-inside space-y-3">
                        @forelse ($data['produkByRevenue'] as $produk)
                            <li class="flex justify-between items-center text-sm">
                                <span class="text-gray-700">{{ $produk->item_name }}</span>
                                <span class="font-bold text-green-600 bg-green-100 px-2 py-1 rounded-full">Rp {{ number_format($produk->total_revenue, 0, ',', '.') }}</span>
                            </li>
                        @empty
                            <p class="text-gray-500 text-sm">Tidak ada data penjualan.</p>
                        @endforelse
                    </ol>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>