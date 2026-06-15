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
use App\Livewire\Expense\ExpenseManager;

Route::get('/', Login::class)->name('login');

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', AdminDashboard::class)->name('dashboard');
    Route::get('/users', UserManagement::class)->name('users');
    Route::post('/users/{id}/bypass-split-limit', [\App\Http\Controllers\Admin\BypassController::class, 'toggle'])->name('bypass.toggle');
});

Route::middleware(['auth', 'user'])->prefix('user')->name('user.')->group(function () {
    Route::get('/dashboard', UserDashboard::class)->name('dashboard');
    Route::get('/goods', Goods::class)->name('goods');
    Route::get('/sales', SalesHistory::class)->name('sales');
    Route::get('/inventory', Inventory::class)->name('inventory');
    Route::get('/profile', Profile::class)->name('profile');
    Route::get('/expenses', ExpenseManager::class)->name('expenses');
    Route::get('/groups', \App\Livewire\User\Groups\GroupList::class)->name('groups');
    Route::get('/groups/join/{token}', [\App\Http\Controllers\GroupInviteController::class, 'join'])->name('groups.join');
    Route::get('/groups/{id}', \App\Livewire\User\Groups\GroupDetail::class)->name('group-detail');
    Route::get('/groups/{id}/podium', \App\Livewire\User\Groups\DebtPodium::class)->name('group-podium');
    Route::get('/api/groups/{id}/debts', [\App\Http\Controllers\Api\DebtController::class, 'index'])->name('api.groups.debts');
    Route::post('/notifications/read-all', function () {
        auth()->user()?->unreadNotifications->markAsRead();
        return back();
    })->name('notifications.read-all');
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
