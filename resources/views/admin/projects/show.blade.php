@extends('layouts.app')

@section('content')
<style>
    .print-only { display: none; }
    .screen-only { display: block; }

    .project-scroll {
        scrollbar-width: thin;
        scrollbar-color: #5a5a5a #3f3f3f;
    }
    .project-scroll::-webkit-scrollbar { width: 8px; }
    .project-scroll::-webkit-scrollbar-track { background: #3f3f3f; }
    .project-scroll::-webkit-scrollbar-thumb { background: #5a5a5a; border-radius: 999px; border: 2px solid #3f3f3f; }
    .project-scroll::-webkit-scrollbar-thumb:hover { background: #6a6a6a; }

    @media print {
        .screen-only { display: none !important; }
        .print-only { display: block !important; }

        .print-page {
            font-family: "Times New Roman", Times, serif;
            color: #000;
            max-width: 7.5in;
            margin: 0 auto;
        }
        .print-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 0.4in;
        }
        .print-title {
            font-size: 20pt;
            font-weight: bold;
            margin-bottom: 10pt;
        }
        .print-meta {
            font-size: 12pt;
        }
        .print-line {
            margin: 2pt 0;
        }
        .print-line .label {
            font-style: italic;
        }
        .print-line .value {
            font-weight: bold;
        }
        .print-image {
            width: 3.7in;
            height: 3.1in;
        }
        .print-image img,
        .print-image .print-image-placeholder {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border: 1px solid #000;
        }
        .print-section-title {
            font-weight: bold;
            font-size: 12pt;
            margin: 16pt 0 6pt;
        }
        .print-description {
            font-size: 12pt;
            line-height: 1.4;
        }
        .print-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11pt;
        }
        .print-table th,
        .print-table td {
            border: 1px solid #000;
            padding: 4px 6px;
            vertical-align: top;
        }
        .print-table th {
            text-align: center;
            font-weight: bold;
        }
        .print-overall {
            font-weight: bold;
            font-size: 12pt;
            margin-top: 6pt;
        }

        .project-scroll {
            max-height: none !important;
            min-height: 0 !important;
            overflow: visible !important;
        }
        .project-scroll thead {
            position: static !important;
        }
        .project-scroll tr {
            break-inside: avoid;
        }
    }
</style>
<div class="print-only">
    <div class="print-page">
        <div class="print-header">
            <div class="print-left">
                <div class="print-title">{{ $project->name }}</div>
                <div class="print-meta">
                    <div class="print-line"><span class="label">Project Code</span>: <span class="value">{{ $project->code }}</span></div>
                    <div class="print-line"><span class="label">Project Type</span>: <span class="value">{{ $project->type }}</span></div>
                    <div class="print-line"><span class="label">Client / Owner</span>: <span class="value">{{ $project->client }}</span></div>
                    <div class="print-line"><span class="label">Location</span>: <span class="value">{{ $project->location }}</span></div>
                    <div class="print-line"><span class="label">Start Date</span>: <span class="value">{{ optional($project->start_date)->format('F d, Y') ?? '-' }}</span></div>
                    <div class="print-line"><span class="label">Expected End Date</span>: <span class="value">{{ optional($project->end_date)->format('F d, Y') ?? '-' }}</span></div>
                    <div class="print-line"><span class="label">Status</span>: <span class="value">{{ ucfirst($project->status ?? 'pending') }}</span></div>
                    <div class="print-line"><span class="label">Progress</span>: <span class="value">{{ $project->progress ?? 0 }}%</span></div>
                </div>
            </div>
            <div class="print-image">
                @if($project->image)
                    <img src="{{ asset('storage/' . $project->image) }}" alt="{{ $project->name }}">
                @else
                    <div class="print-image-placeholder"></div>
                @endif
            </div>
        </div>

        <div class="print-section-title">Description:</div>
        <div class="print-description">
            {{ $project->description }}
        </div>

        <div class="print-section-title">Project Materials</div>
        <table class="print-table">
            <thead>
                <tr>
                    <th>Material</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Total</th>
                    <th>Last Used</th>
                </tr>
            </thead>
            <tbody>
                @forelse($projectMaterials as $row)
                    <tr>
                        <td>{{ $row->material?->material_name ?? 'Unknown' }}</td>
                        <td>
                            {{ rtrim(rtrim(number_format((float) $row->quantity, 2, '.', ''), '0'), '.') }}
                            {{ $row->material?->unit_of_measure ?? '' }}
                        </td>
                        <td>₱{{ number_format((float) ($row->unit_price ?? 0), 2) }}</td>
                        <td>₱{{ number_format((float) ($row->total ?? 0), 2) }}</td>
                        <td>{{ $row->last_used ? $row->last_used->format('m-d-y') : '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">No materials recorded for this project.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="print-overall">Overall Total: ₱{{ number_format((float) ($projectMaterialsTotal ?? 0), 2) }}</div>

        <div class="print-section-title" style="margin-top: 18pt;">Assigned Team</div>
        <table class="print-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Role</th>
                    <th>Location</th>
                    <th>Rate</th>
                </tr>
            </thead>
            <tbody>
                @forelse($project->teamMembers as $member)
                    <tr>
                        <td>{{ $member->name }}</td>
                        <td>{{ $member->role ?? '-' }}</td>
                        <td>{{ $member->location ?? '-' }}</td>
                        <td>{{ $member->salary ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4">No assigned members yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="screen-only">
<div class="p-6">
    <div class="max-w-6xl mx-auto">

        <!--card-->
        <div class="relative overflow-hidden rounded-2xl shadow-2xl border border-gray-800 bg-[#3f3f3f]">

            <div class="grid grid-cols-1 md:grid-cols-5">

                <!--LEFT PANEL-->
                <div class="md:col-span-2 bg-yellow-500/25 p-5 text-white relative ">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <div class="text-xs text-yellow-300/90 font-semibold tracking-wide">PROJECT DETAILS</div>
                            <h1 class="text-2xl font-bold tracking-wide mt-1">{{ $project->name }}</h1>
                        </div>

                        <div class="flex items-center gap-2">
                            <a href="{{ route('projects.index') }}"
                               class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-orange-500 text-black font-semibold hover:bg-orange-400 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M15 19l-7-7 7-7"/>
                                </svg>
                                Back
                            </a>
                        </div>
                    </div>

                    <!-- Image -->
                    <div class="mt-6 rounded-xl overflow-hidden border border-white/10">
                        @if($project->image)
                            <img src="{{ asset('storage/' . $project->image) }}"
                                 alt="{{ $project->name }}"
                                 class="w-full h-52 object-cover">
                        @else
                            <div class="w-full h-52 bg-gradient-to-br from-yellow-500 to-yellow-600"></div>
                        @endif
                    </div>

                    <!--Assigned Team -->
                    <div class="bg-[#3f3f3f] rounded-2xl shadow-2xl border border-gray-800 overflow-hidden mt-6">
                    <div class="px-6 py-4 border-b border-white/10 flex items-center justify-between">
                        <h4 class="text-white font-semibold">Assigned Team</h4>
                        <span class="text-xs text-white/60">{{ $project->teamMembers->count() }} member(s)</span>
                    </div>

                    <div class="max-h-[500px] min-h-[150px] overflow-y-auto project-scroll">
                        <table class="w-full text-left">
                            <thead class="sticky top-0 bg-[#3f3f3f] border-b border-white/10">
                                <tr class="text-[11px] uppercase tracking-wide text-white/60">
                                    <th class="px-6 py-3">Name</th>
                                    <th class="px-6 py-3 w-32">Role</th>
                                    <th class="px-6 py-3 w-28">Location</th>
                                    <th class="px-6 py-3 w-24">Rate</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-white/10">
                                @forelse($project->teamMembers as $member)
                                    <tr class="hover:bg-white/5 transition">
                                        <td class="px-6 py-3">
                                            <div class="flex items-center gap-3">
                                                <div class="w-9 h-9 rounded-lg bg-black/30 border border-white/10 flex items-center justify-center text-xs font-bold text-yellow-300">
                                                    {{ strtoupper(collect(explode(' ', $member->name))->filter()->take(2)->map(fn($w)=>substr($w,0,1))->join('')) }}
                                                </div>
                                                <div>
                                                    <div class="text-sm font-semibold text-white">{{ $member->name }}</div>
                                                    <div class="text-xs text-white/50">{{ $member->role }}</div>
                                                </div>
                                            </div>
                                        </td>

                                        <td class="px-6 py-3 text-sm text-white/80">{{ $member->role ?? '-' }}</td>
                                        <td class="px-6 py-3 text-sm text-white/80">{{ $member->location ?? '-' }}</td>
                                        <td class="px-6 py-3 text-sm text-white/80">{{ $member->salary ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-8 text-center text-white/50 text-sm">
                                            No assigned members yet.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                </div>

                <!--RIGHT PANEL -->
                <div class="md:col-span-3 p-10 text-white">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-lg font-semibold">Project Information</h2>
                        <button type="button" data-report="print"
                                class="no-print inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-[#f6c915] text-black font-semibold hover:brightness-95 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Generate Report
                        </button>
                    </div>

                    <div class="space-y-7">

                        <!--Two columns info -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

                            <div>
                                <label class="text-sm font-semibold text-white/90">Project Code</label>
                                <div class="mt-2 w-full bg-transparent border-b border-white/20 py-2 text-sm text-white/90">
                                    {{ $project->code }}
                                </div>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-white/90">Project Type</label>
                                <div class="mt-2 w-full bg-transparent border-b border-white/20 py-2 text-sm text-white/90">
                                    {{ $project->type }}
                                </div>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-white/90">Client / Owner</label>
                                <div class="mt-2 w-full bg-transparent border-b border-white/20 py-2 text-sm text-white/90">
                                    {{ $project->client }}
                                </div>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-white/90">Location</label>
                                <div class="mt-2 w-full bg-transparent border-b border-white/20 py-2 text-sm text-white/90">
                                    {{ $project->location }}
                                </div>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-white/90">Start Date</label>
                                <div class="mt-2 w-full bg-transparent border-b border-white/20 py-2 text-sm text-white/90">
                                    {{ optional($project->start_date)->format('F d, Y') ?? '-' }}
                                </div>
                            </div>

                            <div>
                                <label class="text-sm font-semibold text-white/90">Expected End Date</label>
                                <div class="mt-2 w-full bg-transparent border-b border-white/20 py-2 text-sm text-white/90">
                                    {{ optional($project->end_date)->format('F d, Y') ?? '-' }}
                                </div>
                            </div>
                        </div>

                        <!--Description -->
                        <div>
                            <label class="text-sm font-semibold text-white/90">Description</label>
                            <div class="mt-2 w-full bg-transparent border border-white/15 rounded-lg p-4 text-sm text-white/80">
                                {{ $project->description }}
                            </div>
                        </div>

                        <!--Status + Progress + Total -->
                        <div class="flex flex-wrap items-center gap-4">
                            <div class="px-4 py-2 rounded-lg bg-black/30 border border-white/10">
                                <span class="text-xs text-white/60">Status:</span>
                                <span class="ml-2 text-sm font-semibold text-white">
                                    {{ ucfirst($project->status ?? 'pending') }}
                                </span>
                            </div>

                            <div class="px-4 py-2 rounded-lg bg-black/30 border border-white/10">
                                <span class="text-xs text-white/60">Progress:</span>
                                <span class="ml-2 text-sm font-semibold text-yellow-300">
                                    {{ $project->progress ?? 0 }}%
                                </span>
                            </div>

                            <div class="px-4 py-2 rounded-lg bg-black/30 border border-white/10 ml-auto">
                                <span class="text-xs text-white/60">Total:</span>
                                <span class="ml-2 text-sm font-semibold text-yellow-300">
                                    ₱{{ number_format((float) ($projectMaterialsTotal ?? 0), 2) }}
                                </span>
                            </div>
                        </div>

                        <!--Project Materials -->
                        <div class="bg-[#3f3f3f] rounded-2xl shadow-2xl border border-gray-800 overflow-hidden">
                            <div class="px-6 py-4 border-b border-white/10 flex items-center justify-between">
                                <h4 class="text-white font-semibold">Project Materials</h4>
                                <span class="text-xs text-white/60">{{ $projectMaterials->count() }} item(s)</span>
                            </div>

                            <div class="max-h-[500px] overflow-y-auto project-scroll">
                                <table class="w-full text-left">
                                    <thead class="sticky top-0 bg-[#3f3f3f] border-b border-white/10">
                                        <tr class="text-[11px] uppercase tracking-wide text-white/60">
                                            <th class="px-6 py-3">Material</th>
                                            <th class="px-6 py-3 w-32">Quantity</th>
                                            <th class="px-6 py-3 w-28">Price</th>
                                            <th class="px-6 py-3 w-28">Total</th>
                                            <th class="px-6 py-3 w-28">Last Used</th>
                                        </tr>
                                    </thead>

                                    <tbody class="divide-y divide-white/10">
                                        @forelse($projectMaterials as $row)
                                            <tr class="hover:bg-white/5 transition">
                                                <td class="px-6 py-3">
                                                    <div class="text-sm font-semibold text-white">
                                                        {{ $row->material?->material_name ?? 'Unknown' }}
                                                    </div>
                                                    <div class="text-xs text-white/50">
                                                        {{ $row->material?->unit_of_measure ?? '' }}
                                                    </div>
                                                </td>
                                                <td class="px-6 py-3 text-sm text-white/80">
                                                    {{ rtrim(rtrim(number_format((float) $row->quantity, 2, '.', ''), '0'), '.') }}
                                                    {{ $row->material?->unit_of_measure ?? '' }}
                                                </td>
                                                <td class="px-6 py-3 text-sm text-white/80">
                                                    ₱{{ number_format((float) ($row->unit_price ?? 0), 2) }}
                                                </td>
                                                <td class="px-6 py-3 text-sm text-white/80">
                                                    ₱{{ number_format((float) ($row->total ?? 0), 2) }}
                                                </td>
                                                <td class="px-6 py-3 text-sm text-white/80">
                                                    {{ $row->last_used ? $row->last_used->format('m-d-y') : '-' }}
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="px-6 py-8 text-center text-white/50 text-sm">
                                                    No materials recorded for this project.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>

    </div>
</div>
</div>
@endsection
