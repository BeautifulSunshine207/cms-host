@extends('employee.layouts.app')

@section('content')
@php
    $currentMember = $project->teamMembers->firstWhere('email', auth()->user()->email) ?? $project->teamMembers->first();
    $projectImage = $project->image ? asset('storage/' . $project->image) : null;
    $startDate = $project->start_date ? \Carbon\Carbon::parse($project->start_date)->format('F d, Y') : '-';
    $endDate = $project->end_date ? \Carbon\Carbon::parse($project->end_date)->format('F d, Y') : '-';
@endphp

<style>
    .pm-page {
        min-height: 100vh;
        background: #dcdcdc;
        padding: 26px 28px 40px;
        font-family: "Inter", sans-serif;
    }

    .pm-title {
        font-size: 18px;
        font-weight: 700;
        color: #111;
        margin: 6px 0 12px 4px;
    }

    .pm-panel {
        border: 1px solid #9b9b9b;
        border-radius: 18px;
        padding: 12px;
        background: #dcdcdc;
    }

    .pm-panel-inner {
        background: #fefefe;
        border-radius: 20px;
        padding: 26px 30px 30px;
        box-shadow: inset 0 0 0 1px rgba(0,0,0,0.04);
    }

    .pm-grid {
        display: grid;
        grid-template-columns: minmax(420px, 1fr) minmax(360px, 520px);
        gap: 32px 40px;
        align-items: start;
    }

    .pm-tag {
        display: inline-block;
        background: #ffd200;
        color: #1b1b1b;
        font-size: 11px;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 4px;
        margin-bottom: 8px;
    }

    .pm-name {
        font-size: 26px;
        font-weight: 700;
        color: #1b1b1b;
        margin: 0 0 8px;
    }

    .pm-desc {
        font-size: 12px;
        color: #5a5a5a;
        line-height: 1.6;
        max-width: 420px;
    }

    .pm-team {
        display: flex;
        align-items: center;
        gap: 10px;
        margin: 18px 0 8px;
    }

    .pm-team-logo {
        width: 36px;
        height: 36px;
        border-radius: 999px;
        background: #111;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }

    .pm-team-logo img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .pm-team-text {
        font-size: 12px;
        color: #0b74ff;
        font-weight: 600;
    }

    .pm-team-name {
        font-size: 18px;
        font-weight: 700;
        margin-top: 2px;
        color: #1b1b1b;
    }

    .pm-info-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        column-gap: 40px;
        row-gap: 14px;
        margin-top: 10px;
    }

    .pm-info-label {
        font-size: 11px;
        color: #0b74ff;
        font-weight: 600;
        margin-bottom: 3px;
    }

    .pm-info-value {
        font-size: 14px;
        color: #1b1b1b;
        font-weight: 600;
    }

    .pm-timeframe {
        margin-top: 16px;
    }

    .pm-timeframe .pm-info-label {
        margin-bottom: 6px;
    }

    .pm-timeframe p {
        margin: 4px 0;
        font-size: 12px;
        color: #1b1b1b;
    }

    .pm-image {
        width: 100%;
        height: 210px;
        border-radius: 28px;
        overflow: hidden;
        background: #d0d0d0;
        box-shadow: 0 10px 18px rgba(0,0,0,0.12);
    }

    .pm-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .pm-bar {
        margin-top: 26px;
        background: #3a3a3a;
        border-radius: 14px;
        padding: 10px 16px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        color: #f3f3f3;
    }

    .pm-bar-title {
        font-size: 14px;
        font-weight: 700;
        letter-spacing: 0.02em;
    }

    .pm-search {
        position: relative;
        width: 190px;
    }

    .pm-search input {
        width: 100%;
        height: 26px;
        border-radius: 999px;
        border: 1px solid #c9c9c9;
        background: transparent;
        color: #f3f3f3;
        font-size: 11px;
        padding: 0 30px 0 12px;
        outline: none;
    }

    .pm-search svg {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        width: 12px;
        height: 12px;
        color: #f3f3f3;
    }

    .pm-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
    font-size: 12px;
    color: #1f1f1f;
    background: #f3f3f3;
    border: 1px solid #cfcfcf;
}

    .pm-table thead tr,
.pm-table tbody tr {
    background: #f3f3f3;
}

    .pm-table th {
    text-align: left;
    font-size: 11px;
    color: #6f6f6f;
    font-weight: 600;
    padding: 10px 4px 8px;
    border-bottom: 1px solid #cfcfcf;
}

    .pm-table td {
    padding: 12px 4px;
    border-bottom: 1px solid #d6d6d6;
    color: #1f1f1f;
}

    .pm-table tbody tr:last-child td {
        border-bottom: 0;
    }

    .pm-assigned {
        margin-top: 22px;
    }

    .pm-assigned .pm-bar {
        margin-top: 0;
    }

    .pm-total {
        text-align: right;
        margin-top: 8px;
        font-weight: 700;
        font-size: 12px;
        color: #1b1b1b;
    }

    @media (max-width: 1200px) {
        .pm-grid {
            grid-template-columns: 1fr;
        }
        .pm-image {
            height: 220px;
        }
    }

    @media (max-width: 768px) {
        .pm-page {
            padding: 18px;
        }
        .pm-panel-inner {
            padding: 20px;
        }
        .pm-info-grid {
            grid-template-columns: 1fr;
        }
        .pm-search {
            width: 140px;
        }
    }
</style>

<div class="pm-page">
    <div class="pm-title">Project Management</div>

    <div class="pm-panel">
        <div class="pm-panel-inner">
            <div class="pm-grid">
                <div>
                    <span class="pm-tag">Project Name</span>
                    <h1 class="pm-name">{{ $project->name }}</h1>
                    <p class="pm-desc">{{ $project->description }}</p>

                    <div class="pm-team">
                        <div class="pm-team-logo">
                            <img src="{{ asset('images/logo-cms.png') }}" alt="Team Logo">
                        </div>
                        <div>
                            <div class="pm-team-text">Team</div>
                            <div class="pm-team-name">Metalift</div>
                        </div>
                    </div>

                    <div class="pm-info-grid">
                        <div>
                            <div class="pm-info-label">Assigned Role</div>
                            <div class="pm-info-value">{{ $currentMember?->role ?? '-' }}</div>
                        </div>
                        <div>
                            <div class="pm-info-label">Project Type</div>
                            <div class="pm-info-value">{{ $project->type ?? '-' }}</div>
                        </div>
                        <div>
                            <div class="pm-info-label">Department</div>
                            <div class="pm-info-value">{{ $currentMember?->location ?? '-' }}</div>
                        </div>
                        <div>
                            <div class="pm-info-label">Project Code</div>
                            <div class="pm-info-value">{{ $project->code ?? '-' }}</div>
                        </div>
                        <div>
                            <div class="pm-info-label">Location</div>
                            <div class="pm-info-value">{{ $project->location ?? '-' }}</div>
                        </div>
                        <div>
                            <div class="pm-info-label">Client/Owner</div>
                            <div class="pm-info-value">{{ $project->client ?? '-' }}</div>
                        </div>
                    </div>

                    <div class="pm-timeframe">
                        <div class="pm-info-label">Timeframe</div>
                        <p><strong>Start Date:</strong> {{ $startDate }}</p>
                        <p><strong>End Date:</strong> {{ $endDate }}</p>
                    </div>
                </div>

                <div>
                    <div class="pm-image">
                        @if($projectImage)
                            <img src="{{ $projectImage }}" alt="{{ $project->name }}">
                        @else
                            <div style="width:100%;height:100%;background:linear-gradient(135deg,#d0d0d0,#bcbcbc);"></div>
                        @endif
                    </div>

                    <div class="pm-bar">
                        <div class="pm-bar-title">Project Materials</div>
                        <div class="pm-search">
                            <input id="materialsSearch" type="text" placeholder="Search">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-5-5m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                    </div>

                    <table class="pm-table" id="materialsTable">
                        <thead>
                            <tr>
                                <th>Material</th>
                                <th>Quantity</th>
                                <th>Price</th>
                                <th>Total</th>
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
                                    <td>&#8369;{{ number_format((float) ($row->unit_price ?? 0), 2) }}</td>
                                    <td>&#8369;{{ number_format((float) ($row->total ?? 0), 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4">No materials recorded for this project.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="pm-total">Overall Total: &#8369;{{ number_format((float) ($projectMaterialsTotal ?? 0), 2) }}</div>
                </div>
            </div>

            <div class="pm-assigned">
                <div class="pm-bar">
                    <div class="pm-bar-title">Assigned Team</div>
                    <div class="pm-search">
                        <input id="teamSearch" type="text" placeholder="Search">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-5-5m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>

                <table class="pm-table" id="teamTable">
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
                                <td colspan="4">No assigned members.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    (function () {
        const materialsInput = document.getElementById('materialsSearch');
        const materialsRows = document.querySelectorAll('#materialsTable tbody tr');

        materialsInput?.addEventListener('input', () => {
            const q = (materialsInput.value || '').toLowerCase().trim();
            materialsRows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(q) ? '' : 'none';
            });
        });

        const teamInput = document.getElementById('teamSearch');
        const teamRows = document.querySelectorAll('#teamTable tbody tr');

        teamInput?.addEventListener('input', () => {
            const q = (teamInput.value || '').toLowerCase().trim();
            teamRows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(q) ? '' : 'none';
            });
        });
    })();
</script>
@endsection




