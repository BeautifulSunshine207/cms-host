@extends('layouts.app')

@section('content')
@php
    $selectedId = request('member');
    $selected = $selectedId ? $teamMembers->firstWhere('id', (int)$selectedId) : null;
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
    <!--heder-->
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

    <!--body-->
    <div class="grid grid-cols-12 gap-10 flex-1 min-h-0">
        <!--left employee list-->
        <div class="col-span-12 lg:col-span-4">
            <div class="bg-[#3E3E3E] rounded-2xl soft-card overflow-hidden h-[70vh] flex flex-col min-h-[645px]">
                <div class="px-6 py-5">
                    <h2 class="text-white text-lg font-bold">Employee’s Documents</h2>
                    <p class="text-gray-300 text-sm mt-2 font-semibold">Name</p>
                </div>

                <div id="membersList" class="flex-1 overflow-y-auto">
                @foreach($teamMembers as $m)
                    @php
                        $active = $selected && $selected->id === $m->id;
                        $hasReq = (bool) $m->pendingUpdateRequest;

                        //if active
                        $href = $active
                            ? route('team.documents')
                            : route('team.documents', ['member' => $m->id]);
                    @endphp

                    <a href="{{ $href }}"
                    class="member-row {{ $active ? 'is-active' : '' }} flex items-center justify-between px-6 py-4 border-t border-gray-700 hover:bg-[#4A4A4A]"
                    data-search="{{ strtolower(($m->name ?? '').' '.($m->role ?? '')) }}"
                    >
                        <!--left contet-->
                        <div class="flex items-center gap-3 min-w-0">
                            <!--avatar and name role-->
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

                            @if($hasReq)
                                <span class="ml-2 inline-flex items-center justify-center w-6 h-6 rounded bg-yellow-500 text-black font-bold">
                                    ✎
                                </span>
                            @endif
                        </div>

                        <!--arow-->
                        <div class="text-white transition hover:text-yellow-400">
                            <!--arrow rigt-->
                            <svg xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                class="size-5 member-arrow">
                                <path stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M8.25 4.5 15.75 12l-7.5 7.5"/>
                            </svg>
                        </div>

                    </a>
                @endforeach

                </div>
            </div>
        </div>

        <!--rigt detials-->
        <div class="col-span-12 lg:col-span-8">
            @if(!$selected)
                <div class="bg-[#ECECEC] rounded-3xl soft-card p-10 h-full min-h-[645px] flex flex-col items-center justify-center text-center text-gray-500">
                    <div class="text-lg font-semibold">
                        Select an employee to view details
                    </div>

                    <img src="{{ asset('images/cmslogoce.png') }}"
                        alt="Logo"
                        class="mt-8 w-[350px] h-[350px] object-contain">
                </div>
            @else
                <div class="bg-[#F6F6F6] rounded-3xl soft-card p-10 overflow-y-auto details-panel">
                    <div class="grid grid-cols-12 gap-10">
                        <!--personel detials-->
                        <div class="col-span-12 md:col-span-6">
                            <div class="text-blue-500 font-semibold mb-4">Personal Details</div>

                            <div class="flex items-start gap-6">
                                <div class="w-44 h-44 rounded-2xl overflow-hidden bg-gray-300">
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
                                        <div class="text-gray-500">Gender</div>
                                        <div class="font-semibold text-gray-900">{{ $selected->gender }}</div>
                                    </div>
                                    <div>
                                        <div class="text-gray-500">Date of Birth</div>
                                        <div class="font-semibold text-gray-900">
                                            {{ \Carbon\Carbon::parse($selected->date_of_birth)->format('M d, Y') }}
                                        </div>
                                    </div>
                                    <div>
                                        <div class="text-gray-500">Nationality</div>
                                        <div class="font-semibold text-gray-900">{{ $selected->nationality }}</div>
                                    </div>
                                </div>
                            </div>

                            <!--address-->
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

                            <!--contact and employe detials-->
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

                        <!--submitted docs-->
                        <div class="col-span-12 md:col-span-6 ">
                            <div class="text-blue-500 font-semibold mb-4 text-right">Submitted Documents</div>

                            <div class="rounded-2xl overflow-hidden bg-gray-300 h-44 mb-6">
                                <!--placehoder bill image-->
                                <div class="w-full h-full bg-gradient-to-br from-gray-200 to-gray-400"></div>
                            </div>

                            <div class="border-2 border-dashed border-gray-300 rounded-2xl p-6">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="font-semibold text-gray-900">Full Details</div>
                                    <div class="text-gray-600 text-xl">👤</div>
                                </div>

                                <div class="text-xs text-gray-500 mb-4">
                                    Resume, Medical History, Certificates, etc.
                                </div>

                                <div class="space-y-3">
                                    @forelse($selected->documents as $doc)
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center gap-3">
                                                <span class="bg-red-100 text-red-700 text-xs font-bold px-2 py-1 rounded">
                                                    {{ strtoupper($doc->type) }}
                                                </span>
                                                <span class="text-sm font-semibold text-gray-800">{{ $doc->name }}</span>
                                            </div>
                                            <span class="text-sm text-gray-500">{{ $doc->size }}</span>
                                        </div>
                                    @empty
                                        <div class="text-sm text-gray-500">No documents submitted.</div>
                                    @endforelse
                                </div>
                            </div>

                            <!--view update req btn-->
                            @if($selected->pendingUpdateRequest)
                                <button
                                    class="mt-6 w-full bg-yellow-500 hover:bg-yellow-600 text-black font-bold py-3 rounded-lg"
                                    onclick="openReqModal()"
                                >
                                    View Update Request
                                </button>
                            @endif
                        </div>

                    </div>
                </div>

                <!--update req modal-->
                @php $req = $selected->pendingUpdateRequest; @endphp
                @if($req)
                    <div id="reqModal" class="fixed inset-0 hidden z-50">
                        <div class="absolute inset-0 bg-black/40"></div>

                        <div class="relative max-w-4xl mx-auto mt-28 bg-[#3E3E3E] text-white rounded-2xl soft-card overflow-hidden">
                            <div class="px-8 py-5 font-bold text-lg flex items-center justify-between">
                                <span>Information</span>
                                <button onclick="closeReqModal()" class="text-gray-200 hover:text-white">✕</button>
                            </div>

                            <div class="px-8 pb-6">
                                <div class="grid grid-cols-3 text-sm font-semibold bg-[#4A4A4A] rounded-lg overflow-hidden">
                                    <div class="px-4 py-3">Information</div>
                                    <div class="px-4 py-3">From</div>
                                    <div class="px-4 py-3">To</div>
                                </div>

                                @php
                                    //only show key in req changes
                                    $changes = $req->changes ?? [];
                                    $labelMap = [
                                        'name' => 'Name',
                                        'role' => 'Role',
                                        'address_city' => 'City',
                                        'email' => 'Email',
                                        'phone' => 'Phone Number',
                                    ];
                                @endphp

                                <div class="divide-y divide-gray-600">
                                    @foreach($changes as $field => $toVal)
                                        <div class="grid grid-cols-3 text-sm">
                                            <div class="px-4 py-3 text-gray-200">
                                                {{ $labelMap[$field] ?? ucfirst(str_replace('_',' ', $field)) }}:
                                            </div>
                                            <div class="px-4 py-3">
                                                {{ data_get($selected, $field) }}
                                            </div>
                                            <div class="px-4 py-3">
                                                {{ $toVal }}
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="flex items-center justify-end gap-3 mt-6">
                                    <form method="POST" action="{{ route('team.update-requests.approve', $req->id) }}">
                                        @csrf
                                        <button class="bg-yellow-500 hover:bg-yellow-600 text-black font-bold px-6 py-2 rounded-lg">
                                            ✓ Approve
                                        </button>
                                    </form>

                                    <form method="POST" action="{{ route('team.update-requests.reject', $req->id) }}">
                                        @csrf
                                        <input name="remarks" required placeholder="Reason..." class="px-3 py-2 rounded-lg text-black">
                                        <button class="bg-red-100 hover:bg-red-200 text-red-700 font-bold px-6 py-2 rounded-lg">
                                            ✕ Reject
                                        </button>
                                    </form>
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
    // filter left list
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

    function openReqModal(){ document.getElementById('reqModal')?.classList.remove('hidden'); }
    function closeReqModal(){ document.getElementById('reqModal')?.classList.add('hidden'); }
</script>
@endsection
