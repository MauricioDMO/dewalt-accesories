<?php

namespace App\Http\Controllers;

use App\Models\Accessory;
use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        if (!session('admin_id')) {
            return redirect()->route('login');
        }

        $totalAccessories = Accessory::count();
        $totalCategories = Category::count();
        $totalSubcategories = Subcategory::count();
        $recentAccessories = Accessory::with(['category', 'subcategory'])
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('totalAccessories', 'totalCategories', 'totalSubcategories', 'recentAccessories'));
    }

    public function accessories(Request $request)
    {
        if (!session('admin_id')) {
            return redirect()->route('login');
        }

        $query = Accessory::with(['category', 'subcategory']);

        // Filtro por búsqueda
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filtro por categoría
        if ($request->has('category') && $request->category != '') {
            $query->where('category_id', $request->category);
        }

        // Filtro por subcategoría
        if ($request->has('subcategory') && $request->subcategory != '') {
            $query->where('subcategory_id', $request->subcategory);
        }

        // Ordenamiento
        $sortBy = $request->get('sort', 'name');
        $sortOrder = $request->get('order', 'asc');
        
        if ($sortBy === 'price') {
            $query->orderBy('price', $sortOrder);
        } elseif ($sortBy === 'code') {
            $query->orderBy('code', $sortOrder);
        } else {
            $query->orderBy('name', $sortOrder);
        }

        $accessories = $query->paginate(20);
        $categories = Category::all();

        // Obtener subcategorías de la categoría seleccionada
        $selectedCategoryId = $request->get('category');
        $subcategories = $selectedCategoryId 
            ? Subcategory::where('category_id', $selectedCategoryId)->orderBy('name')->get()
            : collect();

        return view('admin.accessories.index', compact('accessories', 'categories', 'subcategories'));
    }

    public function createAccessory()
    {
        if (!session('admin_id')) {
            return redirect()->route('login');
        }

        $categories = Category::all();
        return view('admin.accessories.create', compact('categories'));
    }

    public function storeAccessory(Request $request)
    {
        if (!session('admin_id')) {
            return redirect()->route('login');
        }

        $validated = $request->validate([
            'code' => 'required|unique:accessories',
            'name' => 'required',
            'description' => 'nullable',
            'price' => 'required|numeric|min:0',
            'offer' => 'nullable|numeric|min:0',
            'image' => 'nullable',
            'alt' => 'nullable',
            'type' => 'nullable',
            'units' => 'boolean',
            'category_id' => 'nullable|exists:categories,id',
            'subcategory_id' => 'nullable|exists:subcategories,id',
        ]);

        Accessory::create($validated);

        return redirect()->route('admin.accessories')
            ->with('success', 'Accesorio creado exitosamente');
    }

    public function editAccessory(Accessory $accessory)
    {
        if (!session('admin_id')) {
            return redirect()->route('login');
        }

        $categories = Category::all();
        $subcategories = $accessory->category_id 
            ? Subcategory::where('category_id', $accessory->category_id)->get()
            : collect();

        return view('admin.accessories.edit', compact('accessory', 'categories', 'subcategories'));
    }

    public function updateAccessory(Request $request, Accessory $accessory)
    {
        if (!session('admin_id')) {
            return redirect()->route('login');
        }

        $validated = $request->validate([
            'code' => 'required|unique:accessories,code,' . $accessory->id,
            'name' => 'required',
            'description' => 'nullable',
            'price' => 'required|numeric|min:0',
            'offer' => 'nullable|numeric|min:0',
            'image' => 'nullable',
            'alt' => 'nullable',
            'type' => 'nullable',
            'units' => 'boolean',
            'category_id' => 'nullable|exists:categories,id',
            'subcategory_id' => 'nullable|exists:subcategories,id',
        ]);

        $accessory->update($validated);

        return redirect()->route('admin.accessories')
            ->with('success', 'Accesorio actualizado exitosamente');
    }

    public function destroyAccessory(Accessory $accessory)
    {
        if (!session('admin_id')) {
            return redirect()->route('login');
        }

        $accessory->delete();

        return redirect()->route('admin.accessories')
            ->with('success', 'Accesorio eliminado exitosamente');
    }

    // Método para obtener subcategorías por AJAX
    public function getSubcategories($categoryId)
    {
        $subcategories = Subcategory::where('category_id', $categoryId)->get();
        return response()->json($subcategories);
    }
}
