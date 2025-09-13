<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EtalaseView extends Model
{
    protected $fillable = [
        'etalase_owner_id',
        'visitor_ip',
        'visitor_user_id',
        'product_id',
        'user_agent',
        'viewed_at',
    ];

    protected $casts = [
        'viewed_at' => 'datetime',
    ];

    /**
     * Relationship to etalase owner (User)
     */
    public function etalaseOwner()
    {
        return $this->belongsTo(User::class, 'etalase_owner_id');
    }

    /**
     * Relationship to visitor (User) - nullable for guest visitors
     */
    public function visitor()
    {
        return $this->belongsTo(User::class, 'visitor_user_id');
    }

    /**
     * Relationship to product being viewed
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Record a view with spam protection
     */
    public static function recordView($etalaseOwnerId, $productId = null)
    {
        $visitorIp = request()->ip();
        $visitorUserId = auth()->id(); // NULL untuk guest

        // Anti-spam: Only record if no view in last 30 minutes from same IP/user for same etalase/product
        $recentView = self::where('etalase_owner_id', $etalaseOwnerId)
            ->where('visitor_ip', $visitorIp)
            ->where('visitor_user_id', $visitorUserId)
            ->where('product_id', $productId)
            ->where('viewed_at', '>', now()->subMinutes(30))
            ->first();

        if (!$recentView) {
            // Create etalase view record
            $view = self::create([
                'etalase_owner_id' => $etalaseOwnerId,
                'visitor_ip' => $visitorIp,
                'visitor_user_id' => $visitorUserId,
                'product_id' => $productId,
                'user_agent' => request()->userAgent(),
                'viewed_at' => now(),
            ]);
            
            // Update visitors count in users table (only for general etalase views, not product-specific)
            if (!$productId) {
                User::where('id', $etalaseOwnerId)->increment('visitors');
            }
            
            return $view;
        }

        return null; // Skip jika baru saja view
    }

    /**
     * Get analytics data for an etalase owner
     */
    public static function getAnalytics($etalaseOwnerId)
    {
        $baseQuery = self::where('etalase_owner_id', $etalaseOwnerId);
        
        return [
            'total_views' => $baseQuery->count(),
            'unique_visitors' => self::getUniqueVisitorCount($etalaseOwnerId),
            'registered_visitors' => $baseQuery->whereNotNull('visitor_user_id')
                ->distinct('visitor_user_id')->count('visitor_user_id'),
            'guest_visitors' => $baseQuery->whereNull('visitor_user_id')
                ->distinct('visitor_ip')->count('visitor_ip'),
            'views_today' => $baseQuery->whereDate('viewed_at', today())->count(),
            'views_this_week' => $baseQuery->whereBetween('viewed_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'views_this_month' => $baseQuery->whereMonth('viewed_at', now()->month)
                ->whereYear('viewed_at', now()->year)->count(),
        ];
    }

    /**
     * Get unique visitor count (registered users + unique guest IPs)
     */
    public static function getUniqueVisitorCount($etalaseOwnerId)
    {
        return self::where('etalase_owner_id', $etalaseOwnerId)
            ->selectRaw('
                COUNT(DISTINCT 
                    CASE 
                        WHEN visitor_user_id IS NOT NULL THEN CONCAT("user_", visitor_user_id)
                        ELSE CONCAT("guest_", visitor_ip)
                    END
                ) as unique_count
            ')
            ->value('unique_count') ?? 0;
    }

    /**
     * Get product view analytics for an etalase owner
     */
    public static function getProductViewAnalytics($etalaseOwnerId)
    {
        return self::where('etalase_owner_id', $etalaseOwnerId)
            ->whereNotNull('product_id')
            ->groupBy('product_id')
            ->selectRaw('product_id, COUNT(*) as view_count, COUNT(DISTINCT 
                CASE 
                    WHEN visitor_user_id IS NOT NULL THEN CONCAT("user_", visitor_user_id)
                    ELSE CONCAT("guest_", visitor_ip)
                END
            ) as unique_viewers')
            ->with('product')
            ->get()
            ->keyBy('product_id');
    }

    /**
     * Get recent visitors list (mix of registered users and guest IPs)
     */
    public static function getRecentVisitors($etalaseOwnerId, $limit = 10)
    {
        return self::where('etalase_owner_id', $etalaseOwnerId)
            ->with(['visitor', 'product'])
            ->orderBy('viewed_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($view) {
                return [
                    'type' => $view->visitor_user_id ? 'registered' : 'guest',
                    'identifier' => $view->visitor_user_id 
                        ? $view->visitor->full_name 
                        : 'Guest (' . substr($view->visitor_ip, 0, -2) . '**)',
                    'viewed_at' => $view->viewed_at,
                    'product' => $view->product,
                ];
            });
    }
}
