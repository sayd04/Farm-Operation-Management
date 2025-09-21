<?php

namespace Database\Seeders;

use App\Models\InventoryCategory;
use Illuminate\Database\Seeder;

class InventoryCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Rice Seeds',
                'code' => 'SEEDS',
                'description' => 'Various rice seed varieties and planting materials',
                'icon' => 'seedling',
                'color' => '#10B981',
                'metadata' => [
                    'requires_temperature_control' => false,
                    'typical_shelf_life_months' => 12,
                    'storage_humidity_max' => 14,
                ]
            ],
            [
                'name' => 'Fertilizers',
                'code' => 'FERTILIZER',
                'description' => 'Chemical and organic fertilizers for rice cultivation',
                'icon' => 'leaf',
                'color' => '#059669',
                'metadata' => [
                    'requires_dry_storage' => true,
                    'typical_shelf_life_months' => 24,
                    'safety_equipment_required' => true,
                ]
            ],
            [
                'name' => 'Pesticides & Herbicides',
                'code' => 'PESTICIDE',
                'description' => 'Pest control and weed management chemicals',
                'icon' => 'shield-check',
                'color' => '#DC2626',
                'metadata' => [
                    'requires_secure_storage' => true,
                    'safety_equipment_required' => true,
                    'disposal_regulations' => true,
                ]
            ],
            [
                'name' => 'Farm Equipment',
                'code' => 'EQUIPMENT',
                'description' => 'Tools and machinery for rice farming operations',
                'icon' => 'cog',
                'color' => '#7C3AED',
                'metadata' => [
                    'requires_maintenance' => true,
                    'depreciation_tracking' => true,
                ]
            ],
            [
                'name' => 'Rice Products',
                'code' => 'PRODUCE',
                'description' => 'Harvested rice and rice by-products',
                'icon' => 'archive',
                'color' => '#F59E0B',
                'metadata' => [
                    'requires_quality_tracking' => true,
                    'moisture_monitoring' => true,
                    'market_price_tracking' => true,
                ]
            ],
            [
                'name' => 'Fuel & Energy',
                'code' => 'FUEL',
                'description' => 'Fuel, electricity, and energy resources',
                'icon' => 'lightning-bolt',
                'color' => '#EF4444',
                'metadata' => [
                    'requires_safe_storage' => true,
                    'fire_safety_required' => true,
                ]
            ],
            [
                'name' => 'Packaging & Storage',
                'code' => 'PACKAGING',
                'description' => 'Bags, containers, and storage materials',
                'icon' => 'cube',
                'color' => '#6B7280',
                'metadata' => [
                    'bulk_ordering_preferred' => true,
                ]
            ],
        ];

        foreach ($categories as $category) {
            InventoryCategory::create($category);
        }
    }
}