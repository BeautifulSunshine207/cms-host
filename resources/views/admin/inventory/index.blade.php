@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-[#ECECEC] px-10 py-8">
  {{-- Header --}}
  <div class="flex items-start justify-between gap-6">
    <h1 class="text-2xl font-bold text-[#111]">Inventory Management</h1>

    <form method="GET" class="w-[520px] max-w-full">
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

  {{-- Tabs --}}
  <div class="mt-8 flex items-center gap-12 text-sm font-semibold text-gray-500">
    <a href="{{ route('inventory.index') }}" class="border-b-2 border-black pb-2 text-black">Overview</a>
    <a href="{{ route('inventory.stock-inout') }}" class="pb-2 hover:text-black">Stock-in / Stock-out</a>
    <a href="{{ route('inventory.threshold') }}" class="pb-2 hover:text-black">Set Threshold</a>
    <a href="{{ route('inventory.history') }}" class="pb-2 hover:text-black">Stock History</a>
  </div>

  {{-- Outer light frame --}}
  <div class="mt-6 rounded-2xl border border-gray-400 bg-[#E6E6E6] p-6">
    <div class="rounded-2xl border border-gray-500 bg-[#3E3E3E] shadow-[0_14px_24px_rgba(0,0,0,0.18)]">
      <div class="grid grid-cols-12 px-8 py-6 text-sm font-semibold text-[#F5C400]">
        <div class="col-span-4">Material</div>
        <div class="col-span-3">Supplier</div>
        <div class="col-span-3">Remaining Stock</div>
        <div class="col-span-2">Price</div>
      </div>

      <div class="h-px bg-gray-500 mx-8"></div>

      @forelse($materials as $m)
        <div class="grid grid-cols-12 items-center px-8 py-5 text-sm text-white">
          <div class="col-span-4 flex items-center gap-3">
            @if(($m->inventory?->threshold_quantity ?? 0) > 0 && ($m->inventory?->current_quantity ?? 0) <= ($m->inventory?->threshold_quantity ?? 0))
              <span class="text-[#F5C400]">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="none">
                  <path d="M12 3 2 21h20L12 3Z" stroke="currentColor" stroke-width="2" />
                  <path d="M12 9v5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                  <circle cx="12" cy="17" r="1.2" fill="currentColor"/>
                </svg>
              </span>
            @endif
            <span class="font-semibold">{{ $m->material_name }}</span>
          </div>

          <div class="col-span-3">{{ $m->supplier?->supplier_name ?? '-' }}</div>

          <div class="col-span-3">
            {{ rtrim(rtrim(number_format((float)($m->inventory?->current_quantity ?? 0), 2, '.', ''), '0'), '.') }} {{ $m->unit_of_measure }}
          </div>

          <div class="col-span-2">
            &#8369;{{ number_format((float)($m->unit_price ?? 0), 2) }}/{{ $m->unit_of_measure }}
          </div>
        </div>

        <div class="h-px bg-gray-600 mx-8"></div>
      @empty
        <div class="px-8 py-10 text-center text-sm text-gray-300">No materials found.</div>
      @endforelse
    </div>
  </div>
</div>
@endsection
