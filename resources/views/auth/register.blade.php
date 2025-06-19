<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    {{-- UBAH INI: Tambahkan @PwaHead jika diperlukan --}}
    @PwaHead 
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'KasPlus Register') }}</title>

    {{-- UBAH INI: Pastikan `@vite` ada --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    {{-- UBAH INI: Body background jadi PUTIH, dan centering container --}}
    <body class="font-sans antialiased bg-white min-h-screen flex items-center justify-center">

    {{-- Struktur Kontainer Utama (Mensimulasikan Bingkai HP) --}}
    {{-- UBAH INI: Background kontainer utama jadi putih dan styling shadow --}}
    <div class="relative w-full max-w-sm mx-auto h-screen sm:h-[90vh] sm:max-h-[844px] sm:my-8 sm:rounded-2xl sm:shadow-2xl flex flex-col bg-white overflow-hidden">
        
        {{-- UBAH INI: Bar di bagian atas untuk visual notch/status bar yang PUTIH --}}
        <div class="absolute top-0 left-0 w-full h-12 bg-white z-20"></div> 

        {{-- Main Content Area untuk Form Register --}}
        {{-- UBAH INI: Kembalikan justify-center untuk posisi vertikal keseluruhan blok --}}
        <main class="flex-grow flex flex-col justify-center items-center p-8 bg-white relative z-10"> {{-- HAPUS pt-20, TAMBAH justify-center --}}
            
            {{-- UBAH INI: Kontainer untuk Logo dan Text Judul KasPlus --}}
            {{-- Sesuaikan margin untuk mendekatkan ke judul di bawah dan menggeser grup ini ke bawah --}}
            <div class="mb-4 mt-4 flex items-center justify-center"> {{-- UBAH INI: mb-4 menjadi mb-1, mt-0 menjadi mt-4 --}}
                <img src="{{ asset('images/kasplus_logo.png') }}" alt="KasPlus Logo" class="h-12 w-auto mr-2"> 
                <h1 class="text-[45px] font-black text-[#131951] tracking-wide leading-[48px]">KasPlus</h1> 
            </div>

            {{-- UBAH INI: Judul Halaman Register --}}
            {{-- mb-8 dipertahankan untuk jarak ke elemen selanjutnya (form) --}}
            <h3 class="text-xl font-medium text-[#131951] text-center mb-8 tracking-wide">
                Daftar Akun Baru
            </h3>

            {{-- UBAH INI: Bagian untuk session status --}}
            @if (session('status'))
                <div class="mb-4 font-medium text-sm text-green-600 w-full text-center tracking-normal">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}" class="w-full max-w-xs space-y-5">
                @csrf

                {{-- UBAH INI: Field Nama Lengkap --}}
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1 tracking-wide">Nama Lengkap</label>
                    <div class="relative">
                        <input id="name" class="block w-full border-[#D3D3D3] rounded-[14px] shadow-sm focus:border-[#2D5AF7] focus:ring-[#2D5AF7] text-gray-800 p-3 pl-3" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" placeholder="Masukkan nama lengkap Anda" />
                    </div>
                    @error('name')
                        <p class="text-red-500 text-xs mt-1 tracking-normal">{{ $message }}</p>
                    @enderror
                </div>

                {{-- UBAH INI: Field Email --}}
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1 tracking-wide">Email</label>
                    <div class="relative">
                        <input id="email" class="block w-full border-[#D3D3D3] rounded-[14px] shadow-sm focus:border-[#2D5AF7] focus:ring-[#2D5AF7] text-gray-800 p-3 pl-10" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" placeholder="Masukkan email Anda" />
                        {{-- UBAH INI: Ikon Email --}}
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-sm leading-5">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8m-9 6h.01M17 12h.01M7 12h.01M12 12v.01M21 8v10a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h14a2 2 0 012 2z"></path></svg>
                        </span>
                    </div>
                    @error('email')
                        <p class="text-red-500 text-xs mt-1 tracking-normal">{{ $message }}</p>
                    @enderror
                </div>

                {{-- UBAH INI: Field Kata Sandi --}}
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1 tracking-wide">Kata Sandi</label>
                    <div class="relative">
                        <input id="password" class="block w-full border-[#D3D3D3] rounded-[14px] shadow-sm focus:border-[#2D5AF7] focus:ring-[#2D5AF7] text-gray-800 p-3 pl-10" type="password" name="password" required autocomplete="new-password" placeholder="Buat kata sandi Anda" />
                        {{-- UBAH INI: Ikon Password (Mata) --}}
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-sm leading-5">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                        </span>
                    </div>
                    @error('password')
                        <p class="text-red-500 text-xs mt-1 tracking-normal">{{ $message }}</p>
                    @enderror
                </div>

                {{-- UBAH INI: Field Konfirmasi Kata Sandi --}}
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1 tracking-wide">Konfirmasi Kata Sandi</label>
                    <div class="relative">
                        <input id="password_confirmation" class="block w-full border-[#D3D3D3] rounded-[14px] shadow-sm focus:border-[#2D5AF7] focus:ring-[#2D5AF7] text-gray-800 p-3 pl-3" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Konfirmasi kata sandi Anda" />
                    </div>
                    @error('password_confirmation')
                        <p class="text-red-500 text-xs mt-1 tracking-normal">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-center pt-4">
                    {{-- UBAH INI: Tombol Register --}}
                    <button type="submit" class="w-full bg-[#2D5AF7] hover:bg-[#1f42b3] text-white font-semibold py-3 px-4 rounded-[14px] transition-colors duration-200 shadow-md tracking-wide text-xl">
                        Daftar
                    </button>
                </div>
            </form>

            {{-- UBAH INI: Link "Sudah punya akun?" --}}
            <div class="flex items-center justify-center mt-6">
                <a class="text-sm text-[#7B7B7B] hover:text-[#2D5AF7] font-medium tracking-wide" href="{{ route('login') }}">
                    Sudah punya akun? <span class="text-[#2D5AF7] font-semibold">Masuk di sini!</span>
                </a>
            </div>

        </main>

    </div>
</body>
</html>