@extends('layouts.app')

@section('title', 'Set Threshold')

@section('content')
<div class="min-h-screen bg-[#ECECEC] px-10 py-8">
    <div class="flex items-start justify-between gap-6">
        <h1 class="text-2xl font-bold text-[#111]">Inventory Management</h1>

        <form method="GET" action="{{ route('inventory.threshold') }}" class="w-[520px] max-w-full">
            <div class="relative">
                <input name="search" value="{{ request('search') }}" placeholder="Search"
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

    <div class="mt-8 flex items-center gap-12 text-sm font-semibold text-gray-500">
        <a href="{{ route('inventory.index') }}" class="pb-2 hover:text-black">Overview</a>
        <a href="{{ route('inventory.stock-inout') }}" class="pb-2 hover:text-black">Stock-in / Stock-out</a>
        <a href="{{ route('inventory.threshold') }}" class="border-b-2 border-black pb-2 text-black">Set Threshold</a>
        <a href="{{ route('inventory.history') }}" class="pb-2 hover:text-black">Stock History</a>
    </div>

    <div class="mt-6 rounded-2xl border border-gray-400 bg-[#E6E6E6] p-6">
        <div class="rounded-2xl border border-gray-500 bg-[#3E3E3E] shadow-[0_14px_24px_rgba(0,0,0,0.18)]">
            <div class="grid grid-cols-12 px-8 py-6 text-sm font-semibold text-[#F5C400]">
                <div class="col-span-3">Material</div>
                <div class="col-span-3">Supplier</div>
                <div class="col-span-3">Current Threshold</div>
                <div class="col-span-3 text-center">Set Threshold</div>
            </div>
            <div class="h-px bg-gray-500 mx-8"></div>

            @forelse($materials as $material)
                <div class="grid grid-cols-12 items-center px-8 py-5 text-white">
                    <div class="col-span-3 font-semibold">{{ $material->material_name }}</div>
                    <div class="col-span-3">{{ $material->supplier?->supplier_name ?? '-' }}</div>
                    <div class="col-span-3 text-[#F5C400]">
                        {{ number_format((float)($material->inventory?->threshold_quantity ?? 0), 0) }} {{ $material->unit_of_measure }}
                    </div>
                    <div class="col-span-3 flex justify-center">
                        <button class="inline-flex items-center gap-2 rounded-full bg-[#F5C400] px-6 py-1.5 text-sm font-semibold text-black shadow-[0_6px_14px_rgba(0,0,0,0.25)]"
                                onclick="openThresholdModal({{ $material->id }}, '{{ $material->material_name }}', {{ $material->inventory?->threshold_quantity ?? 0 }}, '{{ $material->unit_of_measure }}')">
                            Set
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none">
                                <path d="M12 20h9" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="h-px bg-gray-500 mx-8"></div>
            @empty
                <div class="px-8 py-10 text-center text-gray-300">No materials found.</div>
            @endforelse
        </div>
    </div>
</div>

<!-- set threshold modal -->
<div id="thresholdModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 backdrop-blur-sm px-4">
    <div class="w-[420px] max-w-full rounded-3xl bg-[#2B2B2B] p-6 text-white shadow-[0_30px_80px_rgba(0,0,0,0.45)]">
        <div class="flex items-start justify-between">
            <div class="flex items-center gap-3">
                <div class="h-14 w-14 rounded-2xl border border-[#F2C200] bg-[#1E1E1E] p-2">
                    <img src="{{ asset('images/logo-cms.png') }}" alt="Logo" class="h-full w-full object-cover">
                </div>
                <div>
                    <div class="text-lg font-semibold">Set threshold</div>
                    <div class="text-xs text-gray-300">Minimum and Maximum</div>
                </div>
            </div>
            <button class="text-xl text-gray-300" onclick="closeModal('thresholdModal')">&times;</button>
        </div>

        <form id="thresholdForm" class="mt-6">
            <input type="hidden" id="thresholdMaterialId">
            <div class="rounded-2xl bg-[#3A3A3A] p-4">
                <div class="text-xs text-gray-300">Minimum Supply</div>
                <input type="number" id="minThreshold" step="0.01" min="0" required
                       class="mt-2 w-full rounded-lg bg-[#2F2F2F] border border-gray-600 px-3 py-2 text-sm outline-none" placeholder="Lowest">
                <div class="mt-4 text-xs text-gray-300">Maximum Supply</div>
                <input type="number" id="maxThreshold" step="0.01" min="0"
                       class="mt-2 w-full rounded-lg bg-[#2F2F2F] border border-gray-600 px-3 py-2 text-sm outline-none" placeholder="Highest">
            </div>
            <button type="submit" class="mt-4 w-full rounded-xl bg-[#F5C400] py-2 text-sm font-semibold text-black">Set</button>
            <div class="mt-4 border-t border-gray-600 pt-4 text-center text-xs text-gray-400">
                Encountered any problems? Click <span class="text-[#F5C400]">here</span><br>
                Or contact admin through
                <div class="mt-3 flex justify-center gap-3">
                    <span class="flex h-8 w-8 items-center justify-center rounded-full bg-[#3A3A3A] text-white">M</span>
                    <span class="flex h-8 w-8 items-center justify-center rounded-full bg-[#3A3A3A] text-white">f</span>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

function openThresholdModal(id, material_name, threshold, unit) {
    const idEl = document.getElementById('thresholdMaterialId');
    const minEl = document.getElementById('minThreshold');
    const maxEl = document.getElementById('maxThreshold');
    if (idEl) idEl.value = id;
    if (minEl) minEl.value = threshold;
    if (maxEl) maxEl.value = '';
    const modal = document.getElementById('thresholdModal');
    if (modal) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (!modal) return;
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

document.getElementById('thresholdForm')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const materialId = document.getElementById('thresholdMaterialId')?.value;
    const threshold = document.getElementById('minThreshold')?.value;
    const maxThreshold = document.getElementById('maxThreshold')?.value || null;

    try {
        const res = await fetch('{{ route("inventory.update-threshold") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                material_id: materialId,
                threshold,
                max_threshold: maxThreshold === '' ? null : maxThreshold
            })
        });
        const data = await res.json();
        if (!res.ok) {
            alert(data.message || 'An error occurred');
            return;
        }
        alert(data.message || 'Threshold updated.');
        location.reload();
    } catch (e) {
        alert('An error occurred');
    }
});

document.getElementById('thresholdModal')?.addEventListener('click', (e) => {
    const modal = document.getElementById('thresholdModal');
    if (e.target === modal) {
        closeModal('thresholdModal');
    }
});
</script>
@endpush
