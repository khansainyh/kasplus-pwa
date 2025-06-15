<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800">
            Keuangan
        </h2>
    </x-slot>

    <div class="w-full pb-20"> {{-- Padding bawah agar tidak tertutup tombol + --}}
        {{-- Pesan Sukses atau Error --}}
        @if(session('success'))
            <div class="bg-green-100 text-green-700 p-3 rounded-lg mb-4 text-sm">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="bg-red-100 text-red-700 p-3 rounded-lg mb-4 text-sm">{{ session('error') }}</div>
        @endif

        {{-- Kartu Ringkasan Keuangan (Data dari Controller) --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="bg-blue-100 rounded-lg shadow-md p-4">
                <p class="text-sm text-gray-600">Saldo Akhir</p>
                <p class="font-bold text-blue-800 text-xl">Rp {{ number_format($saldoAkhir, 0, ',', '.') }}</p>
            </div>
            <div class="bg-green-100 rounded-lg shadow-md p-4">
                <p class="text-sm text-gray-600">Total Pemasukan</p>
                <p class="font-bold text-green-800 text-xl">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</p>
            </div>
            <div class="bg-red-100 rounded-lg shadow-md p-4">
                <p class="text-sm text-gray-600">Total Pengeluaran</p>
                <p class="font-bold text-red-800 text-xl">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</p>
            </div>
            <div class="bg-yellow-100 rounded-lg shadow-md p-4">
                <p class="text-sm text-gray-600">Pemasukan Kasir</p>
                <p class="font-bold text-yellow-800 text-xl">Rp {{ number_format($pemasukanKasir, 0, ',', '.') }}</p>
            </div>
        </div>

        {{-- [BARU] Tombol Filter --}}
        <div class="mb-6 flex space-x-2 bg-gray-200 p-1 rounded-lg">
            <a href="{{ route('keuangan.index', ['filter' => 'semua']) }}"
               class="w-full text-center px-4 py-2 rounded-md text-sm font-semibold transition-colors duration-200
                      {{ $filter == 'semua' ? 'bg-white text-blue-700 shadow' : 'text-gray-600 hover:bg-gray-300' }}">
                Semua
            </a>
            <a href="{{ route('keuangan.index', ['filter' => 'pemasukan']) }}"
               class="w-full text-center px-4 py-2 rounded-md text-sm font-semibold transition-colors duration-200
                      {{ $filter == 'pemasukan' ? 'bg-white text-green-700 shadow' : 'text-gray-600 hover:bg-gray-300' }}">
                Pemasukan
            </a>
            <a href="{{ route('keuangan.index', ['filter' => 'pengeluaran']) }}"
               class="w-full text-center px-4 py-2 rounded-md text-sm font-semibold transition-colors duration-200
                      {{ $filter == 'pengeluaran' ? 'bg-white text-red-700 shadow' : 'text-gray-600 hover:bg-gray-300' }}">
                Pengeluaran
            </a>
        </div>

        {{-- Daftar Laporan Keuangan Gabungan --}}
        <h3 class="text-lg font-semibold mb-3 text-gray-700">Riwayat Keuangan</h3>
        
        @forelse ($laporanKeuangan as $laporan)
            <div class="bg-white rounded-lg shadow-md mb-4">
                <div class="p-4 border-b flex justify-between items-center">
                    <div>
                        <p class="font-bold text-md text-gray-800">{{ $laporan->keterangan }}</p>
                        <p class="text-xs text-gray-500">
                            Oleh: {{ $laporan->user_name }} | {{ \Carbon\Carbon::parse($laporan->tanggal)->format('d M Y, H:i') }}
                        </p>
                    </div>

                    @if ($laporan->tipe == 'pengeluaran')
                        <span class="font-bold text-lg text-red-600 whitespace-nowrap">
                            - Rp {{ number_format($laporan->jumlah, 0, ',', '.') }}
                        </span>
                    @else
                        <span class="font-bold text-lg text-green-600 whitespace-nowrap">
                            + Rp {{ number_format($laporan->jumlah, 0, ',', '.') }}
                        </span>
                    @endif
                </div>

                @if ($laporan->tipe == 'pemasukan_kasir' && $laporan->original_data && $laporan->original_data->items->count() > 0)
                <div class="p-4 text-sm border-t">
                    <p class="font-semibold mb-1">Detail Item:</p>
                    <ul class="list-disc list-inside text-xs pl-2">
                        @foreach ($laporan->original_data->items as $item)
                            <li>{{ $item->quantity }}x {{ $item->item_name }} (@Rp {{ number_format($item->item_price, 0, ',', '.') }})</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                @if ($laporan->tipe == 'pemasukan_kasir')
                <div class="bg-gray-50 p-2 text-right rounded-b-lg">
                        <a href="{{ url('kasir/receipt', $laporan->original_data->invoice_number) }}" target="_blank" class="text-blue-600 hover:underline text-xs font-semibold">
                            LIHAT STRUK
                        </a>
                </div>
                @endif
            </div>
        @empty
            <div class="text-center py-10 bg-white rounded-lg shadow-md">
                <p class="text-gray-500">Belum ada riwayat keuangan untuk filter ini.</p>
            </div>
        @endforelse

        {{-- [MODIFIKASI] Link Pagination --}}
        <div class="mt-6">
            {{ $laporanKeuangan->withQueryString()->links() }}
        </div>
    </div>

    {{-- Tombol Aksi Tambah dengan Dropdown --}}
    <div x-data="{ open: false }" class="absolute bottom-6 right-6">
        {{-- Menu Dropdown --}}
        <div x-show="open" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="transform opacity-0 scale-95"
             x-transition:enter-end="transform opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-75"
             x-transition:leave-start="transform opacity-100 scale-100"
             x-transition:leave-end="transform opacity-0 scale-95"
             @click.away="open = false" 
             style="display: none;"
             class="absolute bottom-20 right-0 w-56 bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5 z-10">
            <div class="py-1">
                <a href="{{ route('keuangan.pemasukan.create') }}" class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                    <svg class="w-5 h-5 text-green-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
                    </svg>
                    <span>Tambah Pemasukan</span>
                </a>
                <a href="{{ route('keuangan.pengeluaran.create') }}" class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                    <svg class="w-5 h-5 text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M4 10a.75.75 0 01.75-.75h10.5a.75.75 0 010 1.5H4.75A.75.75 0 014 10z" />
                    </svg>
                    <span>Tambah Pengeluaran</span>
                </a>
            </div>
        </div>

        {{-- Tombol FAB (Floating Action Button) --}}
        <button @click="open = !open" class="bg-blue-600 hover:bg-blue-700 text-white font-bold h-16 w-16 flex items-center justify-center rounded-full shadow-lg focus:outline-none">
            <svg x-show="!open" class="h-8 w-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v12m6-6H6" />
            </svg>
            <svg x-show="open" class="h-8 w-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display: none;">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>
</x-app-layout>