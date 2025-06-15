<x-app-layout>
    <x-slot name="header">
        
        <div class="flex items-center space-x-2">
            {{-- Tag <img> untuk logo KasPlus --}}
            <img src="{{ asset('images/kasplus_logo.png') }}" alt="KasPlus Logo" class="h-8 w-8">
            <h2 class="font-bold text-xl text-gray-800">
                KasPlus
            </h2>
        </div>
    </x-slot>

    <div class="py-0">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        {{-- Pesan Sambutan untuk Dashboard --}}
        <p class="text-xl font-semibold text-dark_gray mb-4">Welcome back, {{ Auth::user()->name }}!</p>
        {{-- Filter Utama - Diubah menjadi chips --}}
        <div class="mb-6">
            {{-- Wrapper untuk chips, menggunakan Alpine.js untuk state management --}}
            <div x-data="{ selectedPeriod: '{{ $periode }}' }" class="flex space-x-2 overflow-x-auto pb-2 hides-scrollbar">
                <button
                    @click="selectedPeriod = 'today'; $dispatch('period-changed', 'today')"
                    :class="selectedPeriod === 'today' ? 'bg-white text-navy_blue border-navy_blue' : 'bg-white text-gray border-gray hover:border-navy_blue hover:text-navy_blue'"
                    class="flex-none px-4 py-2 rounded-full border transition-colors duration-200 text-sm"
                >
                    Hari Ini
                </button>
                <button
                    @click="selectedPeriod = 'week'; $dispatch('period-changed', 'week')"
                    :class="selectedPeriod === 'week' ? 'bg-white text-navy_blue border-navy_blue' : 'bg-white text-gray border-gray hover:border-navy_blue hover:text-navy_blue'"
                    class="flex-none px-4 py-2 rounded-full border transition-colors duration-200 text-sm"
                >
                    Minggu Ini
                </button>
                <button
                    @click="selectedPeriod = 'month'; $dispatch('period-changed', 'month')"
                    :class="selectedPeriod === 'month' ? 'bg-white text-navy_blue border-navy_blue' : 'bg-white text-gray border-gray hover:border-navy_blue hover:text-navy_blue'"
                    class="flex-none px-4 py-2 rounded-full border transition-colors duration-200 text-sm"
                >
                    Bulan Ini
                </button>
                <button
                    @click="selectedPeriod = 'year'; $dispatch('period-changed', 'year')"
                    :class="selectedPeriod === 'year' ? 'bg-white text-navy_blue border-navy_blue' : 'bg-white text-gray border-gray hover:border-navy_blue hover:text-navy_blue'"
                    class="flex-none px-4 py-2 rounded-full border transition-colors duration-200 text-sm"
                >
                    Tahun Ini
                </button>
            </div>
        </div>

            {{-- Kartu KPI (Indikator Utama) - Diubah menjadi horizontal scroll --}}
            {{-- Tambahkan x-data untuk Alpine.js --}}
            <div x-data="{ 
                activeCard: 0, 
                cards: [], 
                init() {
                    this.cards = Array.from(this.$refs.kpiScroll.children);
                    this.updateActiveCard();
                    this.$refs.kpiScroll.addEventListener('scroll', () => {
                        this.updateActiveCard();
                    });
                },
                updateActiveCard() {
                    const scrollContainer = this.$refs.kpiScroll;
                    const containerWidth = scrollContainer.offsetWidth;
                    const scrollLeft = scrollContainer.scrollLeft;

                    let closestCardIndex = 0;
                    let minDistance = Infinity;

                    this.cards.forEach((card, index) => {
                        const cardCenter = card.offsetLeft + (card.offsetWidth / 2);
                        const containerCenter = scrollLeft + (containerWidth / 2);
                        const distance = Math.abs(cardCenter - containerCenter);

                        if (distance < minDistance) {
                            minDistance = distance;
                            closestCardIndex = index;
                        }
                    });
                    this.activeCard = closestCardIndex;
                }
            }" class="mb-6">
                <div x-ref="kpiScroll" class="flex overflow-x-auto pb-4 space-x-4 hides-scrollbar snap-x snap-mandatory">
                    {{-- Saldo Kas Total --}}
                    <div class="flex-none w-72 rounded-lg shadow-lg p-6 bg-navy_blue text-white snap-center">
                        <p class="text-sm font-medium text-gray-300 truncate">Saldo Kas Total</p>
                        <p class="mt-1 text-3xl font-semibold" id="kpi-saldo-kas">Rp {{ number_format($kpi['saldo_kas'], 0, ',', '.') }}</p>
                    </div>
                    
                    {{-- Pendapatan --}}
                    <div class="flex-none w-72 rounded-lg shadow-lg p-6 bg-blue text-white snap-center">
                        <p class="text-sm font-medium text-white truncate">Pendapatan</p>
                        <p class="mt-1 text-3xl font-semibold" id="kpi-pendapatan">Rp {{ number_format($kpi['pendapatan'], 0, ',', '.') }}</p>
                    </div>

                    {{-- Pengeluaran --}}
                    <div class="flex-none w-72 rounded-lg shadow-lg p-6 bg-orange text-white snap-center">
                        <p class="text-sm font-medium text-white truncate">Pengeluaran</p>
                        <p class="mt-1 text-3xl font-semibold" id="kpi-pengeluaran">Rp {{ number_format($kpi['pengeluaran'], 0, ',', '.') }}</p>
                    </div>
                    
                    {{-- Jumlah Transaksi --}}
                    <div class="flex-none w-72 rounded-lg shadow-lg p-6 bg-white border border-navy_blue snap-center">
                        <p class="text-sm font-medium text-gray-500 truncate">Jumlah Transaksi</p>
                        <p class="mt-1 text-3xl font-semibold text-gray-900" id="kpi-jumlah-transaksi">{{ $kpi['jml_transaksi'] }}</p>
                    </div>
                </div>

                {{-- Indikator Scroll --}}
                <div class="flex justify-center mt-4 space-x-2">
                    <template x-for="(card, index) in cards" :key="index">
                        <div 
                            class="w-3 h-3 rounded-full transition-all duration-300"
                            :class="{ 'bg-navy_blue': activeCard === index, 'bg-light_gray': activeCard !== index }"
                        ></div>
                    </template>
                </div>
            </div>

            {{-- Area untuk Grafik --}}
            <div class="mt-8 grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white p-4 rounded-lg shadow-sm"><h3 class="font-semibold text-lg text-gray-800 mb-4">Tren Pendapatan</h3><canvas id="trendPendapatanChart"></canvas></div>
                    <div class="bg-white p-4 rounded-lg shadow-sm"><h3 class="font-semibold text-lg text-gray-800 mb-4">Pemasukan vs Pengeluaran</h3><canvas id="pemasukanPengeluaranChart"></canvas></div>
                </div>
                <div class="space-y-6">
                    <div class="bg-white p-4 rounded-lg shadow-sm"><h3 class="font-semibold text-lg text-gray-800 mb-4">Top 3 Produk Terlaris</h3><canvas id="topProdukChart"></canvas></div>
                    <div class="bg-white p-4 rounded-lg shadow-sm"><h3 class="font-semibold text-lg text-gray-800 mb-4">Stok Segera Habis</h3><div id="stokMenipisContainer"></div></div>
                </div>
            </div>
        </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    Chart.defaults.font.family = 'Satoshi, sans-serif';
    // Konfigurasi awal
    let trendChart, pveChart, topProdukChart;
    const stokContainer = document.getElementById('stokMenipisContainer');
    
    // Konteks untuk setiap canvas
    const ctxTrend = document.getElementById('trendPendapatanChart').getContext('2d');
    const ctxPvE = document.getElementById('pemasukanPengeluaranChart').getContext('2d');
    const ctxTopProduk = document.getElementById('topProdukChart').getContext('2d');

    // Fungsi format Rupiah
    function formatRupiah(number) { return 'Rp ' + new Intl.NumberFormat('id-ID').format(number); }

    // Fungsi untuk render semua komponen
    function renderDashboard(data) {
        // 1. Update Kartu KPI
        document.getElementById('kpi-saldo-kas').textContent = formatRupiah(data.kpi.saldo_kas);
        document.getElementById('kpi-pendapatan').textContent = formatRupiah(data.kpi.pendapatan);
        document.getElementById('kpi-pengeluaran').textContent = formatRupiah(data.kpi.pengeluaran);
        document.getElementById('kpi-jumlah-transaksi').textContent = data.kpi.jml_transaksi;

        // 2. Render Grafik Tren Pendapatan
        if (trendChart) trendChart.destroy();
        trendChart = new Chart(ctxTrend, { 
            type: 'line', 
            data: { 
                labels: data.grafik_pendapatan.labels, 
                datasets: [{ 
                    label: 'Pendapatan', 
                    data: data.grafik_pendapatan.data, 
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.1, 
                    fill: true 
                }] 
            }, 
            options: { 
                responsive: true, 
                scales: { 
                    y: { 
                        beginAtZero: true 
                    } 
                },
                plugins: {
                    legend: {
                        labels: {
                            generateLabels: function(chart) {
                                const labels = Chart.defaults.plugins.legend.labels.generateLabels(chart);
                                labels.forEach(label => {
                                    if (label.text === 'Pendapatan') {
                                        label.fillStyle = 'rgb(59, 130, 246)';
                                    }
                                });
                                return labels;
                            }
                        }
                    }
                }
            } 
        });

        // 3. Render Grafik Pemasukan vs Pengeluaran
        if (pveChart) pveChart.destroy();
        pveChart = new Chart(ctxPvE, { 
            type: 'bar', 
            data: { 
                labels: data.grafik_pemasukan_pengeluaran.labels, 
                datasets: [ 
                    { 
                        label: 'Pemasukan', 
                        data: data.grafik_pemasukan_pengeluaran.pemasukan, 
                        backgroundColor: 'rgb(59, 130, 246)'
                    }, 
                    { 
                        label: 'Pengeluaran', 
                        data: data.grafik_pemasukan_pengeluaran.pengeluaran, 
                        backgroundColor: 'rgb(249, 115, 22)'
                    } 
                ] 
            }, 
            options: { 
                responsive: true, 
                scales: { 
                    y: { 
                        beginAtZero: true 
                    } 
                } 
            } 
        });

        // 4. Render Grafik Top 3 Produk
        if (topProdukChart) topProdukChart.destroy();
        topProdukChart = new Chart(ctxTopProduk, { 
            type: 'pie', 
            data: { 
                labels: data.top_produk.labels, 
                datasets: [{ 
                    label: 'Jumlah Terjual', 
                    data: data.top_produk.data, 
                    backgroundColor: ['rgb(19, 81, 153)', 'rgb(45, 90, 247)', 'rgb(245, 98, 2)', 'rgb(107, 114, 128)'] 
                }] 
            }, 
            options: { responsive: true } 
        });
        
        // 5. Render Kartu Stok Menipis
        stokContainer.innerHTML = ''; // Kosongkan dulu
        if (data.stok_menipis.length > 0) {
            const list = document.createElement('ul');
            list.className = 'text-sm space-y-2';
            data.stok_menipis.forEach(item => {
                const listItem = document.createElement('li');
                listItem.className = 'flex justify-between items-center';
                
                // ========================================================== //
                // KODE YANG DIPERBAIKI ADA DI BARIS BERIKUT                  //
                // Menggunakan backticks (`) untuk membuat template literal   //
                // ========================================================== //
                listItem.innerHTML = `<span>${item.produk.nama} (${item.nama})</span> <span class="font-bold text-red-600">Sisa ${item.stok}</span>`;

                list.appendChild(listItem);
            });
            stokContainer.appendChild(list);
        } else {
            stokContainer.innerHTML = '<p class="text-sm text-gray-500">Semua stok aman.</p>';
        }
    }

    // Fungsi untuk mengambil data dari server
    async function fetchAndUpdateDashboard(period) {
        try {
            document.body.style.cursor = 'wait';
            const response = await fetch(`{{ route('dashboard.data') }}?period=${period}`);
            if (!response.ok) throw new Error('Network response was not ok');
            const data = await response.json();
            renderDashboard(data);
        } catch (error) {
            console.error('Error fetching dashboard data:', error);
            alert('Gagal memuat data dashboard. Lihat console untuk detail.');
        } finally {
            document.body.style.cursor = 'default';
        }
    }

    // Panggil fungsi render pertama kali dengan data dari PHP
    renderDashboard({
        kpi: @json($kpi),
        grafik_pendapatan: @json($grafik_pendapatan),
        grafik_pemasukan_pengeluaran: @json($grafik_pemasukan_pengeluaran),
        top_produk: @json($top_produk),
        stok_menipis: @json($stok_menipis)
    });

    // Listener untuk event saat filter periode diubah
    document.addEventListener('period-changed', (e) => {
        fetchAndUpdateDashboard(e.detail); 
    });
});
</script>
</x-app-layout>