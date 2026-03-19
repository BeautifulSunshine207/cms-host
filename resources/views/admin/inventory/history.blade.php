@extends('layouts.app')

@section('title', 'Stock History')

@section('content')
<div class="min-h-screen bg-[#ECECEC] px-10 py-8">
    <div class="flex items-start justify-between gap-6">
        <h1 class="text-2xl font-bold text-[#111]">Inventory Management</h1>

        <form method="GET" action="{{ route('inventory.history') }}" class="w-[520px] max-w-full">
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
        <a href="{{ route('inventory.threshold') }}" class="pb-2 hover:text-black">Set Threshold</a>
        <a href="{{ route('inventory.history') }}" class="border-b-2 border-black pb-2 text-black">Stock History</a>
    </div>

    <div class="mt-6 rounded-2xl border border-gray-400 bg-[#E6E6E6] p-6">
        <div class="rounded-2xl border border-gray-500 bg-[#3E3E3E] shadow-[0_14px_24px_rgba(0,0,0,0.18)]">
            <div class="grid grid-cols-12 px-8 py-6 text-sm font-semibold text-[#F5C400]">
                <div class="col-span-2">Material</div>
                <div class="col-span-2">Date</div>
                <div class="col-span-2">Transaction Type</div>
                <div class="col-span-2">Quantity</div>
                <div class="col-span-2">Remaining Stock</div>
                <div class="col-span-2">Project</div>
            </div>
            <div class="h-px bg-gray-500 mx-8"></div>

            @forelse($transactions as $transaction)
                <div class="grid grid-cols-12 items-center px-8 py-5 text-white">
                    <div class="col-span-2 font-semibold">{{ $transaction->material->material_name }}</div>
                    <div class="col-span-2">{{ $transaction->created_at->format('m-d-y') }}</div>
                    <div class="col-span-2 font-semibold {{ $transaction->type === 'stock_in' ? 'text-green-400' : 'text-red-400' }}">
                        {{ $transaction->type === 'stock_in' ? 'Add Stock' : 'Use Stock' }}
                    </div>
                    <div class="col-span-2">{{ number_format($transaction->quantity, 0) }}</div>
                    <div class="col-span-2">{{ number_format($transaction->remaining_stock, 0) }}</div>
                    <div class="col-span-2">{{ $transaction->project?->name ?? '---' }}</div>
                </div>
                <div class="h-px bg-gray-500 mx-8"></div>
            @empty
                <div class="px-8 py-10 text-center text-gray-300">No transaction history found.</div>
            @endforelse
        </div>

        @if(method_exists($transactions, 'hasPages') && $transactions->hasPages())
            <div class="mt-6 flex justify-center">
                {{ $transactions->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
