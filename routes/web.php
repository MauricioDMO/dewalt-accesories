<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CartController;

// Rutas públicas del cliente
Route::get('/', [ClientController::class, 'index'])->name('home');
Route::get('/accesorio/{accessory}', [ClientController::class, 'show'])->name('accessory.show');

// Rutas del carrito de compras
Route::prefix('carrito')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('cart.index');
    Route::post('/agregar/{id}', [CartController::class, 'add'])->name('cart.add');
    Route::put('/actualizar/{id}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/eliminar/{id}', [CartController::class, 'remove'])->name('cart.remove');
    Route::get('/checkout', [CartController::class, 'checkout'])->name('cart.checkout');
    Route::post('/procesar', [CartController::class, 'processOrder'])->name('cart.process');
    Route::get('/count', [CartController::class, 'getCartCount'])->name('cart.count');
});

// Ruta de confirmación de orden
Route::get('/orden/confirmacion/{id}', [CartController::class, 'confirmation'])->name('order.confirmation');

// Rutas para Wompi
Route::get('/pago-procesado', [CartController::class, 'paymentProcessed'])->name('payment.processed');
Route::get('/orden/wompi/callback/{id}', [CartController::class, 'wompiCallback'])->name('order.wompi.callback');
Route::post('/webhook/wompi', [CartController::class, 'wompiWebhook'])->name('webhook.wompi');

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
    
    // Órdenes de compra
    Route::get('/ordenes', [AdminController::class, 'orders'])->name('admin.orders');
    Route::get('/ordenes/{order}', [AdminController::class, 'showOrder'])->name('admin.orders.show');
    Route::put('/ordenes/{order}/estado', [AdminController::class, 'updateOrderStatus'])->name('admin.orders.update-status');
});
