<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Auth\Login;
use App\Livewire\Admin\Dashboard as AdminDashboard;
use App\Livewire\Admin\UserManagement;
use App\Livewire\User\Dashboard as UserDashboard;
use App\Livewire\User\Goods;
use App\Livewire\User\SalesHistory;
use App\Livewire\User\Inventory;
use App\Livewire\User\Profile;

Route::get('/', Login::class)->name('login');

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', AdminDashboard::class)->name('dashboard');
    Route::get('/users', UserManagement::class)->name('users');
});

Route::middleware(['auth', 'user'])->prefix('user')->name('user.')->group(function () {
    Route::get('/dashboard', UserDashboard::class)->name('dashboard');
    Route::get('/goods', Goods::class)->name('goods');
    Route::get('/sales', SalesHistory::class)->name('sales');
    Route::get('/inventory', Inventory::class)->name('inventory');
    Route::get('/profile', Profile::class)->name('profile');
});

Route::get('/session-check', function () {
    return response()->json([
        'authenticated' => auth()->check(),
    ]);
})->name('session-check');

Route::post('/logout', function () {
    auth()->logout();
    session()->invalidate();
    session()->regenerateToken();
    return redirect()->route('login');
})->name('logout');
