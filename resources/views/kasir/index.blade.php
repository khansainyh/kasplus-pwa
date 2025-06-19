<x-app-layout>
    <x-slot name="header">
        {{-- UBAH INI: Header untuk Halaman Kasir agar lebih menonjol dan rapi --}}
        <h2 class="font-bold text-2xl text-[#131951] text-center tracking-wide">
            Kasir
        </h2>
    </x-slot>

    {{-- Main container for the Kasir module --}}
    <div class="w-full pb-10 px-4" x-data="kasirApp()"> {{-- Tambahkan px-4 untuk padding horizontal --}}
        {{-- Pesan Sukses atau Error --}}
        @if(session('success'))
            {{-- UBAH INI: Styling pesan sukses --}}
            <div class="bg-green-100 text-green-700 p-3 rounded-lg shadow-sm mb-4 text-sm font-medium tracking-wide">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            {{-- UBAH INI: Styling pesan error --}}
            <div class="bg-red-100 text-red-700 p-3 rounded-lg shadow-sm mb-4 text-sm font-medium tracking-wide">
                {{ session('error') }}
            </div>
        @endif

        {{-- Product List Area --}}
        <div class="mb-6"> {{-- UBAH INI: Margin bawah lebih rapat --}}
            <h3 class="text-xl font-bold mb-3 text-[#131951] tracking-wide">Daftar Produk</h3> {{-- UBAH INI: Font size, weight, color --}}
            
            {{-- Search Bar --}}
            <div class="mb-4 relative">
                {{-- UBAH INI: Styling Search Input agar konsisten dengan input lain --}}
                <input type="text" x-model="searchTerm" placeholder="Cari produk..." class="block w-full border-[#D3D3D3] rounded-[14px] shadow-sm focus:border-[#2D5AF7] focus:ring-[#2D5AF7] text-gray-800 p-3 pl-10 tracking-wide">
                {{-- Tambahkan ikon search --}}
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-sm leading-5 text-gray-400">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 gap-4">
                <template x-for="produk in filteredProduks" :key="produk.id">
                    {{-- UBAH INI: Styling Kartu Produk (background, rounded, shadow, border) --}}
                    <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100 flex flex-col justify-between" 
                         x-data="{ 
                            localSelectedVariasiId: null, 
                            localSelectedVariasiHarga: 0, 
                            localSelectedVariasiStok: 0, 
                            localSelectedVariasiName: '',
                            
                            init() {
                                if (produk.variasis.length > 0) {
                                    const initialVariasi = produk.variasis.find(v => v.stok > 0) || produk.variasis[0];
                                    if (initialVariasi) {
                                        this.localSelectedVariasiId = initialVariasi.id;
                                        this.localSelectedVariasiHarga = parseFloat(initialVariasi.harga);
                                        this.localSelectedVariasiStok = parseInt(initialVariasi.stok);
                                        this.localSelectedVariasiName = initialVariasi.nama;
                                    }
                                } else {
                                    this.localSelectedVariasiHarga = parseFloat(produk.harga);
                                    this.localSelectedVariasiStok = parseInt(produk.stok);
                                }
                            }
                         }"
                    >
                        <h4 class="font-semibold text-lg text-[#131951] mb-1 tracking-wide" x-text="produk.nama"></h4> {{-- Font size, weight, color --}}
                        <p class="text-sm text-gray-500 mb-2 tracking-wide" x-text="produk.kategori ? produk.kategori.nama_kategori : 'Tanpa Kategori'"></p>

                        {{-- Variasi Section (if any) --}}
                        <template x-if="produk.variasis.length > 0">
                            <div class="mb-3">
                                <label class="block text-xs font-medium text-gray-700 mb-1 tracking-wide">Pilih Variasi:</label>
                                {{-- UBAH INI: Styling Dropdown Variasi --}}
                                <select x-model="localSelectedVariasiId" 
                                        @change="
                                            const selectedOption = produk.variasis.find(v => v.id == localSelectedVariasiId);
                                            if(selectedOption) {
                                                localSelectedVariasiHarga = parseFloat(selectedOption.harga);
                                                localSelectedVariasiStok = parseInt(selectedOption.stok);
                                                localSelectedVariasiName = selectedOption.nama;
                                            } else {
                                                localSelectedVariasiHarga = 0;
                                                localSelectedVariasiStok = 0;
                                                localSelectedVariasiName = '';
                                            }
                                        " 
                                        class="block w-full text-sm border-[#D3D3D3] rounded-[10px] shadow-sm focus:border-[#2D5AF7] focus:ring-[#2D5AF7] py-2 px-3 tracking-wide">
                                    <option value="">Pilih Variasi</option>
                                    <template x-for="variasi in produk.variasis" :key="variasi.id">
                                        <option :value="variasi.id" 
                                                :disabled="variasi.stok === 0">
                                            <span x-text="variasi.nama + ' (Rp ' + formatRupiah(variasi.harga) + ') - Stok: ' + variasi.stok"></span>
                                        </option>
                                    </template>
                                </select>
                                <template x-if="produk.variasis.length > 0 && produk.variasis.every(v => v.stok === 0)">
                                    <p class="text-red-500 text-xs mt-1 tracking-normal">Semua variasi habis stok.</p>
                                </template>
                                <p x-show="localSelectedVariasiStok === 0 && localSelectedVariasiId !== null && localSelectedVariasiId !== ''" class="text-red-500 text-xs mt-1 tracking-normal">Variasi ini habis stok.</p>
                            </div>
                        </template>
                        <template x-if="produk.variasis.length === 0">
                            {{-- Tampilan Tanpa Dropdown Variasi --}}
                            <div class="mb-3">
                                <p class="text-md font-semibold text-[#131951] mb-1 tracking-wide">Harga: Rp <span x-text="formatRupiah(produk.harga)"></span></p>
                                <p class="text-xs text-gray-500 tracking-wide">Stok: <span x-text="produk.stok"></span></p>
                                <template x-if="produk.stok === 0">
                                    <p class="text-red-500 text-xs mt-1 tracking-normal">Stok habis.</p>
                                </template>
                            </div>
                        </template>

                        {{-- Tambah ke Keranjang Button --}}
                        <button 
                            @click="
                                addToCart(
                                    produk.id,
                                    produk.nama,
                                    produk.variasis.length > 0 ? localSelectedVariasiId : null,
                                    produk.variasis.length > 0 ? localSelectedVariasiName : null,
                                    produk.variasis.length > 0 ? localSelectedVariasiHarga : parseFloat(produk.harga),
                                    produk.variasis.length > 0 ? localSelectedVariasiStok : parseInt(produk.stok)
                                );
                            "
                            :disabled="produk.variasis.length > 0 ? 
                                (localSelectedVariasiId === null || localSelectedVariasiId === '' || localSelectedVariasiStok === 0) : 
                                (produk.stok === 0)"
                            class="w-full bg-[#2D5AF7] text-white px-4 py-3 rounded-[10px] hover:bg-[#1f42b3] mt-auto disabled:opacity-50 disabled:cursor-not-allowed font-medium text-base transition-colors duration-200 shadow-sm"> {{-- UBAH INI: Styling Tombol Tambah ke Keranjang --}}
                            + Tambah
                        </button>
                    </div>
                </template>
                <template x-if="filteredProduks.length === 0">
                    <p class="text-gray-500 col-span-full text-center tracking-wide">Tidak ada produk ditemukan dengan kata kunci ini.</p>
                </template>
            </div>
        </div>

        {{-- Cart & Payment Area --}}
        <div id="cart-section" class="bg-white rounded-xl shadow-sm p-6 mt-6"> {{-- UBAH INI: shadow-sm, padding, margin-top --}}
            <h3 class="text-xl font-bold mb-4 flex justify-between items-center text-[#131951] tracking-wide">Keranjang Belanja
                <span class="bg-[#2D5AF7] text-white text-xs font-bold px-2 py-1 rounded-full">{{-- UBAH INI: Warna Badge --}}
                    <span x-text="cart.length"></span>
                </span>
            </h3>

            {{-- Cart Items --}}
            <div x-show="cart.length > 0" class="mb-4 max-h-60 overflow-y-auto border-b border-gray-200 pb-2"> {{-- UBAH INI: Border-b color --}}
                <template x-for="(item, index) in cart" :key="index">
                    {{-- UBAH INI: Styling setiap item keranjang --}}
                    <div class="flex justify-between items-center mb-3 text-sm tracking-wide py-2 border-b border-dashed border-gray-200 last:border-b-0 last:mb-0">
                        <div>
                            <span class="font-semibold text-[#131951]" x-text="item.qty"></span>x <span class="text-[#131951]" x-text="item.namaProduk + (item.namaVariasi ? ' (' + item.namaVariasi + ')' : '')"></span>
                            <p class="text-xs text-gray-500 tracking-normal mt-0.5">Rp <span x-text="formatRupiah(item.harga)"></span>/item</p> {{-- Tambah margin-top --}}
                        </div>
                        <div class="flex items-center space-x-2">
                            {{-- UBAH INI: Styling Quantity Buttons --}}
                            <button @click="decreaseQty(index)" class="bg-[#D3D3D3] text-[#131951] w-6 h-6 rounded-full flex items-center justify-center font-bold text-base hover:bg-[#7B7B7B] hover:text-white transition-colors duration-150">-</button>
                            <span x-text="item.qty" class="font-bold text-[#131951] text-base"></span>
                            <button @click="increaseQty(index)" class="bg-[#D3D3D3] text-[#131951] w-6 h-6 rounded-full flex items-center justify-center font-bold text-base hover:bg-[#7B7B7B] hover:text-white transition-colors duration-150">+</button>
                            <button @click="removeFromCart(index)" class="text-red-500 text-lg hover:text-red-700 transition-colors duration-150">&times;</button>
                        </div>
                    </div>
                </template>
            </div>
            <p x-show="cart.length === 0" class="text-gray-500 text-center tracking-wide py-4">Keranjang kosong.</p> {{-- UBAH INI: Padding --}}

            {{-- Total --}}
            <div class="mt-4 text-right">
                <p class="text-xl font-bold text-[#131951] tracking-wide">Total: Rp <span x-text="formatRupiah(calculateTotal())"></span></p> {{-- UBAH INI: Styling Total --}}
            </div>

            {{-- Payment Method --}}
            <div class="mt-6 border-t border-gray-200 pt-4">
                <h4 class="font-bold text-lg mb-3 text-[#131951] tracking-wide">Metode Pembayaran</h4> {{-- UBAH INI: Styling heading --}}
                <div class="flex space-x-4 mb-4">
                    {{-- UBAH INI: Styling Radio Buttons --}}
                    <label class="inline-flex items-center cursor-pointer">
                        <input type="radio" x-model="paymentMethod" value="cash" class="form-radio h-5 w-5 text-[#2D5AF7] focus:ring-[#2D5AF7] border-gray-300 rounded-full">
                        <span class="ml-2 text-md text-[#131951] tracking-wide">Tunai</span>
                    </label>
                    <label class="inline-flex items-center cursor-pointer">
                        <input type="radio" x-model="paymentMethod" value="qris" class="form-radio h-5 w-5 text-[#2D5AF7] focus:ring-[#2D5AF7] border-gray-300 rounded-full">
                        <span class="ml-2 text-md text-[#131951] tracking-wide">QRIS</span>
                    </label>
                </div>

                {{-- Cash Payment Details --}}
                <div x-show="paymentMethod === 'cash'">
                    <label for="amount_paid" class="block text-sm font-medium text-gray-700 mb-1 tracking-wide">Uang Diterima (Rp)</label>
                    {{-- UBAH INI: Styling input uang diterima --}}
                    <input type="number" x-model.number="amountPaid" @input="calculateChange()" class="mt-1 block w-full border-[#D3D3D3] rounded-[10px] shadow-sm focus:border-[#2D5AF7] focus:ring-[#2D5AF7] text-gray-800 p-2 tracking-wide" placeholder="Masukkan jumlah uang">
                    <p class="text-md font-semibold mt-2 text-[#131951] tracking-wide">Kembalian: Rp <span x-text="formatRupiah(change)"></span></p>
                </div>

                {{-- QRIS Payment Details --}}
                <div x-show="paymentMethod === 'qris'">
                    <p class="text-sm text-gray-600 tracking-wide">Total pembayaran QRIS: Rp <span x-text="formatRupiah(calculateTotal())"></span></p>
                </div>

                <button 
                    @click="processCheckout()" 
                    :disabled="cart.length === 0 || (paymentMethod === 'cash' && amountPaid < calculateTotal())"
                    class="w-full bg-[#2D5AF7] text-white px-4 py-3 rounded-[10px] hover:bg-[#1f42b3] mt-4 disabled:opacity-50 disabled:cursor-not-allowed font-medium text-xl transition-colors duration-200 shadow-md"> {{-- UBAH INI: Styling Tombol Proses Pembayaran --}}
                    Proses Pembayaran
                </button>
            </div>
        </div>
    </div>

    {{-- Alpine.js Script --}}
    <script>
        function kasirApp() {
            return {
                produks: @json($produks),
                searchTerm: '',
                cart: [],
                paymentMethod: 'cash',
                amountPaid: 0,
                change: 0,

                init() {
                    this.calculateChange();
                },

                get filteredProduks() {
                    if (!this.produks) {
                        return [];
                    }
                    if (!this.searchTerm) {
                        return this.produks;
                    }
                    const lowerCaseSearchTerm = this.searchTerm.toLowerCase();
                    return this.produks.filter(produk => {
                        if (produk.nama.toLowerCase().includes(lowerCaseSearchTerm)) {
                            return true;
                        }
                        if (produk.kategori && produk.kategori.nama_kategori.toLowerCase().includes(lowerCaseSearchTerm)) {
                            return true;
                        }
                        if (produk.variasis && produk.variasis.some(variasi => variasi.nama.toLowerCase().includes(lowerCaseSearchTerm))) {
                            return true;
                        }
                        return false;
                    });
                },

                addToCart(produkId, namaProduk, variasiId, namaVariasi, harga, stok) {
                    console.log('addToCart called with:');
                    console.log('   produkId:', produkId);
                    console.log('   namaProduk:', namaProduk);
                    console.log('   variasiId:', variasiId);
                    console.log('   namaVariasi:', namaVariasi);
                    console.log('   harga:', harga);
                    console.log('   stok:', stok);

                    if (stok <= 0) {
                        alert('Stok produk/variasi ini habis!');
                        return;
                    }
                    if (harga === 0 || isNaN(harga) || harga === null) {
                        alert('Harga produk/variasi tidak valid (0, kosong, atau null).');
                        return;
                    }

                    const existingItemIndex = this.cart.findIndex(item => {
                        return item.produkId === produkId && item.variasiId === variasiId;
                    });

                    if (existingItemIndex > -1) {
                        if (this.cart[existingItemIndex].qty < stok) {
                            this.cart[existingItemIndex].qty++;
                        } else {
                            alert('Stok tidak mencukupi untuk menambah item ini lagi.');
                        }
                    } else {
                        this.cart.push({
                            produkId: produkId,
                            variasiId: variasiId,
                            namaProduk: namaProduk,
                            namaVariasi: namaVariasi,
                            harga: parseFloat(harga),
                            qty: 1,
                            stokTersedia: parseInt(stok)
                        });
                    }
                    this.calculateChange();
                    console.log('DEBUG: Current Cart:', this.cart);
                },

                removeFromCart(index) {
                    this.cart.splice(index, 1);
                    this.calculateChange();
                },

                increaseQty(index) {
                    if (this.cart[index].qty < this.cart[index].stokTersedia) {
                        this.cart[index].qty++;
                    } else {
                        alert('Stok tidak mencukupi.');
                    }
                    this.calculateChange();
                },

                decreaseQty(index) {
                    if (this.cart[index].qty > 1) {
                        this.cart[index].qty--;
                    } else {
                        this.removeFromCart(index);
                    }
                    this.calculateChange();
                },

                calculateTotal() {
                    return this.cart.reduce((sum, item) => sum + (item.harga * item.qty), 0);
                },

                calculateChange() {
                    if (this.paymentMethod === 'cash') {
                        this.change = this.amountPaid - this.calculateTotal();
                    } else {
                        this.change = 0;
                    }
                },

                formatRupiah(amount) {
                    if (isNaN(amount) || amount === null) {
                        return '0';
                    }
                    return new Intl.NumberFormat('id-ID').format(amount);
                },

                async processCheckout() {
                    if (this.cart.length === 0) {
                        alert('Keranjang belanja kosong!');
                        return;
                    }

                    if (this.paymentMethod === 'cash' && this.amountPaid < this.calculateTotal()) {
                        alert('Uang yang diterima kurang dari total pembayaran.');
                        return;
                    }

                    const checkoutData = {
                        items: this.cart.map(item => ({
                            produk_id: item.produkId,
                            variasi_id: item.variasiId,
                            qty: item.qty
                        })),
                        total: this.calculateTotal(),
                        payment_method: this.paymentMethod,
                        amount_paid: this.paymentMethod === 'cash' ? this.amountPaid : null
                    };

                    try {
                        const response = await fetch('{{ route('kasir.checkout') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify(checkoutData)
                        });

                        const result = await response.json();

                        if (response.ok) {
                            if (result.redirect_url) {
                                window.location.href = result.redirect_url;
                            } else {
                                alert(result.message || 'Transaksi berhasil!');
                                this.cart = [];
                                this.amountPaid = 0;
                                this.change = 0;
                            }
                        } else {
                            alert('Gagal memproses transaksi: ' + (result.message || 'Terjadi kesalahan tidak diketahui.'));
                        }
                    } catch (error) {
                        console.error('Error during checkout:', error);
                        alert('Terjadi kesalahan jaringan atau server saat checkout.');
                    }
                }
            }
        }
    </script>
</x-app-layout>