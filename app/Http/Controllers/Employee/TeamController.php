<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\AttendanceRecord;
use App\Models\Document;
use App\Models\Project;
use App\Models\TeamMember;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    public function index(Request $request)
    {
        $teamMember = $this->resolveTeamMember($request);

        $assignedProjects = collect();
        $submittedReports = collect();
        $latestStatusByProject = collect();
        $completedProjectsCount = 0;
        $monthlyIncome = 0.0;
        $totalIncome = 0.0;

        if ($teamMember) {
            $assignedProjects = Project::query()
                ->whereHas('teamMembers', function ($q) use ($teamMember) {
                    $q->where('team_members.id', $teamMember->id);
                })
                ->orderByDesc('start_date')
                ->orderBy('name')
                ->get();

            $submittedReports = AttendanceRecord::query()
                ->with(['document'])
                ->where('team_member_id', $teamMember->id)
                ->latest('created_at')
                ->get();

            $latestStatusByProject = $submittedReports
                ->filter(function ($row) {
                    return ! empty($row->project_id);
                })
                ->unique('project_id')
                ->mapWithKeys(function ($row) {
                    return [$row->project_id => $row->status];
                });

            $completedProjectsCount = $submittedReports
                ->where('status', 'verified')
                ->pluck('project_id')
                ->filter()
                ->unique()
                ->count();

            $monthlyIncome = $this->parseMoney($teamMember->salary);
            $totalIncome = $monthlyIncome * 12;
        }

        return view('employee.team.index', compact(
            'teamMember',
            'assignedProjects',
            'submittedReports',
            'latestStatusByProject',
            'completedProjectsCount',
            'monthlyIncome',
            'totalIncome'
        ));
    }

    public function submitReport(Request $request, $projectId)
    {
        $teamMember = $this->resolveTeamMember($request);
        if (! $teamMember) {
            return redirect()
                ->route('employee.team.index')
                ->with('error', 'Your account is not linked to a team member record yet.');
        }

        $project = Project::query()
            ->whereHas('teamMembers', function ($q) use ($teamMember) {
                $q->where('team_members.id', $teamMember->id);
            })
            ->find($projectId);

        if (! $project) {
            return redirect()
                ->route('employee.team.index')
                ->with('error', 'You can only submit reports for projects assigned to your account.');
        }

        $hasPending = AttendanceRecord::query()
            ->where('team_member_id', $teamMember->id)
            ->where('project_id', $project->id)
            ->where('status', 'pending')
            ->exists();

        if ($hasPending) {
            return redirect()
                ->route('employee.team.index')
                ->with('error', 'A report for this project is already pending review.');
        }

        $validated = $request->validate([
            'report_file' => ['required', 'file', 'max:15360', 'mimes:jpg,jpeg,png,gif,mp4,pdf,psd,ai,doc,docx,ppt,pptx'],
        ]);

        $file = $validated['report_file'];
        $storedPath = $file->store('attendance-reports/' . $teamMember->id, 'public');

        $document = Document::create([
            'team_member_id' => $teamMember->id,
            'name' => $file->getClientOriginalName(),
            'size' => $this->formatBytes((int) $file->getSize()),
            'type' => strtolower((string) ($file->getClientOriginalExtension() ?: $file->extension() ?: 'file')),
            'path' => 'storage/' . ltrim($storedPath, '/'),
        ]);

        AttendanceRecord::create([
            'team_member_id' => $teamMember->id,
            'document_id' => $document->id,
            'project_id' => $project->id,
            'project' => $project->name,
            'date' => now()->toDateString(),
            'status' => 'pending',
            'remarks' => null,
        ]);

        return redirect()
            ->route('employee.team.index')
            ->with('success', 'Report submitted successfully.')
            ->with('report_submitted', true);
    }

    private function parseMoney(?string $value): float
    {
        if (! $value) {
            return 0.0;
        }

        $clean = preg_replace('/[^0-9.\-]/', '', $value);
        return is_numeric($clean) ? (float) $clean : 0.0;
    }

    private function resolveTeamMember(Request $request): ?TeamMember
    {
        $user = $request->user();
        $email = strtolower(trim((string) ($user->email ?? '')));

        if ($email !== '') {
            $byEmail = TeamMember::whereRaw('LOWER(email) = ?', [$email])->first();
            if ($byEmail) {
                return $byEmail;
            }
        }

        $name = trim((string) ($user->name ?? ''));
        if ($name === '') {
            return null;
        }

        $matches = TeamMember::where('name', $name)->limit(2)->get();
        return $matches->count() === 1 ? $matches->first() : null;
    }

    private function formatBytes(int $bytes): string
    {
        if ($bytes <= 0) {
            return '0 B';
        }

        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $power = (int) floor(log($bytes, 1024));
        $power = min($power, count($units) - 1);
        $value = $bytes / (1024 ** $power);

        return number_format($value, $power === 0 ? 0 : 2) . ' ' . $units[$power];
    }
}
