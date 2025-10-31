<?php

namespace App\Http\Controllers;

use App\Models\Accessory;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\Order;
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
        $totalOrders = Order::count();
        $recentAccessories = Accessory::with(['category', 'subcategory'])
            ->latest()
            ->take(5)
            ->get();
        $recentOrders = Order::latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('totalAccessories', 'totalCategories', 'totalSubcategories', 'totalOrders', 'recentAccessories', 'recentOrders'));
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

    // Métodos para gestionar órdenes
    public function orders(Request $request)
    {
        if (!session('admin_id')) {
            return redirect()->route('login');
        }

        $query = Order::with('items');

        // Filtro por búsqueda
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_email', 'like', "%{$search}%");
            });
        }

        // Filtro por estado
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // Filtro por estado de pago
        if ($request->has('payment_status') && $request->payment_status != '') {
            $query->where('payment_status', $request->payment_status);
        }

        // Ordenamiento
        $sortBy = $request->get('sort', 'created_at');
        $sortOrder = $request->get('order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $orders = $query->paginate(20);

        return view('admin.orders.index', compact('orders'));
    }

    public function showOrder(Order $order)
    {
        if (!session('admin_id')) {
            return redirect()->route('login');
        }

        $order->load('items.accessory');
        return view('admin.orders.show', compact('order'));
    }

    public function updateOrderStatus(Request $request, Order $order)
    {
        if (!session('admin_id')) {
            return redirect()->route('login');
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,processing,completed,cancelled',
        ]);

        $order->update($validated);

        return redirect()->route('admin.orders.show', $order)
            ->with('success', 'Estado de la orden actualizado exitosamente');
    }
}
