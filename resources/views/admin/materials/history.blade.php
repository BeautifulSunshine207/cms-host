@extends('layouts.app')

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
                   class="mt-5 inline-flex w-full items-center justify-center rounded-xl border border-gray-300 py-2 text-sm font-semibold text-white">
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
                   class="mt-5 inline-flex w-full items-center justify-center rounded-xl bg-[#F2C200] py-2 text-sm font-semibold text-black">
                    History
                </a>
            </div>
        </div>

        <div class="col-span-12 lg:col-span-9">
            <h2 class="text-lg font-semibold text-gray-900 mb-3">History</h2>
            <div class="rounded-2xl border border-gray-400 bg-[#E9E9E9] p-6">
                <div class="rounded-2xl bg-[#222222] shadow-[0_10px_22px_rgba(0,0,0,0.12)]">
                    <div class="grid grid-cols-12 px-8 py-6 text-sm font-semibold text-[#F2C200]">
                        <div class="col-span-4">Material</div>
                        <div class="col-span-3">Date</div>
                        <div class="col-span-3">Action</div>
                        <div class="col-span-2 text-right"></div>
                    </div>
                    <div class="h-px bg-gray-600 mx-8"></div>

                    @foreach($history as $h)
                        @php
                            $rowBg = $h->action === 'deleted' ? 'bg-[#3A2323]' : 'bg-[#222222]';
                            $actionColor = $h->action === 'deleted' ? 'text-red-500' : ($h->action === 'updated' ? 'text-green-400' : 'text-blue-400');
                            $matName = $h->material?->material_name ?? 'unknown';
                        @endphp
                        <div class="grid grid-cols-12 items-center px-8 py-4 text-sm {{ $rowBg }}">
                            <div class="col-span-4 text-white">{{ $matName }}</div>
                            <div class="col-span-3 text-white">{{ $h->created_at->format('m-d-y') }}</div>
                            <div class="col-span-3 font-semibold {{ $actionColor }}">{{ ucfirst($h->action) }}</div>
                            <div class="col-span-2 flex justify-end">
                                @if($h->action === 'updated')
                                    <button onclick="openHist({{ $h->id }})" class="text-blue-500 font-semibold">VIEW</button>
                                @else
                                    <span class="text-gray-500 font-semibold">VIEW</span>
                                @endif
                            </div>
                        </div>
                        <div class="h-px bg-gray-700 mx-8"></div>

                        <div id="hist-{{ $h->id }}" class="fixed inset-0 hidden items-center justify-center bg-black/40 z-50 px-4">
                            <div class="w-[420px] max-w-full rounded-2xl bg-white p-6 shadow-[0_18px_40px_rgba(0,0,0,0.30)]">
                                <div class="text-base font-semibold">
                                    Updated: <span class="font-normal">Material Details</span>
                                </div>
                                @php
                                    $from = $h->from_data ? json_decode($h->from_data, true) : [];
                                    $to = $h->to_data ? json_decode($h->to_data, true) : [];
                                @endphp
                                <div class="mt-4 rounded-lg border border-gray-200">
                                    <div class="grid grid-cols-3 bg-gray-100 text-xs font-semibold px-3 py-2">
                                        <div>Info</div>
                                        <div>From</div>
                                        <div>To</div>
                                    </div>
                                    <div class="grid grid-cols-3 px-3 py-2 text-xs border-t">
                                        <div>Material</div>
                                        <div>{{ $from['material_name'] ?? '-' }}</div>
                                        <div>{{ $to['material_name'] ?? '-' }}</div>
                                    </div>
                                    <div class="grid grid-cols-3 px-3 py-2 text-xs border-t">
                                        <div>Unit</div>
                                        <div>{{ $from['unit_of_measure'] ?? '-' }}</div>
                                        <div>{{ $to['unit_of_measure'] ?? '-' }}</div>
                                    </div>
                                    <div class="grid grid-cols-3 px-3 py-2 text-xs border-t">
                                        <div>Supplier</div>
                                        <div>{{ $from['supplier_id'] ?? '-' }}</div>
                                        <div>{{ $to['supplier_id'] ?? '-' }}</div>
                                    </div>
                                </div>
                                <button onclick="closeHist({{ $h->id }})"
                                        class="mt-5 w-full rounded-xl bg-[#F2C200] py-2 text-sm font-semibold text-black">
                                    Close
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function openHist(id) {
    const el = document.getElementById('hist-' + id);
    if (!el) return;
    el.classList.remove('hidden');
    el.classList.add('flex');
}

function closeHist(id) {
    const el = document.getElementById('hist-' + id);
    if (!el) return;
    el.classList.add('hidden');
    el.classList.remove('flex');
}
</script>
@endpush
@endsection
