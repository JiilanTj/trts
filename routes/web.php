<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\InvitationCodeController as AdminInvitationCodeController;
use App\Http\Controllers\Admin\SellerRequestController as AdminSellerRequestController;
use App\Http\Controllers\Admin\SellerInfoController as AdminSellerInfoController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\InvitationCodeController;
use App\Http\Controllers\SellerRequestController;
use App\Http\Controllers\SellerInfoController;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Http\Controllers\User\CategoryBrowseController; // added
use App\Http\Controllers\User\ProductBrowseController; // added
use App\Http\Controllers\Admin\SettingController; // new
// +++ added order controllers
use App\Http\Controllers\User\OrderController as UserOrderController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\User\KycRequestController as UserKycRequestController;
use App\Http\Controllers\Admin\KycRequestController as AdminKycRequestController;
use App\Http\Controllers\User\UserDetailController; // added
// +++ KYC snapshot controllers (user + admin)
use App\Http\Controllers\User\KycController as UserKycController; // new
use App\Http\Controllers\Admin\KycController as AdminKycController; // new

Route::get('/', function () {
    return redirect()->route('login');
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
    
    // Load seller info for regular users who are sellers
    $user->load('sellerInfo');
    
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

// Specific dashboard routes
Route::get('/admin-dashboard', function () {
    $user = auth()->user();
    
    if (!$user->isAdmin()) {
        return redirect()->route('dashboard')->with('error', 'Akses ditolak. Hanya untuk Admin.');
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
    $user = auth()->user();
    // Load seller info for users who are sellers
    $user->load('sellerInfo');
    
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

    // +++ User Order Routes (manual order system)
    Route::prefix('orders')->name('user.orders.')->group(function () {
        Route::get('/', [UserOrderController::class,'index'])->name('index');
        Route::get('/create', [UserOrderController::class,'create'])->name('create');
        Route::post('/', [UserOrderController::class,'store'])->name('store');
        Route::get('/{order}', [UserOrderController::class,'show'])->name('show');
        Route::post('/{order}/upload-proof', [UserOrderController::class,'uploadProof'])->name('upload-proof');
        Route::post('/{order}/cancel', [UserOrderController::class,'cancel'])->name('cancel');
    });
    
    // KYC User routes now support Blade + JSON (index, store, show)
    Route::prefix('kyc')->name('user.kyc.')->group(function(){
        Route::get('requests', [UserKycRequestController::class,'index'])->name('requests.index');
        Route::post('requests', [UserKycRequestController::class,'store'])->name('requests.store');
        Route::get('requests/{kycRequest}', [UserKycRequestController::class,'show'])->name('requests.show');
    });
    // User KYC snapshot (approved data) - supports Blade + JSON
    Route::get('kyc', [UserKycController::class,'show'])->name('user.kyc.show');
    
    // User Detail routes (JSON minimal)
    Route::prefix('user-detail')->name('user.detail.')->group(function(){
        Route::patch('/', [UserDetailController::class,'upsert']);
    });

    // Unified profile page & edit
    Route::get('profile-page', function(){
        $user = auth()->user()->load(['detail','kyc']);
        $detail = $user->detail;
        $kyc = $user->kyc; // approved snapshot
        $status = 'unverified';
        if($kyc){ $status = 'verified'; }
        else {
            $latest = $user->kycRequests()->latest()->first();
            if($latest){ $status = $latest->status_kyc; }
        }
        $statusMap = [
            'verified' => ['label'=>'Verified','color'=>'emerald'],
            'approved' => ['label'=>'Approved','color'=>'emerald'],
            'pending' => ['label'=>'Pending','color'=>'amber'],
            'review' => ['label'=>'Review','color'=>'amber'],
            'rejected' => ['label'=>'Rejected','color'=>'rose'],
            'unverified' => ['label'=>'Belum','color'=>'slate'],
        ];
        $k = $statusMap[$status] ?? $statusMap['unverified'];
        $initials = collect(explode(' ', trim($user->full_name ?: $user->username)))->filter()->take(2)->map(fn($p)=> strtoupper(mb_substr($p,0,1)))->implode('');
        return view('user.profile.index', compact('user','detail','kyc','k','initials'));
    })->name('user.profile.index');
    Route::get('profile/edit', [UserDetailController::class,'editCombined'])->name('user.profile.edit');
    Route::put('profile/edit', [UserDetailController::class,'updateCombined'])->name('user.profile.update');
    
    // Additional Menu
    Route::get('additional-menu', [App\Http\Controllers\User\AdditionalMenuController::class,'index'])->name('user.additional-menu.index');
    
    // Chat / Customer Service
    Route::get('chat', [App\Http\Controllers\User\ChatController::class,'index'])->name('user.chat.index');
    
    // History / Notifications
    Route::get('history', [App\Http\Controllers\User\HistoryController::class,'index'])->name('user.history.index');
});

// Admin Routes - Only for admin users
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('categories', CategoryController::class);
    Route::resource('products', ProductController::class);
    Route::resource('users', UserController::class);
    Route::get('settings', [SettingController::class,'index'])->name('settings.index');
    Route::post('settings', [SettingController::class,'update'])->name('settings.update');
    
    // Admin Invitation Codes Management
    Route::resource('invitation-codes', AdminInvitationCodeController::class)->except(['edit', 'update']);
    Route::post('invitation-codes/{invitationCode}/status', [AdminInvitationCodeController::class, 'updateStatus'])->name('invitation-codes.update-status');
    Route::post('invitation-codes/validate', [AdminInvitationCodeController::class, 'validate'])->name('invitation-codes.validate');
    
    // Admin Seller Requests Management
    Route::get('seller-requests', [AdminSellerRequestController::class, 'index'])->name('seller-requests.index');
    Route::get('seller-requests/{sellerRequest}', [AdminSellerRequestController::class, 'show'])->name('seller-requests.show');
    Route::post('seller-requests/{sellerRequest}/approve', [AdminSellerRequestController::class, 'approve'])->name('seller-requests.approve');
    Route::post('seller-requests/{sellerRequest}/reject', [AdminSellerRequestController::class, 'reject'])->name('seller-requests.reject');
    Route::get('seller-requests-count', [AdminSellerRequestController::class, 'getPendingCount'])->name('seller-requests.count');
    
    // Admin Seller Info Management
    Route::get('sellers', [AdminSellerInfoController::class, 'index'])->name('sellers.index');
    Route::get('sellers/{sellerInfo}', [AdminSellerInfoController::class, 'show'])->name('sellers.show');
    Route::post('sellers/{sellerInfo}/status', [AdminSellerInfoController::class, 'updateStatus'])->name('sellers.update-status');
    Route::post('sellers/{sellerInfo}/credit-score', [AdminSellerInfoController::class, 'updateCreditScore'])->name('sellers.update-credit-score');
    Route::get('sellers/{sellerInfo}/stats', [AdminSellerInfoController::class, 'getStats'])->name('sellers.stats');

    // +++ Admin Order Routes
    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/', [AdminOrderController::class,'index'])->name('index');
        Route::get('/{order}', [AdminOrderController::class,'show'])->name('show');
        Route::post('/{order}/approve-payment', [AdminOrderController::class,'approvePayment'])->name('approve-payment');
        Route::post('/{order}/reject-payment', [AdminOrderController::class,'rejectPayment'])->name('reject-payment');
        Route::post('/{order}/advance-status', [AdminOrderController::class,'advanceStatus'])->name('advance-status');
        Route::post('/{order}/cancel', [AdminOrderController::class,'cancel'])->name('cancel');
    });
    
    // Admin KYC management routes (JSON minimal)
    Route::prefix('kyc')->name('kyc.')->group(function(){
        Route::get('requests', [AdminKycRequestController::class,'index'])->name('requests.index');
        Route::get('requests/{kycRequest}', [AdminKycRequestController::class,'show'])->name('requests.show');
        Route::post('requests/{kycRequest}/start-review', [AdminKycRequestController::class,'startReview'])->name('requests.start-review');
        Route::post('requests/{kycRequest}/approve', [AdminKycRequestController::class,'approve'])->name('requests.approve');
        Route::post('requests/{kycRequest}/reject', [AdminKycRequestController::class,'reject'])->name('requests.reject');
    });
    // Admin KYC snapshots (approved records)
    Route::prefix('kyc/snapshots')->name('kyc.snapshots.')->group(function(){
        Route::get('/', [AdminKycController::class,'index'])->name('index');
        Route::get('/{kyc}', [AdminKycController::class,'show'])->name('show');
    });
});

// Seller System Routes
Route::middleware(['auth'])->group(function () {
    // Invitation Codes
    Route::resource('invitation-codes', InvitationCodeController::class)->except(['edit', 'update']);
    Route::post('invitation-codes/{invitationCode}/status', [InvitationCodeController::class, 'updateStatus'])->name('invitation-codes.update-status');
    Route::post('invitation-codes/validate', [InvitationCodeController::class, 'validate'])->name('invitation-codes.validate');
    
    // Seller Requests
    Route::resource('seller-requests', SellerRequestController::class)->only(['index', 'create', 'store', 'show']);
    
    // Seller Info & Dashboard
    Route::get('sellers', [SellerInfoController::class, 'index'])->name('sellers.index');
    Route::get('sellers/{sellerInfo}', [SellerInfoController::class, 'show'])->name('sellers.show');
    Route::post('sellers/{sellerInfo}/follow', [SellerInfoController::class, 'toggleFollow'])->name('sellers.follow');
    
    // Seller Dashboard (for seller owners)
    Route::middleware(['seller'])->group(function () {
        Route::get('seller/dashboard', [SellerInfoController::class, 'dashboard'])->name('sellers.dashboard');
        Route::get('seller/edit', [SellerInfoController::class, 'edit'])->name('sellers.edit');
        Route::put('seller/update', [SellerInfoController::class, 'update'])->name('sellers.update');
        Route::get('seller/stats', [SellerInfoController::class, 'getStats'])->name('sellers.stats');
    });
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
