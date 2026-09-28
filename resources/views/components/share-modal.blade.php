<div
    x-data="{
        open: false,
        title: '',
        text: '',
        url: '',
        price: '',
        image: '',
        copied: false,

        triggerShare(detail) {
            this.title = detail.title || 'Produk Eskasaba Marketplace';
            this.text = detail.text || ('Cek produk ' + (detail.title || 'ini') + ' di Eskasaba Marketplace!');
            this.url = detail.url || window.location.href;
            this.price = detail.price || '';
            this.image = detail.image || '';
            this.copied = false;
            this.open = true;
        },

        async copyLink() {
            try {
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    await navigator.clipboard.writeText(this.url);
                } else {
                    const input = document.createElement('input');
                    input.value = this.url;
                    document.body.appendChild(input);
                    input.select();
                    document.execCommand('copy');
                    document.body.removeChild(input);
                }
                this.copied = true;
                setTimeout(() => { this.copied = false; }, 3000);
            } catch (err) {
                console.error('Failed to copy link:', err);
            }
        },

        shareNative() {
            if (navigator.share) {
                navigator.share({
                    title: this.title,
                    text: this.text,
                    url: this.url,
                }).catch(() => {});
            }
        },

        get encodedUrl() { return encodeURIComponent(this.url); },
        get encodedText() { return encodeURIComponent(this.text + '\n' + this.url); },
        get whatsappUrl() { return 'https://api.whatsapp.com/send?text=' + this.encodedText; },
        get facebookUrl() { return 'https://www.facebook.com/sharer/sharer.php?u=' + this.encodedUrl; },
        get twitterUrl() { return 'https://twitter.com/intent/tweet?text=' + encodeURIComponent(this.text) + '&url=' + this.encodedUrl; },
        get telegramUrl() { return 'https://t.me/share/url?url=' + this.encodedUrl + '&text=' + encodeURIComponent(this.title); }
    }"
    @open-share-modal.window="triggerShare($event.detail)"
    x-effect="document.body.classList.toggle('overflow-hidden', open)"
    x-show="open"
    x-cloak
    class="relative z-[70]"
    aria-labelledby="share-modal-title"
    role="dialog"
    aria-modal="true"
>
    <!-- Modal Backdrop -->
    <div
        x-show="open"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="open = false"
        class="fixed inset-0 bg-slate-950/70 backdrop-blur-xs transition-opacity"
    ></div>

    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div
                x-show="open"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                @click.outside="open = false"
                class="relative w-full max-w-md transform overflow-hidden rounded-3xl bg-white p-6 text-left align-middle shadow-2xl transition-all dark:bg-slate-900 border border-slate-200 dark:border-slate-800"
            >
                <!-- Modal Header -->
                <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800">
                    <div class="flex items-center gap-2.5">
                        <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-700 dark:bg-emerald-950/80 dark:text-emerald-400">
                            <i class="fa-solid fa-share-nodes text-lg"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-black text-slate-900 dark:text-white" id="share-modal-title">
                                Bagikan Produk
                            </h3>
                            <p class="text-xs text-slate-400 font-medium">Bagikan ke media sosial atau salin link</p>
                        </div>
                    </div>
                    <button
                        type="button"
                        @click="open = false"
                        class="flex h-8 w-8 items-center justify-center rounded-xl bg-slate-100 text-slate-400 hover:bg-slate-200 hover:text-slate-600 dark:bg-slate-800 dark:hover:bg-slate-700 dark:hover:text-white cursor-pointer"
                    >
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <!-- Product Preview Card -->
                <div class="mt-4 flex items-center gap-3.5 rounded-2xl border border-slate-100 bg-slate-50 p-3 dark:border-slate-800/80 dark:bg-slate-800/40">
                    <template x-if="image">
                        <img :src="image" :alt="title" class="h-14 w-14 rounded-xl object-cover border border-slate-200 dark:border-slate-700 shrink-0">
                    </template>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-bold text-slate-900 dark:text-white truncate" x-text="title"></p>
                        <p x-show="price" class="text-xs font-black text-emerald-700 dark:text-emerald-400 mt-0.5" x-text="price"></p>
                        <p class="text-[11px] font-mono text-slate-400 truncate mt-0.5" x-text="url"></p>
                    </div>
                </div>

                <!-- Native Share (if supported) -->
                <div x-show="typeof navigator !== 'undefined' && navigator.share" class="mt-4">
                    <button
                        type="button"
                        @click="shareNative()"
                        class="flex w-full items-center justify-center gap-2 rounded-2xl bg-emerald-800 py-3 text-xs font-bold text-white shadow-xs hover:bg-emerald-900 transition cursor-pointer"
                    >
                        <i class="fa-solid fa-arrow-up-from-bracket"></i>
                        <span>Bagikan Lewat Aplikasi (Bawaan HP)</span>
                    </button>
                </div>

                <!-- Social Share Grid -->
                <div class="mt-4">
                    <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-2">Pilih Media Sosial:</p>
                    <div class="grid grid-cols-4 gap-2.5">
                        
                        <!-- WhatsApp -->
                        <a
                            :href="whatsappUrl"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="flex flex-col items-center justify-center gap-1.5 rounded-2xl border border-slate-100 bg-slate-50 p-3 text-emerald-600 transition hover:bg-emerald-50 hover:border-emerald-200 dark:border-slate-800 dark:bg-slate-800/60 dark:hover:bg-emerald-950/40"
                        >
                            <i class="fa-brands fa-whatsapp text-2xl"></i>
                            <span class="text-[10px] font-bold text-slate-700 dark:text-slate-300">WhatsApp</span>
                        </a>

                        <!-- Facebook -->
                        <a
                            :href="facebookUrl"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="flex flex-col items-center justify-center gap-1.5 rounded-2xl border border-slate-100 bg-slate-50 p-3 text-blue-600 transition hover:bg-blue-50 hover:border-blue-200 dark:border-slate-800 dark:bg-slate-800/60 dark:hover:bg-blue-950/40"
                        >
                            <i class="fa-brands fa-facebook text-2xl"></i>
                            <span class="text-[10px] font-bold text-slate-700 dark:text-slate-300">Facebook</span>
                        </a>

                        <!-- Twitter / X -->
                        <a
                            :href="twitterUrl"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="flex flex-col items-center justify-center gap-1.5 rounded-2xl border border-slate-100 bg-slate-50 p-3 text-slate-900 dark:text-white transition hover:bg-slate-100 hover:border-slate-300 dark:border-slate-800 dark:bg-slate-800/60 dark:hover:bg-slate-700/60"
                        >
                            <i class="fa-brands fa-x-twitter text-2xl"></i>
                            <span class="text-[10px] font-bold text-slate-700 dark:text-slate-300">X (Twitter)</span>
                        </a>

                        <!-- Telegram -->
                        <a
                            :href="telegramUrl"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="flex flex-col items-center justify-center gap-1.5 rounded-2xl border border-slate-100 bg-slate-50 p-3 text-sky-500 transition hover:bg-sky-50 hover:border-sky-200 dark:border-slate-800 dark:bg-slate-800/60 dark:hover:bg-sky-950/40"
                        >
                            <i class="fa-brands fa-telegram text-2xl"></i>
                            <span class="text-[10px] font-bold text-slate-700 dark:text-slate-300">Telegram</span>
                        </a>

                    </div>
                </div>

                <!-- Copy Link Input Box -->
                <div class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-800">
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">Salin Tautan Produk:</label>
                    <div class="flex items-center gap-2">
                        <input
                            type="text"
                            readonly
                            :value="url"
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-xs font-mono text-slate-700 outline-none dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300"
                        />
                        <button
                            type="button"
                            @click="copyLink()"
                            :class="copied ? 'bg-emerald-700 text-white' : 'bg-slate-900 text-white hover:bg-slate-800 dark:bg-emerald-800 dark:hover:bg-emerald-700'"
                            class="inline-flex items-center gap-1.5 shrink-0 rounded-xl px-4 py-2 text-xs font-bold transition cursor-pointer"
                        >
                            <i :class="copied ? 'fa-solid fa-check' : 'fa-solid fa-copy'"></i>
                            <span x-text="copied ? 'Tersalin!' : 'Salin'">Salin</span>
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
    if (typeof window.shareProduct !== 'function') {
        window.shareProduct = function (options) {
            window.dispatchEvent(new CustomEvent('open-share-modal', { detail: options }));
        };
    }
</script>
