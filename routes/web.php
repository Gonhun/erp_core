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

Route::get('/inventory', function () {
    return view('inventory.home');
})->name('inventory.home');

Route::get('/purchasing', function () {
    return view('purchasing.home');
})->name('purchasing.home');
