<x-app-layout>
    <x-slot name="header">
        {{-- Header untuk Halaman Struk, konsisten dengan Kasir dan Keuangan --}}
        <h2 class="font-bold text-xl text-[#131951] text-center tracking-tight py-2 print:hidden">
            Detail Struk Transaksi
        </h2>
    </x-slot>

    {{-- Main container untuk halaman struk, dengan padding yang cukup untuk menampilkan tombol --}}
    {{-- UBAH INI: Menambah padding bawah (pb-40) pada container utama untuk memastikan tombol terlihat di atas navbar bawah. --}}
    <div class="w-full max-w-4xl mx-auto py-8 px-4 font-satoshi pb-40">
        {{-- Area struk yang akan dicetak --}}
        <div id="receipt-content" class="bg-white rounded-2xl shadow-xl p-8 max-w-md mx-auto border border-gray-100">
            <div class="text-center mb-8">
                {{-- Logo/Nama Toko - Tambahkan logo atau nama toko di sini --}}
                <img src="https://placehold.co/120x40/131951/FFFFFF?text=KASPLUS" alt="KasPlus Logo" class="mx-auto mb-4 rounded-md print:hidden">
                <h2 class="text-3xl font-extrabold text-[#131951] mb-2 tracking-tight print:text-lg print:font-bold">STRUK PENJUALAN</h2>
                <p class="text-sm text-gray-700 print:text-xs">No. Invoice: <span class="font-semibold text-[#0D0D0D]">{{ $transaction->invoice_number }}</span></p>
                <p class="text-sm text-gray-700 print:text-xs">Tanggal: {{ $transaction->transaction_date->format('d M Y H:i') }}</p>
                <p class="text-sm text-gray-700 print:text-xs">Kasir: {{ $transaction->user->name ?? 'N/A' }}</p>
            </div>

            <div class="mb-8">
                <h3 class="font-bold text-lg text-[#131951] border-b border-gray-200 pb-3 mb-3 print:text-sm print:pb-1.5 print:mb-1.5 print:border-gray-400">Daftar Item:</h3>
                <ul class="text-base text-gray-800 print:text-xs">
                    @foreach($transaction->items as $item)
                        <li class="flex justify-between items-start py-2 border-b border-dashed border-gray-100 last:border-b-0 print:py-1 print:border-gray-300">
                            <div class="flex-grow pr-4">
                                <span class="font-medium text-[#0D0D0D]">{{ $item->item_name }}</span><br>
                                <span class="text-xs text-gray-600 print:text-xl">{{ $item->quantity }} x Rp {{ number_format($item->item_price, 0, ',', '.') }}</span>
                            </div>
                            <span class="font-semibold text-[#0D0D0D] whitespace-nowrap">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="text-right mb-8 border-t border-gray-200 pt-6">
                <p class="text-xl font-extrabold text-[#131951] mb-2 print:text-base print:font-bold">Total: Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</p>
                <p class="text-base text-gray-700 print:text-sm">Metode Pembayaran: <span class="font-medium">{{ Str::title($transaction->payment_method) }}</span></p>
                @if($transaction->payment_method === 'cash')
                    <p class="text-base text-gray-700 print:text-sm">Uang Diterima: <span class="font-medium">Rp {{ number_format($transaction->amount_paid, 0, ',', '.') }}</span></p>
                    <p class="text-xl font-extrabold text-[#131951] mt-2 print:text-base print:font-bold">Kembalian: Rp {{ number_format($transaction->change_amount, 0, ',', '.') }}</p>
                @endif
            </div>

            <div class="text-center text-sm text-gray-700 print:text-xxl">
                <p class="font-medium">Terima Kasih Atas Pembelian Anda!</p>
                <p class="mt-2 text-xs text-gray-500 print:mt-1 print:text-xl">Barang yang sudah dibeli tidak dapat dikembalikan.</p>
            </div>

            {{-- Tombol untuk aksi (sembunyikan saat dicetak). Pastikan visible di tampilan web --}}
            {{-- UBAH INI: Styling tombol agar terlihat jelas dan konsisten. Remove print:hidden here, it's on the div. --}}
            <div class="mt-8 flex flex-col sm:flex-row justify-center space-y-4 sm:space-y-0 sm:space-x-4 print:hidden">
                <button onclick="window.print()" class="bg-[#2D5AF7] hover:bg-[#1f42b3] text-white px-6 py-3 rounded-xl font-semibold shadow-md transition-colors duration-200">Cetak Struk</button>
                <a href="{{ route('kasir.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-6 py-3 rounded-xl text-center font-semibold shadow-md transition-colors duration-200 flex items-center justify-center">Kembali ke Kasir</a>
            </div>
        </div>
    </div>

    {{-- CSS Khusus Cetak --}}
    <style>
        /* Memuat Font Satoshi untuk tampilan web dan cetak */
        @font-face {
            font-family: 'Satoshi';
            src: url('https://cdn.fontshare.com/wf/E3E8C349-EBAB-4148-A951-404B67DFD0C6/4U3B674513E020A8FA45B2556515A97D/Satoshi-Variable.woff2') format('woff2');
            font-weight: 300 900;
            font-display: swap;
        }

        .font-satoshi {
            font-family: 'Satoshi', sans-serif;
        }

        /* Kelas kustom untuk ukuran font ekstra kecil di mode cetak */
        .print\:text-xxs {
            font-size: 6px !important;
        }

        @media print {
            /* 1. Sembunyikan elemen non-cetak dari app-layout */
            body > *:not(.w-full.max-w-4xl.mx-auto.py-8.px-4.font-satoshi) { /* Targetkan semua child body kecuali wrapper struk utama */
                display: none !important;
            }

            html, body {
                margin: 0 !important;
                padding: 0 !important;
                overflow: hidden !important; /* Mencegah scrollbar */
                min-height: auto !important; /* Reset min-height */
            }

            /* Sembunyikan header aplikasi yang mungkin ada di x-app-layout */
            .print\:hidden {
                display: none !important;
            }

            /* 2. Tampilkan HANYA kontainer utama dari receipt.blade.php */
            /* Mengatur ulang div wrapper halaman struk */
            .w-full.max-w-4xl.mx-auto.py-8.px-4.font-satoshi {
                display: block !important;
                width: 100% !important;
                max-width: none !important;
                margin: 0 !important;
                padding: 0 !important;
                position: absolute;
                left: 0;
                top: 0;
                box-shadow: none !important;
            }
            
            /* 3. Tampilkan konten struk itu sendiri dan semua turunannya */
            #receipt-content {
                display: block !important;
                visibility: visible !important;
                position: static !important; /* Pastikan tidak ada positioning aneh */
                margin: 0 auto !important; /* Pusatkan di halaman cetak */
                padding: 10mm !important; /* Beri padding agar tidak menempel di tepi kertas */
                box-shadow: none !important;
                border-radius: 0 !important;
                max-width: 58mm !important; /* Batasi lebar untuk simulasi printer thermal */
                width: 58mm !important; /* Pastikan lebar tetap */
                overflow: hidden; /* Penting untuk memotong jika terlalu lebar */
            }

            #receipt-content * { /* Pastikan SEMUA elemen di dalam struk terlihat */
                visibility: visible !important;
            }
            /* Elemen flex/inline-flex perlu dipertahankan */
            #receipt-content .flex { display: flex !important; }
            #receipt-content .inline-flex { display: inline-flex !important; }
            #receipt-content .block { display: block !important; } /* Pastikan elemen block tetap block */


            /* 4. Atur properti halaman cetak (ukuran kertas, margin) */
            @page {
                size: 58mm auto; /* Coba atur lebar 58mm, tinggi otomatis */
                margin: 0 !important; /* Hapus margin halaman bawaan browser/printer */
                padding: 0 !important;
            }
            
            /* 5. Styling untuk simulasi printer thermal */
            body {
                font-family: 'Satoshi', 'monospace', sans-serif !important; /* Font Satoshi atau monospace */
                font-size: 8px !important; /* Ukuran font dasar yang kecil */
                line-height: 1.2 !important;
                margin: 0 !important;
                padding: 0 !important;
                -webkit-print-color-adjust: exact; /* Penting untuk mencetak warna latar belakang/teks */
                color-adjust: exact;
                color: #000 !important; /* Pastikan warna teks default hitam */
            }

            /* Override Tailwind CSS untuk ukuran font dan spasi di cetakan */
            #receipt-content h1, #receipt-content h2, #receipt-content h3, #receipt-content h4, 
            #receipt-content p, #receipt-content ul, #receipt-content li, #receipt-content span, 
            #receipt-content div {
                color: #000 !important; /* Pastikan semua teks berwarna hitam */
                background-color: transparent !important; /* Hapus semua background di cetak */
                box-shadow: none !important; /* Hapus shadow */
                border-color: #000 !important; /* Border item menjadi hitam */
            }

            #receipt-content .text-3xl { font-size: 16px !important; line-height: 1.1 !important; }
            #receipt-content .text-2xl { font-size: 14px !important; line-height: 1.1 !important; }
            #receipt-content .text-xl { font-size: 12px !important; line-height: 1.2 !important; }
            #receipt-content .text-lg { font-size: 10px !important; line-height: 1.2 !important; } 
            #receipt-content .text-base { font-size: 9px !important; line-height: 1.2 !important; } 
            #receipt-content .text-sm { font-size: 8px !important; line-height: 1.2 !important; }
            #receipt-content .text-xs { font-size: 7px !important; line-height: 1.2 !important; }
            
            #receipt-content .font-extrabold { font-weight: 800 !important; }
            #receipt-content .font-bold { font-weight: 700 !important; }
            #receipt-content .font-semibold { font-weight: 600 !important; }
            #receipt-content .font-medium { font-weight: 500 !important; }
            
            /* Mengatur ulang margin dan padding untuk cetak */
            #receipt-content .mb-8 { margin-bottom: 8px !important; }
            #receipt-content .mb-6 { margin-bottom: 6px !important; }
            #receipt-content .mb-4 { margin-bottom: 4px !important; }
            #receipt-content .mb-3 { margin-bottom: 3px !important; }
            #receipt-content .mb-2 { margin-bottom: 2px !important; }
            #receipt-content .mb-1 { margin-bottom: 1px !important; }
            #receipt-content .mt-8 { margin-top: 8px !important; }
            #receipt-content .mt-6 { margin-top: 6px !important; }
            #receipt-content .mt-4 { margin-top: 4px !important; }
            #receipt-content .mt-2 { margin-top: 2px !important; }
            #receipt-content .mt-1 { margin-top: 1px !important; }
            #receipt-content .py-2 { padding-top: 2px !important; padding-bottom: 2px !important; }
            #receipt-content .py-1 { padding-top: 1px !important; padding-bottom: 1px !important; }
            #receipt-content .px-4 { padding-left: 4px !important; padding-right: 4px !important; }
            #receipt-content .p-8 { padding: 8px !important; }

            /* Border khusus item */
            #receipt-content .border-b { border-bottom-width: 1px !important; border-bottom-style: dashed !important; }
            #receipt-content .border-dashed { border-style: dashed !important; }
            #receipt-content .border-gray-100 { border-color: #eee !important; } /* Light gray border for print */
            #receipt-content .border-gray-200 { border-color: #ddd !important; }
            #receipt-content .border-gray-300 { border-color: #ccc !important; }
            #receipt-content .border-gray-400 { border-color: #bbb !important; }

            /* Jarak antar item detail */
            #receipt-content ul li {
                margin-bottom: 0 !important;
            }
            #receipt-content .space-y-1 > li + li { margin-top: 1px !important; }
            #receipt-content .space-y-2 > li + li { margin-top: 2px !important; }
            #receipt-content .space-y-3 > li + li { margin-top: 3px !important; }

            /* Ensure text truncation works correctly in print if space is limited */
            #receipt-content .truncate {
                white-space: nowrap !important;
                overflow: hidden !important;
                text-overflow: ellipsis !important;
            }
            #receipt-content .break-all {
                word-break: break-all !important;
            }
            #receipt-content .whitespace-nowrap {
                white-space: nowrap !important;
            }
        }
    </style>
</x-app-layout>