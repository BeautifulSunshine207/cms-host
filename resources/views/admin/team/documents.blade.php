@extends('layouts.app')

@section('content')
@php
    $selectedId = request('member');
    $selected = $selectedId ? $teamMembers->firstWhere('id', (int) $selectedId) : null;
    $pendingDocRequests = ($selected && ($hasDocumentRequestTable ?? false)) ? $selected->pendingDocumentRequests : collect();
    $hasPendingDocRequests = $pendingDocRequests->isNotEmpty();
@endphp

<style>
    .soft-card { box-shadow: 0 10px 22px rgba(0,0,0,.10); }
    #membersList { scrollbar-width: thin; scrollbar-color: #5A5A5A #3E3E3E; }
    #membersList::-webkit-scrollbar { width: 10px; }
    #membersList::-webkit-scrollbar-track { background: #3E3E3E; }
    #membersList::-webkit-scrollbar-thumb { background: #5A5A5A; border-radius: 999px; border: 2px solid #3E3E3E; }
    #membersList::-webkit-scrollbar-thumb:hover { background: #6A6A6A; }
    .member-row { transition: background-color .15s ease, transform .15s ease; }
    .member-row:active { transform: scale(0.99); }
    .member-arrow { transition: transform .2s ease; }
    .member-row.is-active .member-arrow { transform: rotate(180deg); }
    .details-panel { animation: detailsIn .2s ease-out; }
    @keyframes detailsIn { from { opacity: 0; transform: translateY(6px); } to { opacity: 1; transform: translateY(0); } }
</style>

<div class="min-h-screen bg-[#ECECEC] p-8 flex flex-col">
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

    <div class="grid grid-cols-12 gap-10 flex-1 min-h-0">
        <div class="col-span-12 lg:col-span-4">
            <div class="bg-[#3E3E3E] rounded-2xl soft-card overflow-hidden h-[70vh] flex flex-col min-h-[645px]">
                <div class="px-6 py-5">
                    <h2 class="text-white text-lg font-bold">Employee Documents</h2>
                    <p class="text-gray-300 text-sm mt-2 font-semibold">Name</p>
                </div>

                <div id="membersList" class="flex-1 overflow-y-auto">
                @foreach($teamMembers as $m)
                    @php
                        $active = $selected && $selected->id === $m->id;
                        $hasUpdateReq = ($hasUpdateTable ?? false) ? (bool) $m->pendingUpdateRequest : false;
                        $pendingDocCount = ($hasDocumentRequestTable ?? false) ? $m->pendingDocumentRequests->count() : 0;
                        $pendingTotal = ($hasUpdateReq ? 1 : 0) + $pendingDocCount;

                        $href = $active
                            ? route('team.documents')
                            : route('team.documents', ['member' => $m->id]);
                    @endphp

                    <a href="{{ $href }}"
                       class="member-row {{ $active ? 'is-active' : '' }} flex items-center justify-between px-6 py-4 border-t border-gray-700 hover:bg-[#4A4A4A]"
                       data-search="{{ strtolower(($m->name ?? '').' '.($m->role ?? '')) }}">
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

                            @if($pendingTotal > 0)
                                <span class="ml-2 inline-flex items-center justify-center min-w-6 h-6 px-2 rounded bg-yellow-500 text-black text-xs font-bold">
                                    {{ $pendingTotal }}
                                </span>
                            @endif
                        </div>

                        <div class="text-white transition hover:text-yellow-400">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="size-5 member-arrow">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.25 4.5 15.75 12l-7.5 7.5"/>
                            </svg>
                        </div>
                    </a>
                @endforeach
                </div>
            </div>
        </div>

        <div class="col-span-12 lg:col-span-8">
            @if(!$selected)
                <div class="bg-[#ECECEC] rounded-3xl soft-card p-10 h-full min-h-[645px] flex flex-col items-center justify-center text-center text-gray-500">
                    <div class="text-lg font-semibold">Select an employee to view details</div>

                    <img src="{{ asset('images/cmslogoce.png') }}" alt="Logo" class="mt-8 w-[350px] h-[350px] object-contain">
                </div>
            @else
                <div class="bg-[#F6F6F6] rounded-3xl soft-card p-10 overflow-y-auto details-panel">
                    <div class="grid grid-cols-12 gap-10">
                        <div class="col-span-12 md:col-span-6">
                            <div class="text-blue-500 font-semibold mb-4">Personal Details</div>

                            <div class="flex items-start gap-6">
                                <div class="w-44 h-44 rounded-2xl overflow-hidden bg-gray-300">
                                    @if($selected->avatar)
                                        <img src="{{ asset($selected->avatar) }}" class="w-full h-full object-cover" alt="avatar">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-gray-700 font-bold">PHOTO</div>
                                    @endif
                                </div>

                                <div class="space-y-4 text-sm">
                                    <div>
                                        <div class="text-gray-500">Name</div>
                                        <div class="font-semibold text-gray-900">{{ $selected->name }}</div>
                                    </div>
                                    <div>
                                        <div class="text-gray-500">Gender</div>
                                        <div class="font-semibold text-gray-900">{{ $selected->gender }}</div>
                                    </div>
                                    <div>
                                        <div class="text-gray-500">Date of Birth</div>
                                        <div class="font-semibold text-gray-900">{{ \Carbon\Carbon::parse($selected->date_of_birth)->format('M d, Y') }}</div>
                                    </div>
                                    <div>
                                        <div class="text-gray-500">Nationality</div>
                                        <div class="font-semibold text-gray-900">{{ $selected->nationality }}</div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4">
                                <div class="text-blue-500 font-semibold mb-0">Address</div>
                                <div class="grid grid-cols-2 gap-1 text-sm">
                                    <div>
                                        <div class="text-gray-500">Address Line</div>
                                        <div class="font-semibold">{{ $selected->address_line }}</div>
                                    </div>
                                    <div>
                                        <div class="text-gray-500">City</div>
                                        <div class="font-semibold">{{ $selected->address_city }}</div>
                                    </div>
                                    <div>
                                        <div class="text-gray-500">State</div>
                                        <div class="font-semibold">{{ $selected->address_state }}</div>
                                    </div>
                                    <div>
                                        <div class="text-gray-500">Country</div>
                                        <div class="font-semibold">Philippines</div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4 border-t border-gray-500 pt-4">
                                <div class="text-blue-500 font-semibold mb-0">Contact Details</div>
                                <div class="grid grid-cols-2 gap-4 text-sm mb-4">
                                    <div>
                                        <div class="text-gray-500">Phone Number</div>
                                        <div class="font-semibold">{{ $selected->phone }}</div>
                                    </div>
                                    <div>
                                        <div class="text-gray-500">Email</div>
                                        <div class="font-semibold">{{ $selected->email }}</div>
                                    </div>
                                </div>

                                <div class="text-blue-500 font-semibold mb-0 border-t border-gray-500 pt-4">Employee Details</div>
                                <div class="grid grid-cols-2 gap-4 text-sm">
                                    <div>
                                        <div class="text-gray-500">Role</div>
                                        <div class="font-semibold">{{ $selected->role }}</div>
                                    </div>
                                    <div>
                                        <div class="text-gray-500">Location</div>
                                        <div class="font-semibold">{{ $selected->location }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-span-12 md:col-span-6">
                            <div class="text-blue-500 font-semibold mb-4 text-right">Submitted Documents</div>

                            <div class="rounded-2xl overflow-hidden bg-gray-300 h-44 mb-6">
                                <div class="w-full h-full bg-gradient-to-br from-gray-200 to-gray-400"></div>
                            </div>

                            <div class="border-2 border-dashed border-gray-300 rounded-2xl p-6">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="font-semibold text-gray-900">Full Details</div>
                                    <div class="text-gray-600 text-xl">DOC</div>
                                </div>

                                <div class="text-xs text-gray-500 mb-4">Resume, Medical History, Certificates, and IDs</div>

                                <div class="space-y-3">
                                    @forelse($selected->documents as $doc)
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center gap-3 min-w-0">
                                                <span class="bg-red-100 text-red-700 text-xs font-bold px-2 py-1 rounded">
                                                    {{ strtoupper($doc->type) }}
                                                </span>
                                                <span class="text-sm font-semibold text-gray-800 truncate" title="{{ $doc->name }}">{{ $doc->name }}</span>
                                            </div>
                                            <a href="{{ asset($doc->path) }}" target="_blank" class="text-xs font-semibold text-blue-600 hover:underline">View</a>
                                        </div>
                                    @empty
                                        <div class="text-sm text-gray-500">Employee has not submitted any document/s at the moment.</div>
                                    @endforelse
                                </div>
                            </div>

                            @if(($hasUpdateTable ?? false) && $selected->pendingUpdateRequest)
                                <button class="mt-6 w-full bg-yellow-500 hover:bg-yellow-600 text-black font-bold py-3 rounded-lg" onclick="openReqModal()">
                                    View Update Request
                                </button>
                            @endif

                            <button
                                class="mt-3 w-full {{ $hasPendingDocRequests ? 'bg-[#3E3E3E] hover:bg-[#2f2f2f] text-white' : 'bg-gray-300 text-gray-500 cursor-not-allowed' }} font-bold py-3 rounded-lg"
                                onclick="openDocReqModal()"
                                {{ $hasPendingDocRequests ? '' : 'disabled' }}
                            >
                                View Submitted/Updated Documents
                                @if($hasPendingDocRequests)
                                    ({{ $pendingDocRequests->count() }})
                                @endif
                            </button>
                        </div>
                    </div>
                </div>

                @php $req = ($hasUpdateTable ?? false) ? $selected->pendingUpdateRequest : null; @endphp
                @if($req)
                    <div id="reqModal" class="fixed inset-0 hidden z-50">
                        <div class="absolute inset-0 bg-black/40" onclick="closeReqModal()"></div>

                        <div class="relative max-w-4xl mx-auto mt-28 bg-[#3E3E3E] text-white rounded-2xl soft-card overflow-hidden">
                            <div class="px-8 py-5 font-bold text-lg flex items-center justify-between">
                                <span>Information</span>
                                <button onclick="closeReqModal()" class="text-gray-200 hover:text-white">x</button>
                            </div>

                            <div class="px-8 pb-6">
                                <div class="grid grid-cols-3 text-sm font-semibold bg-[#4A4A4A] rounded-lg overflow-hidden">
                                    <div class="px-4 py-3">Information</div>
                                    <div class="px-4 py-3">From</div>
                                    <div class="px-4 py-3">To</div>
                                </div>

                                @php
                                    $changes = $req->changes ?? [];
                                    $labelMap = [
                                        'name' => 'Name',
                                        'role' => 'Role',
                                        'location' => 'Location',
                                        'gender' => 'Gender',
                                        'date_of_birth' => 'Date of Birth',
                                        'nationality' => 'Nationality',
                                        'address_line' => 'Address Line',
                                        'address_city' => 'City',
                                        'address_state' => 'State',
                                        'email' => 'Email',
                                        'phone' => 'Phone Number',
                                        'avatar' => 'Verification Photo',
                                    ];
                                @endphp

                                <div class="divide-y divide-gray-600">
                                    @foreach($changes as $field => $toVal)
                                        <div class="grid grid-cols-3 text-sm">
                                            <div class="px-4 py-3 text-gray-200">{{ $labelMap[$field] ?? ucfirst(str_replace('_', ' ', $field)) }}:</div>
                                            <div class="px-4 py-3">{{ data_get($selected, $field) }}</div>
                                            <div class="px-4 py-3 break-words">{{ is_scalar($toVal) ? $toVal : json_encode($toVal) }}</div>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="flex items-center justify-end gap-3 mt-6">
                                    <form method="POST" action="{{ route('team.update-requests.approve', $req->id) }}">
                                        @csrf
                                        <button class="bg-yellow-500 hover:bg-yellow-600 text-black font-bold px-6 py-2 rounded-lg">
                                            Approve
                                        </button>
                                    </form>

                                    <form method="POST" action="{{ route('team.update-requests.reject', $req->id) }}" class="flex items-center gap-2">
                                        @csrf
                                        <input name="remarks" required placeholder="Reason..." class="px-3 py-2 rounded-lg text-black">
                                        <button class="bg-red-100 hover:bg-red-200 text-red-700 font-bold px-6 py-2 rounded-lg">
                                            Reject
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if($hasPendingDocRequests)
                    <div id="docReqModal" class="fixed inset-0 hidden z-50">
                        <div class="absolute inset-0 bg-black/40" onclick="closeDocReqModal()"></div>

                        <div class="relative max-w-5xl mx-auto mt-20 bg-white rounded-2xl soft-card overflow-hidden">
                            <div class="px-8 py-5 font-bold text-lg flex items-center justify-between border-b border-gray-200">
                                <span>Submitted/Updated Documents for Approval</span>
                                <button onclick="closeDocReqModal()" class="text-gray-500 hover:text-gray-900">x</button>
                            </div>

                            <div class="p-6 max-h-[70vh] overflow-y-auto">
                                <div class="grid grid-cols-12 gap-2 bg-gray-100 text-xs font-semibold text-gray-700 rounded-lg px-4 py-2 mb-3">
                                    <div class="col-span-4">Document</div>
                                    <div class="col-span-2">Type</div>
                                    <div class="col-span-2">Size</div>
                                    <div class="col-span-4 text-right">Actions</div>
                                </div>

                                <div class="space-y-3">
                                    @foreach($pendingDocRequests as $docReq)
                                        <div class="grid grid-cols-12 gap-2 items-center border border-gray-200 rounded-lg px-4 py-3">
                                            <div class="col-span-4 min-w-0">
                                                <a href="{{ asset($docReq->path) }}" target="_blank" class="text-sm font-semibold text-blue-600 hover:underline truncate block" title="{{ $docReq->name }}">
                                                    {{ $docReq->name }}
                                                </a>
                                            </div>
                                            <div class="col-span-2 text-sm text-gray-700">{{ $docReq->type }}</div>
                                            <div class="col-span-2 text-sm text-gray-700">{{ $docReq->size }}</div>
                                            <div class="col-span-4 flex justify-end gap-2">
                                                <form method="POST" action="{{ route('team.document-requests.approve', $docReq->id) }}">
                                                    @csrf
                                                    <button class="bg-yellow-500 hover:bg-yellow-600 text-black text-sm font-semibold px-4 py-2 rounded-lg">
                                                        Approve
                                                    </button>
                                                </form>

                                                <form method="POST" action="{{ route('team.document-requests.reject', $docReq->id) }}" class="flex items-center gap-2">
                                                    @csrf
                                                    <input name="remarks" required placeholder="Reason" class="px-2 py-2 rounded border border-gray-300 text-sm w-32">
                                                    <button class="bg-red-100 hover:bg-red-200 text-red-700 text-sm font-semibold px-4 py-2 rounded-lg">
                                                        Reject
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>

<script>
    (function () {
        const input = document.getElementById('memberSearch');
        const rows = document.querySelectorAll('.member-row');

        input?.addEventListener('input', () => {
            const q = (input.value || '').toLowerCase().trim();
            rows.forEach((row) => {
                const s = row.getAttribute('data-search') || '';
                row.style.display = s.includes(q) ? '' : 'none';
            });
        });
    })();

    function openReqModal() { document.getElementById('reqModal')?.classList.remove('hidden'); }
    function closeReqModal() { document.getElementById('reqModal')?.classList.add('hidden'); }

    function openDocReqModal() { document.getElementById('docReqModal')?.classList.remove('hidden'); }
    function closeDocReqModal() { document.getElementById('docReqModal')?.classList.add('hidden'); }
</script>
@endsection



