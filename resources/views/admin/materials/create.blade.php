@extends('layouts.app')
@php use Illuminate\Support\Facades\Storage; @endphp

@section('content')
<div class="min-h-screen bg-[#ECECEC] px-10 py-8">
    <div class="flex items-start justify-between gap-6">
        <h1 class="text-2xl font-bold text-[#111]">Material Management</h1>

        <form method="GET" class="w-[520px] max-w-full">
            <div class="relative">
                <input name="search" value="{{ request('search') }}" type="text" placeholder="Search"
                       class="w-full rounded-full border border-gray-400 bg-[#EDEDED] px-6 py-2 pr-12 text-sm outline-none">
                <div class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-600">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                        <path d="M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15Z" stroke="currentColor" stroke-width="2"/>
                        <path d="M16.5 16.5 21 21" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </div>
            </div>
        </form>
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
                   class="mt-5 inline-flex w-full items-center justify-center rounded-xl border border-gray-300 py-2 text-sm font-semibold text-white transition hover:border-white hover:text-white">
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
                   class="mt-5 inline-flex w-full items-center justify-center rounded-xl bg-[#F2C200] py-2 text-sm font-semibold text-black transition hover:brightness-105">
                    Create
                </a>
            </div>

            <div class="rounded-2xl bg-[#3E3E3E] p-6 shadow-[0_10px_22px_rgba(0,0,0,0.12)]">
                <div class="flex items-start gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-[#1F1F1F] text-[#F2C200]">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                            <path d="M12 8v4l3 3" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            <path d="M21 12a9 9 0 1 1-9-9" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-white font-semibold">Material History</p>
                        <p class="mt-1 text-xs leading-4 text-gray-300">logs deleted materials and material info updates.</p>
                    </div>
                </div>
                <a href="{{ route('materials.history') }}"
                   class="mt-5 inline-flex w-full items-center justify-center rounded-xl border border-gray-300 py-2 text-sm font-semibold text-white transition hover:border-white hover:text-white">
                    History
                </a>
            </div>
        </div>

        <div class="col-span-12 lg:col-span-9">
            <h2 class="text-lg font-semibold text-gray-900 mb-3">Choose a Supplier</h2>
            <div class="rounded-2xl border border-gray-400 bg-[#E9E9E9] p-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6">
                    @foreach($suppliers as $s)
                        @php
                            $details = [];
                            if ($s->contact_info) {
                                $decoded = json_decode($s->contact_info, true);
                                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                                    $details = $decoded;
                                }
                            }
                            $specializes = $details['specializes_in'] ?? 'Materials';
                            $location = $details['location'] ?? 'Main Office';
                            $logoPath = $details['logo_path'] ?? null;
                            $logoUrl = $logoPath ? Storage::url($logoPath) : null;
                            $partnered = $details['partnered_date'] ?? null;
                        @endphp
                        <a href="{{ route('materials.supplier.products', $s->id) }}"
                           class="rounded-2xl bg-white p-4 shadow-[0_10px_22px_rgba(0,0,0,0.12)] transition hover:-translate-y-0.5 hover:shadow-[0_12px_26px_rgba(0,0,0,0.16)]">
                            <div class="flex h-24 items-center justify-center">
                                @if($logoUrl)
                                    <img src="{{ $logoUrl }}" alt="{{ $s->supplier_name }}" class="h-20 w-20 rounded-full object-cover border border-gray-200">
                                @else
                                    <div class="h-20 w-20 rounded-full border border-gray-200 bg-gray-50 flex items-center justify-center text-xs text-gray-500">
                                        LOGO
                                    </div>
                                @endif
                            </div>
                            <div class="mt-3">
                                <div class="text-sm font-semibold">{{ $s->supplier_name }}</div>
                                <div class="mt-1 text-[11px] text-gray-500">Specializes in: {{ $specializes }}</div>
                                <div class="text-[11px] text-gray-500">Location: {{ $location }}</div>
                                <div class="text-[11px] text-gray-500">Partnered Since: {{ $partnered ? \Carbon\Carbon::parse($partnered)->format('Y') : ($s->created_at?->format('Y') ?? '2020') }}</div>
                            </div>
                        </a>
                    @endforeach

                    <button type="button"
                            onclick="openAddSupplier()"
                            class="rounded-2xl bg-white p-4 shadow-[0_10px_22px_rgba(0,0,0,0.12)] transition hover:-translate-y-0.5 hover:shadow-[0_12px_26px_rgba(0,0,0,0.16)]">
                        <div class="flex h-48 flex-col items-center justify-center gap-4">
                            <div class="flex h-20 w-20 items-center justify-center rounded-2xl border-2 border-gray-300 text-3xl text-gray-700">+</div>
                            <div class="text-xs font-semibold text-gray-700">ADD SUPPLIER</div>
                        </div>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="addSupplierModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 backdrop-blur-sm px-4">
    <div class="w-[860px] max-w-[95vw] rounded-3xl bg-[#2B2B2B] p-6 text-white shadow-[0_30px_80px_rgba(0,0,0,0.45)]">
        <div class="grid grid-cols-12 gap-6">
            <div class="col-span-12 lg:col-span-4 rounded-2xl bg-black p-6">
                <div class="text-lg font-semibold">Add New Supplier</div>
                <input id="supplierLogo" type="file" name="logo" form="supplierForm" accept="image/*" class="sr-only">
                <label for="supplierLogo" class="mt-6 block rounded-2xl border border-dashed border-gray-600 p-6 text-center cursor-pointer transition hover:border-gray-400 hover:bg-[#1E1E1E]">
                    <div class="text-sm text-gray-300">Upload Image for this Supplier</div>
                    <div class="mt-6 flex justify-center">
                        <div class="flex h-14 w-14 items-center justify-center rounded-full border border-gray-500 text-[#F2C200]">
                            <svg width="26" height="26" viewBox="0 0 24 24" fill="none">
                                <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 text-xs text-gray-400">Drag & drop or click to browse (PNG, JPG)</div>
                </label>
            </div>

            <div class="col-span-12 lg:col-span-8 rounded-2xl bg-[#2F2F2F] p-6">
                <form id="supplierForm" method="POST" action="{{ route('materials.suppliers.store') }}" class="grid grid-cols-2 gap-6" enctype="multipart/form-data">
                    @csrf
                    <div class="col-span-2 md:col-span-1">
                        <label class="text-xs text-gray-300">Company Name</label>
                        <input name="supplier_name" required
                               class="mt-2 w-full border-b border-gray-600 bg-transparent px-2 py-1 text-sm outline-none">
                    </div>
                    <div class="col-span-2 md:col-span-1">
                        <label class="text-xs text-gray-300">Date</label>
                        <input type="date" name="partnered_date"
                               class="mt-2 w-full border-b border-gray-600 bg-transparent px-2 py-1 text-sm outline-none">
                    </div>
                    <div class="col-span-2 md:col-span-1">
                        <label class="text-xs text-gray-300">Specializes in</label>
                        <input name="specializes_in"
                               class="mt-2 w-full border-b border-gray-600 bg-transparent px-2 py-1 text-sm outline-none">
                    </div>
                    <div class="col-span-2 md:col-span-1">
                        <label class="text-xs text-gray-300">Location</label>
                        <input name="location"
                               class="mt-2 w-full border-b border-gray-600 bg-transparent px-2 py-1 text-sm outline-none">
                    </div>
                    <div class="col-span-2">
                        <label class="text-xs text-gray-300">Contacts (Optional)</label>
                        <input name="contact_number" placeholder="Number"
                               class="mt-2 w-full border-b border-gray-600 bg-transparent px-2 py-1 text-sm outline-none">
                        <input name="contact_email" placeholder="Email"
                               class="mt-4 w-full border-b border-gray-600 bg-transparent px-2 py-1 text-sm outline-none">
                    </div>

                    <div class="col-span-2 flex items-center justify-end gap-4 pt-4">
                        <button type="button" onclick="closeAddSupplier()"
                                class="w-[160px] rounded-xl bg-[#FAD4D4] py-2 text-sm font-semibold text-[#7A1E1E] transition hover:bg-[#F4BFBF]">
                            Close
                        </button>
                        <button type="submit"
                                class="w-[200px] rounded-xl bg-[#F2C200] py-2 text-sm font-semibold text-black transition hover:brightness-105">
                            Add
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function openAddSupplier() {
    const modal = document.getElementById('addSupplierModal');
    if (!modal) return;
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeAddSupplier() {
    const modal = document.getElementById('addSupplierModal');
    if (!modal) return;
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

document.getElementById('addSupplierModal')?.addEventListener('click', (e) => {
    const modal = document.getElementById('addSupplierModal');
    if (e.target === modal) {
        closeAddSupplier();
    }
});
</script>
@endpush
@endsection
