<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800">
            {{-- Judul akan dinamis tergantung jenis form --}}
            Tambah {{ ucfirst($tipe) }}
        </h2>
    </x-slot>

    <div class="w-full pb-10">
        <div class="max-w-xl mx-auto bg-white p-6 rounded-lg shadow-md">

            {{-- Arahkan form ke route yang sesuai --}}
            <form method="POST" action="{{ $tipe === 'pemasukan' ? route('keuangan.pemasukan.store') : route('keuangan.pengeluaran.store') }}">
                @csrf

                <div class="mb-4">
                    <label for="tanggal" class="block text-sm font-medium text-gray-700">Tanggal</label>
                    <input type="date" name="tanggal" id="tanggal" value="{{ old('tanggal', now()->toDateString()) }}"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required>
                    @error('tanggal')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="jumlah" class="block text-sm font-medium text-gray-700">Jumlah (Rp)</label>
                    <input type="number" name="jumlah" id="jumlah" placeholder="Contoh: 50000"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required>
                    @error('jumlah')
                         <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="keterangan" class="block text-sm font-medium text-gray-700">Keterangan</label>
                    <textarea name="keterangan" id="keterangan" rows="3"
                              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required>{{ old('keterangan') }}</textarea>
                    @error('keterangan')
                         <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end mt-6">
                    <a href="{{ route('keuangan.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md mr-2 hover:bg-gray-300">Batal</a>
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                        Simpan {{ ucfirst($tipe) }}
                    </button>
                </div>
            </form>

        </div>
    </div>
</x-app-layout>