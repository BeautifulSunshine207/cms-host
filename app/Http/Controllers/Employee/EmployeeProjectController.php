<?php

namespace App\Http\Controllers\Employee;

use App\Models\Project;
use App\Models\TeamMember;
use App\Models\InventoryTransaction;
use Illuminate\Http\Request;    

class EmployeeProjectController extends Controller
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
        
        return view('employee.projects.employeedashboard', compact('projects'));
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

            return view('employee.projects.show', compact('project', 'projectMaterials', 'projectMaterialsTotal'));
        }


    

   
}
