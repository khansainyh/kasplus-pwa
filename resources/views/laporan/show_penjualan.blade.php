<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('laporan.index') }}" class="text-gray-500 hover:text-gray-700">
                <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" /></svg>
            </a>
            <h2 class="font-bold text-xl text-gray-800">
                Laporan Penjualan
            </h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Form Filter Tanggal & Tombol Aksi --}}
            <div class="bg-white p-4 rounded-lg shadow mb-6">
                <div class="flex flex-wrap items-end gap-4">
                    <form action="{{ route('laporan.show', ['jenis' => 'penjualan']) }}" method="GET" class="flex flex-wrap items-end gap-4 flex-grow">
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
                        <input type="hidden" name="jenis_laporan" value="penjualan">
                        <input type="hidden" name="tanggal_mulai" value="{{ $tglMulai }}">
                        <input type="hidden" name="tanggal_selesai" value="{{ $tglSelesai }}">
                        <button type="submit" class="px-4 py-2 bg-green-600 text-white text-sm font-semibold rounded-lg shadow hover:bg-green-700 flex items-center gap-2">
                            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
                            Download PDF
                        </button>
                    </form>
                </div>
            </div>

            {{-- Kartu Ringkasan --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-blue-100 p-4 rounded-lg shadow"><p class="text-sm text-gray-600">Total Penjualan</p><p class="font-bold text-blue-800 text-2xl">Rp {{ number_format($data['totalPenjualan'], 0, ',', '.') }}</p></div>
                <div class="bg-indigo-100 p-4 rounded-lg shadow"><p class="text-sm text-gray-600">Jumlah Transaksi</p><p class="font-bold text-indigo-800 text-2xl">{{ $data['jumlahTransaksi'] }}</p></div>
                <div class="bg-purple-100 p-4 rounded-lg shadow"><p class="text-sm text-gray-600">Rata-rata Transaksi</p><p class="font-bold text-purple-800 text-2xl">Rp {{ number_format($data['rataRataTransaksi'], 0, ',', '.') }}</p></div>
            </div>

            {{-- Daftar Transaksi Penjualan --}}
            <div class="space-y-3">
                @forelse ($data['transaksi'] as $trx)
                    <div x-data="{ open: false }" class="bg-white rounded-lg shadow-sm">
                        {{-- Header Kartu --}}
                        <div class="p-4 flex justify-between items-center">
                            <div class="flex items-center gap-4 min-w-0">
                                <div class="flex-shrink-0 p-2 rounded-full bg-blue-100">
                                    <svg class="w-6 h-6 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 3.75V16.5L12 14.25 7.5 16.5V3.75m9 0H18A2.25 2.25 0 0 1 20.25 6v12A2.25 2.25 0 0 1 18 20.25H6A2.25 2.25 0 0 1 3.75 18V6A2.25 2.25 0 0 1 6 3.75h1.5m9 0h-9" /></svg>
                                </div>
                                <div class="min-w-0">
                                    <p class="font-bold text-sm text-gray-800 truncate">Invoice #{{ $trx->invoice_number }}</p>
                                    <p class="text-xs text-gray-500 mt-1">
                                        {{ \Carbon\Carbon::parse($trx->transaction_date)->format('d M Y, H:i') }} | Kasir: {{ $trx->user->name ?? 'N/A' }}
                                    </p>
                                </div>
                            </div>
                            <div class="text-right flex-shrink-0">
                                <p class="font-bold text-lg whitespace-nowrap text-blue-700">Rp {{ number_format($trx->total_amount, 0, ',', '.') }}</p>
                            </div>
                        </div>
                        {{-- Tombol & Detail Item (Accordion) --}}
                        <div class="border-t border-gray-200">
                            <button @click="open = !open" class="w-full px-4 py-2 text-left text-xs text-gray-500 font-semibold hover:bg-gray-50 flex justify-between items-center">
                                <span>LIHAT DETAIL ITEM ({{ $trx->items->count() }})</span>
                                <svg x-show="!open" class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" /></svg>
                                <svg x-show="open" class="w-4 h-4" style="display: none;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 15.75 7.5-7.5 7.5 7.5" /></svg>
                            </button>
                            <div x-show="open" style="display: none;" class="p-4 text-sm border-t bg-gray-50">
                                <ul class="list-disc list-inside space-y-1">
                                    @foreach ($trx->items as $item)
                                        <li>{{ $item->quantity }}x {{ $item->item_name }} (@Rp {{ number_format($item->item_price, 0, ',', '.') }})</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-16 bg-white rounded-lg shadow-sm">
                        <p class="text-gray-500 font-semibold">Tidak ada transaksi penjualan pada rentang tanggal ini.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>