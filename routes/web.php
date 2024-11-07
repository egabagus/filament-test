<?php

use App\Http\Controllers\ShopController;
use App\Livewire\CreateCustomer;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/admin');
});

Route::controller(ShopController::class)->group(function () {
    Route::get('/shop', 'index');
});

Route::get('customer/create', CreateCustomer::class);
