<div
    x-data="{
        open: false,
        title: 'Apakah Anda Yakin?',
        message: 'Apakah Anda yakin ingin melanjutkan tindakan ini?',
        confirmText: 'Ya, Yakin',
        cancelText: 'Batal',
        confirmVariant: 'danger',
        targetForm: null,
        callback: null,

        triggerConfirm(detail) {
            this.title = detail.title || 'Apakah Anda Yakin?';
            this.message = detail.message || 'Apakah Anda yakin ingin melanjutkan tindakan ini?';
            this.confirmText = detail.confirmText || 'Ya, Yakin';
            this.cancelText = detail.cancelText || 'Batal';
            this.confirmVariant = detail.variant || 'danger';
            this.targetForm = detail.form || null;
            this.callback = detail.callback || null;
            this.open = true;
        },

        onConfirm() {
            this.open = false;
            if (this.targetForm) {
                if (typeof this.targetForm === 'string') {
                    const f = document.getElementById(this.targetForm);
                    if (f) f.submit();
                } else if (this.targetForm instanceof HTMLFormElement) {
                    this.targetForm.submit();
                }
            } else if (typeof this.callback === 'function') {
                this.callback();
            }
        }
    }"
    @open-confirm-modal.window="triggerConfirm($event.detail)"
    x-show="open"
    x-cloak
    class="relative z-50"
    aria-labelledby="confirm-modal-title"
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
        class="fixed inset-0 bg-slate-950/70 backdrop-blur-xs transition-opacity"
        @click="open = false"
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
                class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg dark:bg-slate-900 dark:border dark:border-slate-800"
            >
                <div class="bg-white px-5 pt-6 pb-5 sm:p-6 sm:pb-5 dark:bg-slate-900">
                    <div class="sm:flex sm:items-start gap-4">
                        <!-- Icon Badge -->
                        <div
                            :class="{
                                'bg-red-100 text-red-600 dark:bg-red-950/70 dark:text-red-400': confirmVariant === 'danger',
                                'bg-emerald-100 text-emerald-600 dark:bg-emerald-950/70 dark:text-emerald-400': confirmVariant === 'primary' || confirmVariant === 'success',
                                'bg-amber-100 text-amber-600 dark:bg-amber-950/70 dark:text-amber-400': confirmVariant === 'warning'
                            }"
                            class="mx-auto flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl sm:mx-0"
                        >
                            <template x-if="confirmVariant === 'danger'">
                                <i class="fa-solid fa-triangle-exclamation text-xl"></i>
                            </template>
                            <template x-if="confirmVariant === 'primary' || confirmVariant === 'success'">
                                <i class="fa-solid fa-circle-question text-xl"></i>
                            </template>
                            <template x-if="confirmVariant === 'warning'">
                                <i class="fa-solid fa-circle-exclamation text-xl"></i>
                            </template>
                        </div>

                        <!-- Content -->
                        <div class="mt-3 text-center sm:mt-0 sm:text-left flex-1">
                            <h3 class="text-lg font-black leading-6 text-slate-900 dark:text-white" id="confirm-modal-title" x-text="title"></h3>
                            <div class="mt-2">
                                <p class="text-xs leading-relaxed text-slate-500 dark:text-slate-400" x-text="message"></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="bg-slate-50 px-5 py-4 sm:flex sm:flex-row-reverse sm:px-6 gap-3 dark:bg-slate-800/60">
                    <button
                        type="button"
                        @click="onConfirm()"
                        :class="{
                            'bg-red-600 hover:bg-red-700 text-white shadow-lg shadow-red-900/20': confirmVariant === 'danger',
                            'bg-emerald-700 hover:bg-emerald-800 text-white shadow-lg shadow-emerald-900/20': confirmVariant === 'primary' || confirmVariant === 'success',
                            'bg-amber-600 hover:bg-amber-700 text-white shadow-lg shadow-amber-900/20': confirmVariant === 'warning'
                        }"
                        class="inline-flex w-full justify-center rounded-xl px-5 py-2.5 text-xs font-bold transition sm:w-auto cursor-pointer"
                        x-text="confirmText"
                    ></button>

                    <button
                        type="button"
                        @click="open = false"
                        class="mt-2 inline-flex w-full justify-center rounded-xl border border-slate-300 bg-white px-5 py-2.5 text-xs font-bold text-slate-700 shadow-xs hover:bg-slate-100 sm:mt-0 sm:w-auto dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700 cursor-pointer"
                        x-text="cancelText"
                    ></button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    window.confirmAction = function(options) {
        window.dispatchEvent(new CustomEvent('open-confirm-modal', { detail: options }));
    };
</script>
