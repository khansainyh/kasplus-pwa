<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800">
            Chatbot KasPlus
        </h2>
    </x-slot>

    {{-- Main container for the chatbot module --}}
    <div class="w-full pb-10 flex flex-col h-full" x-data="chatbotApp()">
        {{-- Area Riwayat Chat --}}
        <div class="flex-grow overflow-y-auto p-4 bg-white rounded-lg shadow-md mb-4" x-ref="chatHistory">
            <template x-for="(message, index) in messages" :key="index">
                {{-- Sembunyikan pesan dengan role 'system' dari tampilan --}}
                <template x-if="message.role !== 'system'">
                    <div :class="{'text-right': message.role === 'user', 'text-left': message.role === 'assistant'}" class="mb-3">
                        <div :class="{
                                'bg-blue-500 text-white rounded-bl-lg rounded-tl-lg rounded-tr-lg inline-block px-4 py-2 max-w-[80%]': message.role === 'user',
                                'bg-gray-200 text-gray-800 rounded-br-lg rounded-tr-lg rounded-tl-lg inline-block px-4 py-2 max-w-[80%]': message.role === 'assistant'
                             }" 
                             x-html="message.content"
                             class="shadow-sm">
                        </div>
                    </div>
                </template>
            </template>
            <div x-show="isLoading" class="text-center text-gray-500 italic">
                Mengetik...
            </div>
        </div>

        {{-- Area Input Pesan --}}
        <div class="p-4 bg-white rounded-lg shadow-md flex items-center flex-shrink-0">
            <textarea x-model="newMessage" @keydown.enter.prevent="sendMessage" placeholder="Ketik pesan Anda..." 
                      class="flex-grow border-gray-300 rounded-md shadow-sm mr-3 p-2 resize-none" 
                      rows="1" style="min-height: 40px;"></textarea>
            <button @click="sendMessage" :disabled="isLoading || !newMessage.trim()" 
                    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed">
                Kirim
            </button>
        </div>
    </div>

    {{-- Alpine.js Script --}}
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    <script>
        function chatbotApp() {
            return {
                messages: [], // Array untuk menyimpan riwayat chat: { role: 'user'/'assistant'/'system', content: '...' }
                newMessage: '', // Model untuk input pesan baru
                isLoading: false, // Status loading saat AI berpikir

                init() {
                    // System Message (memberitahu AI perannya - TETAP ADA di array, TAPI TIDAK DITAMPILKAN)
                    this.messages.push({ 
                        role: 'system', 
                        content: 'Anda adalah chatbot Customer Service untuk aplikasi KasPlus. Anda dapat membantu pengguna menavigasi aplikasi dan memberikan informasi tentang keuangan, produk, dan laporan. Jawablah pertanyaan dengan sopan, informatif, dan ringkas. Jika diminta informasi spesifik, berikan ringkasan data yang relevan dari data yang saya berikan. Jika pengguna ingin pergi ke halaman tertentu, arahkan mereka dengan menyebutkan nama halamannya atau rutenya dalam format Markdown Link. Contoh: "[Pergi ke Dashboard](/dashboard)", "[Lihat Produk](/produk)". Jangan membuat informasi palsu. Jika Anda tidak memiliki data untuk menjawab, katakan "Maaf, saya tidak memiliki informasi tersebut.".' 
                    });
                    
                    // Pesan Sambutan Awal yang Terlihat Pengguna
                    this.messages.push({ 
                        role: 'assistant', 
                        content: 'Halo! Saya KasPlus Bot, asisten Anda. Saya bisa membantu Anda dengan informasi keuangan, produk, laporan, atau navigasi aplikasi. Silakan tanyakan apa saja yang Anda butuhkan!' 
                    });
                    this.$nextTick(() => this.scrollToBottom());
                },

                async sendMessage() {
                    if (!this.newMessage.trim() || this.isLoading) {
                        return;
                    }

                    const userMessageContent = this.newMessage.trim();
                    this.messages.push({ role: 'user', content: userMessageContent });
                    this.newMessage = ''; // Kosongkan input setelah dikirim
                    this.isLoading = true;
                    this.$nextTick(() => this.scrollToBottom()); // Scroll ke bawah setelah pesan user

                    try {
                        // Siapkan history untuk dikirim ke API LLaMA
                        // Kita hanya perlu mengirimkan role dan content untuk setiap pesan
                        const historyForApi = this.messages.map(msg => ({
                            role: msg.role, // Gunakan role asli, termasuk 'system'
                            content: msg.content 
                        }));
                        
                        const response = await fetch('{{ route('chatbot.ask') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({ 
                                prompt: userMessageContent, // Prompt user saat ini
                                history: historyForApi // Seluruh riwayat percakapan
                            })
                        });

                        const result = await response.json();

                        if (response.ok) {
                            const botResponseContent = marked.parse(result.response || 'Maaf, saya tidak mendapat jawaban.');
                            this.messages.push({ role: 'assistant', content: botResponseContent });
                        } else {
                            const errorMessage = result.error || 'Terjadi kesalahan saat menghubungi AI.';
                            this.messages.push({ role: 'assistant', content: 'Error: ' + errorMessage });
                            console.error('Server error:', result);
                        }
                    } catch (error) {
                        this.messages.push({ role: 'assistant', content: 'Error jaringan: Gagal terhubung ke AI.' });
                        console.error('Network error:', error);
                    } finally {
                        this.isLoading = false;
                        this.$nextTick(() => this.scrollToBottom()); // Scroll ke bawah setelah pesan bot
                    }
                },

                scrollToBottom() {
                    const historyDiv = this.$refs.chatHistory;
                    if (historyDiv) {
                        historyDiv.scrollTop = historyDiv.scrollHeight;
                    }
                }
            }
        }
    </script>
</x-app-layout>