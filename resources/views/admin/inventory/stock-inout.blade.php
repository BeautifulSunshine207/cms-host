@extends('layouts.app')
<a href="https://lordicon.com/">Icons by Lordicon.com</a>
@section('title', 'Stock In/Out')

@section('content')
<div class="min-h-screen bg-[#ECECEC] px-10 py-8">
    <div class="flex items-start justify-between gap-6">
        <h1 class="text-2xl font-bold text-[#111]">Inventory Management</h1>

        <form method="GET" action="{{ route('inventory.stock-inout') }}" class="w-[520px] max-w-full">
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
        <a href="{{ route('inventory.stock-inout') }}" class="border-b-2 border-black pb-2 text-black">Stock-in / Stock-out</a>
        <a href="{{ route('inventory.threshold') }}" class="pb-2 hover:text-black">Set Threshold</a>
        <a href="{{ route('inventory.history') }}" class="pb-2 hover:text-black">Stock History</a>
    </div>

    <div class="mt-6 rounded-2xl border border-gray-400 bg-[#E6E6E6] p-6">
        <div class="grid grid-cols-12 gap-6">
            <div class="col-span-12 xl:col-span-8">
                <div class="rounded-2xl border border-gray-500 bg-[#3E3E3E] shadow-[0_14px_24px_rgba(0,0,0,0.18)]">
                    <div class="grid grid-cols-12 px-8 py-6 text-sm font-semibold text-[#F5C400]">
                        <div class="col-span-6">Material</div>
                        <div class="col-span-6">Action</div>
                    </div>
                    <div class="h-px bg-gray-500 mx-8"></div>

                    @forelse($materials as $material)
                        <div class="grid grid-cols-12 items-center px-8 py-5 text-white">
                            <div class="col-span-6 flex items-center gap-3">
                                @if(($material->inventory?->threshold_quantity ?? 0) > 0 && ($material->inventory?->current_quantity ?? 0) <= ($material->inventory?->threshold_quantity ?? 0))
                                    <span class="inline-flex h-5 w-5 items-center justify-center text-[#F5C400]">
                                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none">
                                            <path d="M12 3 2 21h20L12 3Z" stroke="currentColor" stroke-width="2" />
                                            <path d="M12 9v5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                            <circle cx="12" cy="17" r="1.2" fill="currentColor"/>
                                        </svg>
                                    </span>
                                @endif
                                <span class="text-sm font-semibold">{{ $material->material_name }}</span>
                            </div>
                            <div class="col-span-6 flex items-center gap-4">
                                <button class="inline-flex items-center gap-2 rounded-lg bg-[#C8F7C5] px-4 py-1.5 text-sm font-semibold text-[#1F3D1F] transition hover:brightness-95 hover:shadow-[0_8px_16px_rgba(0,0,0,0.18)]"
                                        onclick="openAddStockModal({{ $material->id }}, '{{ $material->material_name }}', {{ $material->inventory?->current_quantity ?? 0 }}, '{{ $material->unit_of_measure }}', {{ $material->unit_price ?? 0 }})">
                                    <span class="inline-flex h-5 w-5 items-center justify-center rounded-full border border-[#1F3D1F] text-xs">+</span>
                                    Stock-in
                                </button>
                                <button class="inline-flex items-center gap-2 rounded-lg bg-[#FFD2C2] px-4 py-1.5 text-sm font-semibold text-[#6B2D1A] transition hover:brightness-95 hover:shadow-[0_8px_16px_rgba(0,0,0,0.18)]"
                                        onclick="openUseStockModal({{ $material->id }}, '{{ $material->material_name }}', {{ $material->inventory?->current_quantity ?? 0 }}, '{{ $material->unit_of_measure }}')">
                                    <span class="inline-flex h-5 w-5 items-center justify-center rounded-full border border-[#6B2D1A] text-xs">-</span>
                                    Stock-out
                                </button>
                            </div>
                        </div>
                        <div class="h-px bg-gray-500 mx-8"></div>
                    @empty
                        <div class="px-8 py-10 text-center text-gray-300">No materials found.</div>
                    @endforelse
                </div>
            </div>

            <div class="col-span-12 xl:col-span-4 space-y-6">
                <div class="grid grid-cols-2 gap-6">
                    <div class="rounded-2xl bg-[#3E3E3E] p-5 text-white shadow-[0_10px_22px_rgba(0,0,0,0.12)]">
                        <div class="mb-3 text-[#F5C400]">
                            <svg viewBox="0 0 24 24" width="22" height="22" fill="none">
                                <path d="M12 3 2 21h20L12 3Z" stroke="currentColor" stroke-width="2" />
                                <path d="M12 9v5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                <circle cx="12" cy="17" r="1.2" fill="currentColor"/>
                            </svg>
                        </div>
                        <div class="text-sm font-semibold">Low Stock<br>Materials</div>
                        <p class="mt-2 text-xs text-gray-300">All items that are close to running out and need replenishment.</p>
                        <button class="mt-4 w-full rounded-xl bg-[#F5C400] py-2 text-xs font-semibold text-black transition hover:brightness-95 hover:shadow-[0_8px_16px_rgba(0,0,0,0.18)]" onclick="showLowStock()">View</button>
                    </div>

                    <div class="relative overflow-hidden rounded-2xl bg-[#F5D45C] p-5 text-[#111] shadow-[0_10px_22px_rgba(0,0,0,0.12)]">
                        <div class="text-xs font-semibold">Total Material Types</div>
                        <div class="mt-2 text-3xl font-bold">{{ $totalMaterials }}</div>
                        <div class="absolute -bottom-10 -left-10 h-24 w-28 rounded-full bg-[#E1B100]/80"></div>
                        <div class="absolute bottom-4 right-4 h-16 w-16 overflow-hidden rounded-full bg-[#1F1F1F] ring-4 ring-[#F2C200]/40">
                            <img src="{{ asset('images/logo-cms.png') }}" alt="Logo" class="h-full w-full object-cover">
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl bg-white p-5 shadow-[0_10px_22px_rgba(0,0,0,0.10)]">
                    <div class="text-sm font-semibold text-[#111]">Top Used Materials</div>
                    <div class="mt-4 h-44">
                        <canvas id="topMaterialsChart" class="h-full w-full"></canvas>
                    </div>
                    <div class="mt-4 space-y-2 text-xs text-gray-600">
                        @foreach($topMaterials as $item)
                            <div class="flex items-center justify-between">
                                <span>&bull; {{ $item->material->material_name }}</span>
                                <span>{{ number_format($item->total, 0) }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!--add stock modal-->
<div id="addStockModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 backdrop-blur-sm px-4">
    <div class="w-[420px] max-w-full rounded-3xl bg-[#2B2B2B] p-6 text-white shadow-[0_30px_80px_rgba(0,0,0,0.45)]">
        <div class="flex items-start justify-between">
            <div class="flex items-center gap-3">
                <div class="h-16 w-16 rounded-2xl bg-[#2B2B2B] p-0">
                    <img src="{{ asset('images/logo-cms-circle.png') }}" alt="Logo" class="h-full w-full object-cover">
                </div>
                <div>
                    <div class="text-lg font-semibold">Add Stock</div>
                    <div class="text-xs text-gray-300">Quantity</div>
                </div>
            </div>
            <button class="text-xl text-gray-300" onclick="closeModal('addStockModal')">&times;</button>
        </div>

        <form id="addStockForm" class="mt-6">
            <input type="hidden" id="addMaterialId">
            <div class="rounded-2xl bg-[#3A3A3A] p-4">
                <div class="text-xs text-gray-300">Current Stock</div>
                <div id="addCurrentStock" class="mt-1 text-sm">0</div>
                <div class="mt-4 text-xs text-gray-300">Add Stock</div>
                <input type="number" id="addQuantity" step="0.01" min="0" required placeholder="Type Here"
                       class="mt-2 w-full rounded-lg bg-[#2F2F2F] border border-gray-600 px-3 py-2 text-sm outline-none placeholder:text-gray-500">
                <div class="mt-4 text-xs text-gray-300">Unit Price</div>
                <div id="addUnitPriceText" class="mt-1 text-sm">0.00</div>
                <div class="mt-4 text-xs text-gray-300">Total</div>
                <div id="addTotalText" class="mt-1 text-sm font-semibold">0.00</div>
            </div>
            <button type="submit" class="mt-4 w-full rounded-xl bg-[#F5C400] py-2 text-sm font-semibold text-black transition hover:brightness-95 hover:shadow-[0_8px_16px_rgba(0,0,0,0.18)]">Set</button>
        </form>
    </div>
</div>

<!-- use stock modal -->
<div id="useStockModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 backdrop-blur-sm px-4">
    <div class="w-[420px] max-w-full rounded-3xl bg-[#2B2B2B] p-6 text-white shadow-[0_30px_80px_rgba(0,0,0,0.45)]">
        <div class="flex items-start justify-between">
            <div class="flex items-center gap-3">
                <div class="h-14 w-14 rounded-2xl border border-[#F2C200] bg-[#1E1E1E] p-2">
                    <img src="{{ asset('images/logo-cms.png') }}" alt="Logo" class="h-full w-full object-cover">
                </div>
                <div>
                    <div class="text-lg font-semibold">Subtract Stock</div>
                    <div class="text-xs text-gray-300">Quantity</div>
                </div>
            </div>
            <button class="text-xl text-gray-300" onclick="closeModal('useStockModal')">&times;</button>
        </div>

        <form id="useStockForm" class="mt-6">
            <input type="hidden" id="useMaterialId">
            <div class="rounded-2xl bg-[#3A3A3A] p-4">
                <div class="text-xs text-gray-300">Current Stock</div>
                <div id="useCurrentStock" class="mt-1 text-sm">0</div>
                <div class="mt-4 text-xs text-gray-300">Subtract Stock</div>
                <input type="number" id="useQuantity" step="0.01" min="0" required placeholder="Type Here"
                       class="mt-2 w-full rounded-lg bg-[#2F2F2F] border border-gray-600 px-3 py-2 text-sm outline-none placeholder:text-gray-500">
                <div class="mt-4 text-xs text-gray-300">Project</div>
                <select id="useProject" class="mt-2 w-full rounded-lg bg-white text-gray-900 border border-gray-300 px-3 py-2 text-sm outline-none">
                    <option value="">Select a Project</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}">{{ $project->name }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="mt-4 w-full rounded-xl bg-[#F5C400] py-2 text-sm font-semibold text-black transition hover:brightness-95 hover:shadow-[0_8px_16px_rgba(0,0,0,0.18)]">Set</button>
        </form>
    </div>
</div>

<!-- low stock modal -->
<div id="lowStockModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 backdrop-blur-sm px-4">
    <div class="w-[520px] max-w-full rounded-2xl bg-white p-5 shadow-[0_25px_60px_rgba(0,0,0,0.25)]">
        <div class="flex items-center justify-between">
            <div class="text-base font-semibold text-[#111]">Low Stock Materials</div>
            <button class="text-xl text-gray-400" onclick="closeModal('lowStockModal')">&times;</button>
        </div>
        <div class="mt-2 flex items-center justify-between text-xs text-gray-500">
            <div>Items below or equal to threshold.</div>
            <div id="lowStockCount"></div>
        </div>
        <div id="lowStockBody" class="mt-4 max-h-[320px] space-y-2 overflow-auto pr-1"></div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
const currencySymbol = '\u20B1';
let addUnitPrice = 0;
let addUnit = '';

function formatQty(value) {
    const num = Number(value);
    if (!Number.isFinite(num)) {
        return '0';
    }
    return num.toLocaleString(undefined, { maximumFractionDigits: 2 });
}

function formatMoney(value) {
    const num = Number(value);
    if (!Number.isFinite(num)) {
        return '0.00';
    }
    return num.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

function showModal(id) {
    const el = document.getElementById(id);
    if (!el) return;
    el.classList.remove('hidden');
    el.classList.add('flex');
}

function hideModal(id) {
    const el = document.getElementById(id);
    if (!el) return;
    el.classList.add('hidden');
    el.classList.remove('flex');
}

function openAddStockModal(id, material_name, stock, unit, unitPrice) {
    const idEl = document.getElementById('addMaterialId');
    const qtyEl = document.getElementById('addQuantity');
    const currentEl = document.getElementById('addCurrentStock');
    const priceEl = document.getElementById('addUnitPriceText');
    const totalEl = document.getElementById('addTotalText');
    if (idEl) idEl.value = id;
    if (qtyEl) qtyEl.value = '';
    if (currentEl) currentEl.textContent = formatQty(stock);
    addUnitPrice = Number(unitPrice) || 0;
    addUnit = unit || '';
    if (priceEl) {
        const unitText = addUnit ? ` per ${addUnit}` : '';
        priceEl.textContent = `${currencySymbol}${formatMoney(addUnitPrice)}${unitText}`;
    }
    if (totalEl) {
        totalEl.textContent = `${currencySymbol}${formatMoney(0)}`;
    }
    showModal('addStockModal');
}

function openUseStockModal(id, material_name, stock, unit) {
    const idEl = document.getElementById('useMaterialId');
    const qtyEl = document.getElementById('useQuantity');
    const projectEl = document.getElementById('useProject');
    const currentEl = document.getElementById('useCurrentStock');
    if (idEl) idEl.value = id;
    if (qtyEl) qtyEl.value = '';
    if (projectEl) projectEl.value = '';
    if (currentEl) currentEl.textContent = formatQty(stock);
    showModal('useStockModal');
}

function closeModal(modalId) {
    hideModal(modalId);
}

function updateAddTotal() {
    const qtyValue = Number(document.getElementById('addQuantity')?.value);
    const qty = Number.isFinite(qtyValue) ? qtyValue : 0;
    const total = qty * addUnitPrice;
    const totalEl = document.getElementById('addTotalText');
    if (totalEl) {
        totalEl.textContent = `${currencySymbol}${formatMoney(total)}`;
    }
}

async function showLowStock() {
    const countEl = document.getElementById('lowStockCount');
    const bodyEl = document.getElementById('lowStockBody');
    if (countEl) countEl.textContent = '';
    if (bodyEl) bodyEl.innerHTML = '<div class="text-center text-sm text-gray-400 py-6">Loading...</div>';
    showModal('lowStockModal');

    try {
        const res = await fetch('{{ route("inventory.low-stock") }}', { headers: { 'X-Requested-With': 'XMLHttpRequest' }});
        const data = await res.json();
        if (!data || data.length === 0) {
            if (bodyEl) bodyEl.innerHTML = '<div class="text-center text-sm text-gray-400 py-6">No low stock materials.</div>';
            return;
        }

        if (countEl) countEl.textContent = `${data.length} item${data.length > 1 ? 's' : ''}`;
        const rows = data.map((m) => {
            const currentText = formatQty(m.current_quantity ?? 0);
            const thresholdText = formatQty(m.threshold_quantity ?? 0);
            const unit = m.unit_of_measure ?? '';

            return `
                <div class="flex items-center justify-between rounded-xl border border-gray-200 bg-[#F4F4F4] px-4 py-3">
                    <div>
                        <div class="text-sm font-semibold text-gray-900">${m.material_name ?? '-'}</div>
                        <div class="mt-1 text-xs text-gray-600">Current: ${currentText} ${unit} | Threshold: ${thresholdText} ${unit}</div>
                    </div>
                    <span class="rounded-full bg-black px-3 py-1 text-xs font-semibold text-white">Low</span>
                </div>
            `;
        }).join('');

        if (bodyEl) bodyEl.innerHTML = rows;
    } catch (e) {
        if (bodyEl) bodyEl.innerHTML = '<div class="text-center text-sm text-gray-400 py-6">Failed to load low stock data.</div>';
    }
}

document.getElementById('addStockForm')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const materialId = document.getElementById('addMaterialId')?.value;
    const quantity = document.getElementById('addQuantity')?.value;

    try {
        const res = await fetch('{{ route("inventory.add-stock") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ material_id: materialId, quantity })
        });
        const data = await res.json();
        alert(data.message ?? 'Saved');
        location.reload();
    } catch (e) {
        alert('An error occurred');
    }
});

document.getElementById('addQuantity')?.addEventListener('input', updateAddTotal);

document.getElementById('useStockForm')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const materialId = document.getElementById('useMaterialId')?.value;
    const quantity = document.getElementById('useQuantity')?.value;
    let project = document.getElementById('useProject')?.value;
    if (project === '') {
        project = null;
    }

    try {
        const res = await fetch('{{ route("inventory.use-stock") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ material_id: materialId, quantity, project })
        });
        const data = await res.json();
        alert(data.message ?? 'Saved');
        location.reload();
    } catch (e) {
        alert('An error occurred');
    }
});

@if($topMaterials->count() > 0)
if (window.Chart && document.getElementById('topMaterialsChart')) {
    const ctx = document.getElementById('topMaterialsChart').getContext('2d');
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($topMaterials->pluck('material.material_name')) !!},
            datasets: [{
                data: {!! json_encode($topMaterials->pluck('total')) !!},
                backgroundColor: ['#000', '#6B8E23', '#87CEEB', '#9B9B9B', '#FFCC00'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: { legend: { display: false } }
        }
    });
}
@endif

['addStockModal', 'useStockModal', 'lowStockModal'].forEach((id) => {
    const el = document.getElementById(id);
    if (!el) return;
    el.addEventListener('click', (e) => {
        if (e.target === el) {
            hideModal(id);
        }
    });
});
</script>
@endpush
