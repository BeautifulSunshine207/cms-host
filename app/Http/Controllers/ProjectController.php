<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\TeamMember;
use App\Models\InventoryTransaction;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $query = Project::with('teamMembers');
        
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('type', 'like', "%{$search}%");
            });
        }
        
        $projects = $query->get();
        
        return view('admin.projects.index', compact('projects'));
    }

    public function show($id)
        {
            $project = Project::with(['teamMembers' => function ($q) {
                $q->orderBy('name'); // optional
            }])->findOrFail($id);

            $projectMaterials = InventoryTransaction::with('material')
                ->where('project_id', $project->id)
                ->where('type', 'stock_out')
                ->get()
                ->groupBy('material_id')
                ->map(function ($rows) {
                    $first = $rows->first();
                    $qty = $rows->sum(function ($row) {
                        return (float) $row->quantity;
                    });
                    $lastUsed = $rows->sortByDesc('created_at')->first()?->created_at;
                    $unitPrice = (float) ($first?->material?->unit_price ?? 0);
                    $rowTotal = $qty * $unitPrice;

                    return (object) [
                        'material' => $first?->material,
                        'quantity' => $qty,
                        'last_used' => $lastUsed,
                        'unit_price' => $unitPrice,
                        'total' => $rowTotal,
                    ];
                })
                ->values();

            $projectMaterialsTotal = $projectMaterials->sum(function ($row) {
                return (float) ($row->total ?? 0);
            });

            return view('admin.projects.show', compact('project', 'projectMaterials', 'projectMaterialsTotal'));
        }

    public function create()
    {
        $teamMembers = TeamMember::all();
        $projectTypes = [
            'Park Project',
            'House Project',
            'Bridge Project',
            'Factory Project',
            'Building Project',
            'Road Project',
            'Bus Terminal Project'
        ];
        
        return view('admin.projects.create', compact('teamMembers', 'projectTypes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string',
            'code' => 'required|string|unique:projects',
            'location' => 'required|string',
            'description' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'client' => 'required|string',
            'image' => 'nullable|image|max:2048',
            'team_members' => 'array',
            'team_members.*' => 'integer|exists:team_members,id',
        ]);

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('projects', 'public');
            $validated['image'] = $imagePath;
        }

        $validated['status'] = 'pending';
        $validated['progress'] = 0;

        // get team member ids, then remove it from $validated
        $teamMemberIds = $validated['team_members'] ?? [];
        unset($validated['team_members']);

        $project = Project::create($validated);

        // save pivot assignments
        $project->teamMembers()->sync($teamMemberIds);

        return redirect()->route('projects.index')->with('success', 'Project created successfully');
    }

    public function update(Request $request, $id)
    {
        $project = Project::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string',
            'location' => 'required|string',
            'description' => 'required|string',
            'progress' => 'required|integer|min:0|max:100',
            'status' => 'required|in:ongoing,completed,pending',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'client' => 'required|string',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('projects', 'public');
            $validated['image'] = $imagePath;
        }

        $project->update($validated);

        if ($request->has('team_members')) {
            $project->teamMembers()->sync($request->team_members);
        }

        return redirect()->route('projects.show', $project->id)->with('success', 'Project updated successfully');
    }


}
