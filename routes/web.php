<?php

use App\Http\Controllers\CameraController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\FotograferController;
use App\Http\Controllers\MarketplaceController;
use App\Http\Controllers\PembeliController;
use App\Http\Controllers\PhotoController;
use App\Http\Controllers\PhotographerController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PurchaseDownloadController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\SuperAdminPhotographerController;
use App\Models\Camera;
use App\Models\Photo;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::get('/', function () {
    if (auth()->check() && auth()->user()->role === 'pembeli') {
        return redirect()->route('galeri');
    }

    $galleryPhotos = Photo::query()
        ->where('status', 'active')
        ->whereHas('fotografer', fn ($query) => $query->where('is_verified', true)->where('is_active', true))
        ->with(['event', 'fotografer'])
        ->orderByDesc('published_at')
        ->orderByDesc('id')
        ->limit(4)
        ->get();

    return view('welcome', compact('galleryPhotos'));
})->name('landing');

Route::view('/terms-of-service', 'legal.terms')->name('terms');
Route::view('/privacy-policy', 'legal.privacy')->name('privacy');

Route::get('/galeri', [MarketplaceController::class, 'galeri'])->name('galeri');

Route::get('/p/{photo}', [MarketplaceController::class, 'show'])->name('marketplace.show');
Route::get('/media/preview/{photo}', function (Photo $photo) {
    $isPublic = $photo->status === 'active' && $photo->fotografer()->where('is_verified', true)->where('is_active', true)->exists();
    abort_unless($isPublic || auth()->id() === $photo->fotografer_id || auth()->user()?->role === 'superadmin', 404);
    abort_unless(Storage::disk('local')->exists($photo->file_watermark), 404);

    return Storage::disk('local')->response($photo->file_watermark);
})->name('media.preview');
Route::get('/media/profile/{user}', function (User $user) {
    abort_unless($user->profile_photo_path && Storage::disk('public')->exists($user->profile_photo_path), 404);
    abort_unless(($user->is_verified && $user->is_active) || auth()->id() === $user->id || auth()->user()?->role === 'superadmin', 404);

    return Storage::disk('public')->response($user->profile_photo_path);
})->name('media.profile');
Route::get('/media/camera/{camera}', function (Camera $camera) {
    abort_unless($camera->photo_path && Storage::disk('public')->exists($camera->photo_path), 404);
    abort_unless(($camera->fotografer?->is_verified && $camera->fotografer?->is_active) || auth()->id() === $camera->fotografer_id || auth()->user()?->role === 'superadmin', 404);

    return Storage::disk('public')->response($camera->photo_path);
})->name('media.camera');
Route::middleware('auth')->group(function () {
    Route::get('/p/{photo}/checkout', [MarketplaceController::class, 'checkout'])->name('marketplace.checkout');
});

Route::get('/photographers', [PhotographerController::class, 'index'])->name('photographers.index');
Route::get('/photographers/{photographer}', [PhotographerController::class, 'show'])->name('photographers.show');

Route::get('/sitemap.xml', function () {
    $photos = Photo::query()->where('status', 'active')->whereHas('fotografer', fn ($query) => $query->where('is_verified', true)->where('is_active', true))->select(['id', 'updated_at'])->get();
    $photographers = User::query()->where('role', 'fotografer')->where('is_verified', true)->where('is_active', true)->select(['id', 'slug', 'updated_at'])->get();

    return response()
        ->view('sitemap', compact('photos', 'photographers'))
        ->header('Content-Type', 'application/xml');
})->name('sitemap');

Route::get('/dashboard', function () {
    $role = request()->user()->role;
    if ($role === 'superadmin') {
        return redirect()->route('superadmin.dashboard');
    } elseif ($role === 'fotografer') {
        return redirect()->route('fotografer.dashboard');
    }

    return redirect()->route('galeri');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'role:superadmin'])->prefix('superadmin')->name('superadmin.')->group(function () {
    Route::get('/dashboard', [SuperAdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/dashboard/report', [SuperAdminController::class, 'executiveReport'])->name('report');
    Route::post('/withdrawals/{id}/review', [SuperAdminController::class, 'reviewWithdrawal'])->name('withdrawals.review');
    Route::post('/withdrawals/batch', [SuperAdminController::class, 'batchWithdrawals'])->name('withdrawals.batch');
    Route::post('/fotografer/{user}/review', [SuperAdminController::class, 'reviewPhotographer'])->name('fotografer.review');
    Route::patch('/photographers/{photographer}/status', [SuperAdminPhotographerController::class, 'status'])->name('photographers.status');
    Route::resource('photographers', SuperAdminPhotographerController::class);

    Route::get('/compliance', [SuperAdminController::class, 'compliance'])->name('compliance');
    Route::get('/orders', [SuperAdminController::class, 'ledger'])->name('orders');
    Route::get('/orders/export', [SuperAdminController::class, 'exportLedger'])->name('orders.export');
    Route::get('/earnings', [SuperAdminController::class, 'withdrawal'])->name('earnings');
    Route::get('/storage', [SuperAdminController::class, 'storage'])->name('storage');
    Route::patch('/storage/{user}', [SuperAdminController::class, 'updateQuota'])->name('storage.update');
    Route::get('/settings', [SuperAdminController::class, 'settings'])->name('settings');
    Route::patch('/settings', [SuperAdminController::class, 'updateSettings'])->name('settings.update');
});

Route::middleware(['auth', 'role:fotografer'])->prefix('fotografer')->name('fotografer.')->group(function () {
    Route::get('/dashboard', [FotograferController::class, 'dashboard'])->name('dashboard');
    Route::get('/dashboard/data', [FotograferController::class, 'dashboardData'])->name('dashboard.data');
    Route::post('/radar/toggle', [FotograferController::class, 'toggleRadar'])->name('radar.toggle');
    Route::resource('cameras', CameraController::class)->except(['show']);
    Route::resource('events', EventController::class)->only(['index', 'create', 'store', 'show', 'destroy']);
    Route::resource('photos', PhotoController::class)->only(['index', 'create', 'store', 'update', 'destroy']);
    Route::patch('/watermark', [PhotoController::class, 'watermark'])->name('watermark.update');
    Route::post('/withdrawals', [FotograferController::class, 'withdraw'])->name('withdrawals.store');

    // Stubbed routes for missing menus
    Route::get('/orders', [FotograferController::class, 'orders'])->name('orders');
    Route::get('/orders/export', [FotograferController::class, 'exportOrders'])->name('orders.export');
    Route::get('/storage', [FotograferController::class, 'storage'])->name('storage');
    Route::get('/portfolio', [FotograferController::class, 'portfolio'])->name('portfolio');
    Route::patch('/portfolio', [FotograferController::class, 'updatePortfolio'])->name('portfolio.update');
});

Route::middleware('auth')->group(function () {
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add', [CartController::class, 'store'])->name('cart.store')->block();
    Route::post('/cart/remove', [CartController::class, 'destroy'])->name('cart.destroy')->block();
    Route::get('/checkout', [PembeliController::class, 'checkoutPage'])->name('checkout.page');
    Route::post('/checkout', [PembeliController::class, 'processCheckout'])->name('checkout.process')->block();
    Route::get('/checkout/payment/{order}', [PembeliController::class, 'paymentPage'])->name('checkout.payment');
    Route::post('/checkout/payment/{order}/pay', [PembeliController::class, 'simulatePay'])->name('checkout.payment.simulate');
    Route::get('/checkout/success/{order}', [PembeliController::class, 'checkoutSuccess'])->name('checkout.success');
    Route::get('/purchases', [PembeliController::class, 'purchases'])->name('purchases.index');
    Route::get('/purchases/{order}/download/{transaction}', PurchaseDownloadController::class)->name('purchases.download');
    Route::get('/purchases/{order}/preview/{transaction}', [PurchaseDownloadController::class, 'preview'])->name('purchases.preview');
    Route::get('/purchases/{order}', [PembeliController::class, 'purchaseShow'])->name('purchases.show');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
require __DIR__.'/auth.php';

Route::post('/logout-register', function (Request $request) {
    Auth::guard('web')->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('register', ['role' => 'fotografer']);
})->name('logout.register');
