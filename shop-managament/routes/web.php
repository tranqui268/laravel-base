<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UsersController;
use App\Http\Middleware\RedirectIfNotLoggedIn;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('index');
});

Route::post('/login', [
    AuthController::class,
    'login'
])->name('login')->middleware('web');

Route::post('/logout', function () {
    Auth::logout();
    session()->flush();
    return redirect('/');
})->name('logout');

Route::middleware(RedirectIfNotLoggedIn::class)->group(function () {
    Route::get('/products', [ProductController::class,'index'])->name('product');

    Route::get('/products/detail', [ProductController::class, 'showDetail']);

    
    Route::get('/users', function () {
        return view('users.index');
    })->name('user');
    
    
    Route::get('/customers', function () {
        return view('customers.index');
    })->name('customer');


});

Route::middleware(RedirectIfNotLoggedIn::class)->group(function () {
    Route::get('api/users', [UsersController::class, 'getAllUser']);
    Route::post('api/users', [UsersController::class,'createUser']);
    Route::put('api/users/{id}', [UsersController::class,'updateUser']);
    Route::put('api/users/is-active/{id}', [UsersController::class,'updateActiveUser']);
    Route::delete('api/users/{id}', [UsersController::class,'softDeleteUser']);
    Route::post('api/users/check-email', [UsersController::class,'checkEmail']);
    Route::post('api/users/check-email-id', [UsersController::class,'checkEmailWithId']);
});

Route::middleware(RedirectIfNotLoggedIn::class)->group(function () {
    Route::get('api/customers', [CustomerController::class,'getAllCustomer']);
    Route::post('api/customers', [CustomerController::class,'createCustomer']);
    Route::put('api/customers/{id}', [CustomerController::class,'updateCustomer']);
    Route::post('api/customers/check-email-id', [CustomerController::class,'checkEmailWithId']);
    Route::post('api/customers/import', [CustomerController::class, 'importCustomers']);
    Route::get('api/customers/export', [CustomerController::class, 'exportCustomers']);
});

Route::middleware(RedirectIfNotLoggedIn::class)->group(function () {
    Route::get('api/products', [ProductController::class,'getAllProduct']);
    Route::delete('api/products/{id}', [ProductController::class,'softDeleteProduct']);
    Route::get('api/products/{id}', [ProductController::class,'getProduct']);
    Route::post('api/products', [ProductController::class, 'store']);
    Route::put('api/products/{id}', [ProductController::class, 'update']);
});