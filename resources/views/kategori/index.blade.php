<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800">
            Kelola Kategori
        </h2>
    </x-slot>

    <div class="w-full">
        <div class="bg-white p-4 rounded-lg shadow-md mb-4">
            <h3 class="text-lg font-semibold mb-2">Tambah Kategori Baru</h3>
            <form action="{{ route('kategori.store') }}" method="POST">
                @csrf
                <div class="flex space-x-2">
                    <input type="text" name="nama_kategori" class="flex-grow block w-full border-gray-300 rounded-md shadow-sm" placeholder="Contoh: Makanan" required>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">Simpan</button>
                </div>
            </form>
        </div>

        <div class="bg-white p-4 rounded-lg shadow-md">
            <h3 class="text-lg font-semibold mb-2">Daftar Kategori</h3>
            <ul class="space-y-2">
                @forelse ($kategoris as $kategori)
                    <li class="flex justify-between items-center border-b pb-2">
                        <span>{{ $kategori->nama_kategori }}</span>
                        <form action="{{ route('kategori.destroy', $kategori->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-xs text-red-500 hover:text-red-700">Hapus</button>
                        </form>
                    </li>
                @empty
                    <li class="text-center text-gray-500">Belum ada kategori.</li>
                @endforelse
            </ul>
        </div>
    </div>
</x-app-layout>