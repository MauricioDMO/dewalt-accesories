<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\CategoryController;

// Rutas públicas del cliente
Route::get('/', [ClientController::class, 'index'])->name('home');
Route::get('/accesorio/{accessory}', [ClientController::class, 'show'])->name('accessory.show');

// Rutas de autenticación
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Rutas de administración (protegidas)
Route::prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    
    // Accesorios
    Route::get('/accesorios', [AdminController::class, 'accessories'])->name('admin.accessories');
    Route::get('/accesorios/crear', [AdminController::class, 'createAccessory'])->name('admin.accessories.create');
    Route::post('/accesorios', [AdminController::class, 'storeAccessory'])->name('admin.accessories.store');
    Route::get('/accesorios/{accessory}/editar', [AdminController::class, 'editAccessory'])->name('admin.accessories.edit');
    Route::put('/accesorios/{accessory}', [AdminController::class, 'updateAccessory'])->name('admin.accessories.update');
    Route::delete('/accesorios/{accessory}', [AdminController::class, 'destroyAccessory'])->name('admin.accessories.destroy');
    
    // Categorías
    Route::get('/categorias', [CategoryController::class, 'index'])->name('admin.categories');
    Route::get('/categorias/crear', [CategoryController::class, 'create'])->name('admin.categories.create');
    Route::post('/categorias', [CategoryController::class, 'store'])->name('admin.categories.store');
    Route::get('/categorias/{category}/editar', [CategoryController::class, 'edit'])->name('admin.categories.edit');
    Route::put('/categorias/{category}', [CategoryController::class, 'update'])->name('admin.categories.update');
    Route::delete('/categorias/{category}', [CategoryController::class, 'destroy'])->name('admin.categories.destroy');
    
    // Subcategorías
    Route::get('/subcategorias', [CategoryController::class, 'subcategoriesIndex'])->name('admin.subcategories');
    Route::get('/subcategorias/crear', [CategoryController::class, 'subcategoriesCreate'])->name('admin.subcategories.create');
    Route::post('/subcategorias', [CategoryController::class, 'subcategoriesStore'])->name('admin.subcategories.store');
    Route::get('/subcategorias/{subcategory}/editar', [CategoryController::class, 'subcategoriesEdit'])->name('admin.subcategories.edit');
    Route::put('/subcategorias/{subcategory}', [CategoryController::class, 'subcategoriesUpdate'])->name('admin.subcategories.update');
    Route::delete('/subcategorias/{subcategory}', [CategoryController::class, 'subcategoriesDestroy'])->name('admin.subcategories.destroy');
    
    // API para obtener subcategorías
    Route::get('/subcategories/{categoryId}', [AdminController::class, 'getSubcategories'])->name('admin.subcategories.get');
});
