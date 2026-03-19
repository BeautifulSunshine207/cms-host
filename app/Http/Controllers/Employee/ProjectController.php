<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\InventoryTransaction;
use App\Models\Project;
use App\Models\TeamMember;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function dashboard(Request $request)
    {
        $teamMember = $this->resolveTeamMember($request);

        $projectsQuery = Project::query()
            ->with('teamMembers')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('type', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%");
                });
            });

        if ($teamMember) {
            $projectsQuery->whereHas('teamMembers', function ($q) use ($teamMember) {
                $q->where('team_members.id', $teamMember->id);
            });
        } else {
            $projectsQuery->whereRaw('1 = 0');
        }

        $projects = $projectsQuery
            ->orderByDesc('start_date')
            ->orderBy('name')
            ->get();

        return view('employee.projects.employeedashboard', compact('projects', 'teamMember'));
    }

    public function show(Request $request, $id)
    {
        $teamMember = $this->resolveTeamMember($request);
        if (! $teamMember) {
            return redirect()
                ->route('employee.projects.employeedashboard')
                ->with('error', 'Your account is not linked to a team member record yet.');
        }

        $project = Project::with(['teamMembers' => function ($q) {
            $q->orderBy('name');
        }])
            ->whereHas('teamMembers', function ($q) use ($teamMember) {
                $q->where('team_members.id', $teamMember->id);
            })
            ->find($id);

        if (! $project) {
            return redirect()
                ->route('employee.projects.employeedashboard')
                ->with('error', 'You can only view projects assigned to your account.');
        }

        $projectMaterials = InventoryTransaction::with('material')
            ->where('project_id', $project->id)
            ->where('type', 'stock_out')
            ->get()
            ->groupBy('material_id')
            ->map(function ($rows) {
                $first = $rows->first();
                $quantity = $rows->sum(function ($row) {
                    return (float) $row->quantity;
                });
                $lastUsed = $rows->sortByDesc('created_at')->first()?->created_at;
                $unitPrice = (float) ($first?->material?->unit_price ?? 0);
                $total = $quantity * $unitPrice;

                return (object) [
                    'material' => $first?->material,
                    'quantity' => $quantity,
                    'last_used' => $lastUsed,
                    'unit_price' => $unitPrice,
                    'total' => $total,
                ];
            })
            ->values();

        $projectMaterialsTotal = $projectMaterials->sum(function ($row) {
            return (float) ($row->total ?? 0);
        });

        return view('employee.projects.show', compact('project', 'projectMaterials', 'projectMaterialsTotal'));
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

        //Fallback only if a single exact-name record exists.
        $name = trim((string) ($user->name ?? ''));
        if ($name === '') {
            return null;
        }

        $matches = TeamMember::where('name', $name)->limit(2)->get();
        return $matches->count() === 1 ? $matches->first() : null;
    }
}
