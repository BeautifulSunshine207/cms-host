<?php

namespace App\Http\Controllers\Employee;

use App\Models\Material;
use Illuminate\Http\Request;


class EmployeeInventoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Material::query()
            ->with(['supplier', 'inventory'])
            ->where('is_active', 1);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('material_name', 'like', "%{$s}%")
                  ->orWhere('unit_of_measure', 'like', "%{$s}%");
            });
        }

        
        $materials = $query->orderBy('material_name')->get();

        
        $totalMaterials = Material::where('is_active', 1)->count();

        return view('employee.inventory.index', compact('materials', 'totalMaterials'));
    }
}
