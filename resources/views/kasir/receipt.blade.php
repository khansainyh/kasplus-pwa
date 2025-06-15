<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 print:hidden">
            Detail Struk Transaksi
        </h2>
    </x-slot>

    <div class="w-full pb-10">
        {{-- Area struk yang akan dicetak, tambahkan ID untuk targeting yang jelas --}}
        <div id="receipt-content" class="bg-white rounded-lg shadow-md p-6 max-w-md mx-auto">
            <div class="text-center mb-6">
                <h2 class="text-xl font-bold print:text-lg print:font-semibold">STRUK PENJUALAN</h2>
                <p class="text-sm print:text-xs">No. Invoice: <span class="font-semibold">{{ $transaction->invoice_number }}</span></p>
                <p class="text-sm print:text-xs">Tanggal: {{ $transaction->transaction_date->format('d M Y H:i') }}</p>
                <p class="text-sm print:text-xs">Kasir: {{ $transaction->user->name ?? 'N/A' }}</p>
            </div>

            <div class="mb-6">
                <h3 class="font-semibold border-b pb-2 mb-2 print:text-sm print:pb-1 print:mb-1 print:border-gray-600">Daftar Item:</h3>
                <ul class="text-sm print:text-xs">
                    @foreach($transaction->items as $item)
                        <li class="flex justify-between py-1 print:py-0.5">
                            <div>
                                <span class="font-medium">{{ $item->item_name }}</span><br>
                                <span class="text-xs text-gray-600 print:text-xxs">{{ $item->quantity }} x Rp {{ number_format($item->item_price, 0, ',', '.') }}</span>
                            </div>
                            <span class="font-medium">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="text-right mb-6">
                <p class="text-lg font-bold print:text-sm print:font-semibold">Total: Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</p>
                <p class="text-sm text-gray-700 print:text-xs">Metode Pembayaran: {{ Str::title($transaction->payment_method) }}</p>
                @if($transaction->payment_method === 'cash')
                    <p class="text-sm text-gray-700 print:text-xs">Uang Diterima: Rp {{ number_format($transaction->amount_paid, 0, ',', '.') }}</p>
                    <p class="text-lg font-bold print:text-sm print:font-semibold">Kembalian: Rp {{ number_format($transaction->change_amount, 0, ',', '.') }}</p>
                @endif
            </div>

            <div class="text-center text-sm text-gray-700 print:text-xs">
                <p>Terima Kasih Atas Pembelian Anda!</p>
                <p class="mt-2 print:mt-1">Barang yang sudah dibeli tidak dapat dikembalikan.</p>
            </div>

            {{-- Tombol untuk aksi (sembunyikan saat dicetak) --}}
            <div class="mt-6 flex flex-col sm:flex-row justify-center space-y-2 sm:space-y-0 sm:space-x-4 print:hidden">
                <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Cetak Struk</button>
                <a href="{{ route('kasir.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded text-center">Kembali ke Kasir</a>
            </div>
        </div>
    </div>

    {{-- CSS Khusus Cetak --}}
    <style>
        @media print {
            /* 1. Sembunyikan elemen non-cetak dari app-layout */
            body > * { /* Ini menargetkan div utama dari x-app-layout, yang berisi bingkai HP */
                display: none !important;
            }

            /* 2. Tampilkan HANYA kontainer utama dari receipt.blade.php */
            .w-full.pb-10 {
                display: block !important;
                visibility: visible !important;
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                margin: 0;
                padding: 0;
            }
            
            /* 3. Tampilkan konten struk itu sendiri dan semua turunannya */
            #receipt-content {
                display: block !important;
                visibility: visible !important;
                position: static !important; /* Pastikan tidak ada positioning aneh */
                margin: 0 !important;
                padding: 0 !important;
                box-shadow: none !important;
                border-radius: 0 !important;
                max-width: none !important; /* Hapus batasan max-width */
            }

            #receipt-content * { /* Pastikan SEMUA elemen di dalam struk terlihat */
                visibility: visible !important;
                display: block !important; /* Pastikan mereka dirender sebagai blok untuk alur normal */
            }

            /* Beberapa elemen mungkin perlu display inline-block atau flex untuk layout aslinya */
            #receipt-content .flex { display: flex !important; }
            #receipt-content .inline-flex { display: inline-flex !important; }

            /* 4. Atur properti halaman cetak (ukuran kertas, margin) */
            @page {
                size: 58mm auto; /* Coba atur lebar 58mm, tinggi otomatis */
                margin: 0 !important; /* Hapus margin halaman bawaan browser/printer */
                padding: 0 !important;
            }
            
            /* 5. Styling untuk simulasi printer thermal (lebih spesifik ke ID agar tidak terpengaruh luar) */
            #receipt-content {
                width: 58mm; /* Ini akan memaksa lebar cetakan */
                overflow: hidden; /* Penting untuk memotong jika terlalu lebar */
                margin: 0 auto; /* Pusatkan di halaman */
            }

            body {
                font-family: 'monospace', monospace !important; /* Font monospace lebih cocok untuk struk */
                font-size: 8px !important; /* Ukuran font dasar yang kecil */
                line-height: 1.2 !important;
                margin: 0 !important;
                padding: 0 !important;
                -webkit-print-color-adjust: exact; /* Penting untuk mencetak warna latar belakang/teks */
                color-adjust: exact;
            }

            /* Override Tailwind CSS untuk ukuran font di cetakan */
            #receipt-content h1, #receipt-content h2, #receipt-content h3, #receipt-content h4, 
            #receipt-content p, #receipt-content ul, #receipt-content li, #receipt-content span, 
            #receipt-content div {
                color: #000 !important; /* Pastikan semua teks berwarna hitam */
            }
            #receipt-content .text-2xl { font-size: 14px !important; }
            #receipt-content .text-xl { font-size: 12px !important; }
            #receipt-content .text-lg { font-size: 10px !important; }
            #receipt-content .text-md { font-size: 9px !important; } 
            #receipt-content .text-sm { font-size: 8px !important; }
            #receipt-content .text-xs { font-size: 7px !important; }
            #receipt-content .text-xxs { font-size: 6px !important; } 

            #receipt-content .font-bold { font-weight: bold !important; }
            #receipt-content .font-semibold { font-weight: 600 !important; }
            #receipt-content .my-4 { margin-top: 5px !important; margin-bottom: 5px !important; }
            #receipt-content .my-2 { margin-top: 3px !important; margin-bottom: 3px !important; }
            #receipt-content .py-1 { padding-top: 2px !important; padding-bottom: 2px !important; }
            #receipt-content .py-0\.5 { padding-top: 1px !important; padding-bottom: 1px !important; }
            #receipt-content .mb-6 { margin-bottom: 15px !important; }
            #receipt-content .mb-2 { margin-bottom: 5px !important; }
            #receipt-content .mb-1 { margin-bottom: 2px !important; }
            #receipt-content .mt-2 { margin-top: 5px !important; }

            /* Sembunyikan elemen yang hanya untuk tampilan web, di mode cetak */
            .print\:hidden { display: none !important; }
            /* Styling untuk elemen yang menyesuaikan lebar cetak */
            .print\:max-w-full { max-width: 100% !important; }
            .print\:mx-0 { margin-left: 0 !important; margin-right: 0 !important; }
            .print\:shadow-none { box-shadow: none !important; }
            .print\:p-0 { padding: 0 !important; }
        }
    </style>
</x-app-layout>