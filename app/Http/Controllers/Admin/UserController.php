<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Display a listing of the users.
     */
    public function index(Request $request): View
    {
        $search = $request->get('search');
        $role = $request->get('role');
        $level = $request->get('level');
        $sort = $request->get('sort', 'created_at');
        $order = $request->get('order', 'desc');

        $allowedSort = ['full_name', 'username', 'balance', 'level', 'created_at'];
        if (! in_array($sort, $allowedSort, true)) {
            $sort = 'created_at';
        }
        $allowedOrder = ['asc', 'desc'];
        if (! in_array($order, $allowedOrder, true)) {
            $order = 'desc';
        }

        $users = User::query()
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('username', 'like', "%{$search}%")
                      ->orWhere('full_name', 'like', "%{$search}%");
                });
            })
            ->when($role, fn ($q, $role) => $q->where('role', $role))
            ->when($level, fn ($q, $level) => $q->where('level', $level))
            ->orderBy($sort, $order)
            ->paginate(15)
            ->appends($request->query());

        return view('admin.users.index', compact('users', 'search', 'role', 'level', 'sort', 'order'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create(): View
    {
        return view('admin.users.create');
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(StoreUserRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $filename = time().'_'.preg_replace('/\s+/', '_', $file->getClientOriginalName());
            $file->storeAs('profiles', $filename, 'public');
            $data['photo'] = $filename;
        }

        User::create($data);

        return redirect()->route('admin.users.index')
            ->with('success', 'Pengguna berhasil dibuat.');
    }

    /**
     * Display the specified user.
     */
    public function show(User $user): View
    {
        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user): View
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $data = $request->validated();

        if (empty($data['password'])) {
            unset($data['password']);
        }

        if ($request->hasFile('photo')) {
            if ($user->photo && Storage::disk('public')->exists('profiles/'.$user->photo)) {
                Storage::disk('public')->delete('profiles/'.$user->photo);
            }
            $file = $request->file('photo');
            $filename = time().'_'.preg_replace('/\s+/', '_', $file->getClientOriginalName());
            $file->storeAs('profiles', $filename, 'public');
            $data['photo'] = $filename;
        }

        if ($user->role === 'admin' && ($data['role'] ?? 'admin') !== 'admin') {
            $otherAdmins = User::where('role', 'admin')->where('id', '!=', $user->id)->count();
            if ($otherAdmins === 0) {
                return back()->with('error', 'Perubahan peran dibatalkan. Minimal harus ada satu admin.');
            }
        }

        $user->update($data);

        return redirect()->route('admin.users.index')
            ->with('success', 'Pengguna berhasil diperbarui.');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user): RedirectResponse
    {
        // Prevent self delete
        if (auth()->id() === $user->id) {
            return back()->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }

        // Prevent deleting last admin
        if ($user->role === 'admin') {
            $otherAdmins = User::where('role', 'admin')->where('id', '!=', $user->id)->count();
            if ($otherAdmins === 0) {
                return back()->with('error', 'Tidak dapat menghapus admin terakhir yang tersisa.');
            }
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'Pengguna berhasil dihapus.');
    }
}
