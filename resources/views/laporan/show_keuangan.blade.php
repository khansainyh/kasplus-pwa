<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('laporan.index') }}" class="text-gray-500 hover:text-gray-700">
                <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" /></svg>
            </a>
            <h2 class="font-bold text-xl text-gray-800">
                Laporan Keuangan
            </h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Form Filter Tanggal & Tombol Aksi --}}
            <div class="bg-white p-4 rounded-lg shadow mb-6">
                <div class="flex flex-wrap items-end gap-4">
                    <form action="{{ route('laporan.show', ['jenis' => 'keuangan']) }}" method="GET" class="flex flex-wrap items-end gap-4 flex-grow">
                        <div>
                            <label for="tanggal_mulai" class="block text-sm font-medium text-gray-700">Tanggal Mulai</label>
                            <input type="date" name="tanggal_mulai" id="tanggal_mulai" value="{{ $tglMulai }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm">
                        </div>
                        <div>
                            <label for="tanggal_selesai" class="block text-sm font-medium text-gray-700">Tanggal Selesai</label>
                            <input type="date" name="tanggal_selesai" id="tanggal_selesai" value="{{ $tglSelesai }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm">
                        </div>
                        <button type="submit" class="px-4 py-2 bg-blue text-white text-sm font-semibold rounded-lg shadow hover:bg-blue-700">
                            Terapkan
                        </button>
                    </form>

                    <form action="{{ route('laporan.generate') }}" method="POST" target="_blank">
                        @csrf
                        <input type="hidden" name="jenis_laporan" value="keuangan">
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
                <div class="bg-green-100 p-4 rounded-lg shadow"><p class="text-sm text-gray-600">Total Pemasukan</p><p class="font-bold text-green-800 text-2xl">Rp {{ number_format($data['totalPemasukan'], 0, ',', '.') }}</p></div>
                <div class="bg-red-100 p-4 rounded-lg shadow"><p class="text-sm text-gray-600">Total Pengeluaran</p><p class="font-bold text-red-800 text-2xl">Rp {{ number_format($data['totalPengeluaran'], 0, ',', '.') }}</p></div>
                <div class="bg-blue-100 p-4 rounded-lg shadow"><p class="text-sm text-gray-600">Laba / Rugi</p><p class="font-bold text-blue-800 text-2xl">Rp {{ number_format($data['labaRugi'], 0, ',', '.') }}</p></div>
            </div>

            {{-- Daftar Riwayat Keuangan dalam bentuk Kartu --}}
            <div class="space-y-3">
                @forelse ($data['laporanGabungan'] as $item)
                    <div class="bg-white rounded-lg shadow-sm">
                        {{-- [MODIFIKASI] Tambahkan 'gap-4' untuk memberi jarak --}}
                        <div class="p-4 flex justify-between items-center gap-4">
                            
                            {{-- Sisi Kiri: Info Transaksi --}}
                            {{-- [MODIFIKASI] Tambahkan 'min-w-0' agar flex item ini bisa menyusut --}}
                            <div class="flex items-center gap-4 min-w-0">
                                {{-- Ikon --}}
                                <div class="flex-shrink-0 p-2 rounded-full {{ $item->tipe == 'pengeluaran' ? 'bg-red-100' : 'bg-green-100' }}">
                                    @if ($item->tipe == 'pengeluaran')
                                        <svg class="w-6 h-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19.5v-15m0 0l-6.75 6.75M12 4.5l6.75 6.75" /></svg>
                                    @else
                                        <svg class="w-6 h-6 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m0 0l6.75-6.75M12 19.5l-6.75-6.75" /></svg>
                                    @endif
                                </div>
                                {{-- Keterangan & Tanggal --}}
                                {{-- [MODIFIKASI] Tambahkan 'min-w-0' agar teks di dalamnya bisa wrap --}}
                                <div class="min-w-0">
                                    {{-- [MODIFIKASI] Tambahkan 'truncate' agar teks yg sangat panjang diberi '...' --}}
                                    <p class="font-bold text-sm text-gray-800 truncate">{{ $item->keterangan }}</p>
                                    <p class="text-xs text-gray-500 mt-1">
                                        {{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y, H:i') }}
                                    </p>
                                </div>
                            </div>

                            {{-- Sisi Kanan: Nominal --}}
                            {{-- [MODIFIKASI] Tambahkan 'flex-shrink-0' agar bagian ini tidak pernah menyusut --}}
                            <div class="text-right flex-shrink-0">
                                <p class="font-bold text-lg whitespace-nowrap {{ $item->tipe == 'pengeluaran' ? 'text-red-600' : 'text-green-600' }}">
                                    {{ $item->tipe == 'pengeluaran' ? '-' : '+' }} Rp {{ number_format($item->jumlah, 0, ',', '.') }}
                                </p>
                                <span class="px-2 py-0.5 text-xs rounded-full font-semibold {{ $item->tipe == 'pengeluaran' ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                    {{ str_replace('_', ' ', $item->tipe) }}
                                </span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-16 bg-white rounded-lg shadow-sm">
                        <svg class="w-12 h-12 mx-auto text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m-1.125 0H5.625A2.25 2.25 0 0 0 3.375 4.5v15A2.25 2.25 0 0 0 5.625 21h12.75A2.25 2.25 0 0 0 20.625 18.75V10.5M12 18.75h-3.75a.375.375 0 1 1 0-.75H12a.375.375 0 0 1 0 .75Z" /></svg>
                        <p class="text-gray-500 mt-4 font-semibold">Tidak ada data pada rentang tanggal ini.</p>
                        <p class="text-sm text-gray-400">Coba ubah filter tanggal di atas.</p>
                    </div>
                @endforelse
            </div>

        </div>
    </div>
</x-app-layout>