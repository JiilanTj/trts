<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    /**
     * Display analytics dashboard
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        // Basic Stats
        $stats = $this->getBasicStats($user);
        
        // Chart Data
        $chartData = $this->getChartData($user);
        
        // Recent Activities
        $recentActivities = $this->getRecentActivities($user);
        
        // Performance Metrics
        $performance = $this->getPerformanceMetrics($user);
        
        return view('user.analytics.index', compact(
            'stats', 
            'chartData', 
            'recentActivities', 
            'performance'
        ));
    }
    
    /**
     * Get basic statistics
     */
    private function getBasicStats($user)
    {
        $currentMonth = Carbon::now()->startOfMonth();
        $lastMonth = Carbon::now()->subMonth()->startOfMonth();
        
        return [
            'total_orders' => $user->orders()->count(),
            'total_spent' => $user->orders()->where('payment_status', 'confirmed')->sum('grand_total'),
            'pending_orders' => $user->orders()->where('status', 'pending')->count(),
            'completed_orders' => $user->orders()->where('status', 'completed')->count(),
            'current_balance' => $user->balance,
            'credit_score' => $user->credit_score ?? 0,
            'notifications_count' => $user->notifications()->unread()->count(),
            
            // Growth metrics
            'orders_this_month' => $user->orders()->where('created_at', '>=', $currentMonth)->count(),
            'orders_last_month' => $user->orders()->whereBetween('created_at', [$lastMonth, $currentMonth])->count(),
            'spent_this_month' => $user->orders()
                ->where('created_at', '>=', $currentMonth)
                ->where('payment_status', 'confirmed')
                ->sum('grand_total'),
            'spent_last_month' => $user->orders()
                ->whereBetween('created_at', [$lastMonth, $currentMonth])
                ->where('payment_status', 'confirmed')
                ->sum('grand_total'),
        ];
    }
    
    /**
     * Get chart data for various metrics
     */
    private function getChartData($user)
    {
        // Orders per month (last 12 months)
        $ordersPerMonth = $user->orders()
            ->select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(grand_total) as total_spent')
            )
            ->where('created_at', '>=', Carbon::now()->subMonths(12))
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();
        
        // Order status distribution
        $orderStatusDistribution = $user->orders()
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get();
        
        // Top categories by spending
        $topCategories = $user->orders()
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->where('orders.payment_status', 'confirmed')
            ->select('categories.name', DB::raw('SUM(order_items.line_total) as total_spent'))
            ->groupBy('categories.id', 'categories.name')
            ->orderBy('total_spent', 'desc')
            ->limit(10)
            ->get();
        
        // Activity timeline (last 30 days)
        $activityTimeline = $user->orders()
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as orders_count'),
                DB::raw('SUM(grand_total) as daily_spent')
            )
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();
        
        // Payment status distribution
        $paymentStatusDistribution = $user->orders()
            ->select('payment_status', DB::raw('COUNT(*) as count'))
            ->groupBy('payment_status')
            ->get();
        
        // Top categories by spending
        $topCategories = $user->orders()
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->select('categories.name', DB::raw('SUM(order_items.line_total) as total_spent'))
            ->where('orders.payment_status', 'confirmed')
            ->groupBy('categories.id', 'categories.name')
            ->orderBy('total_spent', 'desc')
            ->limit(5)
            ->get();
        
        // Activity over time (last 30 days)
        $activityData = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $activityData[] = [
                'date' => $date->format('Y-m-d'),
                'orders' => $user->orders()->whereDate('created_at', $date)->count(),
                'notifications' => $user->notifications()->whereDate('created_at', $date)->count(),
            ];
        }
        
        return [
            'orders_per_month' => $ordersPerMonth,
            'order_status_distribution' => $orderStatusDistribution,
            'payment_status_distribution' => $paymentStatusDistribution,
            'top_categories' => $topCategories,
            'activity_data' => $activityData,
        ];
    }
    
    /**
     * Get recent activities
     */
    private function getRecentActivities($user)
    {
        $activities = collect();
        
        // Recent orders
        $recentOrders = $user->orders()->latest()->limit(5)->get();
        foreach ($recentOrders as $order) {
            $activities->push([
                'type' => 'order',
                'title' => 'Order #' . $order->id,
                'description' => 'Total: Rp ' . number_format($order->grand_total),
                'status' => $order->status,
                'created_at' => $order->created_at,
                'icon' => 'shopping-bag',
                'color' => $this->getOrderStatusColor($order->status),
            ]);
        }
        
        // Recent notifications
        $recentNotifications = $user->notifications()->latest()->limit(5)->get();
        foreach ($recentNotifications as $notification) {
            $activities->push([
                'type' => 'notification',
                'title' => $notification->title,
                'description' => $notification->message,
                'status' => $notification->read_at ? 'read' : 'unread',
                'created_at' => $notification->created_at,
                'icon' => 'bell',
                'color' => $notification->read_at ? 'text-neutral-400' : 'text-blue-400',
            ]);
        }
        
        return $activities->sortByDesc('created_at')->take(10);
    }
    
    /**
     * Get performance metrics
     */
    private function getPerformanceMetrics($user)
    {
        $totalOrders = $user->orders()->count();
        $completedOrders = $user->orders()->where('status', 'completed')->count();
        $confirmedPayments = $user->orders()->where('payment_status', 'confirmed')->count();
        
        return [
            'completion_rate' => $totalOrders > 0 ? round(($completedOrders / $totalOrders) * 100, 2) : 0,
            'payment_success_rate' => $totalOrders > 0 ? round(($confirmedPayments / $totalOrders) * 100, 2) : 0,
            'avg_order_value' => $confirmedPayments > 0 ? 
                round($user->orders()->where('payment_status', 'confirmed')->avg('grand_total'), 2) : 0,
            'total_savings' => $user->orders()->where('payment_status', 'confirmed')->sum('discount_total'),
            'loyalty_score' => $this->calculateLoyaltyScore($user),
        ];
    }
    
    /**
     * Calculate user loyalty score
     */
    private function calculateLoyaltyScore($user)
    {
        $score = 0;
        
        // Base score from orders
        $score += $user->orders()->count() * 10;
        
        // Bonus for completed orders
        $score += $user->orders()->where('status', 'completed')->count() * 15;
        
        // Bonus for confirmed payments
        $score += $user->orders()->where('payment_status', 'confirmed')->count() * 20;
        
        // Bonus for account age (days * 0.5)
        $score += $user->created_at->diffInDays(now()) * 0.5;
        
        // Bonus for having seller status
        if ($user->isSeller()) {
            $score += 100;
        }
        
        return min(round($score), 1000); // Cap at 1000
    }
    
    /**
     * Get order status color
     */
    private function getOrderStatusColor($status)
    {
        return match($status) {
            'pending' => 'text-yellow-400',
            'processing' => 'text-blue-400',
            'shipped' => 'text-purple-400',
            'completed' => 'text-green-400',
            'cancelled' => 'text-red-400',
            default => 'text-neutral-400',
        };
    }
    
    /**
     * API endpoint for real-time stats
     */
    public function getStats(Request $request)
    {
        $user = $request->user();
        $stats = $this->getBasicStats($user);
        
        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }
    
    /**
     * API endpoint for chart data
     */
    public function apiGetChartData(Request $request)
    {
        $user = $request->user();
        $type = $request->get('type', 'orders_per_month');
        
        $chartData = $this->getChartData($user);
        
        return response()->json([
            'success' => true,
            'data' => $chartData[$type] ?? []
        ]);
    }
}
