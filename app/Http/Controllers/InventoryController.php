<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\InventoryTransaction;
use App\Models\Material;
use App\Models\Project;
use App\Models\ExpenseRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
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

        return view('admin.inventory.index', compact('materials', 'totalMaterials'));
    }

    public function stockInOut(Request $request)
{
    $query = Material::query()
        ->with(['supplier', 'inventory'])
        ->where('is_active', 1);

    if ($request->filled('search')) {
        $search = $request->search;
        $query->where('material_name', 'like', "%{$search}%");
    }

    $materials = $query->orderBy('material_name')->get();

    //project dropdown for stock-out
    $projects = Project::orderBy('name')->get();

    $totalMaterials = Material::where('is_active', 1)->count();

    //dummy data ra ni bai
    $topMaterials = collect([]);

    return view('admin.inventory.stock-inout', compact('materials', 'projects', 'totalMaterials', 'topMaterials'));
}

    public function threshold(Request $request)
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

        return view('admin.inventory.threshold', compact('materials'));
    }

    public function history(Request $request)
    {
        $query = InventoryTransaction::query()
            ->with(['material', 'project'])
            ->latest();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->whereHas('material', function ($mq) use ($s) {
                    $mq->where('material_name', 'like', "%{$s}%");
                })->orWhereHas('project', function ($pq) use ($s) {
                    $pq->where('name', 'like', "%{$s}%");
                })->orWhere('type', 'like', "%{$s}%");
            });
        }

        $transactions = $query->get();

        return view('admin.inventory.history', compact('transactions'));
    }

    public function lowStock()
    {
        $materials = Material::query()
            ->with('inventory')
            ->where('is_active', 1)
            ->whereHas('inventory', function ($q) {
                $q->where('threshold_quantity', '>', 0)
                  ->whereColumn('current_quantity', '<=', 'threshold_quantity');
            })
            ->orderBy('material_name')
            ->get()
            ->map(function ($m) {
                return [
                    'id' => $m->id,
                    'material_name' => $m->material_name,
                    'unit_of_measure' => $m->unit_of_measure,
                    'current_quantity' => (float) ($m->inventory?->current_quantity ?? 0),
                    'threshold_quantity' => (float) ($m->inventory?->threshold_quantity ?? 0),
                ];
            });

        return response()->json($materials);
    }

    public function addStock(Request $request)
    {
        $data = $request->validate([
            'material_id' => ['required', 'integer', 'exists:materials,id'],
            'quantity' => ['required', 'numeric', 'min:0.01'],
        ]);

        return DB::transaction(function () use ($data) {
            $material = Material::where('id', $data['material_id'])->lockForUpdate()->first();

            $unitPrice = (float) ($material->unit_price ?? 0);
            $quantity = (float) $data['quantity'];
            $total = $unitPrice * $quantity;
            $unit = $material->unit_of_measure ?? '';
            $currency = "\u{20B1}";

            $qtyText = number_format($quantity, 2, '.', ',') . ($unit ? ' ' . $unit : '');
            $priceText = $currency . number_format($unitPrice, 2, '.', ',') . ($unit ? ' per ' . $unit : '');
            $totalText = $currency . number_format($total, 2, '.', ',');

            ExpenseRequest::create([
                'material_id' => $material->id,
                'materials' => $material->material_name ?? 'Material',
                'quantity' => $qtyText,
                'quantity_value' => $quantity,
                'price_per_unit' => $priceText,
                'total' => $totalText,
                'date' => now()->toDateString(),
                'status' => 'pending',
            ]);

            return response()->json([
                'message' => 'Stock-in request sent to Finance.',
            ]);
        });
    }

    public function useStock(Request $request)
    {
        $data = $request->validate([
            'material_id' => ['required', 'integer', 'exists:materials,id'],
            'quantity' => ['required', 'numeric', 'min:0.01'],
            'project' => ['nullable', 'integer', 'exists:projects,id'],
        ]);

        return DB::transaction(function () use ($data) {
            $material = Material::where('id', $data['material_id'])->lockForUpdate()->first();
            $inventory = Inventory::where('material_id', $material->id)->lockForUpdate()->first();

            if (!$inventory) {
                return response()->json(['message' => 'No inventory record found.'], 422);
            }

            if ((float) $inventory->current_quantity < (float) $data['quantity']) {
                return response()->json(['message' => 'Not enough stock.'], 422);
            }

            $inventory->current_quantity = (float) $inventory->current_quantity - (float) $data['quantity'];
            $inventory->save();

            InventoryTransaction::create([
                'material_id' => $material->id,
                'type' => 'stock_out',
                'quantity' => $data['quantity'],
                'remaining_stock' => $inventory->current_quantity,
                'project_id' => $data['project'] ?? null,
                'remarks' => 'stock out',
            ]);

            return response()->json([
                'message' => 'Stock-out recorded.',
                'current_quantity' => $inventory->current_quantity,
            ]);
        });
    }

    public function updateThreshold(Request $request)
    {
        $data = $request->validate([
            'material_id' => ['required', 'integer', 'exists:materials,id'],
            'threshold' => ['required', 'numeric', 'min:0'],
            'max_threshold' => ['nullable', 'numeric', 'min:0'],
        ]);

        if ($data['max_threshold'] !== null && $data['max_threshold'] < $data['threshold']) {
            return response()->json(['message' => 'Maximum must be greater than or equal to minimum.'], 422);
        }

        $inventory = Inventory::firstOrCreate(
            ['material_id' => $data['material_id']],
            ['current_quantity' => 0, 'threshold_quantity' => 0, 'max_threshold' => null]
        );

        $inventory->threshold_quantity = $data['threshold'];
        $inventory->max_threshold = $data['max_threshold'];
        $inventory->save();

        return response()->json(['message' => 'Threshold updated.']);
    }
}
