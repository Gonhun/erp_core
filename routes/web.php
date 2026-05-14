<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('/finance', function () {
    return view('finance.home');
})->name('finance.home');

Route::get('/finance/group-accounts', \App\Livewire\GroupAccountManager::class)->name('finance.group-accounts');
Route::get('/finance/accounts', \App\Livewire\AccountManager::class)->name('finance.accounts');
Route::get('/finance/taxes', \App\Livewire\TaxManager::class)->name('finance.taxes');
Route::get('/finance/taxes/{tax}/setup', \App\Livewire\TaxSetupManager::class)->name('finance.taxes.setup');
Route::get('/finance/customers', \App\Livewire\CustomerManager::class)->name('finance.customers');

Route::get('/inventory', function () {
    return view('inventory.home');
})->name('inventory.home');

Route::get('/inventory/location-categories', \App\Livewire\LocationCategoryManager::class)->name('inventory.location-categories');
Route::get('/inventory/warehouses', \App\Livewire\WarehouseManager::class)->name('inventory.warehouses');
Route::get('/inventory/warehouse-locations', \App\Livewire\WarehouseLocationManager::class)->name('inventory.warehouse-locations');
Route::get('/inventory/uom-categories', \App\Livewire\UomCategoryManager::class)->name('inventory.uom-categories');
Route::get('/inventory/brands', \App\Livewire\BrandManager::class)->name('inventory.brands');
Route::get('/inventory/item-categories', \App\Livewire\ItemCategoryManager::class)->name('inventory.item-categories');
Route::get('/inventory/product-types', \App\Livewire\ProductTypeManager::class)->name('inventory.product-types');
Route::get('/inventory/uoms', \App\Livewire\UomManager::class)->name('inventory.uoms');
Route::get('/inventory/products', \App\Livewire\ProductManager::class)->name('inventory.products');
Route::get('/inventory/receipts', \App\Livewire\GoodsReceiptManager::class)->name('inventory.receipts');
Route::get('/inventory/moves', \App\Livewire\StockMoveManager::class)->name('inventory.moves');

Route::get('/purchasing', function () {
    return view('purchasing.home');
})->name('purchasing.home');

Route::get('/purchasing/suppliers', \App\Livewire\SupplierManager::class)->name('purchasing.suppliers');
Route::get('/purchasing/orders', \App\Livewire\PurchaseOrderManager::class)->name('purchasing.orders');
