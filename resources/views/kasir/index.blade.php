<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800">
            Kasir
        </h2>
    </x-slot>

    {{-- Main container for the Kasir module --}}
    <div class="w-full pb-10" x-data="kasirApp()">
        {{-- Pesan Sukses atau Error --}}
        @if(session('success'))
            <div class="bg-green-100 text-green-700 p-2 rounded mb-4">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="bg-red-100 text-red-700 p-2 rounded mb-4">{{ session('error') }}</div>
        @endif

        {{-- Product List Area --}}
        <div class="mb-8">
            <h3 class="text-lg font-semibold mb-3">Daftar Produk</h3>
            
            {{-- Search Bar --}}
            <div class="mb-4">
                <input type="text" x-model="searchTerm" placeholder="Cari produk..." class="block w-full border-gray-300 rounded-md shadow-sm">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 gap-4">
                {{-- Menggunakan x-for dari Alpine.js untuk iterasi filteredProduks --}}
                <template x-for="produk in filteredProduks" :key="produk.id">
                    <div class="bg-white rounded-lg shadow-md p-4"
                         x-data="{ 
                            // State lokal untuk produk dengan variasi
                            localSelectedVariasiId: null, 
                            localSelectedVariasiHarga: 0, 
                            localSelectedVariasiStok: 0, 
                            localSelectedVariasiName: '',
                            
                            init() {
                                // Inisialisasi jika produk memiliki variasi
                                if (produk.variasis.length > 0) {
                                    // Default: pilih variasi pertama yang tersedia atau yang pertama jika hanya ada 1
                                    const initialVariasi = produk.variasis.find(v => v.stok > 0) || produk.variasis[0];
                                    if (initialVariasi) {
                                        this.localSelectedVariasiId = initialVariasi.id;
                                        this.localSelectedVariasiHarga = parseFloat(initialVariasi.harga);
                                        this.localSelectedVariasiStok = parseInt(initialVariasi.stok);
                                        this.localSelectedVariasiName = initialVariasi.nama;
                                    }
                                }
                            }
                         }">
                        <h4 class="font-bold text-md" x-text="produk.nama"></h4>
                        <p class="text-sm text-gray-500 mb-2" x-text="produk.kategori ? produk.kategori.nama_kategori : 'Tanpa Kategori'"></p>

                        {{-- Variasi Section (if any) --}}
                        <template x-if="produk.variasis.length > 0">
                            {{-- Tampilan dengan Dropdown Variasi --}}
                            <div class="mb-3">
                                <label class="block text-xs font-medium text-gray-700 mb-1">Pilih Variasi:</label>
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
                                        class="block w-full text-sm border-gray-300 rounded-md shadow-sm">
                                    <option value="">Pilih Variasi</option>
                                    <template x-for="variasi in produk.variasis" :key="variasi.id">
                                        <option :value="variasi.id" 
                                                :disabled="variasi.stok === 0">
                                            <span x-text="variasi.nama + ' (Rp ' + formatRupiah(variasi.harga) + ') - Stok: ' + variasi.stok"></span>
                                        </option>
                                    </template>
                                </select>
                                <template x-if="produk.variasis.length > 0 && produk.variasis.every(v => v.stok === 0)">
                                    <p class="text-red-500 text-xs mt-1">Semua variasi habis stok.</p>
                                </template>
                                <p x-show="localSelectedVariasiStok === 0 && localSelectedVariasiId !== null && localSelectedVariasiId !== ''" class="text-red-500 text-xs mt-1">Variasi ini habis stok.</p>
                            </div>
                        </template>
                        <template x-if="produk.variasis.length === 0">
                            {{-- Tampilan Tanpa Dropdown Variasi (Default Pilihan) --}}
                            <div>
                                <p class="text-sm font-semibold mb-3">Harga: Rp <span x-text="formatRupiah(produk.harga)"></span></p>
                                <p class="text-xs text-gray-500">Stok: <span x-text="produk.stok"></span></p>
                                <template x-if="produk.stok === 0">
                                    <p class="text-red-500 text-xs mt-1">Stok habis.</p>
                                </template>
                            </div>
                        </template>

                        {{-- Add to Cart Button --}}
                        <button 
                            @click="
                                // Mengambil data langsung dari scope localSelectedVariasi...
                                // Ini adalah cara yang paling andal
                                addToCart(
                                    produk.id,
                                    produk.nama,
                                    produk.variasis.length > 0 ? localSelectedVariasiId : null,
                                    produk.variasis.length > 0 ? localSelectedVariasiName : null, // Nama variasi, bisa null
                                    produk.variasis.length > 0 ? localSelectedVariasiHarga : parseFloat(produk.harga),
                                    produk.variasis.length > 0 ? localSelectedVariasiStok : parseInt(produk.stok)
                                );
                            "
                            :disabled="produk.variasis.length > 0 ? 
                                (localSelectedVariasiId === null || localSelectedVariasiId === '' || localSelectedVariasiStok === 0) : 
                                (produk.stok === 0)"
                            class="w-full bg-blue text-white px-4 py-2 rounded hover:bg-blue mt-2 disabled:opacity-50 disabled:cursor-not-allowed">
                            + Tambah
                        </button>
                    </div>
                </template>
                <template x-if="filteredProduks.length === 0">
                    <p class="text-gray-500 col-span-full text-center">Tidak ada produk ditemukan dengan kata kunci ini.</p>
                </template>
            </div>
        </div>

        {{-- Cart & Payment Area --}}
        <div id="cart-section" class="bg-white rounded-lg shadow-md p-4">
            <h3 class="text-lg font-semibold mb-3 flex justify-between items-center">
                Keranjang Belanja
                <span class="bg-blue-600 text-white text-xs font-bold px-2 py-1 rounded-full" x-text="cart.length"></span>
            </h3>

            {{-- Cart Items --}}
            <div x-show="cart.length > 0" class="mb-4 max-h-60 overflow-y-auto border-b pb-2">
                <template x-for="(item, index) in cart" :key="index">
                    <div class="flex justify-between items-center mb-2 text-sm">
                        <div>
                            <span x-text="item.qty"></span>x <span x-text="item.namaProduk + (item.namaVariasi ? ' (' + item.namaVariasi + ')' : '')"></span>
                            <p class="text-xs text-gray-500">Rp <span x-text="formatRupiah(item.harga)"></span>/item</p>
                        </div>
                        <div class="flex items-center space-x-2">
                            <button @click="decreaseQty(index)" class="bg-gray-200 text-gray-700 px-2 rounded">-</button>
                            <span x-text="item.qty" class="font-bold"></span>
                            <button @click="increaseQty(index)" class="bg-gray-200 text-gray-700 px-2 rounded">+</button>
                            <button @click="removeFromCart(index)" class="text-red-500 text-lg">&times;</button>
                        </div>
                    </div>
                </template>
            </div>
            <p x-show="cart.length === 0" class="text-gray-500 text-center">Keranjang kosong.</p>

            {{-- Total --}}
            <div class="mt-4 text-right">
                <p class="text-md font-semibold">Total: Rp <span x-text="formatRupiah(calculateTotal())"></span></p>
            </div>

            {{-- Payment Method --}}
            <div class="mt-6 border-t pt-4">
                <h4 class="font-semibold mb-3">Metode Pembayaran</h4>
                <div class="flex space-x-4 mb-4">
                    <label class="inline-flex items-center">
                        <input type="radio" x-model="paymentMethod" value="cash" class="form-radio text-blue-600">
                        <span class="ml-2 text-md">Tunai</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="radio" x-model="paymentMethod" value="qris" class="form-radio text-blue-600">
                        <span class="ml-2 text-md">QRIS</span>
                    </label>
                </div>

                {{-- Cash Payment Details --}}
                <div x-show="paymentMethod === 'cash'">
                    <label for="amount_paid" class="block text-sm font-medium text-gray-700">Uang Diterima (Rp)</label>
                    <input type="number" x-model.number="amountPaid" @input="calculateChange()" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" placeholder="Masukkan jumlah uang">
                    <p class="text-md font-semibold mt-2">Kembalian: Rp <span x-text="formatRupiah(change)"></span></p>
                </div>

                {{-- QRIS Payment Details (No Change) --}}
                <div x-show="paymentMethod === 'qris'">
                    <p class="text-sm text-gray-600">Total pembayaran QRIS: Rp <span x-text="formatRupiah(calculateTotal())"></span></p>
                    {{-- Anda bisa menampilkan gambar QRIS di sini jika ada --}}
                </div>

                <button 
                    @click="processCheckout()" 
                    :disabled="cart.length === 0 || (paymentMethod === 'cash' && amountPaid < calculateTotal())"
                    class="w-full bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 mt-4 disabled:opacity-50 disabled:cursor-not-allowed">
                    Proses Pembayaran
                </button>
            </div>
        </div>
    </div>

    {{-- Alpine.js Script --}}
    <script>
        function kasirApp() {
            return {
                produks: @json($produks), // Mengambil data produk dari Laravel
                searchTerm: '', // Properti baru untuk menyimpan input pencarian
                cart: [],
                paymentMethod: 'cash',
                amountPaid: 0,
                change: 0,

                init() {
                    this.calculateChange();
                },

                // Computed property untuk memfilter produk berdasarkan searchTerm
                get filteredProduks() {
                    if (!this.produks) { // Tambahkan pengecekan jika produks belum terisi
                        return [];
                    }
                    if (!this.searchTerm) {
                        return this.produks; // Jika search term kosong, tampilkan semua produk
                    }
                    const lowerCaseSearchTerm = this.searchTerm.toLowerCase();
                    return this.produks.filter(produk => {
                        // Cari berdasarkan nama produk
                        if (produk.nama.toLowerCase().includes(lowerCaseSearchTerm)) {
                            return true;
                        }
                        // Cari berdasarkan nama kategori (jika ada)
                        if (produk.kategori && produk.kategori.nama_kategori.toLowerCase().includes(lowerCaseSearchTerm)) {
                            return true;
                        }
                        // Cari berdasarkan nama variasi (jika ada)
                        if (produk.variasis && produk.variasis.some(variasi => variasi.nama.toLowerCase().includes(lowerCaseSearchTerm))) {
                            return true;
                        }
                        return false;
                    });
                },

                addToCart(produkId, namaProduk, variasiId, namaVariasi, harga, stok) {
                    // Debugging: Log the received parameters
                    console.log('addToCart called with:');
                    console.log('  produkId:', produkId);
                    console.log('  namaProduk:', namaProduk);
                    console.log('  variasiId:', variasiId);
                    console.log('  namaVariasi:', namaVariasi);
                    console.log('  harga:', harga);
                    console.log('  stok:', stok);

                    if (stok <= 0) {
                        alert('Stok produk/variasi ini habis!');
                        return;
                    }
                    if (harga === 0 || isNaN(harga) || harga === null) { // Added null check for price
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
                            harga: parseFloat(harga), // Ensure harga is a float
                            qty: 1,
                            stokTersedia: parseInt(stok) // Ensure stokTersedia is an int
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
                            // Redirect ke halaman struk jika checkout berhasil
                            if (result.redirect_url) {
                                window.location.href = result.redirect_url;
                            } else {
                                // Fallback jika redirect_url tidak ada (seharusnya tidak terjadi)
                                alert(result.message || 'Transaksi berhasil!');
                                this.cart = []; // Clear cart on success if no redirect
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