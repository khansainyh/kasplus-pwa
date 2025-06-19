<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-2">
            <img src="{{ asset('images/kasplus_logo.png') }}" alt="KasPlus Logo" class="h-8 w-8">
            <h2 class="font-bold text-xl text-[#131951]"> {{-- Menggunakan kode HEX langsung --}}
                KasPlus
            </h2>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <p class="text-2xl font-semibold text-[#0D0D0D] mb-3 tracking-tight">Welcome back, {{ Auth::user()->name }}!</p> {{-- Menggunakan kode HEX langsung --}}
        <div x-data="{ selectedPeriod: 'today' }" class="flex space-x-1 overflow-x-auto pb-2 hides-scrollbar mb-2">
            <button
                @click="selectedPeriod = 'today'; $dispatch('period-changed', 'today')"
                :class="selectedPeriod === 'today' ? 'bg-[#2D5AF7]/15 text-[#131951] border-0' : 'bg-white text-[#7B7B7B] border-[#7B7B7B] hover:border-[#131951] hover:text-[#131951]'"
                class="flex-none px-4 rounded-full border-px transition-colors duration-200 h-8 text-sm flex items-center justify-center font-medium"
            >
                Hari Ini
            </button>
            <button
                @click="selectedPeriod = 'week'; $dispatch('period-changed', 'week')"
                :class="selectedPeriod === 'week' ? 'bg-[#2D5AF7]/15 text-[#131951] border-0' : 'bg-white text-[#7B7B7B] border-[#7B7B7B] hover:border-[#131951] hover:text-[#131951]'"
                class="flex-none px-4 rounded-full border-px transition-colors duration-200 h-8 text-sm flex items-center justify-center font-medium"
            >
                Minggu Ini
            </button>
            <button
                @click="selectedPeriod = 'month'; $dispatch('period-changed', 'month')"
                :class="selectedPeriod === 'month' ? 'bg-[#2D5AF7]/15 text-[#131951] border-0' : 'bg-white text-[#7B7B7B] border-[#7B7B7B] hover:border-[#131951] hover:text-[#131951]'"
                class="flex-none px-4 rounded-full border-px transition-colors duration-200 h-8 text-sm flex items-center justify-center font-medium"
            >
                Bulan Ini
            </button>
            <button
                @click="selectedPeriod = 'year'; $dispatch('period-changed', 'year')"
                :class="selectedPeriod === 'year' ? 'bg-[#2D5AF7]/15 text-[#131951] border-0' : 'bg-white text-[#7B7B7B] border-[#7B7B7B] hover:border-[#131951] hover:text-[#131951]'"
                class="flex-none px-4 rounded-full border-px transition-colors duration-200 h-8 text-sm flex items-center justify-center font-medium"
            >
                Tahun Ini
            </button>
        </div>

            {{-- Kartu KPI (Indikator Utama) --}}
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
            }" class="mb-2">
                <div x-ref="kpiScroll" class="flex overflow-x-auto pb-4 space-x-4 hides-scrollbar snap-x snap-mandatory">
                    {{-- Saldo Kas Total --}}
                    <div class="flex-none w-72 rounded-lg shadow-md p-6 bg-[#131951] text-white snap-center"> {{-- UBAH INI: shadow-lg menjadi shadow-md --}}
                        <p class="text-sm font-medium text-gray-300 truncate">Saldo Kas Total</p>
                        <p class="mt-1 text-3xl font-semibold" id="kpi-saldo-kas">Rp {{ number_format($kpi['saldo_kas'], 0, ',', '.') }}</p>
                    </div>

                    {{-- Pendapatan --}}
                    <div class="flex-none w-72 rounded-lg shadow-md p-6 bg-[#2D5AF7] text-white snap-center"> {{-- UBAH INI: shadow-lg menjadi shadow-md --}}
                        <p class="text-sm font-medium text-white truncate">Pendapatan</p>
                        <p class="mt-1 text-3xl font-semibold" id="kpi-pendapatan">Rp {{ number_format($kpi['pendapatan'], 0, ',', '.') }}</p>
                    </div>

                    {{-- Pengeluaran --}}
                    <div class="flex-none w-72 rounded-lg shadow-md p-6 bg-[#F65C02] text-white snap-center"> {{-- UBAH INI: shadow-lg menjadi shadow-md --}}
                        <p class="text-sm font-medium text-white truncate">Pengeluaran</p>
                        <p class="mt-1 text-3xl font-semibold" id="kpi-pengeluaran">Rp {{ number_format($kpi['pengeluaran'], 0, ',', '.') }}</p>
                    </div>

                    {{-- Jumlah Transaksi --}}
                    <div class="flex-none w-72 rounded-lg shadow-md p-6 bg-white border border-[#131951] snap-center"> {{-- UBAH INI: shadow-lg menjadi shadow-md --}}
                        <p class="text-sm font-medium text-gray-500 truncate">Jumlah Transaksi</p>
                        <p class="mt-1 text-3xl font-semibold text-gray-900" id="kpi-jumlah-transaksi">{{ $kpi['jml_transaksi'] }}</p>
                    </div>
                </div>

                {{-- Indikator Scroll --}}
                <div class="flex justify-center mt-2 space-x-2">
                    <template x-for="(card, index) in cards" :key="index">
                        <div
                            class="w-2 h-2 rounded-full transition-all duration-300"
                            :class="{ 'bg-[#131951]': activeCard === index, 'bg-[#D3D3D3]': activeCard !== index }"
                        ></div> {{-- UBAH INI: class bg-navy_blue & bg-light_gray --}}
                    </template>
                </div>
            </div>

            {{-- Area untuk Grafik --}}
            <div class="mt-2 grid grid-cols-1 lg:grid-cols-3 gap-4">
                <div class="lg:col-span-2 space-y-4">
                    {{-- Container chart untuk Tren Pendapatan --}}
                    <div class="bg-white p-4 rounded-xl shadow-sm h-80 lg:h-96 flex flex-col"> {{-- UBAH INI: shadow-md menjadi shadow-sm --}}
                        <h3 class="text-2xl font-bold text-[#131951] mb-3 tracking-tight">Tren Pendapatan</h3>
                        <div class="flex-grow">
                            <canvas id="trendPendapatanChart"></canvas>
                        </div>
                    </div>
                    {{-- Container chart untuk Pemasukan vs Pengeluaran --}}
                    <div class="bg-white p-4 rounded-xl shadow-sm h-80 lg:h-96 flex flex-col"> {{-- UBAH INI: shadow-md menjadi shadow-sm --}}
                        <h3 class="text-2xl font-bold text-[#131951] mb-3 tracking-tight">Pemasukan vs Pengeluaran</h3>
                        <div class="flex-grow">
                            <canvas id="pemasukanPengeluaranChart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="space-y-4">
                    {{-- Container chart untuk Top 3 Produk Terlaris --}}
                    <div class="bg-white p-4 rounded-xl shadow-sm h-80 lg:h-96 flex flex-col"> {{-- UBAH INI: shadow-md menjadi shadow-sm --}}
                        <h3 class="text-2xl font-bold text-[#131951] mb-1 tracking-tight">Top 3 Produk Terlaris</h3> {{-- UBAH INI: mb-3 menjadi mb-1 --}}
                        <div class="flex-grow">
                            <canvas id="topProdukChart"></canvas>
                        </div>
                    </div>
                    {{-- Container chart untuk Stok Segera Habis --}}
                    <div class="bg-white p-4 rounded-xl shadow-sm lg:h-auto"> {{-- UBAH INI: shadow-md menjadi shadow-sm, h-64 dihapus --}}
                        <h3 class="text-2xl font-bold text-[#131951] mb-3 tracking-tight">Stok Segera Habis</h3>
                        <div id="stokMenipisContainer"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    Chart.defaults.font.family = 'Satoshi, sans-serif';
    // Mengatur default tooltip untuk tampilan yang konsisten dan rounded
    Chart.defaults.plugins.tooltip.enabled = true;
    Chart.defaults.plugins.tooltip.mode = 'index';
    Chart.defaults.plugins.tooltip.intersect = false;
    Chart.defaults.plugins.tooltip.backgroundColor = 'rgba(0,0,0,0.7)';
    Chart.defaults.plugins.tooltip.bodyColor = '#fff';
    Chart.defaults.plugins.tooltip.titleColor = '#fff';
    Chart.defaults.plugins.tooltip.padding = 10;
    Chart.defaults.plugins.tooltip.cornerRadius = 8; // Efek rounded pada tooltip

    let trendChart, pveChart, topProdukChart;
    const stokContainer = document.getElementById('stokMenipisContainer');

    const ctxTrend = document.getElementById('trendPendapatanChart').getContext('2d');
    const ctxPvE = document.getElementById('pemasukanPengeluaranChart').getContext('2d');
    const ctxTopProduk = document.getElementById('topProdukChart').getContext('2d');

    // Fungsi format Rupiah
    function formatRupiah(number) { return 'Rp ' + new Intl.NumberFormat('id-ID').format(number); }

    function renderDashboard(data) {
        // Update KPI Cards
        document.getElementById('kpi-saldo-kas').textContent = formatRupiah(data.kpi.saldo_kas);
        document.getElementById('kpi-pendapatan').textContent = formatRupiah(data.kpi.pendapatan);
        document.getElementById('kpi-pengeluaran').textContent = formatRupiah(data.kpi.pengeluaran);
        document.getElementById('kpi-jumlah-transaksi').textContent = data.kpi.jml_transaksi;

        // Render Tren Pendapatan Chart
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
                    tension: 0.4, // Membuat garis lebih halus/rounded
                    fill: true,
                    pointRadius: 0, // Sembunyikan titik pada garis secara default
                    pointHoverRadius: 6, // Tampilkan titik saat hover
                    pointHoverBackgroundColor: 'rgb(59, 130, 246)',
                    pointHoverBorderColor: '#fff',
                    pointHoverBorderWidth: 2,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false, // PENTING: Mematikan aspek rasio bawaan agar chart mengisi container
                scales: {
                    x: {
                        grid: {
                            display: false // Sembunyikan garis grid X
                        },
                        ticks: {
                            color: '#6B7280' // Warna label sumbu X
                        },
                        border: {
                            display: false // Sembunyikan garis sumbu X
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(203, 213, 225, 0.5)', // Warna garis grid Y yang lebih terang
                            drawBorder: false // Sembunyikan border sumbu Y
                        },
                        ticks: {
                            color: '#6B7280', // Warna label sumbu Y
                            callback: function(value) {
                                return formatRupiah(value); // Format label Y menjadi Rupiah
                            }
                        },
                        border: {
                            display: false // Sembunyikan garis sumbu Y
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false // Sembunyikan legend untuk tampilan yang lebih bersih
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += formatRupiah(context.parsed.y);
                                }
                                return label;
                            }
                        }
                    }
                }
            }
        });

        // Render Pemasukan vs Pengeluaran Chart
        if (pveChart) pveChart.destroy();
        pveChart = new Chart(ctxPvE, {
            type: 'bar',
            data: {
                labels: data.grafik_pemasukan_pengeluaran.labels,
                datasets: [
                    {
                        label: 'Pemasukan',
                        data: data.grafik_pemasukan_pengeluaran.pemasukan,
                        backgroundColor: 'rgb(59, 130, 246)',
                        borderRadius: 8, // Rounded bars
                    },
                    {
                        label: 'Pengeluaran',
                        data: data.grafik_pemasukan_pengeluaran.pengeluaran,
                        backgroundColor: 'rgb(249, 115, 22)',
                        borderRadius: 8, // Rounded bars
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false, // PENTING: Mematikan aspek rasio bawaan agar chart mengisi container
                scales: {
                    x: {
                        grid: {
                            display: false // Sembunyikan garis grid X
                        },
                        ticks: {
                            color: '#6B7280' // Warna label sumbu X
                        },
                        border: {
                            display: false // Sembunyikan garis sumbu X
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(203, 213, 225, 0.5)', // Warna garis grid Y yang lebih terang
                            drawBorder: false // Sembunyikan border sumbu Y
                        },
                        ticks: {
                            color: '#6B7280', // Warna label sumbu Y
                            callback: function(value) {
                                return formatRupiah(value); // Format label Y menjadi Rupiah
                            }
                        },
                        border: {
                            display: false // Sembunyikan garis sumbu Y
                        }
                    }
                },
                plugins: {
                    legend: {
                        position: 'top', // Posisi legend di atas
                        labels: {
                            boxWidth: 10, // Ukuran kotak warna di legend
                            usePointStyle: true, // Gunakan bentuk lingkaran untuk legend item
                            color: '#374151' // Warna teks legend
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += formatRupiah(context.parsed.y);
                                }
                                return label;
                            }
                        }
                    }
                }
            }
        });

        // Render Top 3 Produk Terlaris Chart
        if (topProdukChart) topProdukChart.destroy();
        topProdukChart = new Chart(ctxTopProduk, {
            type: 'doughnut',
            data: {
                labels: data.top_produk.labels,
                datasets: [{
                    label: 'Jumlah Terjual',
                    data: data.top_produk.data,
                    backgroundColor: ['rgb(19, 81, 153)', 'rgb(45, 90, 247)', 'rgb(245, 98, 2)', 'rgb(107, 114, 128)'],
                    hoverOffset: 8,
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right', // Tetap di kanan
                        align: 'center', // Penjajaran legend item ke tengah
                        labels: {
                            boxWidth: 10, // Ukuran kotak warna di legend
                            usePointStyle: true, // Gunakan bentuk lingkaran untuk legend item
                            color: '#374151', // Warna teks legend
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed !== null) {
                                    label += context.parsed + ' pcs';
                                }
                                return label;
                            }
                        }
                    },
                    layout: {
                        padding: {
                            top: -25, // <--- SESUAIKAN NILAI NEGATIF INI JIKA PERLU
                            left: 0,
                            right: 0,
                            bottom: 0
                        }
                    }
                }
            }
        });

        // Render Stok Segera Habis List
        stokContainer.innerHTML = '';
        if (data.stok_menipis.length > 0) {
            const list = document.createElement('ul');
            list.className = 'text-sm space-y-2';
            data.stok_menipis.forEach(item => {
                const listItem = document.createElement('li');
                listItem.className = 'flex justify-between items-center p-2 bg-gray-50 rounded-md';
                listItem.innerHTML = `<span><span class="math-inline">\{item\.produk\.nama\} \(</span>{item.nama})</span> <span class="font-bold text-red-600">Sisa ${item.stok}</span>`;
                list.appendChild(listItem);
            });
            stokContainer.appendChild(list);
        } else {
            stokContainer.innerHTML = '<p class="text-sm text-gray-500 p-2 bg-gray-50 rounded-md">Semua stok aman.</p>';
        }
    }

    // Fungsi untuk mengambil dan memperbarui data dashboard
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

    // Render dashboard pertama kali dengan data awal dari server
    renderDashboard({
        kpi: @json($kpi),
        grafik_pendapatan: @json($grafik_pendapatan),
        grafik_pemasukan_pengeluaran: @json($grafik_pemasukan_pengeluaran),
        top_produk: @json($top_produk),
        stok_menipis: @json($stok_menipis)
    });

    // Dengarkan event 'period-changed' dari filter periode
    document.addEventListener('period-changed', (e) => {
        fetchAndUpdateDashboard(e.detail);
    });
});
</script>
</x-app-layout>