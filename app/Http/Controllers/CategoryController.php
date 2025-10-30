<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    // Categorías
    public function index()
    {
        if (!session('admin_id')) {
            return redirect()->route('login');
        }

        $categories = Category::withCount(['accessories', 'subcategories'])
            ->orderBy('name')
            ->paginate(15);

        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        if (!session('admin_id')) {
            return redirect()->route('login');
        }

        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        if (!session('admin_id')) {
            return redirect()->route('login');
        }

        $validated = $request->validate([
            'name' => 'required|unique:categories|max:255',
            'slug' => 'nullable|unique:categories|max:255',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        Category::create($validated);

        return redirect()->route('admin.categories')
            ->with('success', 'Categoría creada exitosamente');
    }

    public function edit(Category $category)
    {
        if (!session('admin_id')) {
            return redirect()->route('login');
        }

        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        if (!session('admin_id')) {
            return redirect()->route('login');
        }

        $validated = $request->validate([
            'name' => 'required|max:255|unique:categories,name,' . $category->id,
            'slug' => 'nullable|max:255|unique:categories,slug,' . $category->id,
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $category->update($validated);

        return redirect()->route('admin.categories')
            ->with('success', 'Categoría actualizada exitosamente');
    }

    public function destroy(Category $category)
    {
        if (!session('admin_id')) {
            return redirect()->route('login');
        }

        // Verificar si tiene accesorios asociados
        if ($category->accessories()->count() > 0) {
            return redirect()->route('admin.categories')
                ->with('error', 'No se puede eliminar la categoría porque tiene accesorios asociados');
        }

        $category->delete();

        return redirect()->route('admin.categories')
            ->with('success', 'Categoría eliminada exitosamente');
    }

    // Subcategorías
    public function subcategoriesIndex()
    {
        if (!session('admin_id')) {
            return redirect()->route('login');
        }

        $subcategories = Subcategory::with('category')
            ->withCount('accessories')
            ->orderBy('name')
            ->paginate(15);

        return view('admin.subcategories.index', compact('subcategories'));
    }

    public function subcategoriesCreate()
    {
        if (!session('admin_id')) {
            return redirect()->route('login');
        }

        $categories = Category::orderBy('name')->get();
        return view('admin.subcategories.create', compact('categories'));
    }

    public function subcategoriesStore(Request $request)
    {
        if (!session('admin_id')) {
            return redirect()->route('login');
        }

        $validated = $request->validate([
            'name' => 'required|max:255',
            'slug' => 'nullable|max:255',
            'category_id' => 'required|exists:categories,id',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        // Verificar que no exista una subcategoría con el mismo slug en la misma categoría
        $exists = Subcategory::where('category_id', $validated['category_id'])
            ->where('slug', $validated['slug'])
            ->exists();

        if ($exists) {
            return back()->withErrors(['name' => 'Ya existe una subcategoría con ese nombre en esta categoría'])
                ->withInput();
        }

        Subcategory::create($validated);

        return redirect()->route('admin.subcategories')
            ->with('success', 'Subcategoría creada exitosamente');
    }

    public function subcategoriesEdit(Subcategory $subcategory)
    {
        if (!session('admin_id')) {
            return redirect()->route('login');
        }

        $categories = Category::orderBy('name')->get();
        return view('admin.subcategories.edit', compact('subcategory', 'categories'));
    }

    public function subcategoriesUpdate(Request $request, Subcategory $subcategory)
    {
        if (!session('admin_id')) {
            return redirect()->route('login');
        }

        $validated = $request->validate([
            'name' => 'required|max:255',
            'slug' => 'nullable|max:255',
            'category_id' => 'required|exists:categories,id',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        // Verificar que no exista otra subcategoría con el mismo slug en la misma categoría
        $exists = Subcategory::where('category_id', $validated['category_id'])
            ->where('slug', $validated['slug'])
            ->where('id', '!=', $subcategory->id)
            ->exists();

        if ($exists) {
            return back()->withErrors(['name' => 'Ya existe una subcategoría con ese nombre en esta categoría'])
                ->withInput();
        }

        $subcategory->update($validated);

        return redirect()->route('admin.subcategories')
            ->with('success', 'Subcategoría actualizada exitosamente');
    }

    public function subcategoriesDestroy(Subcategory $subcategory)
    {
        if (!session('admin_id')) {
            return redirect()->route('login');
        }

        // Verificar si tiene accesorios asociados
        if ($subcategory->accessories()->count() > 0) {
            return redirect()->route('admin.subcategories')
                ->with('error', 'No se puede eliminar la subcategoría porque tiene accesorios asociados');
        }

        $subcategory->delete();

        return redirect()->route('admin.subcategories')
            ->with('success', 'Subcategoría eliminada exitosamente');
    }
}
