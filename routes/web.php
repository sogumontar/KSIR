<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

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
Route::get('/invite/{merchantToken}', [\App\Http\Controllers\InvitationController::class, 'handleInvite'])->name('customer.register.invite');
Route::get('/register', \App\Livewire\Auth\CustomerRegistration::class)->name('customer.register');

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
    Route::get('/storefront', \App\Livewire\Merchant\StorefrontConfig::class)->name('storefront');
    Route::get('/orders', \App\Livewire\Merchant\OrderManagement::class)->name('orders');
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

Route::middleware(['auth', 'user'])->prefix('laundry')->name('laundry.')->group(function () {
    Route::get('/store-select', \App\Livewire\Laundry\StoreSelector::class)->name('store-select');
    Route::get('/dashboard', \App\Livewire\Laundry\Dashboard::class)->name('dashboard');
    Route::get('/services', \App\Livewire\Laundry\ServiceManager::class)->name('services');
    Route::get('/promos', \App\Livewire\Laundry\PromoManager::class)->name('promos');
    Route::get('/settings', \App\Livewire\Laundry\Settings::class)->name('settings');
    Route::get('/orders/create', \App\Livewire\Laundry\CreateOrder::class)->name('orders.create');
    Route::get('/orders/{id}', \App\Livewire\Laundry\OrderDetail::class)->name('orders.show');
    Route::get('/orders/{id}/edit', \App\Livewire\Laundry\EditOrder::class)->name('orders.edit');
    Route::get('/orders/{id}/receipt', [\App\Http\Controllers\LaundryReceiptController::class, 'download'])->name('orders.receipt');
    Route::get('/contributor/join/{token}', [\App\Http\Controllers\LaundryContributorController::class, 'join'])->name('contributor.join');
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

Route::middleware(['auth'])->group(function () {
    Route::get('/customer/dashboard', \App\Livewire\Customer\Dashboard::class)->name('customer.dashboard');
    Route::get('/customer/profile', \App\Livewire\Customer\Profile::class)->name('customer.profile');
    Route::get('/store/{merchantToken}', \App\Livewire\Customer\Storefront::class)->name('customer.storefront');
    Route::get('/cart', \App\Livewire\Customer\Cart::class)->name('customer.cart');
    Route::get('/checkout', \App\Livewire\Customer\Checkout::class)->name('customer.checkout');
});

Route::get('/laundry/track/{tracking_code}', \App\Livewire\Laundry\PublicTracking::class)->name('laundry.public.track');

/**
 * Storage File Server
 *
 * Serves files from storage/app/public directly via PHP.
 * This is required on shared hosting (cPanel) where the project root
 * (~/KSIR) is outside public_html, so `php artisan storage:link`
 * cannot create a working symlink reachable by the web server.
 *
 * Usage in Blade: {{ route('storage.file', ['path' => $order->photo_before]) }}
 * Or use the helper:  storage_url($order->photo_before)
 */
Route::get('/files/{path}', function (string $path) {
    // Decode any URL-encoded slashes
    $path = urldecode($path);

    if (!Storage::disk('public')->exists($path)) {
        abort(404);
    }

    $file     = Storage::disk('public')->path($path);
    $mimeType = Storage::disk('public')->mimeType($path);

    return response()->file($file, ['Content-Type' => $mimeType]);
})->where('path', '.*')->name('storage.file');
