@if(\App\Support\Package::has('accessibility'))
<div x-data="accessibilityTool()"
     x-init="initAccessibility()"
     class="fixed bottom-6 right-6 z-50 print:hidden"
     id="accessibility-widget">

    <!-- Tombol Mengambang (Floating Action Button) -->
    <button @click="toggle()"
            id="accessibility-widget-toggle"
            type="button"
            class="group flex items-center gap-2.5 px-4 py-3 bg-primary text-on-primary hover:bg-primary-container rounded-full shadow-xl border-2 border-secondary focus:ring-4 focus:ring-secondary/50 focus:outline-none transition-all duration-200 transform hover:scale-105"
            aria-haspopup="dialog"
            :aria-expanded="open"
            aria-label="Pusat Bantuan Aksesibilitas Disabilitas (Tekan Alt+A untuk pintasan)"
            title="Aksesibilitas & Ramah Disabilitas (Alt + A)">
        <span class="material-symbols-outlined text-[24px] text-secondary group-hover:rotate-12 transition-transform">accessibility_new</span>
        <span class="font-bold text-xs tracking-wide hidden sm:inline-block">Aksesibilitas</span>
        <span class="sr-only">Buka menu aksesibilitas dan pembaca layar</span>
    </button>

    <!-- Panel Pengaturan Aksesibilitas (Modal / Drawer) -->
    <div x-show="open"
         x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-4 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-4 scale-95"
         @keydown.escape.window="close()"
         @click.outside="close()"
         class="absolute bottom-16 right-0 w-80 sm:w-96 bg-white rounded-2xl shadow-2xl border-2 border-primary overflow-hidden text-gray-800"
         role="dialog"
         aria-modal="true"
         aria-labelledby="acc-widget-title">

        <!-- Header Panel -->
        <div class="bg-primary text-on-primary p-4 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-secondary text-[22px]">accessibility_new</span>
                <div>
                    <h3 id="acc-widget-title" class="font-bold text-sm leading-tight text-white">Alat Aksesibilitas Publik</h3>
                    <p class="text-[10px] text-on-primary-container">Standar WCAG 2.1 AA &bull; Ramah Disabilitas</p>
                </div>
            </div>
            <button @click="close()"
                    type="button"
                    class="w-7 h-7 rounded-lg bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition"
                    aria-label="Tutup panel aksesibilitas">
                <span class="material-symbols-outlined text-[18px]">close</span>
            </button>
        </div>

        <div class="p-4 space-y-4 max-h-[75vh] overflow-y-auto text-xs">
            <!-- 1. Skala Ukuran Teks (Font Size) -->
            <div class="space-y-1.5">
                <div class="flex items-center justify-between">
                    <span class="font-bold text-gray-700 flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[16px] text-primary">format_size</span>
                        <span>Ukuran Teks (Huruf)</span>
                    </span>
                    <span class="text-[11px] font-mono text-gray-500" x-text="scaleLabel"></span>
                </div>
                <div class="grid grid-cols-4 gap-1.5 bg-gray-100 p-1 rounded-xl">
                    <button type="button"
                            @click="setFontScale('100')"
                            :class="fontScale === '100' ? 'bg-primary text-white shadow-xs' : 'bg-transparent text-gray-700 hover:bg-gray-200'"
                            class="py-1.5 rounded-lg font-bold text-xs transition"
                            aria-label="Ukuran normal 100 persen">
                        A (100%)
                    </button>
                    <button type="button"
                            @click="setFontScale('115')"
                            :class="fontScale === '115' ? 'bg-primary text-white shadow-xs' : 'bg-transparent text-gray-700 hover:bg-gray-200'"
                            class="py-1.5 rounded-lg font-bold text-xs transition"
                            aria-label="Ukuran sedang 115 persen">
                        A+ (115%)
                    </button>
                    <button type="button"
                            @click="setFontScale('130')"
                            :class="fontScale === '130' ? 'bg-primary text-white shadow-xs' : 'bg-transparent text-gray-700 hover:bg-gray-200'"
                            class="py-1.5 rounded-lg font-bold text-xs transition"
                            aria-label="Ukuran besar 130 persen">
                        A++ (130%)
                    </button>
                    <button type="button"
                            @click="setFontScale('145')"
                            :class="fontScale === '145' ? 'bg-primary text-white shadow-xs' : 'bg-transparent text-gray-700 hover:bg-gray-200'"
                            class="py-1.5 rounded-lg font-bold text-xs transition"
                            aria-label="Ukuran ekstra besar 145 persen">
                        A+++ (145%)
                    </button>
                </div>
            </div>

            <!-- 2. Kontras Warna & Tampilan -->
            <div class="space-y-1.5">
                <span class="font-bold text-gray-700 flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[16px] text-primary">contrast</span>
                    <span>Mode Tampilan &amp; Kontras</span>
                </span>
                <div class="grid grid-cols-3 gap-1.5">
                    <!-- Normal -->
                    <button type="button"
                            @click="setContrast('normal')"
                            :class="contrast === 'normal' ? 'border-primary bg-primary/5 text-primary font-bold' : 'border-gray-200 bg-white text-gray-700 hover:bg-gray-50'"
                            class="p-2 rounded-xl border text-center transition flex flex-col items-center gap-1"
                            aria-label="Mode kontras normal bawaan">
                        <span class="material-symbols-outlined text-[18px]">light_mode</span>
                        <span class="text-[11px]">Normal</span>
                    </button>
                    <!-- Kontras Tinggi (High Contrast) -->
                    <button type="button"
                            @click="setContrast('high')"
                            :class="contrast === 'high' ? 'border-black bg-black text-yellow-300 font-bold' : 'border-gray-200 bg-white text-gray-700 hover:bg-gray-50'"
                            class="p-2 rounded-xl border text-center transition flex flex-col items-center gap-1"
                            aria-label="Mode kontras tinggi hitam dan kuning">
                        <span class="material-symbols-outlined text-[18px]">hdr_auto</span>
                        <span class="text-[11px]">Kontras Tinggi</span>
                    </button>
                    <!-- Monokrom / Grayscale -->
                    <button type="button"
                            @click="setContrast('grayscale')"
                            :class="contrast === 'grayscale' ? 'border-gray-700 bg-gray-800 text-white font-bold' : 'border-gray-200 bg-white text-gray-700 hover:bg-gray-50'"
                            class="p-2 rounded-xl border text-center transition flex flex-col items-center gap-1"
                            aria-label="Mode hitam putih monokrom">
                        <span class="material-symbols-outlined text-[18px]">filter_b_and_w</span>
                        <span class="text-[11px]">Monokrom</span>
                    </button>
                </div>
            </div>

            <!-- 3. Text-to-Speech (TTS Bahasa Indonesia) -->
            <div class="space-y-2 p-3 bg-sky-50 rounded-xl border border-sky-200">
                <div class="flex items-center justify-between">
                    <span class="font-bold text-sky-950 flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[18px] text-sky-700">record_voice_over</span>
                        <span>Pembaca Suara (TTS)</span>
                    </span>
                    <span class="text-[10px] uppercase font-bold text-sky-700 bg-sky-100 px-2 py-0.5 rounded-full"
                          x-text="ttsStatus"></span>
                </div>
                <p class="text-[11px] text-sky-800 leading-tight">
                    Membacakan teks isi halaman ini secara bersuara dalam Bahasa Indonesia (Web Speech API).
                </p>

                <!-- Tombol Kontrol TTS -->
                <div class="flex items-center gap-1.5 pt-1">
                    <button type="button"
                            @click="speak()"
                            x-show="!isSpeaking"
                            class="flex-1 py-2 bg-primary text-white rounded-lg font-bold text-xs hover:bg-primary-container transition flex items-center justify-center gap-1.5">
                        <span class="material-symbols-outlined text-[16px]">play_arrow</span>
                        <span>Bacakan Halaman</span>
                    </button>

                    <button type="button"
                            @click="pauseResume()"
                            x-show="isSpeaking"
                            class="flex-1 py-2 bg-amber-600 text-white rounded-lg font-bold text-xs hover:bg-amber-700 transition flex items-center justify-center gap-1.5">
                        <span class="material-symbols-outlined text-[16px]" x-text="isPaused ? 'play_arrow' : 'pause'"></span>
                        <span x-text="isPaused ? 'Lanjutkan' : 'Jeda'"></span>
                    </button>

                    <button type="button"
                            @click="stopSpeaking()"
                            x-show="isSpeaking"
                            class="py-2 px-3 bg-rose-600 text-white rounded-lg font-bold text-xs hover:bg-rose-700 transition flex items-center justify-center gap-1"
                            title="Hentikan Pembacaan"
                            aria-label="Hentikan pembacaan suara">
                        <span class="material-symbols-outlined text-[16px]">stop</span>
                        <span>Berhenti</span>
                    </button>
                </div>

                <div x-show="isSpeaking" class="text-[10px] text-sky-700 flex items-center gap-1 pt-1" aria-live="polite">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                    <span>Sedang membaca konten aktif...</span>
                </div>
            </div>

            <!-- 4. Pintasan Keyboard Bantuan -->
            <div class="pt-1 border-t border-gray-200">
                <span class="font-bold text-gray-600 text-[11px] block mb-1">Navigasi Keyboard (WCAG 2.1 AA):</span>
                <div class="grid grid-cols-2 gap-1 text-[10px] text-gray-500">
                    <div><kbd class="px-1 py-0.5 bg-gray-100 rounded border border-gray-300 font-mono">Alt + A</kbd> Buka Panel</div>
                    <div><kbd class="px-1 py-0.5 bg-gray-100 rounded border border-gray-300 font-mono">Esc</kbd> Tutup Panel</div>
                    <div><kbd class="px-1 py-0.5 bg-gray-100 rounded border border-gray-300 font-mono">Tab</kbd> Pindah Elemen</div>
                    <div><kbd class="px-1 py-0.5 bg-gray-100 rounded border border-gray-300 font-mono">Enter</kbd> Aktifkan Tombol</div>
                </div>
            </div>

            <!-- Tombol Reset -->
            <div class="pt-1">
                <button type="button"
                        @click="resetAll()"
                        class="w-full py-1.5 text-center text-gray-500 hover:text-gray-800 text-[11px] font-medium border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                    &circlearrowleft; Kembalikan Pengaturan Standar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function accessibilityTool() {
    return {
        open: false,
        fontScale: '100',
        contrast: 'normal',
        isSpeaking: false,
        isPaused: false,
        synth: null,
        currentUtterance: null,

        get scaleLabel() {
            return this.fontScale === '100' ? 'Normal (100%)' :
                   this.fontScale === '115' ? 'Sedang (115%)' :
                   this.fontScale === '130' ? 'Besar (130%)' : 'Sangat Besar (145%)';
        },

        get ttsStatus() {
            if (this.isSpeaking && this.isPaused) return 'Jeda';
            if (this.isSpeaking) return 'Aktif';
            return 'Siap';
        },

        initAccessibility() {
            if ('speechSynthesis' in window) {
                this.synth = window.speechSynthesis;
            }

            // Pulihkan preferensi dari localStorage
            const savedScale = localStorage.getItem('hjt_font_scale');
            if (savedScale) {
                this.setFontScale(savedScale);
            }

            const savedContrast = localStorage.getItem('hjt_contrast_mode');
            if (savedContrast) {
                this.setContrast(savedContrast);
            }

            // Keyboard shortcut listener Alt+A
            window.addEventListener('keydown', (e) => {
                if (e.altKey && (e.key === 'a' || e.key === 'A')) {
                    e.preventDefault();
                    this.toggle();
                }
            });
        },

        toggle() {
            this.open = !this.open;
        },

        close() {
            this.open = false;
        },

        setFontScale(scale) {
            this.fontScale = scale;
            const root = document.documentElement;
            root.classList.remove('text-scale-100', 'text-scale-115', 'text-scale-130', 'text-scale-145');
            root.classList.add('text-scale-' + scale);
            localStorage.setItem('hjt_font_scale', scale);
        },

        setContrast(mode) {
            this.contrast = mode;
            const root = document.documentElement;
            root.classList.remove('high-contrast', 'grayscale-mode');

            if (mode === 'high') {
                root.classList.add('high-contrast');
            } else if (mode === 'grayscale') {
                root.classList.add('grayscale-mode');
            }

            localStorage.setItem('hjt_contrast_mode', mode);
        },

        speak(customText = null) {
            if (!this.synth) {
                alert('Browser Anda tidak mendukung fitur Text-to-Speech Web Speech API.');
                return;
            }

            this.stopSpeaking();

            let textToRead = customText;

            if (!textToRead) {
                // Ambil teks utama dari artikel atau main-content
                const mainEl = document.querySelector('article') || document.querySelector('#main-content') || document.body;
                // Clone untuk membersihkan script dan elemen widget
                const clone = mainEl.cloneNode(true);
                const toRemove = clone.querySelectorAll('#accessibility-widget, script, style, nav, footer, button');
                toRemove.forEach(el => el.remove());
                textToRead = (clone.innerText || clone.textContent).replace(/\s+/g, ' ').trim();
            }

            if (!textToRead || textToRead.length < 5) {
                textToRead = document.title || 'Halaman resmi instansi pemerintah.';
            }

            this.currentUtterance = new SpeechSynthesisUtterance(textToRead);
            this.currentUtterance.lang = 'id-ID';
            this.currentUtterance.rate = 1.0;
            this.currentUtterance.pitch = 1.0;

            // Cari suara bahasa Indonesia jika tersedia
            const voices = this.synth.getVoices();
            const idVoice = voices.find(v => v.lang.startsWith('id') || v.lang.includes('ID'));
            if (idVoice) {
                this.currentUtterance.voice = idVoice;
            }

            this.currentUtterance.onstart = () => {
                this.isSpeaking = true;
                this.isPaused = false;
            };

            this.currentUtterance.onend = () => {
                this.isSpeaking = false;
                this.isPaused = false;
            };

            this.currentUtterance.onerror = () => {
                this.isSpeaking = false;
                this.isPaused = false;
            };

            this.synth.speak(this.currentUtterance);
        },

        pauseResume() {
            if (!this.synth) return;

            if (this.isPaused) {
                this.synth.resume();
                this.isPaused = false;
            } else {
                this.synth.pause();
                this.isPaused = true;
            }
        },

        stopSpeaking() {
            if (this.synth) {
                this.synth.cancel();
            }
            this.isSpeaking = false;
            this.isPaused = false;
        },

        resetAll() {
            this.setFontScale('100');
            this.setContrast('normal');
            this.stopSpeaking();
            localStorage.removeItem('hjt_font_scale');
            localStorage.removeItem('hjt_contrast_mode');
        }
    };
}
</script>
@endif
