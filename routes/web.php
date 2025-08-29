<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    $user = auth()->user();
    
    if ($user->isAdmin()) {
        return view('admin-dashboard');
    }
    
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

// Specific dashboard routes
Route::get('/admin-dashboard', function () {
    $user = auth()->user();
    
    if (!$user->isAdmin()) {
        return redirect()->route('dashboard')->with('error', 'Access denied. Admin only.');
    }
    
    return view('admin-dashboard');
})->middleware(['auth'])->name('admin.dashboard');

Route::get('/user-dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('user.dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
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
