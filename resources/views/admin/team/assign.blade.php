@extends('layouts.app')

@section('content')
@php
    $selectedProjectId = request('project');
    $selectedProject = null;

    if ($selectedProjectId) {
   
        $selectedProject = $projects->firstWhere('id', (int)$selectedProjectId);
    }
@endphp

<style>
    
    .soft-card { box-shadow: 0 10px 22px rgba(0,0,0,.10); }
    .soft-card-2 { box-shadow: 0 14px 28px rgba(0,0,0,.12); }
</style>

<div class="min-h-screen bg-[#ECECEC] p-8">
    <!--header row-->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-bold text-gray-900">
                {{ $selectedProject ? 'Team Management - Assign Roles' : 'Team Management' }}
            </h1>

            @if(!$selectedProject)
                <p class="text-sm text-gray-700 mt-3 font-semibold">Select a Project</p>
            @endif
        </div>

        <!--serch bar (rigt side)-->
        <div class="w-[480px] max-w-full">
            <div class="relative">
                <input
                    id="{{ $selectedProject ? 'employeeSearch' : 'projectSearch' }}"
                    type="text"
                    placeholder="Search"
                    class="w-full rounded-full border border-gray-400 bg-[#EDEDED] px-5 py-2 pr-12 text-sm outline-none"
                >
                <div class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-600">
                    <!--magnefier-->
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                        <path d="M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15Z" stroke="currentColor" stroke-width="2"/>
                        <path d="M16.5 16.5 21 21" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!--flash messages-->
    @if(session('success'))
        <div class="mb-6 rounded-xl bg-green-50 border border-green-200 text-green-800 px-4 py-3">
            {{ session('success') }}
        </div>
    @endif
    @if($errors->any())
        <div class="mb-6 rounded-xl bg-red-50 border border-red-200 text-red-800 px-4 py-3">
            <div class="font-semibold mb-1">Fix the errors:</div>
            <ul class="list-disc pl-5">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-----------------------------------------state 1: select project------------------------------>
    <div class="min-h-[600px] rounded-3xl bg-[#ECECEC] p-6 border border-gray-400">
    @if(!$selectedProject)
            
            <div id="projectsGrid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-8">
            @foreach($projects as $p)
                @php
                    $img = $p->image_path ?? $p->image ?? $p->photo ?? null;
                    $imgUrl = null;
                    if ($img) {
                        if (\Illuminate\Support\Str::startsWith($img, ['http://', 'https://'])) {
                            $imgUrl = $img;
                        } elseif (\Illuminate\Support\Str::startsWith($img, ['/','storage/','images/'])) {
                            $imgUrl = asset(ltrim($img, '/'));
                        } else {
                            $imgUrl = asset('storage/'.$img);
                        }
                    }
                    $progress = $p->progress ?? $p->progress_percentage ?? null; //<!--aint final-->
                    $isCompleted = (strtolower($p->status ?? '') === 'completed');
                @endphp

                <a
                    href="{{ route('team.assign', ['project' => $p->id]) }}"
                    class="project-card block rounded-2xl bg-[#F2F2F2] soft-card overflow-hidden hover:scale-[1.01] transition"
                    data-title="{{ strtolower($p->name ?? '') }}"
                >
                    <div class="p-4">
                        <!--imgae-->
                        <div class="rounded-xl overflow-hidden soft-card-2 bg-gray-300 h-24 shadow-2xl">
                            @if($imgUrl)
                                <img src="{{ $imgUrl }}" class="w-full h-full object-cover" alt="Project image">
                            @else
                                <div class="w-full h-full bg-gradient-to-r from-gray-300 to-gray-200"></div>
                            @endif
                        </div>

                        <!--txt-->
                        <div class="mt-4">
                            <div class="text-sm font-bold text-gray-800">
                                {{ $p->name ?? 'Untitled Project' }}
                            </div>
                            <div class="text-xs text-gray-600">
                                {{ $p->type ?? ($p->category ?? 'Project') }}
                            </div>

                            <div class="mt-3 flex items-center gap-2 text-xs text-gray-700">
                                @if($isCompleted)
                                    <span class="inline-flex items-center gap-1">
                                        <!--chek-->
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none">
                                            <path d="M20 6 9 17l-5-5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                        Completed
                                    </span>
                                @else
                                    <!--hourglass icon-->
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none">
                                        <path d="M6 2h12M6 22h12M8 2v4c0 2 2 3 4 4-2 1-4 2-4 4v4M16 2v4c0 2-2 3-4 4 2 1 4 2 4 4v4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                    </svg>

                                    <span>
                                        {{ $progress ? $progress.'%' : ($p->status ?? 'On going') }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </a>
            @endforeach
                </div>
            
        

        <script>
            //<!--projct serch filtr-->
            (function () {
                const input = document.getElementById('projectSearch');
                const cards = document.querySelectorAll('.project-card');

                input?.addEventListener('input', () => {
                    const q = (input.value || '').toLowerCase().trim();
                    cards.forEach(card => {
                        const title = card.getAttribute('data-title') || '';
                        card.style.display = title.includes(q) ? '' : 'none';
                    });
                });
            })();
        </script>
    </div>
    <!--state 2: projet detail + asign-->
    @else
   
        @php
            $assignedIds = $selectedProject->teamMembers?->pluck('id')->toArray() ?? [];
            $assignedCount = $selectedProject->teamMembers?->count() ?? 0;

            //<!--incse the user manual edts url to invalid id-->
            if (!$selectedProject) {
                $assignedIds = [];
                $assignedCount = 0;
            }
        @endphp


            <div class="rounded-2xl bg-[#ECECEC] p-0">
                @if(!$selectedProject)
            <div class="rounded-2xl bg-white soft-card p-8">
                <div class="text-lg font-bold">Project not found.</div>
                <a href="{{ route('team.assign') }}" class="inline-block mt-4 px-4 py-2 rounded-lg bg-gray-800 text-white">
                    Back
                </a>
            </div>
                @else
            <div class="relative grid grid-cols-12 gap-8 items-stretch">
                <div class="col-span-12 flex items-center justify-between">
                    <a href="{{ route('team.assign') }}"
                        class="inline-flex items-center bg-yellow-500/90 text-black text-xs font-bold px-3 py-2 rounded-md hover:bg-yellow-400 transition gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 24 24"
                            fill="currentColor"
                            class="size-4">
                            <path fill-rule="evenodd"
                                d="M11.03 3.97a.75.75 0 0 1 0 1.06l-6.22 6.22H21a.75.75 0 0 1 0 1.5H4.81l6.22 6.22a.75.75 0 1 1-1.06 1.06l-7.5-7.5a.75.75 0 0 1 0-1.06l7.5-7.5a.75.75 0 0 1 1.06 0Z"
                                clip-rule="evenodd" />
                        </svg>
                        Back to project list
                    </a>
                    <button
                        type="button"
                        onclick="document.getElementById('assignForm').submit()"
                        class="inline-flex items-center gap-1 bg-yellow-500/90 text-black text-xs font-bold px-3 py-2 rounded-md hover:bg-yellow-400 transition"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 24 24"
                            fill="currentColor"
                            class="size-4">
                            <path fill-rule="evenodd"
                                d="M20 6 9 17l-5-5" />
                        </svg>
                        Save
                    </button>
                </div>
                <!--left: project info-->
                <div class="col-span-12 lg:col-span-7">
                    <div class="relative rounded-2xl bg-[#ECECEC]">
                        <h2 class="text-3xl font-extrabold text-gray-900">
                            {{ $selectedProject->name }}
                        </h2>

                        <p class="text-sm text-gray-700 mt-3 max-w-2xl">
                            {{ $selectedProject->description ?? 'No description provided for this project.' }}
                        </p>

                        <!--bullet details-->
                        <div class="mt-6 space-y-3 text-sm text-gray-800">
                            @php
                                $details = [
                                    'Project Name' => $selectedProject->name,
                                    'Project Type' => $selectedProject->type ?? $selectedProject->category ?? null,
                                    'Project Code' => $selectedProject->code ?? null,
                                    'Client / Owner' => $selectedProject->client ?? $selectedProject->owner ?? null,
                                    'Location' => $selectedProject->location ?? null,
                                ];
                            @endphp

                            @foreach($details as $k => $v)
                                @if($v)
                                    <div class="flex items-start gap-3">
                                        <span class="mt-[2px] text-green-600">
                                            <!--check icon-->
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                                                <path d="M20 6 9 17l-5-5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                        </span>
                                        <div>
                                            <span class="font-semibold">{{ $k }}:</span>
                                            <span class="text-gray-700">{{ $v }}</span>
                                        </div>
                                    </div>
                                @endif
                            @endforeach

                            <div class="flex items-start gap-3">
                                <span class="mt-[2px] text-green-600">
                                    <!--check icon-->
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                                        <path d="M20 6 9 17l-5-5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </span>
                                <div>
                                    <span class="font-semibold bg-gray-400/50 text-black px-2 py-1 rounded">Overall workforce assigned:</span>
                                    <span class="text-gray-700 font-bold text-lg">{{ $assignedCount }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!--midle: projet imgae-->
                <div class="col-span-12 lg:col-span-5 flex">
                    @php
                        $img = $selectedProject->image_path ?? $selectedProject->image ?? $selectedProject->photo ?? null;
                        $imgUrl = null;
                        if ($img) {
                            if (\Illuminate\Support\Str::startsWith($img, ['http://', 'https://'])) {
                                $imgUrl = $img;
                            } elseif (\Illuminate\Support\Str::startsWith($img, ['/','storage/','images/'])) {
                                $imgUrl = asset(ltrim($img, '/'));
                            } else {
                                $imgUrl = asset('storage/'.$img);
                            }
                        }
                    @endphp

                    <div class="w-full h-full min-h-[320px] rounded-2xl overflow-hidden soft-card-2 bg-gray-300">
                        @if($imgUrl)
                            <img src="{{ $imgUrl }}" class="w-full h-full object-cover" alt="Project image">
                        @else
                            <div class="w-full h-full bg-gradient-to-r from-gray-300 to-gray-200"></div>
                        @endif
                    </div>
                </div>

            </div>

            <!--asign aditonal employe section-->
            <div class="mt-10">
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-gray-900">Assign Additional Employee</h3>
                    <div class="text-lg font-semibold text-gray-500 px-4 py-2 rounded-lg border border-gray-400 items-center inline-flex gap-2">
                        Number of employee added: <span id="addedCount" class="text-blue-800 text-2xl">0</span>
                    </div>
                </div>

                <div class="grid grid-cols-12 gap-8">
                    <!--left: employe table card-->
                    <div class="col-span-12 bg-[#3E3E3E] rounded-2xl soft-card p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="text-white text-sm font-semibold">
                                <span id="selectedCountLabel">0</span> selected
                            </div>

                            <!--serch insde table-->
                            <div class="w-56 max-w-full">
                                <div class="relative">
                                    <input
                                        id="employeeSearchInner"
                                        type="text"
                                        placeholder="Search"
                                        class="w-full rounded-full bg-[#2F2F2F] border border-gray-600 px-4 py-2 pr-10 text-sm text-white outline-none"
                                    >
                                    <div class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-300">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                                            <path d="M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15Z" stroke="currentColor" stroke-width="2"/>
                                            <path d="M16.5 16.5 21 21" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('team.assign-to-project', $selectedProject->id) }}" id="assignForm">
                            @csrf

                            <div class="overflow-x-auto rounded-xl">
                                <table class="min-w-full text-sm">
                                    <thead class="text-gray-200">
                                        <tr class="border-b border-gray-600">
                                            <th class="py-3 px-3 text-left w-12"></th>
                                            <th class="py-3 px-3 text-left">Employee</th>
                                            <th class="py-3 px-3 text-left">Workload Availability</th>
                                            <th class="py-3 px-3 text-left">Salary</th>
                                            <th class="py-3 px-3 text-left">Role</th>
                                        </tr>
                                    </thead>

                                    <tbody id="employeesBody" class="text-gray-100">
                                        @foreach($teamMembers as $m)
                                            @php
                                                //<!--if you later add ->withcount('projects') in controller, use $m->projects_count-->
                                                $projectsCount = isset($m->projects_count) ? $m->projects_count : null;
                                                $projectsCount = $projectsCount ?? ($m->projects?->count() ?? null);

                                                //<!--fllback if not loded-->
                                                if ($projectsCount === null) {
                                                    try { $projectsCount = $m->projects()->count(); } catch (\Throwable $e) { $projectsCount = 0; }
                                                }

                                                $badgeText = $projectsCount . ' ' . ($projectsCount == 1 ? 'Project' : 'Projects');

                                                //<!--color maping rufly like yur desgin-->
                                                $badgeClass = 'bg-green-500 text-black';
                                                if ($projectsCount >= 6) $badgeClass = 'bg-red-500 text-white';
                                                elseif ($projectsCount >= 4) $badgeClass = 'bg-orange-400 text-black';
                                                elseif ($projectsCount >= 2) $badgeClass = 'bg-lime-400 text-black';
                                            @endphp

                                            <tr class="employee-row border-b border-gray-700"
                                                data-search="{{ strtolower(($m->name ?? '').' '.($m->role ?? '').' '.($m->location ?? '')) }}">
                                                <td class="py-3 px-3">
                                                    <input
                                                        class="emp-check w-4 h-4"
                                                        type="checkbox"
                                                        name="team_members[]"
                                                        value="{{ $m->id }}"
                                                        {{ in_array($m->id, $assignedIds) ? 'checked' : '' }}
                                                    >
                                                </td>

                                                <td class="py-3 px-3">
                                                    <div class="flex items-center gap-3">
                                                        <div class="w-9 h-9 rounded-full overflow-hidden bg-gray-600">
                                                            @if($m->avatar)
                                                                <img src="{{ asset($m->avatar) }}" class="w-full h-full object-cover" alt="avatar">
                                                            @else
                                                                <div class="w-full h-full flex items-center justify-center text-white text-xs font-bold">
                                                                    {{ $m->initials ?? strtoupper(substr($m->name ?? 'E', 0, 1)) }}
                                                                </div>
                                                            @endif
                                                        </div>
                                                        <div class="min-w-0">
                                                            <div class="font-semibold text-white truncate">{{ $m->name }}</div>
                                                            <div class="text-xs text-gray-300 truncate">{{ $m->location }}</div>
                                                        </div>
                                                    </div>
                                                </td>

                                                <td class="py-3 px-3">
                                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold {{ $badgeClass }}">
                                                        {{ $badgeText }}
                                                    </span>
                                                </td>

                                                <td class="py-3 px-3 text-gray-200">
                                                    {{ $m->salary ?? '—' }}
                                                </td>

                                                <td class="py-3 px-3 text-gray-200">
                                                    {{ $m->role ?? '—' }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </form>
                    </div>

                </div>
            </div>

            <script>
                (function () {
                    const assignedIds = @json($assignedIds);

                    const checks = Array.from(document.querySelectorAll('.emp-check'));
                    const selectedLabel = document.getElementById('selectedCountLabel');
                    const addedCountEl = document.getElementById('addedCount');

                    function updateCounts() {
                        const selected = checks.filter(c => c.checked).map(c => parseInt(c.value));
                        const selectedCount = selected.length;

                        const added = selected.filter(id => !assignedIds.includes(id));
                        const addedCount = added.length;

                        selectedLabel.textContent = selectedCount;
                        addedCountEl.textContent = addedCount;
                    }

                    checks.forEach(c => c.addEventListener('change', updateCounts));
                    updateCounts();

                    //<!--search employees-->
                    const search = document.getElementById('employeeSearchInner');
                    const rows = Array.from(document.querySelectorAll('.employee-row'));

                    search?.addEventListener('input', () => {
                        const q = (search.value || '').toLowerCase().trim();
                        rows.forEach(r => {
                            const s = r.getAttribute('data-search') || '';
                            r.style.display = s.includes(q) ? '' : 'none';
                        });
                    });

                    //<!--also wire top heder search (optinal)-->
                    const headerSearch = document.getElementById('employeeSearch');
                    headerSearch?.addEventListener('input', () => {
                        search.value = headerSearch.value;
                        search.dispatchEvent(new Event('input'));
                    });
                })();
            </script>
                @endif
            </div>
    
    @endif
</div>
@endsection
