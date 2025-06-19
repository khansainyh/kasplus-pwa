<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @PwaHead
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Konfirmasi Kata Sandi KasPlus') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-white min-h-screen flex items-center justify-center">

    <div class="relative w-full max-w-sm mx-auto h-screen sm:h-[90vh] sm:max-h-[844px] sm:my-8 sm:rounded-2xl sm:shadow-2xl flex flex-col bg-white overflow-hidden">
        
        <div class="absolute top-0 left-0 w-full h-12 bg-white z-20"></div> 

        <main class="flex-grow flex flex-col justify-center items-center p-8 bg-white relative z-10">
            
            {{-- UBAH INI: HILANGKAN Kontainer untuk Logo dan Text Judul KasPlus --}}
            {{-- <div class="mb-1 mt-4 flex items-center justify-center">
                <img src="{{ asset('images/kasplus_logo.png') }}" alt="KasPlus Logo" class="h-12 w-auto mr-2"> 
                <h1 class="text-[45px] font-black text-[#131951] tracking-wide leading-[48px]">KasPlus</h1> 
            </div> --}}

            {{-- UBAH INI: Judul Halaman Konfirmasi Kata Sandi --}}
            <h3 class="text-2xl font-semibold text-[#131951] text-center mb-4 tracking-wide">
                Konfirmasi Kata Sandi
            </h3>

            {{-- UBAH INI: Deskripsi instruksi --}}
            <div class="mb-6 text-[16px] text-[#7B7B7B] text-center tracking-wide leading-tight">
                Mohon konfirmasi kata sandi Anda sebelum melanjutkan.
            </div>

            <x-auth-session-status class="mb-4 font-medium text-sm text-green-600 w-full text-center tracking-normal" :status="session('status')" />

            <form method="POST" action="{{ route('password.confirm') }}" class="w-full max-w-xs space-y-5">
                @csrf

                <div>
                    <x-input-label for="password" class="block text-sm font-medium text-gray-700 mb-1 tracking-wide" :value="__('Kata Sandi')" />

                    <div class="relative">
                        {{-- UBAH INI: Ubah rounded kolom kata sandi menjadi lebih rounded --}}
                        <x-text-input id="password" class="block w-full border-[#D3D3D3] rounded-[18px] shadow-sm focus:border-[#2D5AF7] focus:ring-[#2D5AF7] text-gray-800 p-3 pl-10"
                                      type="password"
                                      name="password"
                                      required autocomplete="current-password" 
                                      placeholder="Masukkan kata sandi Anda" /> {{-- Diubah dari rounded-[14px] --}}
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-sm leading-5">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                        </span>
                    </div>

                    <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-500 text-xs tracking-normal" />
                </div>

                <div class="flex items-center justify-center pt-4">
                    <x-primary-button class="w-full bg-[#2D5AF7] hover:bg-[#1f42b3] text-white font-semibold py-3 px-4 rounded-[14px] transition-colors duration-200 shadow-md tracking-wide text-xl normal-case text-center">
                        {{ __('Konfirmasi') }}
                    </x-primary-button>
                </div>
            </form>
        </main>
    </div>
    @RegisterServiceWorkerScript
</body>
</html>