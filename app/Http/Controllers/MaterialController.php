<?php

namespace App\Http\Controllers;

use App\Models\Material;
use App\Models\Supplier;
use App\Models\Inventory;
use App\Models\SupplierProduct;
use App\Models\MaterialHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MaterialController extends Controller
{
    //overview page (view materials)
    public function overview(Request $request)
    {
        $q = Material::query()
            ->with('supplier')
            ->where('is_active', 1);

        if ($request->filled('search')) {
            $s = $request->search;

   
            $q->where(function ($x) use ($s) {
                $x->where('material_name', 'like', "%{$s}%")
                    ->orWhere('unit_of_measure', 'like', "%{$s}%")
                    ->orWhereHas('supplier', function ($sq) use ($s) {
                        $sq->where('supplier_name', 'like', "%{$s}%");
                    });
            });
        }

        $materials = $q->orderBy('material_name')->get();

        return view('admin.materials.overview', compact('materials'));
    }

    //create page 1: choose supplier
    public function chooseSupplier(Request $request)
    {
        $q = Supplier::query()->where('status', 'active');

        if ($request->filled('search')) {
            $s = $request->search;
            $q->where('supplier_name', 'like', "%{$s}%");
        }

        $suppliers = $q->orderBy('supplier_name')->get();

        return view('admin.materials.create', compact('suppliers'));
    }

    //create page 2: show supplier products
    public function supplierProducts(Request $request, Supplier $supplier)
    {
        $products = SupplierProduct::query()
            ->where('supplier_id', $supplier->id)
            ->orderBy('product_name')
            ->get();

        $cart = session()->get('mms_cart', []);

        return view('admin.materials.supplier-products', compact('supplier', 'products', 'cart'));
    }

    public function addSupplierProduct(Request $request, Supplier $supplier)
    {
        $data = $request->validate([
            'product_name' => ['required', 'string', 'max:255'],
            'unit_of_measure' => ['required', 'string', 'max:50'],
            'price' => ['required', 'numeric', 'min:0'],
            'image' => ['nullable', 'image', 'max:2048'],
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('supplier-products', 'public');
        }

        SupplierProduct::create([
            'supplier_id' => $supplier->id,
            'product_name' => $data['product_name'],
            'unit_of_measure' => $data['unit_of_measure'],
            'price' => $data['price'],
            'image_path' => $imagePath,
        ]);

        return back()->with('success', 'material added to supplier');
    }

    public function storeSupplier(Request $request)
    {
        $data = $request->validate([
            'supplier_name' => ['required', 'string', 'max:255'],
            'partnered_date' => ['nullable', 'date'],
            'specializes_in' => ['nullable', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'contact_number' => ['nullable', 'string', 'max:50'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'logo' => ['nullable', 'image', 'max:2048'],
        ]);

        $logoPath = null;
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('supplier-logos', 'public');
        }

        $details = [
            'partnered_date' => $data['partnered_date'] ?? null,
            'specializes_in' => $data['specializes_in'] ?? null,
            'location' => $data['location'] ?? null,
            'contact_number' => $data['contact_number'] ?? null,
            'contact_email' => $data['contact_email'] ?? null,
            'logo_path' => $logoPath,
        ];

        $contactInfo = json_encode(array_filter($details, function ($value) {
            return $value !== null && $value !== '';
        }));

        Supplier::create([
            'supplier_name' => $data['supplier_name'],
            'contact_info' => $contactInfo === '[]' ? null : $contactInfo,
            'status' => 'active',
        ]);

        return redirect()->route('materials.create')->with('success', 'supplier added');
    }

    // add to cart (session)
    public function cartAdd(Request $request)
    {
        $data = $request->validate([
            'supplier_id' => ['required', 'integer', 'exists:suppliers,id'],
            'product_id'  => ['required', 'integer', 'exists:supplier_products,id'],
        ]);

        $product = SupplierProduct::where('id', $data['product_id'])
            ->where('supplier_id', $data['supplier_id'])
            ->firstOrFail();

        $cart = session()->get('mms_cart', []);

        // key: supplier_id:product_id
        $key = $data['supplier_id'] . ':' . $data['product_id'];

        $cart[$key] = [
            'supplier_id'     => $product->supplier_id,
            'product_id'      => $product->id,
            'product_name'    => $product->product_name,
            'unit_of_measure' => $product->unit_of_measure,
            'price'           => $product->price,
            'image_path'      => $product->image_path,
        ];

        session()->put('mms_cart', $cart);

        return back()->with('success', 'added to cart');
    }

    // remove from cart
    public function cartRemove(Request $request)
    {
        $data = $request->validate([
            'key' => ['required', 'string'],
        ]);

        $cart = session()->get('mms_cart', []);
        unset($cart[$data['key']]);
        session()->put('mms_cart', $cart);

        return back()->with('success', 'removed');
    }

    // view cart page (the modal style page)
    public function cartView()
    {
        $cart = session()->get('mms_cart', []);
        return view('admin.materials.cart', compact('cart'));
    }

    // create materials from cart then also create inventory rows
    public function cartCheckout(Request $request)
    {
        $cart = session()->get('mms_cart', []);

        if (count($cart) === 0) {
            return back()->with('error', 'cart is empty');
        }

        DB::transaction(function () use ($cart) {
            foreach ($cart as $item) {
                // create material
                $material = Material::create([
                    'material_name'   => $item['product_name'],
                    'unit_of_measure' => $item['unit_of_measure'],
                    'supplier_id'     => $item['supplier_id'],
                    'unit_price'      => $item['price'] ?? 0,
                    'is_active'       => 1,
                ]);

                // create inventory row (bridge to ims later)
                Inventory::create([
                    'material_id'        => $material->id,
                    'current_quantity'   => 0,
                    'threshold_quantity' => 0,
                ]);

                // save history (added)
                MaterialHistory::create([
                    'material_id' => $material->id,
                    'action'      => 'added',
                    'from_data'    => null,
                    'to_data'      => json_encode([
                        'material_name'   => $material->material_name,
                        'unit_of_measure' => $material->unit_of_measure,
                        'supplier_id'     => $material->supplier_id,
                        'unit_price'      => $material->unit_price,
                    ]),
                ]);
            }
        });

        session()->forget('mms_cart');

        return redirect()->route('materials.overview')->with('success', 'materials created');
    }

    // update (only material_name, unit_of_measure, supplier_id)
    public function update(Request $request, Material $material)
    {
        $data = $request->validate([
            'material_name'   => ['required', 'string', 'max:255'],
            'unit_of_measure' => ['required', 'string', 'max:50'],
            'supplier_id'     => ['required', 'integer', 'exists:suppliers,id'],
        ]);

        $from = [
            'material_name'   => $material->material_name,
            'unit_of_measure' => $material->unit_of_measure,
            'supplier_id'     => $material->supplier_id,
        ];

        $material->update($data);

        MaterialHistory::create([
            'material_id' => $material->id,
            'action'      => 'updated',
            'from_data'   => json_encode($from),
            'to_data'     => json_encode($data),
        ]);

        return back()->with('success', 'updated');
    }

    //delete (soft delete using is_active = 0)
    public function delete(Material $material)
    {
        $from = [
            'material_name'   => $material->material_name,
            'unit_of_measure' => $material->unit_of_measure,
            'supplier_id'     => $material->supplier_id,
        ];

        $material->update(['is_active' => 0]);

        MaterialHistory::create([
            'material_id' => $material->id,
            'action'      => 'deleted',
            'from_data'   => json_encode($from),
            'to_data'     => null,
        ]);

        return back()->with('success', 'deleted');
    }

    //history page
    public function history()
    {
        $history = MaterialHistory::query()
            ->with(['material', 'material.supplier'])
            ->latest()
            ->get();

        return view('admin.materials.history', compact('history'));
    }
}
