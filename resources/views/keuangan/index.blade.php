<x-app-layout>
    <x-slot name="header">
        {{-- Header untuk Halaman Keuangan agar lebih menonjol dan rapi, konsisten dengan halaman Kasir --}}
        <h2 class="font-bold text-3xl text-[#131951] text-center tracking-tight py-2">
            Keuangan
        </h2>
    </x-slot>

    {{-- UBAH INI: Menambah padding bawah (pb-40) pada container utama untuk memberikan ruang lebih dari navbar bawah. --}}
    <div class="w-full max-w-4xl mx-auto py-8 px-4 font-satoshi pb-40">
        {{-- Pesan Sukses atau Error (konsisten dengan halaman Kasir) --}}
        @if(session('success'))
            <div class="bg-green-100 text-green-700 p-4 rounded-xl shadow-md mb-6 text-sm font-medium tracking-wide">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="bg-red-100 text-red-700 p-4 rounded-xl shadow-md mb-6 text-sm font-medium tracking-wide">
                {{ session('error') }}
            </div>
        @endif

        {{-- Kartu Ringkasan Keuangan (Data dari Controller) --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            {{-- Saldo Akhir - Background Navy Blue, Teks Putih, Tanpa Border --}}
            <div class="bg-[#131951] rounded-xl shadow-lg p-5 transform transition-transform duration-200 hover:scale-[1.01] hover:shadow-xl">
                <p class="text-sm text-white mb-1 tracking-wide">Saldo Akhir</p>
                <p class="font-bold text-white text-2xl tracking-tight">Rp {{ number_format($saldoAkhir, 0, ',', '.') }}</p>
            </div>
            {{-- Total Pemasukan - Background Blue, Teks Putih, Tanpa Border --}}
            <div class="bg-[#2D5AF7] rounded-xl shadow-lg p-5 transform transition-transform duration-200 hover:scale-[1.01] hover:shadow-xl">
                <p class="text-sm text-white mb-1 tracking-wide">Total Pemasukan</p>
                <p class="font-bold text-white text-2xl tracking-tight">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</p>
            </div>
            {{-- Total Pengeluaran - Background Orange, Teks Putih, Tanpa Border --}}
            <div class="bg-[#F65C02] rounded-xl shadow-lg p-5 transform transition-transform duration-200 hover:scale-[1.01] hover:shadow-xl">
                <p class="text-sm text-white mb-1 tracking-wide">Total Pengeluaran</p>
                <p class="font-bold text-white text-2xl tracking-tight">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</p>
            </div>
            {{-- Pemasukan Kasir - Background Putih, Teks Dark Gray, Border Light Gray, Shadow --}}
            <div class="bg-white rounded-xl shadow-lg p-5 border border-[#D3D3D3] transform transition-transform duration-200 hover:scale-[1.01] hover:shadow-xl">
                <p class="text-sm text-[#0D0D0D] mb-1 tracking-wide">Pemasukan Kasir</p>
                <p class="font-bold text-[#0D0D0D] text-2xl tracking-tight">Rp {{ number_format($pemasukanKasir, 0, ',', '.') }}</p>
            </div>
        </div>

        {{-- Tombol Filter --}}
        <div class="mb-8 p-1 bg-gray-100 rounded-full flex shadow-sm">
            <a href="{{ route('keuangan.index', ['filter' => 'semua']) }}"
               class="flex-1 text-center px-4 py-2.5 rounded-2xl text-base transition-all duration-200 tracking-wide
                     {{ $filter == 'semua' ? 'bg-[#2D5AF7] text-white shadow-md font-medium bg-opacity-90' : 'text-[#7B7B7B] hover:bg-gray-200 font-normal' }}">
                Semua
            </a>
            <a href="{{ route('keuangan.index', ['filter' => 'pemasukan']) }}"
               class="flex-1 text-center px-4 py-2.5 rounded-2xl text-base transition-all duration-200 tracking-wide
                     {{ $filter == 'pemasukan' ? 'bg-[#16a34a] text-white shadow-md font-medium bg-opacity-90' : 'text-[#7B7B7B] hover:bg-gray-200 font-normal' }}">
                Pemasukan
            </a>
            <a href="{{ route('keuangan.index', ['filter' => 'pengeluaran']) }}"
               class="flex-1 text-center px-4 py-2.5 rounded-2xl text-base transition-all duration-200 tracking-wide
                     {{ $filter == 'pengeluaran' ? 'bg-red-600 text-white shadow-md font-medium bg-opacity-90' : 'text-[#7B7B7B] hover:bg-gray-200 font-normal' }}">
                Pengeluaran
            </a>
        </div>

        {{-- Daftar Laporan Keuangan Gabungan --}}
        <h3 class="text-xl font-bold mb-4 text-[#131951] tracking-tight">Riwayat Keuangan</h3>
        
        <div class="space-y-4">
            @forelse ($laporanKeuangan as $laporan)
                <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden transform transition-transform duration-200 hover:scale-[1.005] hover:shadow-xl">
                    <div class="p-5 flex justify-between items-start">
                        <div class="flex-1 min-w-0 pr-4">
                            <p class="font-semibold text-lg text-[#131951] tracking-tight truncate">
                                @if ($laporan->tipe == 'pemasukan_kasir' && Str::startsWith($laporan->keterangan, 'Penjualan Kasir - Invoice'))
                                    Penjualan Kasir - Invoice
                                @else
                                    {{ $laporan->keterangan }}
                                @endif
                            </p>
                            
                            @if ($laporan->tipe == 'pemasukan_kasir')
                                <p class="text-sm text-[#0D0D0D] mt-1 tracking-wide break-all">
                                    #INV-{{ $laporan->original_data->invoice_number }}
                                </p>
                            @endif
                            
                            <p class="text-xs text-gray-500 mt-1 tracking-wide">
                                Oleh: <span class="font-medium">{{ $laporan->user_name }}</span> | {{ \Carbon\Carbon::parse($laporan->tanggal)->format('d M Y, H:i') }}
                            </p>
                        </div>

                        <div class="flex-shrink-0 text-right ml-4">
                            @if ($laporan->tipe == 'pengeluaran')
                                <span class="font-extrabold text-xl text-red-600 whitespace-nowrap">
                                    - Rp {{ number_format($laporan->jumlah, 0, ',', '.') }}
                                </span>
                            @else
                                <span class="font-extrabold text-xl text-green-600 whitespace-nowrap">
                                    + Rp {{ number_format($laporan->jumlah, 0, ',', '.') }}
                                </span>
                            @endif
                        </div>
                    </div>

                    @if ($laporan->tipe == 'pemasukan_kasir' && $laporan->original_data && $laporan->original_data->items->count() > 0)
                    <div class="bg-gray-50 p-5 pt-3 border-t border-gray-100">
                        <p class="font-semibold text-sm text-gray-700 mb-2 tracking-wide">Detail Item:</p>
                        <ul class="text-sm text-gray-600 list-disc list-inside pl-2 space-y-1">
                            @foreach ($laporan->original_data->items as $item)
                                <li>{{ $item->quantity }}x <span class="font-medium">{{ $item->item_name }}</span> (@Rp {{ number_format($item->item_price, 0, ',', '.') }})</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    @if ($laporan->tipe == 'pemasukan_kasir')
                    <div class="bg-gray-50 p-4 text-right border-t border-gray-100 rounded-b-2xl">
                            <a href="{{ url('kasir/receipt', $laporan->original_data->invoice_number) }}" target="_blank" class="text-[#2D5AF7] hover:text-[#1f42b3] text-sm font-semibold transition-colors duration-200 tracking-wide">
                                LIHAT STRUK <span class="ml-1">&#8599;</span>
                            </a>
                    </div>
                    @endif
                </div>
            @empty
                <div class="text-center py-10 bg-white rounded-2xl shadow-lg border border-gray-100">
                    <p class="text-gray-500 text-lg font-medium tracking-wide">Belum ada riwayat keuangan untuk filter ini.</p>
                </div>
            @endforelse
        </div>

        {{-- Link Pagination --}}
        <div class="mt-8 flex justify-center">
            {{ $laporanKeuangan->withQueryString()->links() }}
        </div>
    </div>

    {{-- UBAH INI: Tombol Aksi Tambah dengan Dropdown - Posisi FAB di atas navbar --}}
    <div x-data="{ open: false }" class="fixed bottom-28 right-6 z-20"> {{-- UBAH INI: bottom-6 menjadi bottom-28 --}}
        {{-- Menu Dropdown --}}
        <div x-show="open"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="transform opacity-0 translate-y-4"
             x-transition:enter-end="transform opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="transform opacity-100 translate-y-0"
             x-transition:leave-end="transform opacity-0 translate-y-4"
             @click.away="open = false"
             style="display: none;"
             class="absolute bottom-20 right-0 w-64 bg-white rounded-xl shadow-2xl ring-1 ring-black ring-opacity-5 z-10 p-2">
            <div class="py-1">
                <a href="{{ route('keuangan.pemasukan.create') }}" class="flex items-center gap-3 px-4 py-3 text-base text-gray-700 hover:bg-gray-50 rounded-lg transition-colors duration-200">
                    <svg class="w-6 h-6 text-green-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm.75-11.25a.75.75 0 00-1.5 0v2.5h-2.5a.75.75 0 000 1.5h2.5v2.5a.75.75 0 001.5 0v-2.5h2.5a.75.75 0 000-1.5h-2.5v-2.5z" clip-rule="evenodd" />
                    </svg>
                    <span>Tambah Pemasukan</span>
                </a>
                <a href="{{ route('keuangan.pengeluaran.create') }}" class="flex items-center gap-3 px-4 py-3 text-base text-gray-700 hover:bg-gray-50 rounded-lg transition-colors duration-200">
                    <svg class="w-6 h-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v12m6-6H6" />
                    </svg>
                    <span>Tambah Pengeluaran</span>
                </a>
            </div>
        </div>

        {{-- Tombol FAB (Floating Action Button) --}}
        <button @click="open = !open" class="bg-[#2D5AF7] hover:bg-[#1f42b3] text-white font-bold h-16 w-16 flex items-center justify-center rounded-full shadow-xl hover:shadow-2xl focus:outline-none transition-all duration-200 transform hover:scale-105">
            <svg x-show="!open" class="h-8 w-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v12m6-6H6" />
            </svg>
            <svg x-show="open" class="h-8 w-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display: none;">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    {{-- Custom Font and General Styling --}}
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

        /* Styling untuk pagination bawaan Laravel jika tidak menggunakan custom view */
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 2rem;
            gap: 0.5rem;
        }

        .pagination .page-item {
            display: inline-block;
        }

        .pagination .page-item .page-link {
            padding: 0.75rem 1rem;
            border-radius: 0.75rem; /* rounded-xl */
            font-weight: 500; /* font-medium */
            color: #4a5568; /* gray-700 */
            background-color: #f7fafc; /* gray-50 */
            transition: all 0.2s ease-in-out;
            border: 1px solid #e2e8f0; /* gray-200 */
        }

        .pagination .page-item .page-link:hover {
            background-color: #edf2f7; /* gray-100 */
            color: #2d3748; /* gray-800 */
        }

        .pagination .page-item.active .page-link {
            background-color: #2D5AF7;
            color: white;
            border-color: #2D5AF7;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); /* shadow-md */
        }

        .pagination .page-item.disabled .page-link {
            opacity: 0.5;
            cursor: not-allowed;
        }
    </style>
</x-app-layout>
