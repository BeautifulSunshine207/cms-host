@extends('employee.layouts.app')

@section('content')
@php
    $memberName = $teamMember?->name ?? auth()->user()->name;
    $memberRole = $teamMember?->role ?? 'Employee';
    $memberLocation = $teamMember?->location ?? '-';
    $memberAvatar = $teamMember?->avatar
        ? (str_starts_with($teamMember->avatar, 'http') ? $teamMember->avatar : asset(ltrim($teamMember->avatar, '/')))
        : null;

    $assignedCount = $assignedProjects->count();
    $completedCount = (int) ($completedProjectsCount ?? 0);

    $formatCompact = function (float $amount): string {
        if ($amount >= 1000000) {
            return rtrim(rtrim(number_format($amount / 1000000, 1, '.', ''), '0'), '.') . 'm';
        }
        if ($amount >= 1000) {
            return rtrim(rtrim(number_format($amount / 1000, 1, '.', ''), '0'), '.') . 'k';
        }
        return number_format($amount, 0);
    };

    $monthlyLabel = $formatCompact((float) ($monthlyIncome ?? 0));
    $totalLabel = $formatCompact((float) ($totalIncome ?? 0));

    $cardImages = [
        'linear-gradient(135deg,#8fc54a,#4e8f31)',
        'linear-gradient(135deg,#7fb4d2,#355f95)',
        'linear-gradient(135deg,#4f6db5,#f2c23d)',
        'linear-gradient(135deg,#66b8ee,#2e73b7)',
        'linear-gradient(135deg,#b08b5e,#8d693e)',
        'linear-gradient(135deg,#7d9da0,#4a5e5f)',
        'linear-gradient(135deg,#696969,#3f3f3f)',
        'linear-gradient(135deg,#6c8f5f,#4f6c45)',
    ];
@endphp

<div class="min-h-screen bg-[#ECECEC] px-4 py-5 md:px-6 md:py-6">
    <div class="mb-4 flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
        <h1 class="text-2xl font-bold text-[#171717] md:text-[28px]">Team Management</h1>

        <div class="relative w-full lg:w-[460px]">
            <input
                id="teamSearchInput"
                type="text"
                placeholder="Search"
                class="h-10 w-full rounded-full border border-gray-400 bg-[#EDEDED] px-4 pr-10 text-sm text-gray-700 outline-none"
            >
            <svg class="pointer-events-none absolute right-3 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-600" viewBox="0 0 24 24" fill="none">
                <path d="M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15Z" stroke="currentColor" stroke-width="2"></path>
                <path d="M16.5 16.5 21 21" stroke="currentColor" stroke-width="2" stroke-linecap="round"></path>
            </svg>
        </div>
    </div>

    @if(session('error'))
        <div class="mb-3 rounded-lg border border-red-300 bg-red-50 px-4 py-2 text-sm text-red-700">
            {{ session('error') }}
        </div>
    @endif

    @if(session('success'))
        <div class="mb-3 rounded-lg border border-green-300 bg-green-50 px-4 py-2 text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mb-3 rounded-lg border border-red-300 bg-red-50 px-4 py-2 text-sm text-red-700">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="grid grid-cols-1 gap-5 xl:grid-cols-[300px_minmax(0,1fr)]">
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-1">
            <section class="rounded-xl border border-[#B8BAC5] bg-[#EFEFEF] p-4 shadow-[0_4px_10px_rgba(0,0,0,0.16)]">
                <div class="mx-auto h-24 w-24 overflow-hidden rounded-full border-2 border-[#F5C400] bg-[#D9D9D9]">
                    @if($memberAvatar)
                        <img src="{{ $memberAvatar }}" alt="{{ $memberName }}" class="h-full w-full object-cover">
                    @else
                        <div class="flex h-full w-full items-center justify-center text-xl font-semibold text-[#4F4F4F]">
                            {{ strtoupper(substr($memberName, 0, 2)) }}
                        </div>
                    @endif
                </div>

                <div class="mt-4 text-center text-[24px] font-semibold leading-none text-[#2D3470]">{{ $memberName }}</div>
                <div class="mt-1 text-center text-sm text-[#4E4E4E]">{{ $memberRole }}</div>

                <div class="mt-1 flex items-center justify-center gap-1 text-xs text-[#4E4E4E]">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none">
                        <path d="M12 13.5a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" stroke="currentColor" stroke-width="2"></path>
                        <path d="M19.5 10.5c0 5.25-7.5 11.25-7.5 11.25S4.5 15.75 4.5 10.5a7.5 7.5 0 1 1 15 0Z" stroke="currentColor" stroke-width="2"></path>
                    </svg>
                    {{ $memberLocation }}
                </div>

                <div class="mt-4 grid grid-cols-3 gap-2 text-center">
                    <div>
                        <div class="text-3xl font-bold leading-none text-[#111]">{{ $assignedCount }}</div>
                        <div class="mt-1 text-[11px] text-[#5D5D5D]">Current Projects</div>
                    </div>
                    <div>
                        <div class="text-3xl font-bold leading-none text-[#111]">{{ $monthlyLabel }}</div>
                        <div class="mt-1 text-[11px] text-[#5D5D5D]">Monthly</div>
                    </div>
                    <div>
                        <div class="text-3xl font-bold leading-none text-[#111]">{{ $totalLabel }}</div>
                        <div class="mt-1 text-[11px] text-[#5D5D5D]">Total Income</div>
                    </div>
                </div>

                <div class="mt-3 grid grid-cols-2 gap-2">
                    <a href="{{ route('employee.profile.show') }}" class="rounded-lg bg-[#F5C400] px-2 py-2 text-center text-xs font-medium text-[#181818]">View Profile</a>
                    <a href="{{ route('employee.profile.show') }}" class="rounded-lg bg-[#F5C400] px-2 py-2 text-center text-xs font-medium text-[#181818]">Edit Profile</a>
                </div>
            </section>

            <section class="relative overflow-hidden rounded-2xl bg-[linear-gradient(145deg,#F1D15D_0%,#F4CC3F_62%,#E8B916_100%)] p-4 shadow-[0_6px_12px_rgba(0,0,0,0.13)]">
                <div class="pointer-events-none absolute -bottom-16 -right-10 h-40 w-40 rounded-full bg-black/20"></div>

                <div class="text-2xl font-semibold leading-tight text-[#3F3F3F]">Completed Projects</div>
                <div class="mt-2 text-5xl font-bold leading-none text-[#323232]">{{ $completedCount }}</div>

                <div class="absolute bottom-4 right-4 flex h-20 w-20 items-center justify-center rounded-full border border-[#F5C400] bg-[#2E3138]">
                    <img src="{{ asset('images/logo-cms.png') }}" alt="Metalift logo" class="h-[58%] w-[58%] object-contain">
                </div>
            </section>
        </div>

        <section class="min-h-[640px] rounded-2xl border border-[#B0B1B3] bg-[#ECECEC] p-3">
            <div class="flex items-center gap-6 px-1 pb-2">
                <button type="button" data-tab="submit" class="et-tab-btn border-b-2 border-[#121212] pb-1 text-base font-semibold text-[#121212]">
                    Submit Report
                </button>
                <button type="button" data-tab="submitted" class="et-tab-btn border-b-2 border-transparent pb-1 text-base font-semibold text-[#888888]">
                    Submitted Report
                </button>
            </div>

            <div id="submitPanel" class="px-1 pt-1">
                <div id="etCards" class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                    @forelse($assignedProjects as $i => $project)
                        @php
                            $latestStatus = $latestStatusByProject[$project->id] ?? null;
                            $isPending = $latestStatus === 'pending';
                            $isCompleted = $latestStatus === 'verified';
                            $imageUrl = $project->image ? asset('storage/' . $project->image) : null;
                        @endphp
                        <article
                            class="rounded-xl border border-[#DDDDDD] bg-[#EEEEEE] p-3 shadow-[0_7px_11px_rgba(0,0,0,0.13)] {{ $isCompleted ? '!bg-[#B7B7B7]' : '' }}"
                            data-project-card="true"
                            data-search="{{ strtolower(($project->name ?? '') . ' ' . ($project->type ?? '')) }}"
                        >
                            <div class="h-20 overflow-hidden rounded-lg shadow-[0_6px_10px_rgba(0,0,0,0.26)]" style="{{ $imageUrl ? '' : 'background:' . $cardImages[$i % count($cardImages)] . ';' }}">
                                @if($imageUrl)
                                    <img src="{{ $imageUrl }}" alt="{{ $project->name }}" class="h-full w-full object-cover">
                                @endif
                            </div>

                            <div class="mt-3 truncate text-[15px] font-semibold leading-tight text-[#2C2C2C]">{{ $project->name }}</div>
                            <div class="mt-1 text-xs text-[#424242]">{{ $project->type ?? 'Project' }}</div>

                            @if($isCompleted)
                                <div class="mt-3 inline-flex items-center gap-1 text-xs text-[#202020]">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none">
                                        <path d="M20 6 9 17l-5-5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                    </svg>
                                    Completed
                                </div>
                            @elseif($isPending)
                                <button type="button" class="mt-3 w-full cursor-not-allowed rounded-lg bg-[#D7D7D7] py-2 text-xs font-medium text-[#4B4B4B]">
                                    In Progress
                                </button>
                            @else
                                <button
                                    type="button"
                                    class="js-open-upload mt-3 w-full rounded-lg bg-[#F5C400] py-2 text-xs font-medium text-[#1A1A1A]"
                                    data-submit-url="{{ route('employee.team.submit-report', $project->id) }}"
                                    data-project-name="{{ $project->name }}"
                                >
                                    Submit Report
                                </button>
                            @endif
                        </article>
                    @empty
                        <div class="col-span-full rounded-lg border border-dashed border-[#ADADAD] px-3 py-6 text-center text-sm text-[#5C5C5C]">
                            No assigned projects found.
                        </div>
                    @endforelse
                </div>

                <div id="submitEmpty" class="mt-3 hidden rounded-lg border border-dashed border-[#ADADAD] px-3 py-4 text-center text-sm text-[#5C5C5C]">
                    No projects match your search.
                </div>
            </div>

            <div id="submittedPanel" class="hidden px-1 pt-1">
                <div class="rounded-2xl border border-[#4A4A4A] bg-[#3F3F3F] p-2 shadow-[0_10px_16px_rgba(0,0,0,0.22)]">
                    <div class="overflow-x-auto">
                        <div class="min-w-[760px]">
                            <div class="grid grid-cols-12 items-center rounded-lg bg-[#3A3A3A] px-3 py-2 text-sm font-semibold text-white">
                                <div class="col-span-4">Files/Proof</div>
                                <div class="col-span-3">Project</div>
                                <div class="col-span-2">Date</div>
                                <div class="col-span-2">Status</div>
                                <div class="col-span-1"></div>
                            </div>

                            <div class="max-h-[470px] overflow-y-auto pr-1">
                        @forelse($submittedReports as $row)
                            @php
                                $fileName = $row->document?->name ?? 'No file';
                                $status = $row->status;
                                $isRejected = $status === 'denied';
                                $isProgress = $status === 'pending';
                                $isApproved = $status === 'verified';
                                $remarks = trim((string) ($row->remarks ?? ''));

                                $adminResponseUrl = null;
                                if ($row->admin_response_path) {
                                    $adminResponseUrl = str_starts_with($row->admin_response_path, 'http')
                                        ? $row->admin_response_path
                                        : asset(ltrim($row->admin_response_path, '/'));
                                }
                            @endphp
                            <div
                                class="grid grid-cols-12 items-center border-b border-[#474747] px-3 py-2.5 text-sm {{ $isRejected ? 'bg-[#5A3F3F]/80' : 'bg-[#3F3F3F]' }}"
                                data-report-row="true"
                                data-search="{{ strtolower($fileName . ' ' . ($row->project ?? '') . ' ' . optional($row->date)->format('m-d-y') . ' ' . $status) }}"
                            >
                                <div class="col-span-4 flex items-center gap-3 text-[#ECECEC]">
                                    <div class="relative h-8 w-7 overflow-hidden rounded-[2px] bg-white">
                                        <div class="absolute right-0 top-0 h-2 w-2 bg-[#DBDBDB] [clip-path:polygon(0_0,100%_100%,0_100%)]"></div>
                                        <div class="absolute bottom-0 left-0 right-0 bg-[#EF4444] py-[1px] text-center text-[8px] font-bold text-white">PDF</div>
                                    </div>
                                    <span class="truncate">{{ $fileName }}</span>
                                </div>

                                <div class="col-span-3 truncate text-[#EDEDED]">{{ $row->project ?? '-' }}</div>
                                <div class="col-span-2 text-[#DFDFDF]">{{ optional($row->date)->format('m-d-y') }}</div>

                                <div class="col-span-2">
                                    @if($isProgress)
                                        <span class="inline-flex min-w-[96px] items-center justify-center rounded-md bg-[#4338CA] px-3 py-1 text-[13px] font-medium text-white">In Progress</span>
                                    @elseif($isRejected)
                                        <span class="inline-flex min-w-[96px] items-center justify-center rounded-md bg-[#FCA5A5] px-3 py-1 text-[13px] font-semibold text-[#DC2626]">Rejected</span>
                                    @elseif($isApproved)
                                        <span class="inline-flex min-w-[96px] items-center justify-center rounded-md bg-[#43A85D] px-3 py-1 text-[13px] font-medium text-white">Approved</span>
                                    @endif
                                </div>

                                <div class="col-span-1 flex justify-center">
                                    @if($isRejected)
                                        <button
                                            type="button"
                                            class="js-view-reject text-[13px] font-semibold text-[#FF5E5E]"
                                            data-remarks="{{ e($remarks) }}"
                                            data-response-url="{{ $adminResponseUrl ?? '' }}"
                                            data-response-name="{{ e((string) ($row->admin_response_name ?? '')) }}"
                                        >
                                            VIEW
                                        </button>
                                    @elseif($isProgress)
                                        <span class="inline-flex h-7 w-7 items-center justify-center rounded-full border border-[#4F46E5] text-[#4F46E5]">
                                            <span class="h-2 w-2 rounded-full bg-[#4F46E5]"></span>
                                        </span>
                                    @elseif($isApproved)
                                        <span class="inline-flex h-7 w-7 items-center justify-center rounded-md border border-[#4FB56A] text-[#4FB56A]">
                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none">
                                                <path d="M20 6 9 17l-5-5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                            </svg>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="px-3 py-6 text-center text-sm text-[#DFDFDF]">No submitted reports yet.</div>
                        @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                <div id="submittedEmpty" class="mt-3 hidden rounded-lg border border-dashed border-[#ADADAD] px-3 py-4 text-center text-sm text-[#5C5C5C]">
                    No submitted reports match your search.
                </div>
            </div>
        </section>
    </div>
</div>

<div id="uploadModal" class="fixed inset-0 z-[120] hidden" aria-hidden="true">
    <div class="absolute inset-0 bg-black/35 backdrop-blur-[5px]" data-close-upload="1"></div>

    <div class="relative z-10 flex min-h-full items-center justify-center p-4">
        <form id="uploadForm" method="POST" enctype="multipart/form-data" class="relative w-[calc(100vw-34px)] max-w-[392px] rounded-[4px] bg-[#F8F8F8] p-4 shadow-[0_14px_22px_rgba(0,0,0,0.2)]">
            @csrf

            <button type="button" class="absolute right-2 top-2 h-7 w-7 rounded-full border border-[#BEBEBE] bg-white text-sm text-[#4B4B4B]" data-close-upload="1">
                &times;
            </button>
            <div class="mb-3 text-center text-[24px] font-semibold leading-none text-[#181818]">Upload</div>
            <div id="uploadProjectLabel" class="mb-3 text-center text-xs text-[#5D5D5D]"></div>

            <input id="reportFileInput" type="file" name="report_file" required class="hidden" accept=".jpg,.jpeg,.png,.gif,.mp4,.pdf,.psd,.ai,.doc,.docx,.ppt,.pptx">

            <div id="dropZone" class="flex min-h-[286px] flex-col items-center justify-center gap-2 rounded-[2px] border border-dashed border-[#CDD2F2] bg-[#F4F5FB] px-4 py-7 text-center">
                <svg class="h-12 w-12 text-[#E2B500]" viewBox="0 0 24 24" fill="none">
                    <path d="M7 18.5A5.5 5.5 0 1 1 8.8 7.8a6.5 6.5 0 0 1 12.2 2.8A4.4 4.4 0 0 1 19 19H7Z" stroke="currentColor" stroke-width="1.8"></path>
                    <path d="M12 16V10" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"></path>
                    <path d="m9.75 12.25 2.25-2.25 2.25 2.25" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"></path>
                </svg>

                <div class="pt-1 text-[16px] font-medium leading-tight text-[#1E1E1E]">
                    Drag &amp; drop files or
                    <button type="button" id="browseBtn" class="text-[16px] font-medium text-[#CCA300] underline">Browse</button>
                </div>

                <div class="text-[10px] text-[#8A8A8A]">Supported formats: JPEG, PNG, GIF, MP4, PDF, PSD, AI, Word, PPT</div>
                <div id="selectedFileName" class="mt-1 max-w-[95%] truncate text-[11px] text-[#4B4B4B]">No file selected.</div>
            </div>

            <button type="submit" id="submitFileBtn" class="mt-4 w-full rounded-[4px] bg-[#F0D878] py-2.5 text-[13px] font-medium tracking-[0.01em] text-[#545454]">
                SUBMIT FILE
            </button>
        </form>
    </div>
</div>

<div id="confirmModal" class="fixed inset-0 z-[120] hidden" aria-hidden="true">
    <div class="absolute inset-0 bg-black/35 backdrop-blur-[4px]" data-close-confirm="1"></div>
    <div class="relative mx-auto mt-20 w-[calc(100vw-28px)] max-w-[350px] overflow-hidden rounded-[20px] border border-[#2A2A2A] shadow-[0_18px_26px_rgba(0,0,0,0.26)]">
        <div class="relative bg-[#3E3E40] px-4 pb-16 pt-5 text-center text-[#F7F7F7]">
            <div class="mx-auto mb-3 flex h-20 w-20 items-center justify-center overflow-hidden rounded-full border-2 border-[#F5C400] bg-[#212227]">
                <img src="{{ asset('images/logo-cms.png') }}" alt="Metalift logo" class="h-[78%] w-[78%] object-contain">
            </div>
            <div class="text-[28px] font-normal leading-none">Report Submitted</div>
            <div class="absolute bottom-5 right-5 flex h-7 w-7 items-center justify-center rounded-full bg-[#F5C400] text-sm font-bold text-[#1F1F1F] shadow-[0_4px_9px_rgba(0,0,0,0.35)]">&#10003;</div>
        </div>

        <div class="bg-[#F4F4F4] px-4 pb-4 pt-3">
            <div class="mb-3 text-sm leading-relaxed text-[#252525]">It will now be forwarded to the team management for approval.</div>
            <button type="button" class="w-full rounded-[10px] bg-[#F5C400] py-2 text-sm font-semibold text-[#171717]" data-close-confirm="1">Close</button>
        </div>
    </div>
</div>

<div id="rejectDetailsModal" class="fixed inset-0 z-[120] hidden" aria-hidden="true">
    <div class="absolute inset-0 bg-black/35 backdrop-blur-[4px]" data-close-reject-details="1"></div>
    <div class="relative mx-auto mt-24 w-[calc(100vw-28px)] max-w-[420px] overflow-hidden rounded-2xl border border-[#C7C7C7] bg-white shadow-[0_16px_26px_rgba(0,0,0,0.22)]">
        <div class="flex items-center justify-between border-b border-gray-200 px-4 py-3">
            <div class="text-sm font-semibold text-[#1E1E1E]">Rejected Report Details</div>
            <button type="button" class="text-lg text-[#6A6A6A]" data-close-reject-details="1">&times;</button>
        </div>
        <div class="space-y-3 px-4 py-4">
            <div>
                <div class="mb-1 text-xs font-semibold text-[#4B4B4B]">Remarks</div>
                <div id="rejectRemarksText" class="rounded-md bg-[#F5F5F5] px-3 py-2 text-sm text-[#3A3A3A]">No remarks provided.</div>
            </div>
            <div id="rejectFileWrap" class="hidden">
                <div class="mb-1 text-xs font-semibold text-[#4B4B4B]">Admin Attachment</div>
                <a id="rejectFileLink" href="#" target="_blank" class="inline-flex items-center gap-2 rounded-md bg-[#F5C400] px-3 py-2 text-xs font-semibold text-[#1E1E1E]">
                    Open Attached File
                </a>
                <div id="rejectFileName" class="mt-1 text-xs text-gray-600"></div>
            </div>
        </div>
    </div>
</div>

<script>
    (function () {
        const tabs = Array.from(document.querySelectorAll('.et-tab-btn'));
        const cards = Array.from(document.querySelectorAll('[data-project-card="true"]'));
        const reportRows = Array.from(document.querySelectorAll('[data-report-row="true"]'));
        const searchInput = document.getElementById('teamSearchInput');

        const submitPanel = document.getElementById('submitPanel');
        const submittedPanel = document.getElementById('submittedPanel');
        const submitEmpty = document.getElementById('submitEmpty');
        const submittedEmpty = document.getElementById('submittedEmpty');

        let activeTab = 'submit';

        const setTabActive = (tab, active) => {
            tab.classList.toggle('text-[#121212]', active);
            tab.classList.toggle('border-[#121212]', active);
            tab.classList.toggle('text-[#888888]', !active);
            tab.classList.toggle('border-transparent', !active);
        };

        const switchPanels = () => {
            const isSubmit = activeTab === 'submit';
            submitPanel.classList.toggle('hidden', !isSubmit);
            submittedPanel.classList.toggle('hidden', isSubmit);
        };

        const filterSubmit = (q) => {
            let visible = 0;
            cards.forEach((card) => {
                const text = (card.dataset.search || '').toLowerCase();
                const show = text.includes(q);
                card.style.display = show ? '' : 'none';
                if (show) visible += 1;
            });
            submitEmpty.classList.toggle('hidden', visible > 0);
        };

        const filterSubmitted = (q) => {
            let visible = 0;
            reportRows.forEach((row) => {
                const text = (row.dataset.search || '').toLowerCase();
                const show = text.includes(q);
                row.style.display = show ? '' : 'none';
                if (show) visible += 1;
            });
            submittedEmpty.classList.toggle('hidden', visible > 0);
        };

        const applyFilters = () => {
            const q = (searchInput?.value || '').toLowerCase().trim();
            if (activeTab === 'submit') {
                filterSubmit(q);
            } else {
                filterSubmitted(q);
            }
        };

        tabs.forEach((tab) => {
            tab.addEventListener('click', () => {
                activeTab = tab.dataset.tab || 'submit';
                tabs.forEach((t) => setTabActive(t, t === tab));
                switchPanels();
                applyFilters();
            });
        });

        switchPanels();
        applyFilters();
        searchInput?.addEventListener('input', applyFilters);

        const uploadModal = document.getElementById('uploadModal');
        const confirmModal = document.getElementById('confirmModal');
        const rejectDetailsModal = document.getElementById('rejectDetailsModal');

        const uploadForm = document.getElementById('uploadForm');
        const openUploadButtons = document.querySelectorAll('.js-open-upload');
        const closeUploadButtons = document.querySelectorAll('[data-close-upload="1"]');
        const closeConfirmButtons = document.querySelectorAll('[data-close-confirm="1"]');
        const closeRejectDetailsButtons = document.querySelectorAll('[data-close-reject-details="1"]');

        const reportFileInput = document.getElementById('reportFileInput');
        const browseBtn = document.getElementById('browseBtn');
        const selectedFileName = document.getElementById('selectedFileName');
        const dropZone = document.getElementById('dropZone');
        const uploadProjectLabel = document.getElementById('uploadProjectLabel');

        const rejectRemarksText = document.getElementById('rejectRemarksText');
        const rejectFileWrap = document.getElementById('rejectFileWrap');
        const rejectFileLink = document.getElementById('rejectFileLink');
        const rejectFileName = document.getElementById('rejectFileName');

        const lockBody = () => {
            const hasOpen = !uploadModal.classList.contains('hidden')
                || !confirmModal.classList.contains('hidden')
                || !rejectDetailsModal.classList.contains('hidden');
            document.body.classList.toggle('overflow-hidden', hasOpen);
        };

        const setSelectedFile = () => {
            selectedFileName.textContent = reportFileInput?.files?.[0]?.name || 'No file selected.';
        };

        const openUpload = (button) => {
            reportFileInput.value = '';
            setSelectedFile();

            const submitUrl = button.getAttribute('data-submit-url') || '';
            const projectName = button.getAttribute('data-project-name') || '';
            uploadForm.action = submitUrl;
            uploadProjectLabel.textContent = projectName ? `Project: ${projectName}` : '';

            uploadModal.classList.remove('hidden');
            lockBody();
        };

        const closeUpload = () => {
            uploadModal.classList.add('hidden');
            lockBody();
        };

        const openConfirm = () => {
            confirmModal.classList.remove('hidden');
            lockBody();
        };

        const closeConfirm = () => {
            confirmModal.classList.add('hidden');
            lockBody();
        };

        const openRejectDetails = (button) => {
            const remarks = button.getAttribute('data-remarks') || '';
            const responseUrl = button.getAttribute('data-response-url') || '';
            const responseName = button.getAttribute('data-response-name') || '';

            rejectRemarksText.textContent = remarks !== '' ? remarks : 'No remarks provided.';

            if (responseUrl !== '') {
                rejectFileWrap.classList.remove('hidden');
                rejectFileLink.href = responseUrl;
                rejectFileName.textContent = responseName !== '' ? responseName : '';
            } else {
                rejectFileWrap.classList.add('hidden');
                rejectFileLink.removeAttribute('href');
                rejectFileName.textContent = '';
            }

            rejectDetailsModal.classList.remove('hidden');
            lockBody();
        };

        const closeRejectDetails = () => {
            rejectDetailsModal.classList.add('hidden');
            lockBody();
        };

        openUploadButtons.forEach((button) => {
            button.addEventListener('click', () => openUpload(button));
        });
        closeUploadButtons.forEach((button) => button.addEventListener('click', closeUpload));
        closeConfirmButtons.forEach((button) => button.addEventListener('click', closeConfirm));
        closeRejectDetailsButtons.forEach((button) => button.addEventListener('click', closeRejectDetails));

        document.querySelectorAll('.js-view-reject').forEach((button) => {
            button.addEventListener('click', () => openRejectDetails(button));
        });

        browseBtn?.addEventListener('click', () => reportFileInput?.click());
        reportFileInput?.addEventListener('change', setSelectedFile);

        ['dragenter', 'dragover'].forEach((eventName) => {
            dropZone?.addEventListener(eventName, (event) => {
                event.preventDefault();
                dropZone.classList.remove('border-[#CDD2F2]', 'bg-[#F4F5FB]');
                dropZone.classList.add('border-[#E2B500]', 'bg-[#FFFCE8]');
            });
        });

        ['dragleave', 'drop'].forEach((eventName) => {
            dropZone?.addEventListener(eventName, (event) => {
                event.preventDefault();
                dropZone.classList.remove('border-[#E2B500]', 'bg-[#FFFCE8]');
                dropZone.classList.add('border-[#CDD2F2]', 'bg-[#F4F5FB]');
            });
        });

        dropZone?.addEventListener('drop', (event) => {
            const files = event.dataTransfer?.files;
            if (!files || !files.length) return;

            try {
                reportFileInput.files = files;
                setSelectedFile();
            } catch (_) {}
        });

        uploadForm?.addEventListener('submit', () => {
            const submitBtn = document.getElementById('submitFileBtn');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.textContent = 'Submitting...';
            }
        });

        const shouldOpenConfirm = @json((bool) session('report_submitted'));
        if (shouldOpenConfirm) {
            openConfirm();
        }
    })();
</script>
@endsection
