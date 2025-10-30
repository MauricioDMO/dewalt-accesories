<?php

namespace App\Http\Controllers;

use App\Models\Accessory;
use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index(Request $request)
    {
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
        } else {
            $query->orderBy('name', $sortOrder);
        }

        $accessories = $query->paginate(12);
        $categories = Category::with('subcategories')->get();

        // Obtener subcategorías de la categoría seleccionada
        $selectedCategoryId = $request->get('category');
        $subcategories = $selectedCategoryId 
            ? Subcategory::where('category_id', $selectedCategoryId)->orderBy('name')->get()
            : collect();

        return view('client.index', compact('accessories', 'categories', 'subcategories'));
    }

    public function show(Accessory $accessory)
    {
        $accessory->load(['category', 'subcategory']);
        $relatedAccessories = Accessory::where('category_id', $accessory->category_id)
            ->where('id', '!=', $accessory->id)
            ->take(4)
            ->get();

        return view('client.show', compact('accessory', 'relatedAccessories'));
    }
}
