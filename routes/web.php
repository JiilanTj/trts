<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Http\Controllers\User\CategoryBrowseController; // added
use App\Http\Controllers\User\ProductBrowseController; // added

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    $user = auth()->user();
    
    if ($user->isAdmin()) {
        $recentUsers = User::where('role','user')->latest()->take(8)->get();
        $totalUsers = User::where('role','user')->count();
        // Perhitungan pertumbuhan pengguna (hanya role user)
        $currentMonthUsers = User::where('role','user')
            ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->count();
        $lastMonthUsers = User::where('role','user')
            ->whereBetween('created_at', [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()])
            ->count();
        if ($lastMonthUsers === 0) {
            if ($currentMonthUsers === 0) {
                $userGrowthPercent = 0.0;
                $userGrowthText = '0%';
            } else {
                // Definisikan 100% jika bulan lalu 0 dan sekarang ada pengguna baru
                $userGrowthPercent = 100.0;
                $userGrowthText = '+100%';
            }
        } else {
            $userGrowthPercent = (($currentMonthUsers - $lastMonthUsers) / $lastMonthUsers) * 100;
            $userGrowthText = ($userGrowthPercent >= 0 ? '+' : '') . number_format($userGrowthPercent, 1, ',', '.') . '%';
        }
        return view('admin-dashboard', compact('recentUsers', 'totalUsers', 'userGrowthPercent', 'userGrowthText'));
    }
    
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

// Specific dashboard routes
Route::get('/admin-dashboard', function () {
    $user = auth()->user();
    
    if (!$user->isAdmin()) {
        return redirect()->route('dashboard')->with('error', 'Access denied. Admin only.');
    }
    $recentUsers = User::where('role','user')->latest()->take(8)->get();
    $totalUsers = User::where('role','user')->count();
    // Perhitungan pertumbuhan pengguna (hanya role user)
    $currentMonthUsers = User::where('role','user')
        ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
        ->count();
    $lastMonthUsers = User::where('role','user')
        ->whereBetween('created_at', [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()])
        ->count();
    if ($lastMonthUsers === 0) {
        if ($currentMonthUsers === 0) {
            $userGrowthPercent = 0.0;
            $userGrowthText = '0%';
        } else {
            $userGrowthPercent = 100.0;
            $userGrowthText = '+100%';
        }
    } else {
        $userGrowthPercent = (($currentMonthUsers - $lastMonthUsers) / $lastMonthUsers) * 100;
        $userGrowthText = ($userGrowthPercent >= 0 ? '+' : '') . number_format($userGrowthPercent, 1, ',', '.') . '%';
    }
    return view('admin-dashboard', compact('recentUsers', 'totalUsers', 'userGrowthPercent', 'userGrowthText'));
})->middleware(['auth'])->name('admin.dashboard');

Route::get('/user-dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('user.dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // User-facing browse routes (GET only)
    Route::get('kategori', [CategoryBrowseController::class,'index'])->name('browse.categories.index');
    Route::get('kategori/{category}', [CategoryBrowseController::class,'show'])->name('browse.categories.show');
    Route::get('produk', [ProductBrowseController::class,'index'])->name('browse.products.index');
    Route::get('produk/{product}', [ProductBrowseController::class,'show'])->name('browse.products.show');
    Route::post('produk/{product}/beli', [ProductBrowseController::class,'buy'])->name('browse.products.buy'); // new buy route
});

// Admin Routes - Only for admin users
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('categories', CategoryController::class);
    Route::resource('products', ProductController::class);
    Route::resource('users', UserController::class);
});

Route::get('/debug-auth', function () {
    if (auth()->check()) {
        $user = auth()->user();
        return response()->json([
            'authenticated' => true,
            'user_id' => $user->id,
            'username' => $user->username,
            'full_name' => $user->full_name,
            'role' => $user->role,
            'balance' => $user->balance,
            'level' => $user->level,
        ]);
    }
    return response()->json(['authenticated' => false]);
})->middleware(['auth'])->name('debug.auth');

Route::get('/test-blade', function () {
    return view('test-blade');
})->middleware(['auth'])->name('test.blade');

require __DIR__.'/auth.php';
