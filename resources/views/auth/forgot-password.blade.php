<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @PwaHead
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Lupa Kata Sandi KasPlus') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-white min-h-screen flex items-center justify-center">

    <div class="relative w-full max-w-sm mx-auto h-screen sm:h-[90vh] sm:max-h-[844px] sm:my-8 sm:rounded-2xl sm:shadow-2xl flex flex-col bg-white overflow-hidden">
        
        <div class="absolute top-0 left-0 w-full h-12 bg-white z-20"></div> 

        <main class="flex-grow flex flex-col justify-center items-center p-8 bg-white relative z-10">
            
            <h3 class="text-2xl font-bold text-[#131951] text-center mb-4 tracking-wide">
                Lupa Kata Sandi Anda?
            </h3>

            {{-- UBAH INI: Atur jarak antar baris lebih dekat --}}
            <div class="mb-6 text-[16px] text-[#7B7B7B] text-center tracking-wide leading-tight"> {{-- Diubah dari leading-relaxed menjadi leading-tight --}}
                Tidak masalah. Kami akan mengirimkan tautan reset kata sandi ke email Anda.
            </div>

            <x-auth-session-status class="mb-4 font-medium text-sm text-green-600 w-full text-center tracking-normal" :status="session('status')" />

            <form method="POST" action="{{ route('password.email') }}" class="w-full max-w-xs space-y-5">
                @csrf

                <div>
                    <x-input-label for="email" class="block text-sm font-medium text-gray-700 mb-1 tracking-wide" :value="__('Email')" />
                    <div class="relative">
                        {{-- UBAH INI: Tambah rounded lebih besar pada input email --}}
                        <x-text-input id="email" class="block w-full border-[#D3D3D3] rounded-[17px] shadow-sm focus:border-[#2D5AF7] focus:ring-[#2D5AF7] text-gray-800 p-3 pl-10" type="email" name="email" :value="old('email')" required autofocus placeholder="Masukkan email Anda" /> {{-- Diubah dari rounded-[14px] --}}
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-sm leading-5">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8m-9 6h.01M17 12h.01M7 12h.01M12 12v.01M21 8v10a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h14a2 2 0 012 2z"></path></svg>
                        </span>
                    </div>
                    <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-500 text-xs tracking-normal" />
                </div>

                <div class="flex items-center justify-center pt-4">
                    {{-- Tombol "Kirim Tautan" --}}
                    {{-- PERHATIAN: Tinggi tombol diatur di file komponen primary-button.blade.php --}}
                    <x-primary-button class="w-full bg-[#2D5AF7] hover:bg-[#1f42b3] text-white font-semibold py-3 px-4 rounded-[14px] transition-colors duration-200 shadow-md tracking-wide text-xl normal-case text-center">
                        Kirim Tautan
                    </x-primary-button>
                </div>
                
                {{-- Link "Kembali ke Login" --}}
                <div class="flex items-center justify-center mt-6">
                    {{-- UBAH INI: Sesuaikan styling link agar seragam dengan "Daftar di sini!" --}}
                    <a class="text-sm text-[#2D5AF7] hover:text-[#1f42b3] font-medium tracking-wide flex items-center" href="{{ route('login') }}">
                        <svg class="h-4 w-4 mr-1 text-[#2D5AF7]" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                        Kembali ke Login
                    </a>
                </div>
            </form>
        </main>
    </div>
    @RegisterServiceWorkerScript
</body>
</html>