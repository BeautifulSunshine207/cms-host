@extends('layouts.app')

@section('content')
@php
    // same behavior as documents page: use query string as the "open/close" state
    $selectedId = request('member');
    $selected = $selectedId ? $teamMembers->firstWhere('id', (int)$selectedId) : null;
@endphp

<style>
    .soft-card { box-shadow: 0 10px 22px rgba(0,0,0,.10); }
    #payrollRecordsTable { scrollbar-width: thin; scrollbar-color: #d5d5d5 #ffffff; }
    #membersList { scrollbar-width: thin; scrollbar-color: #5A5A5A #3E3E3E; }
    #membersList::-webkit-scrollbar { width: 10px; }
    #membersList::-webkit-scrollbar-track { background: #3E3E3E; }
    #membersList::-webkit-scrollbar-thumb { background: #5A5A5A; border-radius: 999px; border: 2px solid #3E3E3E; }
    #membersList::-webkit-scrollbar-thumb:hover { background: #6A6A6A; }
    #payrollRecordsTable::-webkit-scrollbar { width: 8px; }
    #payrollRecordsTable::-webkit-scrollbar-track { background: #ffffff; }
    #payrollRecordsTable::-webkit-scrollbar-thumb { background: #d5d5d5; border-radius: 999px; border: 2px solid #ffffff; }
    #payrollRecordsTable::-webkit-scrollbar-thumb:hover { background: #c2c2c2; }
</style>

<div class="min-h-screen bg-[#ECECEC] p-8">
    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Team Management</h1>
        </div>

        <div class="w-[420px] max-w-full">
            <div class="relative">
                <input id="memberSearch" type="text" placeholder="Search"
                       class="w-full rounded-full border border-gray-400 bg-[#EDEDED] px-5 py-2 pr-12 text-sm outline-none">
                <div class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-600">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                        <path d="M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15Z" stroke="currentColor" stroke-width="2"/>
                        <path d="M16.5 16.5 21 21" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Body --}}
    <div class="grid grid-cols-12 gap-10">
        {{-- LEFT: Employee list --}}
        <div class="col-span-12 lg:col-span-4">
            <div class="bg-[#3E3E3E] rounded-2xl soft-card overflow-hidden">
                <div class="px-6 py-5">
                    <h2 class="text-white text-lg font-bold">Employee’s Payroll</h2>
                    <p class="text-gray-300 text-sm mt-2 font-semibold">Name</p>
                </div>

                <div id="membersList" class="max-h-[70vh] overflow-y-auto">
                    @foreach($teamMembers as $m)
                        @php
                            $active = $selected && $selected->id === $m->id;

                            // if active: clicking closes (remove member param)
                            $href = $active
                                ? route('team.payroll')
                                : route('team.payroll', ['member' => $m->id]);
                        @endphp

                        <a href="{{ $href }}"
                           class="member-row flex items-center justify-between px-6 py-4 border-t border-gray-700 hover:bg-[#4A4A4A]"
                           data-search="{{ strtolower(($m->name ?? '').' '.($m->role ?? '')) }}"
                        >
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-10 h-10 rounded-lg overflow-hidden bg-gray-600 flex items-center justify-center">
                                    @if($m->avatar)
                                        <img src="{{ asset($m->avatar) }}" class="w-full h-full object-cover" alt="avatar">
                                    @else
                                        <span class="text-xs font-bold text-white">
                                            {{ $m->initials ?? strtoupper(substr($m->name ?? 'E', 0, 1)) }}
                                        </span>
                                    @endif
                                </div>

                                <div class="min-w-0">
                                    <div class="text-white font-semibold truncate">{{ $m->name }}</div>
                                    <div class="text-xs text-gray-300 truncate">{{ $m->role }}</div>
                                </div>
                            </div>

                            <div class="text-2xl font-light {{ $active ? 'text-yellow-400' : 'text-white' }}">
                                {!! $active ? '&#x2039;' : '&#x203A;' !!}
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- RIGHT: Details --}}
        <div class="col-span-12 lg:col-span-8">
            @if(!$selected)
                {{-- default state (logo only) --}}
                <div class="flex items-center justify-center h-[70vh]">
                    <img src="{{ asset('images/metalift-logo.png') }}"
                         class="max-h-[420px] opacity-90"
                         onerror="this.style.display='none'"
                         alt="Logo">
                </div>
            @else
                {{-- Details panel --}}
                <div class="bg-[#F6F6F6] rounded-3xl soft-card p-10">
                    <div class="grid grid-cols-12 gap-10">
                        {{-- Left: employee info --}}
                        <div class="col-span-12 md:col-span-5">
                            <div class="text-blue-500 font-semibold mb-4">Employee Details</div>

                            <div class="flex items-start gap-5">
                                <div class="w-36 h-36 rounded-2xl overflow-hidden bg-gray-300">
                                    @if($selected->avatar)
                                        <img src="{{ asset($selected->avatar) }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-gray-700 font-bold">
                                            PHOTO
                                        </div>
                                    @endif
                                </div>

                                <div class="space-y-4 text-sm">
                                    <div>
                                        <div class="text-gray-500">Name</div>
                                        <div class="font-semibold text-gray-900">{{ $selected->name }}</div>
                                    </div>
                                    <div>
                                        <div class="text-gray-500">Role</div>
                                        <div class="font-semibold text-gray-900">{{ $selected->role }}</div>
                                    </div>
                                    <div>
                                        <div class="text-gray-500">Salary</div>
                                        <div class="font-semibold text-gray-900">{{ $selected->salary }}</div>
                                    </div>
                                    <div>
                                        <div class="text-gray-500">Location</div>
                                        <div class="font-semibold text-gray-900">{{ $selected->location }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Right: payroll records --}}
                        <div class="col-span-12 md:col-span-7">
                            <div class="text-blue-500 font-semibold mb-4 text-right">Payroll Records</div>

                            <div class="bg-white rounded-2xl soft-card p-6">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="font-bold text-gray-900">Payroll Summary</div>
                                    <div class="text-sm text-gray-500">
                                        {{ $selected->payrollRecords->count() }} record(s)
                                    </div>
                                </div>

                                <div id="payrollRecordsTable" class="max-h-[280px] overflow-y-auto">
                                    <table class="w-full text-sm">
                                        <thead>
                                            <tr class="text-gray-500 border-b">
                                                <th class="text-left py-2 pr-4">Date Range</th>
                                                <th class="text-left py-2 pr-4">Project</th>
                                                <th class="text-left py-2 pr-4">Days</th>
                                                <th class="text-right py-2">Salary</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($selected->payrollRecords as $p)
                                                <tr class="border-b last:border-b-0">
                                                    <td class="py-3 pr-4 font-semibold text-gray-800">
                                                        {{ $p->date_range }}
                                                    </td>
                                                    <td class="py-3 pr-4 text-gray-700">
                                                        {{ $p->project }}
                                                    </td>
                                                    <td class="py-3 pr-4 text-gray-700">
                                                        {{ $p->days }}
                                                    </td>
                                                    <td class="py-3 text-right font-semibold text-gray-900">
                                                        {{ $p->salary }}
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4" class="py-6 text-center text-gray-500">
                                                        No payroll records yet.
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            {{-- optional: big button like your style (can connect later) --}}
                            <button class="mt-6 w-full bg-yellow-500 hover:bg-yellow-600 text-black font-bold py-3 rounded-lg">
                                View Payroll Details
                            </button>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
    // search filter (same as documents)
    (function () {
        const input = document.getElementById('memberSearch');
        const rows = document.querySelectorAll('.member-row');

        input?.addEventListener('input', () => {
            const q = (input.value || '').toLowerCase().trim();
            rows.forEach(r => {
                const s = r.getAttribute('data-search') || '';
                r.style.display = s.includes(q) ? '' : 'none';
            });
        });
    })();
</script>
@endsection
