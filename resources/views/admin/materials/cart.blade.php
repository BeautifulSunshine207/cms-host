@extends('layouts.app')

@section('content')
@php
    $backUrl = url()->previous();
    if (str_contains($backUrl, '/materials/cart')) {
        $backUrl = route('materials.create');
    }
@endphp
<div class="min-h-screen bg-[#ECECEC] px-10 py-8">
    <a href="{{ $backUrl }}"
       class="inline-flex items-center gap-2 rounded-lg bg-[#F2C200] px-4 py-2 text-sm font-semibold text-black shadow-[0_8px_16px_rgba(0,0,0,0.2)]">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
            <path d="M15 6l-6 6 6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        Back
    </a>

    <div class="fixed inset-0 z-40 flex items-center justify-center bg-black/40 backdrop-blur-sm px-4">
        <div class="w-[700px] max-w-[92vw] rounded-2xl bg-[#3E3E3E] p-6 shadow-[0_18px_40px_rgba(0,0,0,0.4)]">
            <div class="grid grid-cols-12 text-sm font-semibold text-white bg-[#5A5A5A] rounded-lg px-4 py-2">
                <div class="col-span-6">Material</div>
                <div class="col-span-3">Price</div>
                <div class="col-span-3 text-right"> </div>
            </div>

            <div class="divide-y divide-gray-700">
                @forelse($cart as $key => $item)
                    <div class="grid grid-cols-12 items-center py-3 text-white px-4">
                        <div class="col-span-6">{{ $item['product_name'] }}</div>
                        <div class="col-span-3">{{ number_format($item['price'], 0) }}</div>
                        <div class="col-span-3 flex justify-end">
                            <form method="POST" action="{{ route('materials.cart.remove') }}">
                                @csrf
                                <input type="hidden" name="key" value="{{ $key }}">
                                <button class="rounded-md bg-[#F2C200] px-3 py-1 text-xs font-semibold text-black">
                                    Remove
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="py-8 text-center text-gray-200">cart is empty</div>
                @endforelse
            </div>

            <div class="mt-6 flex justify-center gap-4">
                <form id="cartCheckoutForm" method="POST" action="{{ route('materials.cart.checkout') }}">
                    @csrf
                    <button type="submit" class="w-[240px] rounded-xl bg-[#F2C200] py-2 text-sm font-semibold text-black shadow-[0_10px_20px_rgba(0,0,0,0.25)]">
                        Create
                    </button>
                </form>
                <button type="button" id="cartCloseBtn"
                   data-back-url="{{ $backUrl }}"
                   class="inline-flex w-[240px] items-center justify-center rounded-xl bg-[#F19C9C] py-2 text-sm font-semibold text-black shadow-[0_10px_20px_rgba(0,0,0,0.20)]">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script>
document.getElementById('cartCloseBtn')?.addEventListener('click', () => {
    const url = document.getElementById('cartCloseBtn')?.dataset?.backUrl;
    if (url) {
        window.location.href = url;
        return;
    }
    if (window.history.length > 1) {
        window.history.back();
    } else {
        window.location.href = '{{ route('materials.create') }}';
    }
});
</script>
@endpush
@endsection
