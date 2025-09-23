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
use App\Http\Controllers\User\HistoryController; // new
// Add topup controllers
use App\Http\Controllers\User\TopupController as UserTopupController;
use App\Http\Controllers\Admin\TopupController as AdminTopupController;
// Add withdrawal controllers
use App\Http\Controllers\Admin\WithdrawalController as AdminWithdrawalController;
// Add analytics controller
use App\Http\Controllers\User\AnalyticsController;
// Add loan request controller
use App\Http\Controllers\User\LoanRequestController;
use App\Http\Controllers\Admin\LoanRequestController as AdminLoanRequestController;
// Add API controllers
use App\Http\Controllers\Api\NotificationController as ApiNotificationController;
// Add Guest chat controller
use App\Http\Controllers\Guest\ChatController as GuestChatController;
// Add ticket controllers
use App\Http\Controllers\User\TicketController as UserTicketController;
use App\Http\Controllers\Admin\TicketController as AdminTicketController;
// Add store showcase controllers
use App\Http\Controllers\User\StoreShowcaseController as UserStoreShowcaseController;
use App\Http\Controllers\Admin\StoreShowcaseController as AdminStoreShowcaseController;
// +++ Scheduled Order Batch controller
use App\Http\Controllers\Admin\ScheduledOrderBatchController; // new
use App\Http\Controllers\Admin\OrderByAdminController as AdminOrderByAdminController; // new
use App\Http\Controllers\User\OrderByAdminController as UserOrderByAdminController; // NEW user-facing controller
use App\Http\Controllers\Admin\ScheduledOrderByAdminController; // import missing controller for scheduled order-by-admin routes

Route::get('/', function () {
    return redirect()->route('login');
});

// Public shared etalase
Route::get('/etalase/shared/{token}', [UserStoreShowcaseController::class, 'showShared'])
    ->name('etalase.shared');

// Buy product from shared etalase
Route::post('/etalase/buy/{product}', [UserStoreShowcaseController::class, 'buyFromEtalase'])
    ->name('etalase.buy-product');

// Follow/Unfollow seller (public routes)
Route::post('/seller/follow', [UserStoreShowcaseController::class, 'followSeller'])
    ->name('seller.follow');
Route::post('/seller/unfollow', [UserStoreShowcaseController::class, 'unfollowSeller'])
    ->name('seller.unfollow');

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
    
    // Password management
    Route::get('password/edit', function() {
        return view('user.password.edit');
    })->name('user.password.edit');
    
    // Withdrawal Requests
    Route::prefix('withdrawals')->name('user.withdrawals.')->group(function () {
        Route::get('/', [App\Http\Controllers\User\WithdrawalController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\User\WithdrawalController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\User\WithdrawalController::class, 'store'])->name('store');
        Route::get('/{withdrawal}', [App\Http\Controllers\User\WithdrawalController::class, 'show'])->name('show');
        Route::delete('/{withdrawal}', [App\Http\Controllers\User\WithdrawalController::class, 'destroy'])->name('cancel');
        Route::post('/preview', [App\Http\Controllers\User\WithdrawalController::class, 'preview'])->name('preview');
    });
    
    // Additional Menu
    Route::get('additional-menu', [App\Http\Controllers\User\AdditionalMenuController::class,'index'])->name('user.additional-menu.index');
    
    // Analytics / Business Evaluation
    Route::prefix('analytics')->name('user.analytics.')->group(function () {
        Route::get('/', [App\Http\Controllers\User\AnalyticsController::class, 'index'])->name('index');
        Route::get('/stats', [App\Http\Controllers\User\AnalyticsController::class, 'getStats'])->name('stats');
        Route::get('/chart-data', [App\Http\Controllers\User\AnalyticsController::class, 'apiGetChartData'])->name('chart-data');
    });
    
    // Loan Requests / Financial Services
    Route::prefix('loan-requests')->name('user.loan-requests.')->group(function () {
        Route::get('/', [LoanRequestController::class, 'index'])->name('index');
        Route::get('/create', [LoanRequestController::class, 'create'])->name('create');
        Route::post('/', [LoanRequestController::class, 'store'])->name('store');
        Route::get('/{loanRequest}', [LoanRequestController::class, 'show'])->name('show');
        Route::get('/{loanRequest}/edit', [LoanRequestController::class, 'edit'])->name('edit');
        Route::put('/{loanRequest}', [LoanRequestController::class, 'update'])->name('update');
        Route::delete('/{loanRequest}', [LoanRequestController::class, 'destroy'])->name('destroy');
    });
    
    // Tickets / Support System
    Route::prefix('tickets')->name('user.tickets.')->group(function () {
        Route::get('/', [UserTicketController::class, 'index'])->name('index');
        Route::get('/create', [UserTicketController::class, 'create'])->name('create');
        Route::post('/', [UserTicketController::class, 'store'])->name('store');
        Route::get('/{ticket}', [UserTicketController::class, 'show'])->name('show');
        Route::post('/{ticket}/comment', [UserTicketController::class, 'addComment'])->name('comment');
        Route::get('/{ticket}/download/{type}/{index}', [UserTicketController::class, 'downloadAttachment'])->name('download');
    });
    
    // Store Showcase / Etalase Management
    Route::prefix('etalase')->name('user.showcases.')->group(function () {
        Route::get('/', [UserStoreShowcaseController::class, 'index'])->name('index');
        Route::get('/create', [UserStoreShowcaseController::class, 'create'])->name('create');
        Route::post('/', [UserStoreShowcaseController::class, 'store'])->name('store');
        Route::get('/{showcase}', [UserStoreShowcaseController::class, 'show'])->name('show');
        Route::get('/{showcase}/edit', [UserStoreShowcaseController::class, 'edit'])->name('edit');
        Route::put('/{showcase}', [UserStoreShowcaseController::class, 'update'])->name('update');
        Route::delete('/{showcase}', [UserStoreShowcaseController::class, 'destroy'])->name('destroy');
        Route::post('/{showcase}/toggle-active', [UserStoreShowcaseController::class, 'toggleActive'])->name('toggle-active');
        Route::post('/{showcase}/toggle-featured', [UserStoreShowcaseController::class, 'toggleFeatured'])->name('toggle-featured');
        Route::post('/update-order', [UserStoreShowcaseController::class, 'updateOrder'])->name('update-order');
        Route::post('/generate-etalase-share-token', [UserStoreShowcaseController::class, 'generateEtalaseShareToken'])->name('generate-etalase-share-token');
        Route::post('/debug-ajax', [UserStoreShowcaseController::class, 'debugAjax'])->name('debug-ajax');
    });
    
    // Chat / Customer Service
    Route::prefix('chat')->name('user.chat.')->group(function () {
        Route::get('/', [App\Http\Controllers\User\ChatController::class, 'index'])->name('index');
        Route::get('/{chatRoom}', [App\Http\Controllers\User\ChatController::class, 'show'])->name('show');
        Route::post('/create', [App\Http\Controllers\User\ChatController::class, 'store'])->name('create');
        Route::post('/{chatRoom}/send', [App\Http\Controllers\User\ChatController::class, 'sendMessage'])->name('send');
        Route::post('/typing', [App\Http\Controllers\User\ChatController::class, 'typing'])->name('typing');
        Route::post('/mark-read', [App\Http\Controllers\User\ChatController::class, 'markAsRead'])->name('mark-read');
        
        // Polling API for production
        Route::get('/{chatRoom}/poll', [App\Http\Controllers\User\ChatController::class, 'poll'])->name('poll');
    });
    
    // History / Notifications
    Route::get('history', [HistoryController::class,'index'])->name('user.history.index');
    Route::patch('notifications/{notification}/read', [HistoryController::class,'markAsRead'])->name('user.notifications.read');
    Route::patch('notifications/read-all', [HistoryController::class,'markAllAsRead'])->name('user.notifications.read-all');
    
    // User Topup Routes
    Route::prefix('topup')->name('user.topup.')->group(function () {
        Route::get('/', [UserTopupController::class, 'index'])->name('index');
        Route::get('/create', [UserTopupController::class, 'create'])->name('create');
        Route::post('/', [UserTopupController::class, 'store'])->name('store');
        Route::get('/{topupRequest}', [UserTopupController::class, 'show'])->name('show');
    });
    
    // User Wholesale Routes
    Route::prefix('wholesale')->name('user.wholesale.')->group(function () {
        Route::get('/', [App\Http\Controllers\User\WholesaleController::class, 'index'])->name('index');
        Route::post('/create-order', [App\Http\Controllers\User\WholesaleController::class, 'createOrder'])->name('create-order');
    });

    // User-facing Orders By Admin routes
    Route::prefix('order-task-list')->name('user.orders-by-admin.')->group(function () {
        Route::get('/', [UserOrderByAdminController::class, 'index'])->name('index');
        Route::get('/{orders_by_admin}', [UserOrderByAdminController::class, 'show'])->name('show');
        Route::patch('/{orders_by_admin}/confirm', [UserOrderByAdminController::class, 'confirm'])->name('confirm');
    });
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

    // Lightweight API for seller showcases (for Order By Admin forms)
    Route::get('api/showcases', [\App\Http\Controllers\Admin\OrderByAdminController::class, 'apiShowcases'])->name('api.showcases');

    // +++ Admin Order Routes
    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/', [AdminOrderController::class,'index'])->name('index');
        // New: create & store admin order
        Route::get('/create', [AdminOrderController::class,'create'])->name('create');
        Route::post('/', [AdminOrderController::class,'store'])->name('store');
        Route::get('/{order}', [AdminOrderController::class,'show'])->name('show');
        Route::post('/{order}/approve-payment', [AdminOrderController::class,'approvePayment'])->name('approve-payment');
        Route::post('/{order}/reject-payment', [AdminOrderController::class,'rejectPayment'])->name('reject-payment');
        Route::post('/{order}/advance-status', [AdminOrderController::class,'advanceStatus'])->name('advance-status');
        Route::post('/{order}/update-status', [AdminOrderController::class,'updateStatus'])->name('update-status');
        Route::post('/{order}/cancel', [AdminOrderController::class,'cancel'])->name('cancel');
        Route::post('/{order}/refund', [AdminOrderController::class,'refund'])->name('refund'); // added refund route
    });
    
    // Admin Orders Created By Admin
    Route::resource('orders-by-admin', AdminOrderByAdminController::class)
        ->parameters(['orders-by-admin' => 'orders_by_admin']);
    Route::post('orders-by-admin/{orders_by_admin}/confirm', [AdminOrderByAdminController::class, 'confirm'])
        ->name('orders-by-admin.confirm');

    // Scheduled Order-By-Admin
    Route::prefix('orders-by-admin/scheduled')->name('orders-by-admin.scheduled.')->group(function(){
        Route::get('/', [ScheduledOrderByAdminController::class, 'index'])->name('index');
        Route::post('/', [ScheduledOrderByAdminController::class, 'store'])->name('store');
        Route::get('/{scheduled}', [ScheduledOrderByAdminController::class, 'show'])->name('show');
        Route::post('/{scheduled}/cancel', [ScheduledOrderByAdminController::class, 'cancel'])->name('cancel');
        Route::post('/{scheduled}/run-now', [ScheduledOrderByAdminController::class, 'runNow'])->name('run-now');
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
    
    // Admin Topup Routes
    Route::prefix('topup')->name('topup.')->group(function () {
        Route::get('/', [AdminTopupController::class, 'index'])->name('index');
        Route::get('/{topupRequest}', [AdminTopupController::class, 'show'])->name('show');
        Route::post('/{topupRequest}/approve', [AdminTopupController::class, 'approve'])->name('approve');
        Route::post('/{topupRequest}/reject', [AdminTopupController::class, 'reject'])->name('reject');
        Route::post('/bulk', [AdminTopupController::class, 'bulk'])->name('bulk');
    });
    
    // Admin Withdrawal Routes
    Route::prefix('withdrawals')->name('withdrawals.')->group(function () {
        Route::get('/', [AdminWithdrawalController::class, 'index'])->name('index');
        Route::get('/{withdrawal}', [AdminWithdrawalController::class, 'show'])->name('show');
        Route::post('/{withdrawal}/process', [AdminWithdrawalController::class, 'process'])->name('process');
        Route::post('/{withdrawal}/complete', [AdminWithdrawalController::class, 'complete'])->name('complete');
        Route::post('/{withdrawal}/reject', [AdminWithdrawalController::class, 'reject'])->name('reject');
        Route::post('/bulk', [AdminWithdrawalController::class, 'bulkAction'])->name('bulk');
        Route::get('/stats/overview', [AdminWithdrawalController::class, 'stats'])->name('stats');
    });
    
    // API Routes for real-time counts & admin helpers
    Route::prefix('api')->name('api.')->group(function () {
        Route::get('/withdrawals/count', [AdminWithdrawalController::class, 'getPendingCount']);

        // --- Lightweight admin API used by scheduled order UI ---
        Route::get('/buyers', function(\Illuminate\Http\Request $request){
            $q = trim((string) $request->query('q', ''));
            $users = \App\Models\User::query()
                ->where('role','user')
                ->when($q !== '', function($qq) use ($q){
                    $qq->where(function($w) use ($q){
                        $w->where('full_name','like',"%{$q}%")
                          ->orWhere('username','like',"%{$q}%");
                        if (ctype_digit($q)) { $w->orWhere('id', (int)$q); }
                    });
                })
                ->orderByDesc('id')
                ->limit(20)
                ->get(['id','full_name','username']);
            return response()->json($users);
        })->name('buyers');

        Route::get('/sellers', function(\Illuminate\Http\Request $request){
            $q = trim((string) $request->query('q', ''));
            $users = \App\Models\User::query()
                ->where('role','user')
                ->where('is_seller', true)
                ->with(['sellerInfo:id,user_id,store_name'])
                ->when($q !== '', function($qq) use ($q){
                    $qq->where(function($w) use ($q){
                        $w->where('full_name','like',"%{$q}%")
                          ->orWhere('username','like',"%{$q}%")
                          ->orWhereHas('sellerInfo', function($h) use ($q){ $h->where('store_name','like',"%{$q}%"); });
                        if (ctype_digit($q)) { $w->orWhere('id', (int)$q); }
                    });
                })
                ->orderByDesc('id')
                ->limit(20)
                ->get(['id','full_name','username']);
            return response()->json($users);
        })->name('sellers');

        Route::get('/products', function(\Illuminate\Http\Request $request){
            $sellerId = (int) $request->query('seller_id', 0);
            if (!$sellerId) { return response()->json([]); }
            $q = trim((string) $request->query('q', ''));

            // Ambil produk dari Etalase (StoreShowcase) seller tsb
            $productIds = \App\Models\StoreShowcase::query()
                ->where('user_id', $sellerId)
                ->active()
                ->pluck('product_id');

            if ($productIds->isEmpty()) { return response()->json([]); }

            $products = \App\Models\Product::query()
                ->whereIn('id', $productIds)
                ->where('status', 'active')
                ->when($q !== '', function($qq) use ($q){
                    $qq->where(function($w) use ($q){
                        $w->where('name','like',"%{$q}%");
                        if (ctype_digit($q)) { $w->orWhere('id', (int)$q); }
                    });
                })
                ->orderBy('name')
                ->limit(50)
                ->get(['id','name','stock','sell_price','promo_price']);

            return response()->json($products);
        })->name('products');

        // New: list active showcases for a seller (with product names)
        Route::get('/showcases', function(\Illuminate\Http\Request $request){
            $sellerId = (int) $request->query('seller_id', 0);
            if (!$sellerId) { return response()->json([]); }
            $q = trim((string) $request->query('q', ''));

            $showcases = \App\Models\StoreShowcase::query()
                ->where('user_id', $sellerId)
                ->active()
                ->with(['product:id,name,sell_price,promo_price,image'])
                ->when($q !== '', function($qq) use ($q){
                    $qq->whereHas('product', function($w) use ($q){
                        $w->where('name','like',"%{$q}%");
                        if (ctype_digit($q)) { $w->orWhere('id', (int)$q); }
                    });
                })
                ->orderByDesc('id')
                ->limit(100)
                ->get(['id','user_id','product_id']);

            $payload = $showcases->map(function($s){
                return [
                    'id' => $s->id,
                    'product_id' => $s->product_id,
                    'product_name' => optional($s->product)->name,
                    'sell_price' => optional($s->product)->sell_price,
                    'promo_price' => optional($s->product)->promo_price,
                    'image_url' => optional($s->product)->image_url,
                ];
            })->values();

            return response()->json($payload);
        })->name('showcases');
    });
    
    // Admin Loan Request Routes
    Route::prefix('loan-requests')->name('loan-requests.')->group(function () {
        Route::get('/', [AdminLoanRequestController::class, 'index'])->name('index');
        Route::get('/{loanRequest}', [AdminLoanRequestController::class, 'show'])->name('show');
        Route::post('/{loanRequest}/update-status', [AdminLoanRequestController::class, 'updateStatus'])->name('update-status');
        Route::post('/bulk-update', [AdminLoanRequestController::class, 'bulkUpdate'])->name('bulk-update');
        Route::get('/{loanRequest}/document/{documentIndex}', [AdminLoanRequestController::class, 'downloadDocument'])->name('download-document');
        Route::get('/analytics/view', [AdminLoanRequestController::class, 'analytics'])->name('analytics');
    });
    
    // Admin Ticket Management Routes
    Route::prefix('tickets')->name('tickets.')->group(function () {
        Route::get('/', [AdminTicketController::class, 'index'])->name('index');
        Route::get('/{ticket}', [AdminTicketController::class, 'show'])->name('show');
        Route::post('/{ticket}/assign', [AdminTicketController::class, 'assign'])->name('assign');
        Route::post('/{ticket}/status', [AdminTicketController::class, 'updateStatus'])->name('update-status');
        Route::post('/{ticket}/comment', [AdminTicketController::class, 'addComment'])->name('comment');
        Route::post('/bulk-update', [AdminTicketController::class, 'bulkUpdate'])->name('bulk-update');
        Route::get('/{ticket}/download/{type}/{index}', [AdminTicketController::class, 'downloadAttachment'])->name('download');
    });
    
    // Admin Store Showcase Management Routes  
    Route::prefix('etalase')->name('showcases.')->group(function () {
        Route::get('/', [AdminStoreShowcaseController::class, 'index'])->name('index');
        Route::get('/user/{user}', [AdminStoreShowcaseController::class, 'show'])->name('show');
        Route::get('/user-showcase/{user}', [AdminStoreShowcaseController::class, 'userShowcase'])->name('user-showcase');
        Route::post('/{showcase}/toggle-active', [AdminStoreShowcaseController::class, 'toggleActive'])->name('toggle-active');
        Route::post('/{showcase}/toggle-featured', [AdminStoreShowcaseController::class, 'toggleFeatured'])->name('toggle-featured');
        Route::delete('/{showcase}', [AdminStoreShowcaseController::class, 'destroy'])->name('destroy');
        Route::post('/bulk-action', [AdminStoreShowcaseController::class, 'bulkAction'])->name('bulk-action');
        Route::get('/stats', [AdminStoreShowcaseController::class, 'stats'])->name('stats');
    });
    
    // Admin Chat Management Routes
    Route::prefix('chat')->name('chat.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\ChatController::class, 'index'])->name('index');
        Route::get('/{chatRoom}', [App\Http\Controllers\Admin\ChatController::class, 'show'])->name('show');
        Route::post('/{chatRoom}/assign', [App\Http\Controllers\Admin\ChatController::class, 'assign'])->name('assign');
        Route::post('/{chatRoom}/close', [App\Http\Controllers\Admin\ChatController::class, 'close'])->name('close');
        Route::post('/{chatRoom}/send-message', [App\Http\Controllers\Admin\ChatController::class, 'sendMessage'])->name('send-message');
        Route::get('/statistics/view', [App\Http\Controllers\Admin\ChatController::class, 'statistics'])->name('statistics');
    });
    
    // Admin Chat API Routes
    Route::prefix('api/chat')->name('api.chat.')->group(function () {
        Route::post('/typing', [App\Http\Controllers\Admin\ChatController::class, 'typing'])->name('typing');
        Route::get('/statistics', [App\Http\Controllers\Admin\ChatController::class, 'statisticsApi'])->name('statistics');
        
        // Polling endpoints for production
        Route::get('/dashboard-updates', [App\Http\Controllers\Admin\ChatController::class, 'dashboardUpdates'])->name('dashboard-updates');
        Route::get('/{chatRoom}/messages', [App\Http\Controllers\Admin\ChatController::class, 'getNewMessages'])->name('messages');
        Route::post('/{chatRoom}/send', [App\Http\Controllers\Admin\ChatController::class, 'sendMessageApi'])->name('send');
        Route::get('/{chatRoom}/typing', [App\Http\Controllers\Admin\ChatController::class, 'getTypingStatus'])->name('typing-status');
    });
    
    // Admin API Routes for sidebar counts
    Route::prefix('api')->name('api.')->group(function () {
        Route::get('/tickets/count', [AdminTicketController::class, 'getCount'])->name('tickets.count');
        Route::get('/chats/count', [App\Http\Controllers\Admin\ChatController::class, 'getCount'])->name('chats.count');
    });
    
    // +++ Admin Scheduled Orders (Batches)
    Route::prefix('scheduled-orders')->name('scheduled-orders.')->group(function () {
        // Blade UI
        Route::get('/ui', function(){ return view('admin.scheduled-orders.index'); })->name('ui.index');
        Route::get('/create', function(){ return view('admin.scheduled-orders.create'); })->name('create');
        Route::get('/{batch}/ui', function($batch){ return view('admin.scheduled-orders.show'); })->name('ui.show');

        // JSON API
        Route::get('/', [ScheduledOrderBatchController::class, 'index'])->name('index'); // JSON list for now
        Route::post('/', [ScheduledOrderBatchController::class, 'store'])->name('store');
        Route::get('/{batch}', [ScheduledOrderBatchController::class, 'show'])->name('show');
        Route::post('/{batch}/cancel', [ScheduledOrderBatchController::class, 'cancel'])->name('cancel');
        Route::post('/{batch}/run-now', [ScheduledOrderBatchController::class, 'runNow'])->name('run-now');
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

// API Routes for Real-time features
Route::prefix('api')->middleware(['auth'])->group(function () {
    Route::get('/notifications/unread-count', [ApiNotificationController::class, 'getUnreadCount'])
        ->name('api.notifications.unread-count');
    Route::post('/notifications/mark-all-read', [ApiNotificationController::class, 'markAllAsRead'])
        ->name('api.notifications.mark-all-read');
    // Chat API Routes
    Route::prefix('chat')->name('api.chat.')->group(function () {
        Route::post('/typing', [App\Http\Controllers\User\ChatController::class, 'typing'])->name('typing');
        Route::post('/read', [App\Http\Controllers\User\ChatController::class, 'markAsRead'])->name('read');
    });
});

// Guest Chat Routes (no authentication required)
Route::get('guest/chat', [GuestChatController::class, 'index'])->name('guest.chat');
Route::prefix('guest/chat')->name('guest.chat.')->group(function () {
    Route::post('/start', [GuestChatController::class, 'startChat'])->name('start');
    Route::post('/send-message', [GuestChatController::class, 'sendMessage'])->name('send-message');
    Route::get('/messages', [GuestChatController::class, 'getMessages'])->name('messages');
    Route::get('/status', [GuestChatController::class, 'getChatStatus'])->name('status');
    Route::post('/end', [GuestChatController::class, 'endChat'])->name('end');
    Route::get('/poll', [GuestChatController::class, 'pollMessages'])->name('poll');
});

require __DIR__.'/auth.php';
