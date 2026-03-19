@extends('layouts.app')

@section('content')
<div class="p-6">
    <div class="max-w-6xl mx-auto">

        <!--modal-like card-->
        <div class="relative overflow-hidden rounded-2xl shadow-2xl border border-gray-800 bg-[#3f3f3f]">

  
            <div class="absolute -top-10 -right-10 w-40 h-40 bg-yellow-500/20 rounded-full"></div>
            <div class="absolute top-6 right-10 w-20 h-20 bg-yellow-500/25 rounded-full"></div>

            <div class="grid grid-cols-1 md:grid-cols-5">

                <!--LEFT PANEL-->
                <div class="md:col-span-2 bg-black p-5 text-white relative">
                    <h1 class="text-2xl font-bold tracking-wide">Create Project</h1>

                    <!--Uploadbox-->
                    <div class="mt-8 border border-dashed border-gray-600 rounded-xl p-8 flex flex-col items-center justify-center text-center gap-3">
                        <input type="file" id="imageUpload" name="image" accept="image/*" class="hidden" form="createProjectForm">

                        <button type="button"
                            onclick="document.getElementById('imageUpload').click()"
                            class="w-full flex flex-col items-center justify-center gap-3">
                            <div class="w-14 h-14 rounded-full border border-yellow-400 flex items-center justify-center">
                                <svg class="w-7 h-7 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M12 12V4m0 8l-3-3m3 3l3-3" />
                                </svg>
                            </div>

                            <div class="text-sm text-gray-300">
                                Drag & drop files or <span class="text-yellow-400 font-semibold underline">Browse</span>
                            </div>
                            <div class="text-xs text-gray-500">Upload Image of the Project Here</div>
                        </button>

                        <!--preview-->
                        <img id="imagePreview" class="hidden mt-3 w-full rounded-lg border border-gray-700 object-cover max-h-48" alt="Preview">
                    </div>

                    <!--left Buttons-->
                    <div class="mt-8 flex items-center justify-center gap-7">
                        <a href="{{ route('projects.index') }}"
                           class="inline-flex items-center gap-1 px-5 py-2 rounded-lg bg-orange-500 text-black font-semibold hover:bg-orange-400 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            Close
                        </a>

                        <button type="submit" form="createProjectForm"
                            class="inline-flex items-center gap-1 px-5 py-2 rounded-lg bg-yellow-400 text-black font-semibold hover:bg-yellow-300 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Save
                        </button>
                    </div>

                    <!--assigned Team (below buttons)-->
                <div class="mt-10 bg-[#3f3f3f] border border-white/10 rounded-xl overflow-hidden">
                    <!--header -->
                    <div class="px-4 py-3 border-b border-white/10 flex items-center justify-between gap-3">
                        <div class="text-white font-semibold">Assigned Team</div>

                        <div class="relative w-48">
                            <input id="assignedSearch" type="text" placeholder="Search"
                                class="w-full rounded-full bg-black/30 border border-white/10 text-white placeholder:text-white/40 pl-4 pr-9 py-1.5 text-xs outline-none focus:ring-0 focus:ring-yellow-400 focus:border-yellow-400">
                            <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                    </div>

                    <!--table-->
                    <div class="max-h-72 overflow-y-auto">
                        <table class="w-full text-left">
                            <thead class="sticky top-0 bg-[#3f3f3f] border-b border-white/10">
                                <tr class="text-[11px] uppercase tracking-wide text-white/60">
                                    <th class="px-4 py-3">Name</th>
                                    <th class="px-4 py-3 w-24">Rates</th>
                                    <th class="px-4 py-3 w-24">Role</th>
                                    <th class="px-4 py-3 w-10"></th>
                                </tr>
                            </thead>

                            <tbody id="assignedWorkersBody" class="divide-y divide-white/10">
                                <tr>
                                    <td colspan="4" class="px-4 py-6 text-center text-white/50 text-sm">
                                        No assigned workers yet.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                </div>



                </div>

                <!--RIGHT PANEL (Form)-->
                <div class="md:col-span-3 p-10 text-white">
                    <h2 class="text-lg font-semibold mb-6">Project Information</h2>

                    <form action="{{ route('projects.store') }}" method="POST" enctype="multipart/form-data" id="createProjectForm" class="space-y-6">
                        @csrf

                        <!-- Top row -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <!--Project Name-->
                            <div>
                                <label class="text-sm font-semibold text-white/90">Project Name</label>
                                <input type="text" name="name" required placeholder="Type here"
                                    class="mt-2 w-full bg-transparent border-b border-white/20 focus:border-yellow-400 outline-none py-2 text-sm placeholder:text-white/30">
                            </div>

                            <!--Project Type-->
                            <div>
                                <label class="text-sm font-semibold text-white/90">Project Type</label>
                                <select name="type" required
                                    class="mt-2 w-full bg-white text-gray-900 rounded-md px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-yellow-400">
                                    <option value="">Select a project</option>
                                    @foreach($projectTypes as $type)
                                        <option value="{{ $type }}">{{ $type }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!--Location-->
                            <div>
                                <label class="text-sm font-semibold text-white/90">Location</label>
                                <input type="text" name="location" required placeholder="Type here"
                                    class="mt-2 w-full bg-transparent border-b border-white/20 focus:border-yellow-400 outline-none py-2 text-sm placeholder:text-white/30">
                            </div>

                            <!--Project Code-->
                            <div>
                                <label class="text-sm font-semibold text-white/90">Project Code</label>
                                <input type="text" name="code" required placeholder="Type here"
                                    class="mt-2 w-full bg-transparent border-b border-white/20 focus:border-yellow-400 outline-none py-2 text-sm placeholder:text-white/30">
                            </div>

                            <!--Start Date-->
                            <div>
                                <label class="text-sm font-semibold text-white/90">Start Date</label>
                                <input type="date" name="start_date" required
                                    class="mt-2 w-full bg-transparent border-b border-white/20 focus:border-yellow-400 outline-none py-2 text-sm text-white [color-scheme:dark]">
                            </div>

                            <!--End Date-->
                            <div>
                                <label class="text-sm font-semibold text-white/90">Expected End Date</label>
                                <input type="date" name="end_date" required
                                    class="mt-2 w-full bg-transparent border-b border-white/20 focus:border-yellow-400 outline-none py-2 text-sm text-white [color-scheme:dark]">
                            </div>
                        </div>

                        <!--Client-->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div>
                                <label class="text-sm font-semibold text-white/90">Client / Owner</label>
                                <input type="text" name="client" required placeholder="Type here"
                                    class="mt-2 w-full bg-transparent border-b border-white/20 focus:border-yellow-400 outline-none py-2 text-sm placeholder:text-white/30">
                            </div>
                            <div class="flex items-end justify-center mt-0 border border-dashed border-gray-600 rounded-xl p-4 flex flex-col items-center justify-center text-center gap-3">
                                <button type="button" onclick="openWorkersModal()"
                                    class="inline-flex items-center gap-2 bg-yellow-400 text-black font-semibold px-4 py-2 rounded-lg hover:bg-yellow-300 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    Add Role
                                </button>
                            </div>
                        </div>

                        <!--Description-->
                        <div>
                            <label class="text-sm font-semibold text-white/90">Description</label>
                            <textarea name="description" rows="3" required placeholder="Type here"
                                class="mt-2 w-full bg-transparent border border-white/15 rounded-lg p-3 outline-none focus:border-yellow-400 text-sm placeholder:text-white/30"></textarea>
                        </div>
                        
                
                        <div id="selectedWorkersInputs" class="hidden"></div>
                        

                    </form>
                </div>
            </div>
        </div>

    </div>
</div>


<!-- Add Role / Select Workers Modal -->
<div id="workersModal" class="fixed inset-0 z-[9999] hidden">
    <!-- Backdrop -->
    <div class="absolute inset-0 bg-black/70 backdrop-blur-sm" onclick="closeWorkersModal()"></div>

    <div class="relative w-full h-full flex items-center justify-center p-6">
        <div class="w-full max-w-6xl rounded-2xl overflow-hidden shadow-2xl border border-gray-700 bg-[#3f3f3f]">

            <!-- Header -->
            <div class="px-6 py-4 border-b border-white/10 flex items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-4 h-4 rounded bg-yellow-400"></div>
                    <div class="text-sm font-semibold text-white/90">
                        <span id="selectedCount">0</span> selected
                    </div>
                </div>

                <div class="relative w-full max-w-md">
                    <input id="workersSearch" type="text" placeholder="Search"
                        class="w-full rounded-full bg-black/30 border border-white/10 text-white placeholder:text-white/40 pl-4 pr-10 py-2 text-sm outline-none focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400">
                    <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-5 h-5 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
            </div>

            <!-- Table -->
            <div class="bg-white">
                <div class="max-h-[60vh] overflow-y-auto">
                    <table class="w-full">
                        <thead class="sticky top-0 bg-white border-b border-gray-200">
                            <tr class="text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                <th class="w-12 px-4 py-3 text-left"></th>
                                <th class="px-4 py-3 text-left">Name</th>
                                <th class="px-4 py-3 text-left">Location</th>
                                <th class="px-4 py-3 text-left">Salary</th>
                                <th class="px-4 py-3 text-left">Role</th>
                            </tr>
                        </thead>
                        <tbody id="workersTableBody" class="divide-y divide-gray-200">
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-gray-500 text-sm">Loading...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Footer -->
            <div class="px-6 py-4 border-t border-white/10 flex items-center justify-end gap-3">
                <button type="button" onclick="closeWorkersModal()"
                    class="px-6 py-2.5 rounded-lg bg-orange-500 text-black font-semibold hover:bg-orange-400 transition">
                    Close
                </button>

                <button type="button" onclick="saveSelectedWorkers()"
                    class="px-6 py-2.5 rounded-lg bg-yellow-400 text-black font-semibold hover:bg-yellow-300 transition">
                    Save
                </button>
            </div>

        </div>
    </div>
</div>



<script>
/** preview upload */
document.getElementById('imageUpload')?.addEventListener('change', function(e) {
    const file = e.target.files?.[0];
    const preview = document.getElementById('imagePreview');
    if (!file || !preview) return;

    const url = URL.createObjectURL(file);
    preview.src = url;
    preview.classList.remove('hidden');
});

/** Workers modal logic */
let selectedWorkers = new Map();  // id => worker object
let workersCache = [];

function openWorkersModal() {
    document.getElementById('workersModal')?.classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
    fetchWorkers('');
}

function closeWorkersModal() {
    document.getElementById('workersModal')?.classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
}

document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') closeWorkersModal();
});

document.getElementById('workersSearch')?.addEventListener('input', function () {
    fetchWorkers(this.value.trim());
});

async function fetchWorkers(search) {
    const tbody = document.getElementById('workersTableBody');
    if (!tbody) return;

    tbody.innerHTML = `<tr><td colspan="5" class="px-4 py-8 text-center text-gray-500 text-sm">Loading...</td></tr>`;

    try {
        const url = `{{ route('team.members.list') }}?search=${encodeURIComponent(search)}`;
        const res = await fetch(url);
        const data = await res.json();

        workersCache = data;
        renderWorkersTable(data);
    } catch (err) {
        console.error(err);
        tbody.innerHTML = `<tr><td colspan="5" class="px-4 py-8 text-center text-red-600 text-sm">Failed to load workers.</td></tr>`;
    }
}

function renderWorkersTable(workers) {
    const tbody = document.getElementById('workersTableBody');
    if (!tbody) return;

    if (!workers || workers.length === 0) {
        tbody.innerHTML = `<tr><td colspan="5" class="px-4 py-8 text-center text-gray-500 text-sm">No results.</td></tr>`;
        updateSelectedCount();
        return;
    }

    tbody.innerHTML = workers.map(w => {
        const id = String(w.id);
        const checked = selectedWorkers.has(id) ? 'checked' : '';
        const initials = makeInitials(w.name);

        const badge = (w.location === 'Onsite')
            ? `<span class="px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">Onsite</span>`
            : `<span class="px-3 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-700">Remote</span>`;

        return `
            <tr class="hover:bg-yellow-50/40 transition">
                <td class="px-4 py-4">
                    <input type="checkbox" class="w-4 h-4 accent-yellow-500"
                        ${checked}
                        onchange="toggleWorker('${id}')">
                </td>

                <td class="px-4 py-4">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-full bg-yellow-100 text-yellow-700 flex items-center justify-center text-xs font-extrabold">
                            ${initials}
                        </div>
                        <div class="text-sm font-semibold text-gray-800">${escapeHtml(w.name ?? '')}</div>
                    </div>
                </td>

                <td class="px-4 py-4">${badge}</td>

                <td class="px-4 py-4 text-sm text-gray-700">${escapeHtml(w.salary ?? '')}</td>

                <td class="px-4 py-4 text-sm font-semibold text-gray-700">${escapeHtml(w.role ?? '')}</td>
            </tr>
        `;
    }).join('');

    updateSelectedCount();
}

function toggleWorker(id) {
    id = String(id);
    const worker = workersCache.find(w => String(w.id) === id);

    if (selectedWorkers.has(id)) selectedWorkers.delete(id);
    else if (worker) selectedWorkers.set(id, worker);

    updateSelectedCount();
}

function updateSelectedCount() {
    const el = document.getElementById('selectedCount');
    if (el) el.textContent = selectedWorkers.size;
}

function saveSelectedWorkers() {
    const tbody = document.getElementById('assignedWorkersBody');
    const inputs = document.getElementById('selectedWorkersInputs');
    if (!tbody || !inputs) return;

    // clear previous
    tbody.innerHTML = '';
    inputs.innerHTML = '';

    if (selectedWorkers.size === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="4" class="px-4 py-6 text-center text-white/50 text-sm">
                    No assigned workers yet.
                </td>
            </tr>
        `;
        closeWorkersModal();
        return;
    }

    selectedWorkers.forEach((w, id) => {
        const row = document.createElement('tr');
        row.className = 'hover:bg-white/5 transition';

        // you can show salary as rate, or format it if you want
        const rate = w.salary ?? '';

        row.innerHTML = `
            <td class="px-4 py-3">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-black/30 border border-white/10 flex items-center justify-center text-xs font-bold text-yellow-300">
                        ${makeInitials(w.name)}
                    </div>
                    <div>
                        <div class="text-sm font-semibold text-white">${escapeHtml(w.name ?? '')}</div>
                        <div class="text-xs text-white/50">${escapeHtml(w.role ?? '')}</div>
                    </div>
                </div>
            </td>

            <td class="px-4 py-3 text-sm text-white/80">${escapeHtml(rate)}</td>
            <td class="px-4 py-3 text-sm text-white/80">${escapeHtml(w.location ?? '')}</td>

            <td class="px-4 py-3 text-right">
                <button type="button"
                        class="p-2 rounded-lg hover:bg-white/10 transition"
                        onclick="removeSelectedWorker('${id}')"
                        title="Remove">
                    <svg class="w-4 h-4 text-white/70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </td>
        `;
        tbody.appendChild(row);

        // hidden input for submit
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'team_members[]';
        input.value = id;
        inputs.appendChild(input);
    });

    closeWorkersModal();
}


function removeSelectedWorker(id) {
    selectedWorkers.delete(String(id));
    saveSelectedWorkers();  // rerender table + hidden inputs
    updateSelectedCount();
}


function makeInitials(name) {
    if (!name) return 'NA';
    return name.split(' ').filter(Boolean).slice(0,2).map(x => x[0]).join('').toUpperCase();
}

function escapeHtml(str) {
    return String(str).replace(/[&<>"']/g, s => ({
        '&':'&amp;', '<':'&lt;', '>':'&gt;', '"':'&quot;', "'":'&#39;'
    }[s]));
}

document.getElementById('assignedSearch')?.addEventListener('input', function () {
    const q = this.value.toLowerCase();
    const rows = document.querySelectorAll('#assignedWorkersBody tr');

    rows.forEach(r => {
        const text = r.textContent.toLowerCase();
        r.style.display = text.includes(q) ? '' : 'none';
    });
});


</script>

@endsection
