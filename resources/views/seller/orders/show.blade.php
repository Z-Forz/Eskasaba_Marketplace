{{-- resources/views/seller/orders/show.blade.php --}}
<x-layouts.seller>
    <div class="container">
        <h1>Order Show</h1>
    </div>
</x-layouts.seller>
<x-layouts.seller>
    <div class="mx-auto max-w-5xl space-y-6">

        {{-- Header --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

            <div>
                <a
                    href="{{ route('seller.orders.index') }}"
                    class="mb-2 inline-flex items-center gap-1 text-sm font-medium text-blue-600 hover:text-blue-700">
                    ← Kembali ke Pesanan
                </a>

                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                    Pesanan #{{ $order->id }}
                </h1>

                <p class="mt-1 text-sm text-gray-500">
                    {{ $order->created_at?->format('d M Y, H:i') }}
                </p>
            </div>

            <x-badge
                :type="$order->status"
                :label="ucfirst(str_replace('_', ' ', $order->status))"
            />

        </div>

        {{-- Buyer --}}
        <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">

            <h2 class="text-lg font-bold text-gray-900 dark:text-white">
                Informasi Pembeli
            </h2>

            <div class="mt-4 grid gap-4 sm:grid-cols-2">

                <div>
                    <p class="text-xs uppercase tracking-wide text-gray-400">
                        Nama
                    </p>

                    <p class="mt-1 font-semibold text-gray-900 dark:text-white">
                        {{ $order->user?->name ?? '-' }}
                    </p>
                </div>

                <div>
                    <p class="text-xs uppercase tracking-wide text-gray-400">
                        Email
                    </p>

                    <p class="mt-1 font-semibold text-gray-900 dark:text-white">
                        {{ $order->user?->email ?? '-' }}
                    </p>
                </div>

            </div>

        </div>

        {{-- Items --}}
        <div class="rounded-2xl border border-gray-100 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">

            <div class="border-b border-gray-100 p-5 dark:border-gray-800">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white">
                    Produk Pesanan
                </h2>
            </div>

            <div class="divide-y divide-gray-100 dark:divide-gray-800">

                @foreach($order->items as $item)

                    <div class="flex flex-col gap-3 p-5 sm:flex-row sm:items-center sm:justify-between">

                        <div>
                            <p class="font-semibold text-gray-900 dark:text-white">
                                {{ $item->product_name }}
                            </p>

                            <p class="mt-1 text-sm text-gray-500">
                                {{ $item->quantity }} ×
                                Rp {{ number_format($item->unit_price, 0, ',', '.') }}
                            </p>
                        </div>

                        <p class="font-bold text-gray-900 dark:text-white">
                            Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                        </p>

                    </div>

                @endforeach

            </div>

            <div class="border-t border-gray-100 p-5 dark:border-gray-800">

                <div class="flex items-center justify-between">

                    <span class="font-semibold text-gray-700 dark:text-gray-300">
                        Total
                    </span>

                    <span class="text-xl font-bold text-blue-600">
                        Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                    </span>

                </div>

            </div>

        </div>

        {{-- Update Status --}}
        <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">

            <h2 class="text-lg font-bold text-gray-900 dark:text-white">
                Update Status Pesanan
            </h2>

            <form
                action="{{ route('seller.orders.update', $order) }}"
                method="POST"
                class="mt-4 flex flex-col gap-3 sm:flex-row">

                @csrf
                @method('PUT')

                <select
                    name="status"
                    class="flex-1 rounded-xl border-gray-200 bg-gray-50 px-4 py-3 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white">

                    <option value="pending" @selected($order->status === 'pending')}>
                        Menunggu
                    </option>

                    <option value="paid" @selected($order->status === 'paid')}>
                        Dibayar
                    </option>

                    <option value="processing" @selected($order->status === 'processing')}>
                        Diproses
                    </option>

                    <option value="ready" @selected($order->status === 'ready')}>
                        Siap Diambil
                    </option>

                    <option value="completed" @selected($order->status === 'completed')}>
                        Selesai
                    </option>

                    <option value="cancelled" @selected($order->status === 'cancelled')}>
                        Dibatalkan
                    </option>

                </select>

                <button
                    type="submit"
                    class="rounded-xl bg-blue-600 px-6 py-3 text-sm font-semibold text-white transition hover:bg-blue-700">
                    Simpan Status
                </button>

            </form>

        </div>

    </div>
</x-layouts.seller>