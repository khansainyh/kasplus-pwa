<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'KasPlus Login') }}</title> {{-- Ubah judul --}}

    <link rel="stylesheet" href="https://fonts.bunny.net/css2?family=Nunito:wght@400;600;700&display=swap">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-300">

    {{-- Struktur Bingkai HP --}}
    <div class="relative w-full max-w-sm mx-auto h-screen bg-gray-100 sm:h-[90vh] sm:max-h-[844px] sm:my-8 sm:rounded-2xl sm:shadow-2xl flex flex-col">
        
        {{-- Header untuk Halaman Login --}}
        <header class="bg-white shadow-sm z-10">
            <div class="w-full mx-auto py-4 px-4 flex justify-center items-center">
                <h2 class="font-bold text-xl text-gray-800">Login KasPlus</h2> {{-- Judul di header --}}
            </div>
        </header>

        {{-- Main Content Area untuk Form Login --}}
        <main class="flex-grow overflow-y-auto pt-4 px-4 flex items-center justify-center">
            <div class="w-full px-4"> {{-- Padding horizontal untuk form di dalam bingkai --}}
                @if (session('status'))
                    <div class="mb-4 font-medium text-sm text-green-600">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="bg-white p-6 rounded-lg shadow-md"> {{-- Tambah styling form --}}
                    @csrf

                    <div class="mb-4">
                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input id="email" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" />
                        @error('email')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                        <input id="password" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm"
                                type="password"
                                name="password"
                                required autocomplete="current-password" />
                        @error('password')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="block mb-4">
                        <label for="remember_me" class="inline-flex items-center">
                            <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500" name="remember">
                            <span class="ml-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
                        </label>
                    </div>

                    <div class="flex items-center justify-between mt-6"> {{-- Disesuaikan untuk layout mobile --}}
                        @if (Route::has('password.request'))
                            <a class="underline text-sm text-gray-600 hover:text-gray-900" href="{{ route('password.request') }}">
                                {{ __('Forgot your password?') }}
                            </a>
                        @endif

                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">
                            {{ __('Log in') }}
                        </button>
                    </div>

                    {{-- Tombol Register --}}
                    <div class="flex items-center justify-center mt-4"> {{-- Rata tengah --}}
                        <a class="underline text-sm text-blue-600 hover:text-blue-800" href="{{ route('register') }}">
                            {{ __('Don\'t have an account? Register here!') }}
                        </a>
                    </div>

                </form>
            </div>
        </main>

        {{-- Bottom Navbar - Biasanya tidak ada di halaman login --}}
        {{-- Jika Anda tetap ingin navbar bawah ini muncul di halaman login, salin kodenya dari app.blade.php di sini --}}

    </div>
</body>
</html>