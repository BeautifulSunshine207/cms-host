<?php

namespace App\Http\Controllers;
use App\Models\TeamMemberUpdateRequest;
use App\Models\TeamMember;
use App\Models\Project;
use App\Models\AttendanceRecord;
use App\Models\VerificationHistory;
use App\Models\PayrollRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class TeamController extends Controller
{
    public function index()
    {
        $teamMembers = TeamMember::all();
        $projects = Project::all();
        $verificationHistory = VerificationHistory::with('teamMember')->latest()->take(7)->get();
        $verifiedCount = VerificationHistory::where('status', 'Verified')->count();
        $deniedCount = VerificationHistory::where('status', 'Denied')->count();
        $attendanceRecords = AttendanceRecord::with(['teamMember', 'document'])->where('status', 'pending')->get();
        
        return view('admin.team.index', compact(
            'teamMembers',
            'projects',
            'verificationHistory',
            'verifiedCount',
            'deniedCount',
            'attendanceRecords'
        ));
    }

    public function documents(Request $request)
    {
        $hasUpdateTable = Schema::hasTable('team_member_update_requests');

        $query = TeamMember::query()->with('documents');
        if ($hasUpdateTable) {
            $query->with('pendingUpdateRequest');
        }

        $teamMembers = $query->orderBy('name')->get();

        return view('admin.team.documents', compact('teamMembers', 'hasUpdateTable'));
    }

    public function payroll()
    {
        $teamMembers = TeamMember::with('payrollRecords')->get();
        
        return view('admin.team.payroll', compact('teamMembers'));
    }

    public function assign()
    {
        $projects = Project::with('teamMembers')->get();
        $teamMembers = TeamMember::all();
        
        return view('admin.team.assign', compact('projects', 'teamMembers'));
    }

    public function attendance()
    {
        $attendanceRecords = AttendanceRecord::with(['teamMember', 'document'])->where('status', 'pending')->get();
        
        return view('admin.team.attendance', compact('attendanceRecords'));
    }

    public function approveAttendance(Request $request, $id)
    {
        $attendance = AttendanceRecord::with(['teamMember', 'document'])->findOrFail($id);
        $attendance->update(['status' => 'verified']);

        $member = $attendance->teamMember;
        $doc = $attendance->document;

        PayrollRequest::create([
            'name' => $member?->name ?? 'Unknown',
            'file_name' => $doc?->name ?? 'Attendance Report',
            'file_path' => $doc?->path ?? '',
            'rate' => $member?->salary ?? 'â‚±0',
            'date' => $attendance->date ?? now()->toDateString(),
            'status' => 'pending',
        ]);
        
        VerificationHistory::create([
            'team_member_id' => $attendance->team_member_id,
            'status' => 'Verified',
            'date_submitted' => $attendance->date,
            'date_checked' => now(),
        ]);
        
        return redirect()->back()->with('success', 'Attendance approved');
    }

    public function rejectAttendance(Request $request, $id)
    {
        $validated = $request->validate([
            'remarks' => 'required|string',
            'admin_response_file' => 'nullable|file|max:15360|mimes:jpg,jpeg,png,gif,mp4,pdf,psd,ai,doc,docx,ppt,pptx',
        ]);

        $attendance = AttendanceRecord::findOrFail($id);

        $adminResponseName = $attendance->admin_response_name;
        $adminResponsePath = $attendance->admin_response_path;

        if ($request->hasFile('admin_response_file')) {
            $file = $request->file('admin_response_file');
            $storedPath = $file->store('attendance-responses', 'public');
            $adminResponseName = $file->getClientOriginalName();
            $adminResponsePath = 'storage/' . ltrim($storedPath, '/');
        }

        $attendance->update([
            'status' => 'denied',
            'remarks' => $validated['remarks'],
            'admin_response_name' => $adminResponseName,
            'admin_response_path' => $adminResponsePath,
        ]);
        
        VerificationHistory::create([
            'team_member_id' => $attendance->team_member_id,
            'status' => 'Denied',
            'date_submitted' => $attendance->date,
            'date_checked' => now(),
        ]);
        
        return redirect()->back()->with('success', 'Attendance rejected');
    }

    public function assignToProject(Request $request, $projectId)
{
    $validated = $request->validate([
        'team_members' => 'required|array|min:1',
        'team_members.*' => 'integer|exists:team_members,id',
        // optional: lets us “replace” vs “add”
        'mode' => 'nullable|in:replace,add',
    ]);

    $project = Project::findOrFail($projectId);

    // Default: replace assigned members for that project
    if (($validated['mode'] ?? 'replace') === 'add') {
        $project->teamMembers()->syncWithoutDetaching($validated['team_members']);
    } else {
        $project->teamMembers()->sync($validated['team_members']);
    }

    return redirect()->back()->with('success', 'Team members assigned successfully');
}


    public function listMembers(Request $request)
    {
        $search = $request->query('search');

        $q = TeamMember::query()
            ->select('id', 'name', 'location', 'salary', 'role', 'avatar');

        if ($search) {
            $q->where(function ($w) use ($search) {
                $w->where('name', 'like', "%{$search}%")
                ->orWhere('role', 'like', "%{$search}%")
                ->orWhere('location', 'like', "%{$search}%");
            });
        }

        return response()->json(
            $q->orderBy('name')->limit(200)->get()
        );
    }

    public function approveUpdateRequest($id)
    {
        $req = TeamMemberUpdateRequest::with('teamMember')->findOrFail($id);

        // apply changes to the team member
        $req->teamMember->update($req->changes);

        $req->update([
            'status' => 'approved',
            'reviewed_at' => now(),
            'remarks' => null,
        ]);

        return redirect()->back()->with('success', 'Update request approved');
    }

    public function rejectUpdateRequest(Request $request, $id)
    {
        $data = $request->validate([
            'remarks' => 'required|string',
        ]);

        $req = TeamMemberUpdateRequest::findOrFail($id);

        $req->update([
            'status' => 'rejected',
            'reviewed_at' => now(),
            'remarks' => $data['remarks'],
        ]);

        return redirect()->back()->with('success', 'Update request rejected');
    }



}
