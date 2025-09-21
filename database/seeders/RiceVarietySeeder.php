<?php

namespace Database\Seeders;

use App\Models\RiceVariety;
use Illuminate\Database\Seeder;

class RiceVarietySeeder extends Seeder
{
    public function run(): void
    {
        $varieties = [
            [
                'name' => 'IR64',
                'variety_code' => 'IR64',
                'description' => 'High-yielding variety with good grain quality and disease resistance',
                'maturity_days' => 115,
                'average_yield_per_hectare' => 4500.00,
                'season' => 'both',
                'grain_type' => 'long',
                'resistance_level' => 'high',
                'characteristics' => [
                    'drought_tolerance' => 'moderate',
                    'flood_tolerance' => 'low',
                    'salinity_tolerance' => 'moderate',
                    'pest_resistance' => ['brown_planthopper', 'green_leafhopper'],
                    'disease_resistance' => ['bacterial_blight', 'tungro']
                ]
            ],
            [
                'name' => 'PSB Rc82',
                'variety_code' => 'PSB_RC82',
                'description' => 'Premium quality rice variety with excellent cooking quality',
                'maturity_days' => 120,
                'average_yield_per_hectare' => 5200.00,
                'season' => 'both',
                'grain_type' => 'long',
                'resistance_level' => 'high',
                'characteristics' => [
                    'drought_tolerance' => 'high',
                    'flood_tolerance' => 'moderate',
                    'salinity_tolerance' => 'low',
                    'pest_resistance' => ['brown_planthopper', 'stem_borer'],
                    'disease_resistance' => ['bacterial_blight', 'blast']
                ]
            ],
            [
                'name' => 'NSIC Rc222',
                'variety_code' => 'NSIC_RC222',
                'description' => 'High-yielding variety suitable for lowland conditions',
                'maturity_days' => 110,
                'average_yield_per_hectare' => 4800.00,
                'season' => 'wet',
                'grain_type' => 'medium',
                'resistance_level' => 'medium',
                'characteristics' => [
                    'drought_tolerance' => 'low',
                    'flood_tolerance' => 'high',
                    'salinity_tolerance' => 'low',
                    'pest_resistance' => ['green_leafhopper'],
                    'disease_resistance' => ['tungro', 'bacterial_blight']
                ]
            ],
            [
                'name' => 'NSIC Rc216',
                'variety_code' => 'NSIC_RC216',
                'description' => 'Drought-tolerant variety ideal for upland and rainfed areas',
                'maturity_days' => 105,
                'average_yield_per_hectare' => 3800.00,
                'season' => 'dry',
                'grain_type' => 'medium',
                'resistance_level' => 'high',
                'characteristics' => [
                    'drought_tolerance' => 'very_high',
                    'flood_tolerance' => 'low',
                    'salinity_tolerance' => 'moderate',
                    'pest_resistance' => ['stem_borer', 'brown_planthopper'],
                    'disease_resistance' => ['blast', 'bacterial_blight']
                ]
            ],
            [
                'name' => 'Jasmine Rice',
                'variety_code' => 'JASMINE',
                'description' => 'Aromatic rice variety with premium market value',
                'maturity_days' => 125,
                'average_yield_per_hectare' => 3500.00,
                'season' => 'both',
                'grain_type' => 'long',
                'resistance_level' => 'medium',
                'characteristics' => [
                    'drought_tolerance' => 'moderate',
                    'flood_tolerance' => 'moderate',
                    'salinity_tolerance' => 'low',
                    'aroma' => 'high',
                    'market_premium' => 'high',
                    'pest_resistance' => ['green_leafhopper'],
                    'disease_resistance' => ['tungro']
                ]
            ]
        ];

        foreach ($varieties as $variety) {
            RiceVariety::create($variety);
        }
    }
}