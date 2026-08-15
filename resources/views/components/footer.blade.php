<footer class="border-t border-slate-200 bg-white">

    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 sm:py-12 lg:px-8">

        <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-3">

            {{-- Brand --}}
            <div class="sm:col-span-2 lg:col-span-1">

                <div class="flex items-center gap-3">

                    @if(isset($settings) && $settings->logo)
                        <img
                            src="{{ asset('storage/' . $settings->logo) }}"
                            alt="{{ $settings->website_name ?? 'Eskasaba Market' }}"
                            class="h-9 w-9 rounded-xl object-cover"
                        >
                    @else
                        <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-slate-900 text-sm font-bold text-white">
                            E
                        </div>
                    @endif

                    <span class="font-bold text-slate-900">
                        {{ $settings->website_name ?? 'Eskasaba Market' }}
                    </span>

                </div>

                <p class="mt-4 max-w-md text-sm leading-6 text-slate-500">
                    {{ $settings->about ?? 'Marketplace internal sekolah untuk mendukung transaksi jual beli yang aman dan mudah.' }}
                </p>

            </div>

            {{-- Contact --}}
            <div>

                <h3 class="text-sm font-semibold text-slate-900">
                    Kontak
                </h3>

                <div class="mt-4 space-y-2.5 text-sm text-slate-500">

                    @if($settings?->address)
                        <p>{{ $settings->address }}</p>
                    @endif

                    @if($settings?->email)
                        <p class="break-all">
                            {{ $settings->email }}
                        </p>
                    @endif

                    @if($settings?->phone)
                        <p>{{ $settings->phone }}</p>
                    @endif

                </div>

            </div>

            {{-- Social --}}
            <div>

                <h3 class="text-sm font-semibold text-slate-900">
                    Media Sosial
                </h3>

                <div class="mt-4 flex flex-wrap gap-2">

                    @foreach([
                        'Instagram' => $settings?->instagram,
                        'Facebook' => $settings?->facebook,
                        'TikTok' => $settings?->tiktok,
                    ] as $platform => $url)

                        @if($url)
                            <a
                                href="{{ $url }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="rounded-xl border border-slate-200 px-3 py-2 text-xs font-medium text-slate-600 transition hover:border-slate-900 hover:text-slate-900"
                            >
                                {{ $platform }}
                            </a>
                        @endif

                    @endforeach

                </div>

            </div>

        </div>

        {{-- Copyright --}}
        <div class="mt-8 border-t border-slate-200 pt-6">

            <p class="text-center text-xs leading-5 text-slate-400">
                {{ $settings->copyright ?? '© ' . date('Y') . ' Eskasaba Market. All Rights Reserved.' }}
            </p>

        </div>

    </div>

</footer>