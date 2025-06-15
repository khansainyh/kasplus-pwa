<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'KasPlus') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://api.fontshare.com/v2/css?f[]=satoshi@400,500,700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-300">

    {{-- Struktur Bingkai HP --}}
    <div class="relative w-full max-w-sm mx-auto h-screen bg-gray-100 sm:h-[90vh] sm:max-h-[844px] sm:my-8 sm:rounded-2xl sm:shadow-2xl flex flex-col">

        {{-- Page Heading (Header dari setiap halaman) --}}
        @if (isset($header))
            <header class="bg-white shadow-sm z-10">
                <div class="w-full mx-auto px-4 flex justify-between items-center
                    @if(request()->routeIs('dashboard'))
                        py-2 {{-- Pilihan 1: Lebih dekat (0.5rem = 8px) --}}
                        {{-- Atau py-1 jika ingin lebih dekat lagi (0.25rem = 4px) --}}
                        {{-- Atau py-0 jika ingin menempel (0px) --}}
                    @else
                        py-4 {{-- Padding default untuk halaman lain --}}
                    @endif
                ">
                    {{ $header }}
                    {{-- Tambahkan container flex untuk Ikon Chatbot dan Dropdown User --}}
                    <div class="flex items-center space-x-4"> {{-- Menggunakan space-x-4 untuk jarak --}}
                        {{-- Ikon Robot Chatbot --}}
                        <a href="{{ route('chatbot.show') }}" class="text-gray-600 hover:text-gray-800">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
                        </a>

                        {{-- Dropdown User (Nama User dan Logout) --}}
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = ! open" class="flex items-center text-sm font-medium text-gray-600 hover:text-gray-800">
                                <div class="text-base">Profil</div>
                                <div class="ml-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                </div>
                            </button>
                            <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 py-1" style="display: none;">
                                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Edit Profil</a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Logout</button>
                                </form>
                            </div>
                        </div>
                    </div> {{-- Akhir dari flex container untuk ikon & dropdown --}}
                </div>
            </header>
        @endif

        {{-- Konten Utama Halaman --}}
        <main class="flex-grow overflow-y-auto pt-4 px-4">
            {{ $slot }}
        </main>

        {{-- Bottom Navbar dengan 5 menu --}}
        <div class="h-20 bg-white">
            <div class="flex items-center justify-around h-full px-2">
                {{-- Kelompok Kiri --}}
                <div class="flex justify-around flex-grow">
                    {{-- Dashboard --}}
                    <a href="{{ route('dashboard') }}" class="flex flex-col items-center justify-center text-center w-auto {{ request()->routeIs('dashboard') ? 'text-blue-600' : 'text-gray-500' }}">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                        <span class="text-xs mt-1">Dashboard</span>
                    </a>
                    {{-- Kasir --}}
                    <a href="{{ route('kasir.index') }}" class="flex flex-col items-center justify-center text-center w-auto {{ request()->routeIs('kasir.index') ? 'text-blue-600' : 'text-gray-500' }}">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                        <span class="text-xs mt-1">Kasir</span>
                    </a>
                </div>
                
                {{-- Tombol Tengah untuk Keuangan --}}
                <div class="w-1/5 flex justify-center">
                    <a href="{{ route('keuangan.index') }}" class="-mt-10 flex h-16 w-16 items-center justify-center rounded-full bg-navy_blue text-white shadow-lg hover:bg-blue-700">
                        <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 8h6m-5 0H4a2 2 0 00-2 2v4a2 2 0 002 2h12a2 2 0 002-2V8a2 2 0 00-2-2H9z"></path></svg>
                    </a>
                </div>

                {{-- Kelompok Kanan --}}
                <div class="flex justify-around w-2/5">
                    {{-- Produk --}}
                    <a href="{{ route('produk.index') }}" class="flex flex-col items-center justify-center text-center w-auto {{ request()->routeIs('produk.index*') ? 'text-blue-600' : 'text-gray-500' }}">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                        <span class="text-xs mt-1">Produk</span>
                    </a>
                    {{-- Laporan --}}
                    <a href="{{ route('laporan.index') }}" class="flex flex-col items-center justify-center text-center w-auto text-gray-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V7a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        <span class="text-xs mt-1">Laporan</span>
                    </a>
                </div>
            </div>
        </div>

    </div>
</body>
</html>