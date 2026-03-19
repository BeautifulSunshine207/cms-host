@extends('layouts.app')

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
                   class="mt-5 inline-flex w-full items-center justify-center rounded-xl bg-[#F2C200] py-2 text-sm font-semibold text-black">
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
                   class="mt-5 inline-flex w-full items-center justify-center rounded-xl border border-gray-300 bg-transparent py-2 text-sm font-semibold text-white">
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
                   class="mt-5 inline-flex w-full items-center justify-center rounded-xl border border-gray-300 bg-transparent py-2 text-sm font-semibold text-white">
                    History
                </a>
            </div>
        </div>

        <div class="col-span-12 lg:col-span-9">
            <div class="rounded-2xl border border-gray-400 bg-[#E9E9E9] p-6">
                <div class="rounded-2xl bg-[#3E3E3E] shadow-[0_10px_22px_rgba(0,0,0,0.12)]">
                    <div class="grid grid-cols-12 px-8 py-6 text-sm font-semibold text-[#F2C200]">
                        <div class="col-span-4">Material</div>
                        <div class="col-span-2">Unit</div>
                        <div class="col-span-3">Supplier</div>
                        <div class="col-span-3 text-center">Action</div>
                    </div>
                    <div class="h-px bg-gray-600 mx-8"></div>

                    @forelse($materials as $m)
                        <div class="grid grid-cols-12 items-center px-8 py-4 text-sm text-white">
                            <div class="col-span-4">
                                <div class="inline-flex min-w-[170px] rounded-lg border border-[#5A5A5A] px-4 py-1.5">
                                    {{ $m->material_name }}
                                </div>
                            </div>
                            <div class="col-span-2">{{ $m->unit_of_measure }}</div>
                            <div class="col-span-3">{{ $m->supplier?->supplier_name ?? '-' }}</div>
                            <div class="col-span-3 flex items-center justify-center gap-4">
                                <button type="button"
                                        onclick="openUpdate({{ $m->id }})"
                                        class="inline-flex items-center gap-2 rounded-lg bg-[#F2C200] px-4 py-1.5 text-xs font-semibold text-black shadow-[0_6px_14px_rgba(0,0,0,0.25)]">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none">
                                        <path d="M12 20h9" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                        <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    Update
                                </button>

                                <form method="POST" action="{{ route('materials.delete', $m->id) }}">
                                    @csrf
                                    <button class="inline-flex items-center gap-2 rounded-lg bg-[#F9D6D1] px-4 py-1.5 text-xs font-semibold text-black shadow-[0_6px_14px_rgba(0,0,0,0.18)]">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none">
                                            <path d="M3 6h18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                            <path d="M8 6V4h8v2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                            <path d="M6 6l1 14h10l1-14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                        </svg>
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                        <div class="h-px bg-gray-600 mx-8"></div>

                        <div id="update-modal-{{ $m->id }}" class="fixed inset-0 hidden items-center justify-center bg-black/40 z-50 px-4">
                            <div class="w-[520px] max-w-full rounded-2xl bg-white p-6 shadow-[0_18px_40px_rgba(0,0,0,0.30)]">
                                <div class="flex items-center justify-between">
                                    <div class="text-lg font-semibold">Update Material</div>
                                    <button type="button" class="text-gray-500" onclick="closeUpdate({{ $m->id }})">&times;</button>
                                </div>

                                <form method="POST" action="{{ route('materials.update', $m->id) }}" class="mt-4 space-y-3">
                                    @csrf

                                    <div>
                                        <label class="text-xs text-gray-500">material name</label>
                                        <input name="material_name" value="{{ $m->material_name }}"
                                               class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm outline-none">
                                    </div>

                                    <div>
                                        <label class="text-xs text-gray-500">unit</label>
                                        <input name="unit_of_measure" value="{{ $m->unit_of_measure }}"
                                               class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm outline-none">
                                    </div>

                                    <div>
                                        <label class="text-xs text-gray-500">supplier</label>
                                        <select name="supplier_id" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm outline-none">
                                            @foreach(\App\Models\Supplier::where('status','active')->orderBy('supplier_name')->get() as $s)
                                                <option value="{{ $s->id }}" @selected($m->supplier_id == $s->id)>{{ $s->supplier_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="flex gap-3 pt-2">
                                        <button class="flex-1 rounded-xl bg-[#F2C200] py-2 text-sm font-semibold text-black">Save</button>
                                        <button type="button" onclick="closeUpdate({{ $m->id }})"
                                                class="flex-1 rounded-xl border border-gray-300 py-2 text-sm font-semibold">
                                            Close
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="px-8 py-10 text-center text-gray-300">no materials found</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function openUpdate(id) {
    const el = document.getElementById('update-modal-' + id);
    if (!el) return;
    el.classList.remove('hidden');
    el.classList.add('flex');
}

function closeUpdate(id) {
    const el = document.getElementById('update-modal-' + id);
    if (!el) return;
    el.classList.add('hidden');
    el.classList.remove('flex');
}
</script>
@endpush
@endsection
