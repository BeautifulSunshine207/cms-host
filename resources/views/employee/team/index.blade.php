@extends('layouts.app')

@section('content')
@php
    $employeeCount = $teamMembers->count();
    $monthLabel = now()->format('F Y');
    $yearLabel = now()->format('Y');

    $daysOfWeek = ['SUN', 'MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT'];
@endphp

<style>
    #verificationHistoryTable { scrollbar-width: thin; scrollbar-color: rgba(255,255,255,0.18) #2b2b2b; }
    #verificationHistoryTable::-webkit-scrollbar { width: 8px; }
    #verificationHistoryTable::-webkit-scrollbar-track { background: #2b2b2b; }
    #verificationHistoryTable::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.18); border-radius: 999px; border: 2px solid #2b2b2b; }
    #verificationHistoryTable::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.28); }
</style>

<div class="min-h-screen bg-[#ededed]">
    <div class="w-full px-6 py-8 lg:px-8 2xl:px-10">
        <div class="grid grid-cols-12 gap-5">

            <!--left: action cards-->
            <div class="col-span-12 lg:col-span-3 space-y-5">
                <!--employee records-->
                <div class="flex min-h-[210px] flex-col rounded-2xl bg-[#3f3f3f] p-5 shadow-[0_6px_16px_rgba(0,0,0,0.5)]">
                    <div class="flex items-start gap-4">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-[#2b2b2b]">
                            <!--iconn linkk-->
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 text-yellow-400">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.375c.621 0 1.125.504 1.125 1.125V18a2.25 2.25 0 0 1-2.25 2.25M16.5 7.5V18a2.25 2.25 0 0 0 2.25 2.25M16.5 7.5V4.875c0-.621-.504-1.125-1.125-1.125H4.125C3.504 3.75 3 4.254 3 4.875V18a2.25 2.25 0 0 0 2.25 2.25h13.5M6 7.5h3v3H6v-3Z" />
                            </svg>

                                <path d="M9 12h6" stroke="#FACC15" stroke-width="2" stroke-linecap="round"/>
                                <path d="M9 16h6" stroke="#FACC15" stroke-width="2" stroke-linecap="round"/>
                                <path d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8l-5-5Z" stroke="#FACC15" stroke-width="2" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <h3 class="text-[17px] font-semibold leading-tight text-white pb-5">Employee<br/>Records</h3>
                            <p class="mt-1 text-[14px] leading-4 text-white/70">View employee records.</p>
                        </div>
                    </div>

                    <a href="{{ route('team.documents') }}"
                       class="mt-auto inline-flex w-full items-center justify-center rounded-lg bg-[#f6c915] px-4 py-2.5 text-[13px] font-semibold text-black shadow-sm hover:brightness-95 transition">
                        View Documents
                    </a>
                </div>

                <!--payroll-->
                <div class="flex min-h-[210px] flex-col rounded-2xl bg-[#3f3f3f] p-5 shadow-[0_6px_16px_rgba(0,0,0,0.5)]">
                    <div class="flex items-start gap-4">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-[#2b2b2b]">
                            <!--iconn linkk-->
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 text-yellow-400">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z" />
                            </svg>

                                <path d="M3 10h18" stroke="#FACC15" stroke-width="2" stroke-linecap="round"/>
                                <path d="M7 15h1" stroke="#FACC15" stroke-width="2" stroke-linecap="round"/>
                                <path d="M11 15h1" stroke="#FACC15" stroke-width="2" stroke-linecap="round"/>
                                <path d="M6 5h12a3 3 0 0 1 3 3v8a3 3 0 0 1-3 3H6a3 3 0 0 1-3-3V8a3 3 0 0 1 3-3Z" stroke="#FACC15" stroke-width="2" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <h3 class="text-[17px] font-semibold leading-tight text-white pb-4">Payroll</h3>
                            <p class="mt-1 text-[14px] leading-4 text-white/70">Review payroll records and <br>verify attendance-based pay <br> for employees.</p>
                        </div>
                    </div>

                    <a href="{{ route('team.payroll') }}"
                       class="mt-auto inline-flex w-full items-center justify-center rounded-lg bg-[#f6c915] px-4 py-2.5 text-[13px] font-semibold text-black shadow-sm hover:brightness-95 transition">
                        View Payroll
                    </a>
                </div>

                <!--project assignment-->
                <div class="flex min-h-[210px] flex-col rounded-2xl bg-[#3f3f3f] p-5 shadow-[0_6px_16px_rgba(0,0,0,0.5)]">
                    <div class="flex items-start gap-4">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-[#2b2b2b]">
                            <!--icon link-->
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 text-yellow-400">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M18 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0ZM3 19.235v-.11a6.375 6.375 0 0 1 12.75 0v.109A12.318 12.318 0 0 1 9.374 21c-2.331 0-4.512-.645-6.374-1.766Z" />
                            </svg>

                                <path d="M12 12a4 4 0 1 0-8 0 4 4 0 0 0 8 0Z" stroke="#FACC15" stroke-width="2"/>
                                <path d="M2 20a6 6 0 0 1 12 0" stroke="#FACC15" stroke-width="2" stroke-linecap="round"/>
                                <path d="M18 8v8" stroke="#FACC15" stroke-width="2" stroke-linecap="round"/>
                                <path d="M14 12h8" stroke="#FACC15" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <h3 class="text-[17px] font-semibold leading-tight text-white pb-4">Project<br/>Assignment</h3>
                            <p class="mt-1 text-[14px] leading-4 text-white/70">Assign additional <br>employee to project.</p>
                        </div>
                    </div>

                    <a href="{{ route('team.assign') }}"
                       class="mt-auto inline-flex w-full items-center justify-center rounded-lg bg-[#f6c915] px-4 py-2.5 text-[13px] font-semibold text-black shadow-sm hover:brightness-95 transition">
                        Assign Roles
                    </a>
                </div>
            </div>

            <!--middle: calendar + verification history-->
            <div class="col-span-12 lg:col-span-6 space-y-5">
                <!--calendar-->
                <div class="rounded-2xl bg-[#3f3f3f] p-5 shadow-[0_6px_16px_rgba(0,0,0,0.5)]">
                    <div class="relative flex items-center">
                        <h3 class="text-[18px] font-semibold text-white">Calendar</h3>
                        <div class="absolute left-1/2 hidden -translate-x-1/2 text-[12px] text-white/65 sm:block">
                            {{ $monthLabel }}
                        </div>
                        <div class="ml-auto flex items-center gap-2">
                            <div class="rounded-full border border-white/40 px-3 py-1.5 text-[11px] font-semibold text-white/90">
                                {{ $yearLabel }}
                            </div>
                            <svg class="h-3.5 w-3.5 text-white/70" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M6 8l4 4 4-4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                    </div>

                    <div class="mt-4">
                        <div class="grid grid-cols-7 text-center">
                            @foreach($daysOfWeek as $day)
                                <div class="py-1.5 text-[9px] font-semibold tracking-[0.2em] text-white/60">{{ $day }}</div>
                            @endforeach
                        </div>

                        <!--simple 5-week grid (visual only, for the design)-->
                        <div class="grid grid-cols-7 gap-1.5 text-center">
                            @for($i = 1; $i <= 35; $i++)
                                @php
                                    $isToday = $i === (int) now()->format('j');
                                @endphp
                                <div class="flex h-8 items-center justify-center rounded-md text-[11px] font-medium
                                    {{ $isToday ? 'bg-[#f6c915] text-black' : 'text-white/80 hover:bg-white/10' }}">
                                    {{ $i <= 31 ? $i : '' }}
                                </div>
                            @endfor
                        </div>
                    </div>
                </div>

                <!--report verification history-->
                <div class="rounded-2xl bg-[#3f3f3f] p-5 shadow-[0_6px_16px_rgba(0,0,0,0.5)]">
                    <h3 class="text-[18px] font-semibold text-white">Report Verification History</h3>

                    <div id="verificationHistoryTable" class="mt-3 rounded-xl border border-white/10 max-h-[280px] overflow-y-auto">
                        <table class="w-full">
                            <thead class="bg-white/5">
                                <tr class="text-left text-[10px] font-semibold uppercase tracking-[0.2em] text-white/60">
                                    <th class="px-4 py-2.5">Name</th>
                                    <th class="px-4 py-2.5">Status</th>
                                    <th class="px-4 py-2.5">Date Submitted</th>
                                    <th class="px-4 py-2.5">Date Checked</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/10">
                                @forelse($verificationHistory as $history)
                                    <tr class="hover:bg-white/5 transition">
                                        <td class="px-4 py-2.5">
                                            <div class="flex items-center gap-3">
                                                <div class="flex h-10 w-10 items-center justify-center overflow-hidden rounded-full bg-[#2b2b2b]">
                                                    <!--temporary ni bai, e replace og image pag fetching na-->
                                                    <span class="text-[13px] font-semibold text-white">
                                                        {{ $history->teamMember->initials ?? 'TM' }}
                                                    </span>
                                                </div>
                                                <div class="min-w-0">
                                                    <div class="truncate text-[13px] font-semibold text-white">
                                                        {{ $history->teamMember->name ?? 'Unknown' }}
                                                    </div>
                                                    <div class="truncate text-[11px] text-white/55">
                                                        {{ $history->teamMember->role ?? '' }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-2.5">
                                            @if($history->status === 'Verified')
                                                <span class="text-[12px] font-semibold text-green-400">Verified</span>
                                            @else
                                                <span class="text-[12px] font-semibold text-red-400">Denied</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-2.5 text-[12px] text-white/80">
                                            {{ optional($history->date_submitted)->format('m/d/Y') }}
                                        </td>
                                        <td class="px-4 py-2.5 text-[12px] text-white/80">
                                            {{ optional($history->date_checked)->format('m/d/Y') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-4 py-8 text-center text-[12px] text-white/60">No verification history yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!--right: summary cards-->
            <div class="col-span-12 lg:col-span-3 space-y-5">

                <!--employees card (yellow)-->
                <div class="relative flex min-h-[210px] flex-col overflow-hidden rounded-2xl bg-[#f4d35e] p-5 shadow-[0_6px_16px_rgba(0,0,0,0.5)]">
                    <div class="relative z-10">
                        <div class="text-[18px] font-bold text-[#2b2b2b]">Total Employees</div>
                        <div class="mt-3 text-5xl font-bold tracking-tight text-[#2b2b2b]">{{ $employeeCount }}</div>

                        <div class="mt-4 flex items-center gap-3 text-[#2b2b2b]">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-10">
                                <path fill-rule="evenodd" d="M8.25 6.75a3.75 3.75 0 1 1 7.5 0 3.75 3.75 0 0 1-7.5 0ZM15.75 9.75a3 3 0 1 1 6 0 3 3 0 0 1-6 0ZM2.25 9.75a3 3 0 1 1 6 0 3 3 0 0 1-6 0ZM6.31 15.117A6.745 6.745 0 0 1 12 12a6.745 6.745 0 0 1 6.709 7.498.75.75 0 0 1-.372.568A12.696 12.696 0 0 1 12 21.75c-2.305 0-4.47-.612-6.337-1.684a.75.75 0 0 1-.372-.568 6.787 6.787 0 0 1 1.019-4.38Z" clip-rule="evenodd" />
                                <path d="M5.082 14.254a8.287 8.287 0 0 0-1.308 5.135 9.687 9.687 0 0 1-1.764-.44l-.115-.04a.563.563 0 0 1-.373-.487l-.01-.121a3.75 3.75 0 0 1 3.57-4.047ZM20.226 19.389a8.287 8.287 0 0 0-1.308-5.135 3.75 3.75 0 0 1 3.57 4.047l-.01.121a.563.563 0 0 1-.373.486l-.115.04c-.567.2-1.156.349-1.764.441Z" />
                            </svg>



                            
                        </div>
                    </div>

                    <!--design onli blobs-->
                    <div class="absolute -bottom-8 -right-12 h-24 w-72 rounded-[999px] bg-[#eab308]/80"></div>
                    <div class="absolute -bottom-12 right-8 h-16 w-40 rounded-[999px] bg-[#3f3f3f]/50"></div>

                    <!--logo placeholder-->
                    <div class="absolute right-4 bottom-4 flex h-[140px] w-[140px] items-center justify-center rounded-full ">
                        <img src="{{ asset('images/logo-cms-circle.png') }}" alt="Logo" class="w-full h-full object-contain"
                             onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden');">
                        <span class="hidden text-[10px] font-bold text-white">METALIFT</span>
                    </div>
                </div>

                <!--attendance card-->
                <div class="flex min-h-[210px] flex-col rounded-2xl bg-[#3f3f3f] p-5 shadow-[0_6px_16px_rgba(0,0,0,0.5)]">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h3 class="text-[18px] font-semibold text-white pb-4">Attendance</h3>
                            <p class="mt-2 text-[14px] leading-4 text-white/70">Log employee attendance with proof for verification.</p>
                        </div>
                        <div class="flex flex-col items-end gap-2">
                            <!--icon lik-->
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-7 text-yellow-400">
                                <path d="M21.731 2.269a2.625 2.625 0 0 0-3.712 0l-1.157 1.157 3.712 3.712 1.157-1.157a2.625 2.625 0 0 0 0-3.712ZM19.513 8.199l-3.712-3.712-8.4 8.4a5.25 5.25 0 0 0-1.32 2.214l-.8 2.685a.75.75 0 0 0 .933.933l2.685-.8a5.25 5.25 0 0 0 2.214-1.32l8.4-8.4Z" />
                                <path d="M5.25 5.25a3 3 0 0 0-3 3v10.5a3 3 0 0 0 3 3h10.5a3 3 0 0 0 3-3V13.5a.75.75 0 0 0-1.5 0v5.25a1.5 1.5 0 0 1-1.5 1.5H5.25a1.5 1.5 0 0 1-1.5-1.5V8.25a1.5 1.5 0 0 1 1.5-1.5h5.25a.75.75 0 0 0 0-1.5H5.25Z" />
                            </svg>
                        </div>
                    </div>

                    <a href="{{ route('team.attendance') }}"
                       class="mt-auto inline-flex w-full items-center justify-center rounded-lg bg-[#f6c915] px-4 py-2.5 text-[13px] font-semibold text-black shadow-sm hover:brightness-95 transition">
                        Record Attendance
                    </a>
                </div>

                <!--verification overview-->
                <div class="relative flex min-h-[210px] flex-col overflow-hidden rounded-2xl bg-[#f4d35e] p-5 shadow-[0_6px_16px_rgba(0,0,0,0.5)]">
                    <div class="relative z-10">
                        <h3 class="text-[18px] font-bold text-[#2b2b2b]">Verification Overview</h3>

                        <div class="mt-5 flex flex-col items-center gap-2">
                            <div class="grid grid-cols-[110px_56px] items-center justify-center gap-2">
                                <div class="rounded-xl bg-[#3f3f3f] px-4 py-1 text-center text-[20px] font-extrabold text-green-400">Verified</div>
                                <div class="w-[56px] text-center text-[35px] font-bold text-black">{{ $verifiedCount }}</div>
                            </div>
                            <div class="grid grid-cols-[110px_56px] items-center justify-center gap-2">
                                <div class="rounded-xl bg-[#3f3f3f] px-5 py-1 text-center text-[20px] font-extrabold text-red-400">Denied</div>
                                <div class="w-[56px] text-center text-[35px] font-bold text-black">{{ $deniedCount }}</div>
                            </div>
                        </div>
                    </div>

                    <!--decorative yellow swoosh-ish-->
                    <div class="absolute -bottom-6 -left-12 h-20 w-64 rounded-[999px] bg-[#3f3f3f]/50"></div>
                    <div class="absolute -bottom-10 left-6 h-28 w-80 rounded-[999px] bg-[#eab308]/60"></div>

                    
                </div>

            </div>

        </div>
    </div>
</div>
@endsection
