@extends('layouts.app')
@php use Illuminate\Support\Facades\Storage; @endphp

@section('content')
<div class="min-h-screen bg-[#ECECEC] px-10 py-8">
    <div class="flex items-start justify-between gap-6">
        <h1 class="text-2xl font-bold text-[#111]">Material Management</h1>

        <div class="w-[520px] max-w-full">
            <div class="relative">
                <input type="text" placeholder="Search"
                       class="w-full rounded-full border border-gray-400 bg-[#EDEDED] px-6 py-2 pr-12 text-sm outline-none">
                <div class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-600">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                        <path d="M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15Z" stroke="currentColor" stroke-width="2"/>
                        <path d="M16.5 16.5 21 21" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-6 grid grid-cols-12 gap-8">
        <div class="col-span-12 lg:col-span-3 space-y-6">
            <div class="rounded-2xl bg-[#3E3E3E] p-6 shadow-[0_10px_22px_rgba(0,0,0,0.12)]">
                <div class="flex items-start gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-[#1F1F1F]">
                        <div class="grid grid-cols-2 gap-1">
                            <span class="h-2 w-2 rounded-sm bg-[#F2C200]"></span>
                            <span class="h-2 w-2 rounded-sm bg-[#F2C200]"></span>
                            <span class="h-2 w-2 rounded-sm bg-[#F2C200]"></span>
                            <span class="h-2 w-2 rounded-sm bg-[#F2C200]"></span>
                        </div>
                    </div>
                    <div>
                        <p class="text-white font-semibold">View Materials</p>
                        <p class="mt-1 text-xs leading-4 text-gray-300">view all materials currently stored in the system.</p>
                    </div>
                </div>
                <a href="{{ route('materials.overview') }}"
                   class="mt-5 inline-flex w-full items-center justify-center rounded-xl border border-gray-300 py-2 text-sm font-semibold text-white">
                    Overview
                </a>
            </div>

            <div class="rounded-2xl bg-[#3E3E3E] p-6 shadow-[0_10px_22px_rgba(0,0,0,0.12)]">
                <div class="flex items-start gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-[#1F1F1F] text-[#F2C200]">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                            <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-white font-semibold">Create Material</p>
                        <p class="mt-1 text-xs leading-4 text-gray-300">add new material through supplier</p>
                    </div>
                </div>
                <a href="{{ route('materials.create') }}"
                   class="mt-5 inline-flex w-full items-center justify-center rounded-xl bg-[#F2C200] py-2 text-sm font-semibold text-black">
                    Create
                </a>
            </div>

            <div class="rounded-2xl bg-white p-5 shadow-[0_10px_22px_rgba(0,0,0,0.12)]">
                <div class="flex items-center gap-3">
                    <div class="h-14 w-14 rounded-full border border-gray-200 bg-gray-50 flex items-center justify-center text-xs text-gray-500 overflow-hidden">
                        @if($supplier->logo_path)
                            <img src="{{ Storage::url($supplier->logo_path) }}" alt="{{ $supplier->supplier_name }}" class="h-full w-full object-cover">
                        @else
                            LOGO
                        @endif
                    </div>
                    <div>
                        <div class="text-sm font-semibold">{{ $supplier->supplier_name }}</div>
                        <div class="text-xs text-gray-500">Specialize in: Materials</div>
                        <div class="text-xs text-gray-500">Location: Main Office</div>
                    </div>
                </div>

                <div class="mt-4 rounded-2xl border border-gray-200 bg-white p-4 text-center shadow-[0_8px_16px_rgba(0,0,0,0.08)]">
                    <div class="text-2xl font-semibold text-[#111]">122</div>
                    <div class="text-xs text-gray-500">Total Materials Sold</div>
                    <div class="mt-3 text-2xl font-semibold text-[#111]">800k</div>
                    <div class="text-xs text-gray-500">Total Sales Value</div>
                    <a href="{{ route('materials.cart.view') }}"
                       class="mt-4 inline-flex w-full items-center justify-center rounded-xl bg-[#F2C200] py-2 text-sm font-semibold text-black">
                        View Cart
                    </a>
                </div>
            </div>
        </div>

        <div class="col-span-12 lg:col-span-9">
            <h2 class="text-lg font-semibold text-gray-900 mb-3">Choose a Product</h2>
            <div class="rounded-2xl border border-gray-400 bg-[#E9E9E9] p-6">
                <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach($products as $p)
                        <div class="rounded-2xl bg-white p-4 shadow-[0_10px_22px_rgba(0,0,0,0.12)]">
                            <div class="h-24 flex items-center justify-center">
                                @if($p->image_path)
                                    <img src="{{ Storage::url($p->image_path) }}" alt="{{ $p->product_name }}" class="h-20 w-20 rounded-xl object-cover">
                                @else
                                    <div class="h-20 w-20 rounded-xl bg-gray-100 flex items-center justify-center text-xs text-gray-400">IMG</div>
                                @endif
                            </div>

                            <div class="mt-3">
                                <div class="text-xs font-semibold">{{ $p->product_name }}</div>
                                <div class="text-[11px] text-gray-500">Price: <span class="text-green-600">&#8369;{{ number_format($p->price, 0) }}</span> per {{ $p->unit_of_measure }}</div>
                            </div>

                            <form method="POST" action="{{ route('materials.cart.add') }}" class="mt-3">
                                @csrf
                                <input type="hidden" name="supplier_id" value="{{ $supplier->id }}">
                                <input type="hidden" name="product_id" value="{{ $p->id }}">
                                <button class="w-full rounded-lg bg-[#F2C200] py-2 text-[11px] font-semibold text-black">
                                    Add to Cart
                                </button>
                            </form>
                        </div>
                    @endforeach

                    <button type="button"
                            onclick="openAddProduct()"
                            class="rounded-2xl bg-white p-4 shadow-[0_10px_22px_rgba(0,0,0,0.12)] transition hover:-translate-y-0.5">
                        <div class="flex h-44 flex-col items-center justify-center gap-3">
                            <div class="flex h-14 w-14 items-center justify-center rounded-full border-2 border-gray-300 text-2xl">+</div>
                            <div class="text-xs font-semibold text-gray-700 text-center">Add Material for This Supplier</div>
                        </div>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="addProductModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 backdrop-blur-sm px-4">
    <div class="w-[420px] max-w-full rounded-2xl bg-white p-6 shadow-[0_18px_40px_rgba(0,0,0,0.30)]">
        <div class="flex items-center justify-between">
            <div class="text-base font-semibold">Add Material for {{ $supplier->supplier_name }}</div>
            <button type="button" class="text-gray-500" onclick="closeAddProduct()">&times;</button>
        </div>

        <form method="POST" action="{{ route('materials.supplier.products.store', $supplier->id) }}" enctype="multipart/form-data" class="mt-4 space-y-3">
            @csrf
            <div>
                <label class="text-xs text-gray-500">material name</label>
                <input name="product_name" required
                       class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm outline-none">
            </div>
            <div>
                <label class="text-xs text-gray-500">unit of measure</label>
                <input name="unit_of_measure" required
                       class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm outline-none">
            </div>
            <div>
                <label class="text-xs text-gray-500">price</label>
                <input name="price" type="number" step="0.01" min="0" required
                       class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm outline-none">
            </div>
            <div>
                <label class="text-xs text-gray-500">image (optional)</label>
                <input name="image" type="file" accept="image/*"
                       class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm outline-none">
            </div>
            <button class="mt-2 w-full rounded-xl bg-[#F2C200] py-2 text-sm font-semibold text-black">
                Save
            </button>
        </form>
    </div>
</div>

@push('scripts')
<script>
function openAddProduct() {
    const modal = document.getElementById('addProductModal');
    if (!modal) return;
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeAddProduct() {
    const modal = document.getElementById('addProductModal');
    if (!modal) return;
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

document.getElementById('addProductModal')?.addEventListener('click', (e) => {
    const modal = document.getElementById('addProductModal');
    if (e.target === modal) {
        closeAddProduct();
    }
});
</script>
@endpush
@endsection
