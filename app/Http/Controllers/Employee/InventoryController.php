<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Material;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Material::query()
            ->with(['supplier', 'inventory'])
            ->where('is_active', 1);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('material_name', 'like', "%{$search}%")
                    ->orWhere('unit_of_measure', 'like', "%{$search}%");
            });
        }

        $materials = $query->orderBy('material_name')->get();

        return view('employee.inventory.index', compact('materials'));
    }
}
