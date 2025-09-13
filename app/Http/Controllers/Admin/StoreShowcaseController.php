<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StoreShowcase;
use App\Models\User;
use Illuminate\Http\Request;

class StoreShowcaseController extends Controller
{
    /**
     * Display a listing of all users' showcases.
     */
    public function index(Request $request)
    {
        $query = StoreShowcase::with(['user', 'product'])
            ->latest();

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->active();
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            } elseif ($request->status === 'featured') {
                $query->featured();
            }
        }

        $showcases = $query->paginate(15);

        // Get all users for filter dropdown
        $users = User::select('id', 'full_name')->orderBy('full_name')->get();

        return view('admin.store-showcases.index', compact('showcases', 'users'));
    }

    /**
     * Display the specified user's showcases.
     */
    public function show($id)
    {
        $user = User::findOrFail($id);
        
        $showcases = StoreShowcase::with(['product'])
            ->where('user_id', $user->id)
            ->orderBy('sort_order')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.store-showcases.show', compact('user', 'showcases'));
    }

    /**
     * Display user's active showcases (public view).
     */
    public function userShowcase($userId)
    {
        $user = User::findOrFail($userId);
        
        $showcases = StoreShowcase::with(['product'])
            ->where('user_id', $user->id)
            ->active()
            ->orderBy('sort_order')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.store-showcases.user-showcase', compact('user', 'showcases'));
    }

    /**
     * Toggle active status of a showcase.
     */
    public function toggleActive($id)
    {
        $showcase = StoreShowcase::with('user')->findOrFail($id);
        
        $showcase->is_active = !$showcase->is_active;
        $showcase->save();

        return response()->json([
            'success' => true,
            'message' => 'Showcase status updated successfully.',
            'is_active' => $showcase->is_active
        ]);
    }

    /**
     * Toggle featured status of a showcase.
     */
    public function toggleFeatured($id)
    {
        $showcase = StoreShowcase::with('user')->findOrFail($id);
        
        $showcase->is_featured = !$showcase->is_featured;
        
        // If setting as featured, set featured_until to 30 days from now
        if ($showcase->is_featured) {
            $showcase->featured_until = now()->addDays(30);
        } else {
            $showcase->featured_until = null;
        }
        
        $showcase->save();

        return response()->json([
            'success' => true,
            'message' => 'Showcase featured status updated successfully.',
            'is_featured' => $showcase->is_featured,
            'featured_until' => $showcase->featured_until?->format('Y-m-d H:i:s')
        ]);
    }

    /**
     * Remove the specified showcase.
     */
    public function destroy($id)
    {
        $showcase = StoreShowcase::with('user')->findOrFail($id);
        
        $userName = $showcase->user->full_name;
        $productName = $showcase->product->name ?? 'Unknown Product';
        
        $showcase->delete();

        return redirect()->back()->with('success', 
            "Showcase '{$productName}' from {$userName}'s store has been removed successfully."
        );
    }

    /**
     * Get showcase statistics for dashboard.
     */
    public function stats()
    {
        $stats = [
            'total_showcases' => StoreShowcase::count(),
            'active_showcases' => StoreShowcase::active()->count(),
            'featured_showcases' => StoreShowcase::featured()->count(),
            'total_users_with_showcases' => StoreShowcase::distinct('user_id')->count('user_id'),
        ];

        return response()->json($stats);
    }

    /**
     * Bulk actions for showcases.
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:activate,deactivate,feature,unfeature,delete',
            'showcase_ids' => 'required|array',
            'showcase_ids.*' => 'exists:store_showcases,id'
        ]);

        $showcases = StoreShowcase::whereIn('id', $request->showcase_ids);

        switch ($request->action) {
            case 'activate':
                $showcases->update(['is_active' => true]);
                $message = 'Selected showcases have been activated.';
                break;
            
            case 'deactivate':
                $showcases->update(['is_active' => false]);
                $message = 'Selected showcases have been deactivated.';
                break;
            
            case 'feature':
                $showcases->update([
                    'is_featured' => true,
                    'featured_until' => now()->addDays(30)
                ]);
                $message = 'Selected showcases have been featured.';
                break;
            
            case 'unfeature':
                $showcases->update([
                    'is_featured' => false,
                    'featured_until' => null
                ]);
                $message = 'Selected showcases have been unfeatured.';
                break;
            
            case 'delete':
                $showcases->delete();
                $message = 'Selected showcases have been deleted.';
                break;
        }

        return redirect()->back()->with('success', $message);
    }
}
