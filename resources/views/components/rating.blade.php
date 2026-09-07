@props([
    'rating' => 0,
    'count' => null,
    'size' => 'sm',
])

@php
    $rating = max(0, min(5, (float) $rating));

    $sizeClass = match ($size) {
        'xs' => 'h-3.5 w-3.5',
        'md' => 'h-5 w-5',
        default => 'h-4 w-4',
    };
@endphp

<div class="flex items-center gap-1">

    <div class="flex items-center">

        @for($i = 1; $i <= 5; $i++)

            @if($rating >= $i)

                {{-- Full --}}
                <svg
                    class="{{ $sizeClass }} text-amber-400"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                >
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.539 1.118l-2.8-2.034a1 1 0 00-1.176 0l-2.8 2.034c-.783.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L3.72 8.72c-.783-.57-.38-1.81.588-1.81H7.77a1 1 0 00.95-.69l1.07-3.292z"/>
                </svg>

            @elseif($rating >= ($i - 0.5))

                {{-- Half --}}
                <div class="relative">

                    <svg
                        class="{{ $sizeClass }} text-slate-200"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                    >
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.539 1.118l-2.8-2.034a1 1 0 00-1.176 0l-2.8 2.034c-.783.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L3.72 8.72c-.783-.57-.38-1.81.588-1.81H7.77a1 1 0 00.95-.69l1.07-3.292z"/>
                    </svg>

                    <div class="absolute inset-y-0 left-0 w-1/2 overflow-hidden">

                        <svg
                            class="{{ $sizeClass }} text-amber-400"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                        >
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.539 1.118l-2.8-2.034a1 1 0 00-1.176 0l-2.8 2.034c-.783.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L3.72 8.72c-.783-.57-.38-1.81.588-1.81H7.77a1 1 0 00.95-.69l1.07-3.292z"/>
                        </svg>

                    </div>

                </div>

            @else

                {{-- Empty --}}
                <svg
                    class="{{ $sizeClass }} text-slate-200"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                >
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.539 1.118l-2.8-2.034a1 1 0 00-1.176 0l-2.8 2.034a1 1 0 00-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L3.72 8.72c-.783-.57-.38-1.81.588-1.81H7.77a1 1 0 00.95-.69l1.07-3.292z"/>
                </svg>

            @endif

        @endfor

    </div>

    <span class="text-xs font-medium text-slate-600">
        {{ number_format($rating, 1) }}
    </span>

    @if(!is_null($count))
        <span class="text-xs text-slate-400">
            ({{ $count }})
        </span>
    @endif

</div>