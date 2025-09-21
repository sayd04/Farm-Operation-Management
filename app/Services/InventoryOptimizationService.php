<?php

namespace App\Services;

use App\Models\InventoryItem;
use App\Models\InventoryCategory;
use App\Models\InventoryAlert;
use App\Models\InventoryTransaction;
use App\Models\Planting;
use App\Models\RiceGrowthStage;
use App\Models\TaskTemplate;
use Carbon\Carbon;

class InventoryOptimizationService
{
    /**
     * Generate inventory alerts for all items
     */
    public function generateInventoryAlerts(): array
    {
        $alertsGenerated = [
            'low_stock' => 0,
            'out_of_stock' => 0,
            'expiring' => 0,
            'expired' => 0,
            'reorder_point' => 0,
        ];

        $items = InventoryItem::where('is_active', true)->get();

        foreach ($items as $item) {
            $alerts = $this->checkItemAlerts($item);
            
            foreach ($alerts as $alertData) {
                // Check if similar alert already exists
                $existingAlert = InventoryAlert::where('inventory_item_id', $item->id)
                    ->where('alert_type', $alertData['alert_type'])
                    ->where('is_active', true)
                    ->where('created_at', '>=', Carbon::now()->subHours(24))
                    ->first();

                if (!$existingAlert) {
                    InventoryAlert::create($alertData);
                    $alertsGenerated[$alertData['alert_type']]++;
                }
            }
        }

        return $alertsGenerated;
    }

    /**
     * Check alerts for a specific inventory item
     */
    protected function checkItemAlerts(InventoryItem $item): array
    {
        $alerts = [];

        // Out of stock alert
        if ($item->isOutOfStock()) {
            $alerts[] = [
                'inventory_item_id' => $item->id,
                'alert_type' => InventoryAlert::TYPE_OUT_OF_STOCK,
                'severity' => InventoryAlert::SEVERITY_CRITICAL,
                'title' => 'Out of Stock',
                'message' => "{$item->name} is completely out of stock",
                'alert_data' => [
                    'current_quantity' => $item->quantity,
                    'min_stock' => $item->min_stock,
                ],
                'recommendations' => [
                    'Reorder immediately',
                    'Check if there are alternative suppliers',
                    'Consider substitutes if available',
                    'Review usage patterns to prevent future stockouts'
                ],
                'expires_at' => Carbon::now()->addDays(7),
            ];
        }
        // Low stock alert (only if not out of stock)
        elseif ($item->isLowStock()) {
            $alerts[] = [
                'inventory_item_id' => $item->id,
                'alert_type' => InventoryAlert::TYPE_LOW_STOCK,
                'severity' => InventoryAlert::SEVERITY_HIGH,
                'title' => 'Low Stock Warning',
                'message' => "{$item->name} is running low ({$item->quantity} {$item->unit} remaining)",
                'alert_data' => [
                    'current_quantity' => $item->quantity,
                    'min_stock' => $item->min_stock,
                    'days_until_stockout' => $this->calculateDaysUntilStockout($item),
                ],
                'recommendations' => [
                    'Place order soon to avoid stockout',
                    'Review minimum stock levels',
                    'Consider increasing safety stock'
                ],
                'expires_at' => Carbon::now()->addDays(3),
            ];
        }

        // Reorder point alert
        if ($item->hasReachedReorderPoint()) {
            $alerts[] = [
                'inventory_item_id' => $item->id,
                'alert_type' => InventoryAlert::TYPE_REORDER_POINT,
                'severity' => InventoryAlert::SEVERITY_MEDIUM,
                'title' => 'Reorder Point Reached',
                'message' => "{$item->name} has reached its reorder point",
                'alert_data' => [
                    'current_quantity' => $item->quantity,
                    'reorder_point' => $item->reorder_point,
                    'suggested_order_quantity' => $this->calculateOptimalOrderQuantity($item),
                    'lead_time_days' => $item->lead_time_days,
                ],
                'recommendations' => [
                    "Order {$this->calculateOptimalOrderQuantity($item)} {$item->unit}",
                    'Contact supplier: ' . ($item->supplier ?: 'Not specified'),
                    'Expected delivery in ' . ($item->lead_time_days ?: 7) . ' days'
                ],
                'expires_at' => Carbon::now()->addDays(5),
            ];
        }

        // Expiring items alert
        if ($item->isExpiringSoon(30)) {
            $daysToExpiry = $item->expiry_date->diffInDays(Carbon::now());
            $severity = $daysToExpiry <= 7 ? InventoryAlert::SEVERITY_HIGH : InventoryAlert::SEVERITY_MEDIUM;
            
            $alerts[] = [
                'inventory_item_id' => $item->id,
                'alert_type' => InventoryAlert::TYPE_EXPIRING,
                'severity' => $severity,
                'title' => 'Item Expiring Soon',
                'message' => "{$item->name} expires in {$daysToExpiry} days",
                'alert_data' => [
                    'expiry_date' => $item->expiry_date,
                    'days_to_expiry' => $daysToExpiry,
                    'quantity_at_risk' => $item->quantity,
                ],
                'recommendations' => [
                    'Use this item in upcoming tasks',
                    'Check if item can be used beyond expiry date',
                    'Consider donating if safe to do so',
                    'Review ordering patterns to reduce waste'
                ],
                'expires_at' => $item->expiry_date,
            ];
        }

        // Expired items alert
        if ($item->isExpired()) {
            $alerts[] = [
                'inventory_item_id' => $item->id,
                'alert_type' => InventoryAlert::TYPE_EXPIRED,
                'severity' => InventoryAlert::SEVERITY_CRITICAL,
                'title' => 'Expired Item',
                'message' => "{$item->name} has expired",
                'alert_data' => [
                    'expiry_date' => $item->expiry_date,
                    'days_expired' => Carbon::now()->diffInDays($item->expiry_date),
                    'quantity_expired' => $item->quantity,
                ],
                'recommendations' => [
                    'Remove expired items from inventory',
                    'Dispose of safely according to regulations',
                    'Investigate why item was not used before expiry',
                    'Adjust ordering to prevent future waste'
                ],
                'expires_at' => Carbon::now()->addDays(30),
            ];
        }

        return $alerts;
    }

    /**
     * Calculate days until stockout based on usage patterns
     */
    protected function calculateDaysUntilStockout(InventoryItem $item): int
    {
        // Get average daily usage over last 30 days
        $dailyUsage = $this->calculateAverageDailyUsage($item, 30);
        
        if ($dailyUsage <= 0) {
            return 999; // Unknown usage pattern
        }

        return (int) ceil($item->quantity / $dailyUsage);
    }

    /**
     * Calculate average daily usage for an item
     */
    protected function calculateAverageDailyUsage(InventoryItem $item, int $days = 30): float
    {
        $startDate = Carbon::now()->subDays($days);
        
        $totalUsage = $item->transactions()
                          ->where('transaction_date', '>=', $startDate)
                          ->where('transaction_type', InventoryTransaction::TYPE_OUT)
                          ->sum('quantity');

        return abs($totalUsage) / $days; // Make positive and get daily average
    }

    /**
     * Calculate optimal order quantity
     */
    protected function calculateOptimalOrderQuantity(InventoryItem $item): float
    {
        $dailyUsage = $this->calculateAverageDailyUsage($item);
        $leadTimeDays = $item->lead_time_days ?: 7;
        $safetyStock = $item->min_stock ?: ($dailyUsage * 7); // 7 days safety stock
        
        // Order enough for lead time + safety stock + buffer
        $optimalQuantity = ($dailyUsage * $leadTimeDays) + $safetyStock + ($dailyUsage * 14); // 2 weeks buffer
        
        // Ensure minimum order of 1 unit
        return max(1, round($optimalQuantity, 2));
    }

    /**
     * Get inventory recommendations for rice farming activities
     */
    public function getRiceFarmingRecommendations(Planting $planting): array
    {
        $recommendations = [];
        $currentStage = $planting->getCurrentStage();
        
        if (!$currentStage) {
            return $recommendations;
        }

        $stageName = $currentStage->riceGrowthStage->stage_code;
        $areaPlanted = $planting->area_planted;

        // Get stage-specific inventory requirements
        $requirements = $this->getRiceStageRequirements($stageName, $areaPlanted);

        foreach ($requirements as $requirement) {
            $item = InventoryItem::where('name', 'like', "%{$requirement['item']}%")
                                ->where('is_active', true)
                                ->first();

            if ($item) {
                $needed = $requirement['quantity_needed'];
                $available = $item->quantity;

                if ($available < $needed) {
                    $recommendations[] = [
                        'type' => 'shortage',
                        'item' => $item,
                        'needed' => $needed,
                        'available' => $available,
                        'shortage' => $needed - $available,
                        'urgency' => $requirement['urgency'],
                        'stage' => $stageName,
                        'recommendation' => "Need {$needed} {$item->unit} of {$item->name} for {$stageName} stage. Currently have {$available} {$item->unit}.",
                    ];
                }
            } else {
                $recommendations[] = [
                    'type' => 'missing_item',
                    'item_name' => $requirement['item'],
                    'needed' => $requirement['quantity_needed'],
                    'urgency' => $requirement['urgency'],
                    'stage' => $stageName,
                    'recommendation' => "Required item '{$requirement['item']}' not found in inventory. Need {$requirement['quantity_needed']} units for {$stageName} stage.",
                ];
            }
        }

        return $recommendations;
    }

    /**
     * Get rice stage requirements
     */
    protected function getRiceStageRequirements(string $stageName, float $areaHectares): array
    {
        $requirements = [];

        switch ($stageName) {
            case 'LAND_PREP':
                $requirements = [
                    ['item' => 'Fuel', 'quantity_needed' => $areaHectares * 20, 'urgency' => 'high'],
                    ['item' => 'Herbicide', 'quantity_needed' => $areaHectares * 2, 'urgency' => 'medium'],
                ];
                break;

            case 'SEED_SOW':
                $requirements = [
                    ['item' => 'Rice Seeds', 'quantity_needed' => $areaHectares * 120, 'urgency' => 'critical'],
                    ['item' => 'Fungicide', 'quantity_needed' => $areaHectares * 0.5, 'urgency' => 'medium'],
                ];
                break;

            case 'TRANSPLANT':
                $requirements = [
                    ['item' => 'Fertilizer', 'quantity_needed' => $areaHectares * 50, 'urgency' => 'high'],
                ];
                break;

            case 'TILLERING':
                $requirements = [
                    ['item' => 'Urea Fertilizer', 'quantity_needed' => $areaHectares * 100, 'urgency' => 'high'],
                    ['item' => 'Herbicide', 'quantity_needed' => $areaHectares * 1.5, 'urgency' => 'medium'],
                ];
                break;

            case 'PANICLE_INIT':
                $requirements = [
                    ['item' => 'Complete Fertilizer', 'quantity_needed' => $areaHectares * 75, 'urgency' => 'high'],
                    ['item' => 'Pesticide', 'quantity_needed' => $areaHectares * 1, 'urgency' => 'medium'],
                ];
                break;

            case 'FLOWERING':
                $requirements = [
                    ['item' => 'Pesticide', 'quantity_needed' => $areaHectares * 1, 'urgency' => 'medium'],
                ];
                break;

            case 'GRAIN_FILL':
                $requirements = [
                    ['item' => 'Pesticide', 'quantity_needed' => $areaHectares * 0.5, 'urgency' => 'low'],
                ];
                break;

            case 'HARVEST':
                $requirements = [
                    ['item' => 'Fuel', 'quantity_needed' => $areaHectares * 15, 'urgency' => 'high'],
                    ['item' => 'Storage Bags', 'quantity_needed' => $areaHectares * 100, 'urgency' => 'high'],
                ];
                break;
        }

        return $requirements;
    }

    /**
     * Get inventory analytics and insights
     */
    public function getInventoryAnalytics(): array
    {
        $totalItems = InventoryItem::where('is_active', true)->count();
        $totalValue = InventoryItem::where('is_active', true)
                                  ->selectRaw('SUM(quantity * cost_per_unit) as total')
                                  ->value('total') ?: 0;

        $lowStockCount = InventoryItem::where('is_active', true)
                                     ->whereRaw('quantity <= min_stock')
                                     ->count();

        $outOfStockCount = InventoryItem::where('is_active', true)
                                       ->where('quantity', '<=', 0)
                                       ->count();

        $expiringCount = InventoryItem::where('is_active', true)
                                     ->where('expiry_date', '<=', Carbon::now()->addDays(30))
                                     ->where('expiry_date', '>', Carbon::now())
                                     ->count();

        $expiredCount = InventoryItem::where('is_active', true)
                                    ->where('expiry_date', '<=', Carbon::now())
                                    ->count();

        // Category breakdown
        $categoryStats = InventoryCategory::withCount('inventoryItems')
                                         ->with(['inventoryItems' => function($query) {
                                             $query->selectRaw('category_id, SUM(quantity * cost_per_unit) as total_value')
                                                   ->groupBy('category_id');
                                         }])
                                         ->get()
                                         ->map(function($category) {
                                             return [
                                                 'name' => $category->name,
                                                 'items_count' => $category->inventory_items_count,
                                                 'total_value' => $category->total_value,
                                                 'low_stock_count' => $category->low_stock_items_count,
                                             ];
                                         });

        // Recent transactions
        $recentTransactions = InventoryTransaction::with(['inventoryItem', 'user'])
                                                 ->latest('transaction_date')
                                                 ->limit(10)
                                                 ->get();

        // Top consuming items
        $topConsumingItems = $this->getTopConsumingItems();

        return [
            'summary' => [
                'total_items' => $totalItems,
                'total_value' => $totalValue,
                'low_stock_count' => $lowStockCount,
                'out_of_stock_count' => $outOfStockCount,
                'expiring_count' => $expiringCount,
                'expired_count' => $expiredCount,
                'stock_health_percentage' => $totalItems > 0 ? 
                    (($totalItems - $lowStockCount - $outOfStockCount) / $totalItems) * 100 : 100,
            ],
            'category_stats' => $categoryStats,
            'recent_transactions' => $recentTransactions,
            'top_consuming_items' => $topConsumingItems,
            'alerts_summary' => $this->getAlertsCountByType(),
        ];
    }

    /**
     * Get top consuming items
     */
    protected function getTopConsumingItems(int $days = 30): array
    {
        $startDate = Carbon::now()->subDays($days);

        return InventoryTransaction::where('transaction_date', '>=', $startDate)
                                  ->where('transaction_type', InventoryTransaction::TYPE_OUT)
                                  ->with('inventoryItem')
                                  ->selectRaw('inventory_item_id, SUM(ABS(quantity)) as total_consumed')
                                  ->groupBy('inventory_item_id')
                                  ->orderBy('total_consumed', 'desc')
                                  ->limit(10)
                                  ->get()
                                  ->map(function($transaction) {
                                      return [
                                          'item' => $transaction->inventoryItem,
                                          'total_consumed' => $transaction->total_consumed,
                                          'daily_average' => $transaction->total_consumed / 30,
                                      ];
                                  })
                                  ->toArray();
    }

    /**
     * Get alerts count by type
     */
    protected function getAlertsCountByType(): array
    {
        return [
            'low_stock' => InventoryAlert::active()->where('alert_type', InventoryAlert::TYPE_LOW_STOCK)->count(),
            'out_of_stock' => InventoryAlert::active()->where('alert_type', InventoryAlert::TYPE_OUT_OF_STOCK)->count(),
            'expiring' => InventoryAlert::active()->where('alert_type', InventoryAlert::TYPE_EXPIRING)->count(),
            'expired' => InventoryAlert::active()->where('alert_type', InventoryAlert::TYPE_EXPIRED)->count(),
            'reorder_point' => InventoryAlert::active()->where('alert_type', InventoryAlert::TYPE_REORDER_POINT)->count(),
        ];
    }

    /**
     * Optimize inventory levels based on usage patterns
     */
    public function optimizeInventoryLevels(): array
    {
        $optimizations = [];
        $items = InventoryItem::where('is_active', true)->get();

        foreach ($items as $item) {
            $dailyUsage = $this->calculateAverageDailyUsage($item);
            $leadTime = $item->lead_time_days ?: 7;

            if ($dailyUsage > 0) {
                // Calculate optimal levels
                $optimalMinStock = $dailyUsage * ($leadTime + 7); // Lead time + 1 week safety
                $optimalReorderPoint = $dailyUsage * ($leadTime + 3); // Lead time + 3 days
                $optimalMaxStock = $dailyUsage * ($leadTime + 30); // Lead time + 1 month

                $changes = [];
                if ($item->min_stock != $optimalMinStock) {
                    $changes['min_stock'] = ['from' => $item->min_stock, 'to' => $optimalMinStock];
                }
                if ($item->reorder_point != $optimalReorderPoint) {
                    $changes['reorder_point'] = ['from' => $item->reorder_point, 'to' => $optimalReorderPoint];
                }
                if ($item->max_stock != $optimalMaxStock) {
                    $changes['max_stock'] = ['from' => $item->max_stock, 'to' => $optimalMaxStock];
                }

                if (!empty($changes)) {
                    $optimizations[] = [
                        'item' => $item,
                        'changes' => $changes,
                        'daily_usage' => $dailyUsage,
                        'lead_time' => $leadTime,
                    ];
                }
            }
        }

        return $optimizations;
    }
}