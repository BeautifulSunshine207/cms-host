@extends('layouts.app')

@section('content')
<style>
    .soft-card { box-shadow: 0 10px 22px rgba(0,0,0,.10); }
</style>

<div class="min-h-screen bg-[#ECECEC] p-8">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Team Management</h1>
            <p class="text-sm text-gray-700 mt-2 font-semibold">Record Attendance</p>
        </div>

        <div class="w-[520px] max-w-full">
            <div class="relative">
                <input id="attendanceSearch" type="text" placeholder="Search" class="w-full rounded-full border border-gray-400 bg-[#EDEDED] px-5 py-2 pr-12 text-sm outline-none">
                <div class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-600">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                        <path d="M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15Z" stroke="currentColor" stroke-width="2"/>
                        <path d="M16.5 16.5 21 21" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    @if($errors->has('project_progress'))
        <div class="mb-4 rounded-xl border border-red-300 bg-red-50 px-4 py-3 text-sm text-red-700">
            {{ $errors->first('project_progress') }}
        </div>
    @endif

    <div class="bg-white rounded-2xl soft-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-[#F3F3F3] text-gray-700">
                    <tr>
                        <th class="text-left px-6 py-4 font-semibold">Employee</th>
                        <th class="text-left px-6 py-4 font-semibold">Proof</th>
                        <th class="text-left px-6 py-4 font-semibold">Project</th>
                        <th class="text-left px-6 py-4 font-semibold">Date</th>
                        <th class="text-left px-6 py-4 font-semibold">View</th>
                        <th class="text-left px-6 py-4 font-semibold">Decision</th>
                    </tr>
                </thead>

                <tbody id="attendanceBody" class="divide-y divide-gray-200">
                    @forelse($attendanceRecords as $rec)
                        @php
                            $member = $rec->teamMember;
                            $doc = $rec->document;
                            $projectItem = $rec->projectItem;

                            $fileName = $doc->name ?? 'File';
                            $fileType = strtolower($doc->type ?? '');
                            $fileSize = $doc->size ?? null;
                            $viewUrl = $doc ? asset($doc->path) : null;
                            $avatar = $member->avatar ?? null;
                            $role = $member->role ?? '';
                            $projectName = $rec->project ?? ($projectItem?->name ?? 'Project');
                            $currentProgress = (int) ($projectItem?->progress ?? 0);
                        @endphp

                        <tr class="attendance-row" data-search="{{ strtolower(($member->name ?? '').' '.$role.' '.$projectName.' '.$fileName) }}">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full overflow-hidden bg-gray-300 flex items-center justify-center">
                                        @if($avatar)
                                            <img src="{{ asset($avatar) }}" class="w-full h-full object-cover" alt="avatar">
                                        @else
                                            <span class="text-xs font-bold text-gray-700">
                                                {{ $member?->initials ?? strtoupper(substr($member->name ?? 'E', 0, 1)) }}
                                            </span>
                                        @endif
                                    </div>

                                    <div class="leading-tight">
                                        <div class="font-semibold text-gray-900">{{ $member->name ?? 'Unknown' }}</div>
                                        <div class="text-xs text-gray-500">{{ $role }}</div>
                                    </div>
                                </div>
                            </td>

                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded bg-gray-100 flex items-center justify-center">
                                        <span class="text-xs font-bold text-gray-700">{{ $fileType ? strtoupper($fileType) : 'FILE' }}</span>
                                    </div>

                                    <div>
                                        <div class="text-gray-900 font-semibold">{{ $fileName }}</div>
                                        <div class="text-xs text-green-600 font-semibold">{{ $fileSize ?? '' }}</div>
                                    </div>
                                </div>
                            </td>

                            <td class="px-6 py-4 text-gray-900 font-semibold">{{ $projectName }}</td>

                            <td class="px-6 py-4 text-gray-900">{{ \Carbon\Carbon::parse($rec->date)->format('m/d/y') }}</td>

                            <td class="px-6 py-4">
                                @if($viewUrl)
                                    <a href="{{ $viewUrl }}" target="_blank" class="text-blue-600 font-semibold hover:underline">VIEW</a>
                                @else
                                    <span class="text-gray-400 font-semibold">N/A</span>
                                @endif
                            </td>

                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <button type="button" class="px-5 py-2 rounded-lg bg-red-100 text-red-700 font-semibold hover:bg-red-200 inline-flex items-center gap-2" onclick="openRejectModal({{ $rec->id }})">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                                            <path d="M18 6 6 18M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                        </svg>
                                        Reject
                                    </button>

                                    <button
                                        type="button"
                                        class="px-5 py-2 rounded-lg bg-green-100 text-green-700 font-semibold hover:bg-green-200 inline-flex items-center gap-2"
                                        onclick="openApproveModal({{ $rec->id }}, @js($projectName), {{ $currentProgress }})"
                                    >
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                                            <path d="M20 6 9 17l-5-5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                        Approve
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-gray-500">No pending attendance reports found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div id="approveModal" class="fixed inset-0 hidden z-50">
        <div class="absolute inset-0 bg-black/40" onclick="closeApproveModal()"></div>

        <div class="relative max-w-xl mx-auto mt-24 bg-white rounded-2xl soft-card overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-lg font-bold text-gray-900">Approve Report and Update Project Progress</h3>
                <button type="button" onclick="closeApproveModal()" class="text-gray-500 hover:text-gray-800">x</button>
            </div>

            <form id="approveForm" method="POST" action="" class="p-6 space-y-4">
                @csrf
                <input type="hidden" name="attendance_id" id="approveAttendanceId" value="{{ old('attendance_id') }}">
                <input type="hidden" name="project_name" id="approveProjectNameInput" value="{{ old('project_name') }}">

                <div class="rounded-lg border border-gray-200 bg-gray-50 p-3 text-sm text-gray-700">
                    Project: <span id="approveProjectName" class="font-semibold text-gray-900">{{ old('project_name', 'Project') }}</span>
                </div>

                <div>
                    <label for="projectProgressInput" class="text-sm font-semibold text-gray-700">Project Progress (%)</label>
                    <input
                        id="projectProgressInput"
                        type="number"
                        name="project_progress"
                        min="0"
                        max="100"
                        step="1"
                        required
                        value="{{ old('project_progress') }}"
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 outline-none focus:border-[#3b82f6]"
                    >
                    <p class="mt-1 text-xs text-gray-500">Enter a value from 0 to 100. Project is completed only at 100%.</p>
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" onclick="closeApproveModal()" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-100">Cancel</button>
                    <button type="submit" class="rounded-lg bg-yellow-500 px-5 py-2 text-sm font-semibold text-black hover:bg-yellow-600">Approve and Update</button>
                </div>
            </form>
        </div>
    </div>

    <div id="rejectModal" class="fixed inset-0 hidden z-50">
        <div class="absolute inset-0 bg-black/40"></div>

        <div class="relative max-w-5xl mx-auto mt-16 bg-white rounded-3xl overflow-hidden soft-card">
            <div class="p-8 grid grid-cols-12 gap-6">
                <button class="absolute top-5 right-5 w-9 h-9 rounded-full border border-yellow-500 text-yellow-600 flex items-center justify-center hover:bg-yellow-50" onclick="closeRejectModal()">x</button>

                <div class="col-span-12 lg:col-span-6">
                    <div class="flex items-center gap-2 mb-6">
                        <span class="inline-block w-3 h-3 rounded-full bg-yellow-500"></span>
                        <span class="inline-block w-12 h-[2px] bg-yellow-500"></span>
                        <span class="inline-block w-3 h-3 rounded-full bg-yellow-500"></span>
                        <span class="inline-block w-12 h-[2px] bg-yellow-500"></span>
                        <span class="inline-block w-3 h-3 rounded-full bg-yellow-500"></span>
                    </div>

                    <h2 class="text-2xl font-extrabold text-gray-900 mb-4">Remarks</h2>

                    <div class="border rounded-xl p-4 text-sm text-gray-700 mb-4">
                        A rejection remark is required. Enter the reason below.
                    </div>

                    <form id="rejectForm" method="POST" action="" enctype="multipart/form-data">
                        @csrf
                        <textarea name="remarks" class="w-full border rounded-xl p-4 h-64 outline-none focus:ring-2 focus:ring-yellow-200" placeholder="Type here:" required></textarea>

                        <div class="mt-3">
                            <label class="block text-xs text-gray-600 mb-1">Attach file for employee (optional)</label>
                            <input id="adminResponseFile" type="file" name="admin_response_file" accept=".jpg,.jpeg,.png,.gif,.mp4,.pdf,.psd,.ai,.doc,.docx,.ppt,.pptx" class="w-full border rounded-xl p-3 text-sm outline-none focus:ring-2 focus:ring-yellow-200">
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 text-black font-bold px-8 py-3 rounded-md">Send Now</button>
                        </div>
                    </form>
                </div>

                <div class="col-span-12 lg:col-span-6 flex flex-col items-center justify-center gap-6">
                    <div class="w-48 h-48 rounded-full bg-[#2E2E2E] flex items-center justify-center border-4 border-yellow-200">
                        <img src="{{ asset('images/metalift-logo.png') }}" alt="Logo" class="w-32 h-32 object-contain" onerror="this.style.display='none'">
                    </div>

                    <div class="w-full border-2 border-dashed border-blue-200 rounded-2xl p-10 text-center bg-[#F7F9FF]">
                        <div class="text-4xl mb-3">Cloud</div>
                        <div class="text-sm font-semibold text-gray-700">Drag and drop files or <span class="text-blue-600 underline">Browse</span></div>
                        <div class="text-xs text-gray-500 mt-2">Supported formats: JPEG, PNG, GIF, MP4, PDF, PSD, AI, Word, PPT</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="approvedModal" class="fixed inset-0 hidden z-50">
        <div class="absolute inset-0 bg-black/40"></div>

        <div class="relative max-w-md mx-auto mt-40 rounded-3xl overflow-hidden soft-card">
            <div class="bg-[#3E3E3E] p-8 text-center text-white">
                <div class="w-20 h-20 rounded-full overflow-hidden bg-[#2E2E2E] mx-auto flex items-center justify-center mb-4">
                    <img src="{{ asset('images/logo-cms-circle.png') }}" alt="Logo" class="w-full h-full object-cover" onerror="this.style.display='none'">
                </div>
                <div class="text-2xl font-extrabold">Report Approved</div>

                <div class="mt-4 inline-flex items-center justify-center w-10 h-10 rounded-full bg-yellow-500 text-black font-bold">OK</div>
            </div>

            <div class="bg-white p-6 text-center">
                <p class="text-sm text-gray-700 mb-5">Report validated and forwarded to Finance.</p>

                <button class="w-full bg-yellow-500 hover:bg-yellow-600 text-black font-bold py-3 rounded-xl" onclick="closeApprovedModal()">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    (function () {
        const input = document.getElementById('attendanceSearch');
        const rows = document.querySelectorAll('.attendance-row');

        input?.addEventListener('input', () => {
            const q = (input.value || '').toLowerCase().trim();
            rows.forEach((row) => {
                const s = row.getAttribute('data-search') || '';
                row.style.display = s.includes(q) ? '' : 'none';
            });
        });
    })();

    function openApproveModal(id, projectName, currentProgress) {
        const modal = document.getElementById('approveModal');
        const form = document.getElementById('approveForm');
        const progressInput = document.getElementById('projectProgressInput');
        const projectNameLabel = document.getElementById('approveProjectName');
        const projectNameInput = document.getElementById('approveProjectNameInput');
        const attendanceIdInput = document.getElementById('approveAttendanceId');

        form.action = "{{ url('/team/attendance') }}/" + id + "/approve";
        projectNameLabel.textContent = projectName || 'Project';
        projectNameInput.value = projectName || 'Project';
        attendanceIdInput.value = id;

        if (!progressInput.value) {
            progressInput.value = Number.isFinite(currentProgress) ? currentProgress : 0;
        }

        modal.classList.remove('hidden');
    }

    function closeApproveModal() {
        const modal = document.getElementById('approveModal');
        const progressInput = document.getElementById('projectProgressInput');
        modal.classList.add('hidden');
        if (!{{ $errors->has('project_progress') ? 'true' : 'false' }}) {
            progressInput.value = '';
        }
    }

    function openRejectModal(id) {
        const modal = document.getElementById('rejectModal');
        const form = document.getElementById('rejectForm');
        form.action = "{{ url('/team/attendance') }}/" + id + "/reject";
        form.reset();
        modal.classList.remove('hidden');
    }

    function closeRejectModal() {
        document.getElementById('rejectModal').classList.add('hidden');
    }

    function openApprovedModal() {
        document.getElementById('approvedModal').classList.remove('hidden');
    }

    function closeApprovedModal() {
        document.getElementById('approvedModal').classList.add('hidden');
    }

    @if($errors->has('project_progress'))
        openApproveModal(
            {{ (int) old('attendance_id', 0) }},
            @js(old('project_name', 'Project')),
            {{ (int) old('project_progress', 0) }}
        );
    @endif

    @if(session('success') === 'Attendance approved')
        openApprovedModal();
    @endif
</script>
@endsection
