<?php

namespace Database\Seeders;

use App\Models\RiceGrowthStage;
use Illuminate\Database\Seeder;

class RiceGrowthStageSeeder extends Seeder
{
    public function run(): void
    {
        $stages = [
            [
                'name' => 'Land Preparation',
                'stage_code' => 'LAND_PREP',
                'description' => 'Preparing the field for planting including plowing, harrowing, and leveling',
                'typical_duration_days' => 7,
                'order_sequence' => 1,
                'key_activities' => [
                    'Primary tillage (plowing)',
                    'Secondary tillage (harrowing)',
                    'Field leveling',
                    'Bund repair',
                    'Water management system check'
                ],
                'weather_requirements' => [
                    'temperature_range' => '25-30°C',
                    'rainfall' => 'moderate',
                    'humidity' => '60-80%'
                ],
                'nutrient_requirements' => [
                    'organic_matter' => 'Apply compost or manure',
                    'lime' => 'If soil pH < 5.5'
                ],
                'water_requirements' => [
                    'flooding' => 'Initial flooding for softening',
                    'drainage' => 'Good drainage during tillage'
                ],
                'common_problems' => [
                    'Hard pan formation',
                    'Poor drainage',
                    'Weed infestation',
                    'Uneven field surface'
                ]
            ],
            [
                'name' => 'Seed Preparation & Sowing',
                'stage_code' => 'SEED_SOW',
                'description' => 'Seed treatment, nursery preparation, and sowing or direct seeding',
                'typical_duration_days' => 21,
                'order_sequence' => 2,
                'key_activities' => [
                    'Seed selection and treatment',
                    'Nursery bed preparation',
                    'Sowing in nursery',
                    'Seedling care',
                    'Direct seeding (if applicable)'
                ],
                'weather_requirements' => [
                    'temperature_range' => '25-30°C',
                    'rainfall' => 'light to moderate',
                    'humidity' => '70-85%'
                ],
                'nutrient_requirements' => [
                    'phosphorus' => 'High for root development',
                    'nitrogen' => 'Moderate for initial growth'
                ],
                'water_requirements' => [
                    'moisture' => 'Consistent moisture in nursery',
                    'depth' => '2-3 cm water level'
                ],
                'common_problems' => [
                    'Poor germination',
                    'Fungal diseases',
                    'Bird damage',
                    'Damping off'
                ]
            ],
            [
                'name' => 'Transplanting',
                'stage_code' => 'TRANSPLANT',
                'description' => 'Moving seedlings from nursery to main field',
                'typical_duration_days' => 3,
                'order_sequence' => 3,
                'key_activities' => [
                    'Seedling selection',
                    'Field preparation',
                    'Transplanting',
                    'Gap filling',
                    'Initial water management'
                ],
                'weather_requirements' => [
                    'temperature_range' => '25-28°C',
                    'rainfall' => 'light',
                    'humidity' => '70-80%',
                    'wind' => 'minimal'
                ],
                'nutrient_requirements' => [
                    'nitrogen' => 'Light application for establishment'
                ],
                'water_requirements' => [
                    'depth' => '2-3 cm water level',
                    'quality' => 'Clean water'
                ],
                'common_problems' => [
                    'Transplant shock',
                    'Root damage',
                    'Uneven spacing',
                    'Water stress'
                ]
            ],
            [
                'name' => 'Tillering',
                'stage_code' => 'TILLERING',
                'description' => 'Active vegetative growth phase with tiller production',
                'typical_duration_days' => 35,
                'order_sequence' => 4,
                'key_activities' => [
                    'Water management',
                    'Fertilizer application',
                    'Weed control',
                    'Pest monitoring',
                    'Disease prevention'
                ],
                'weather_requirements' => [
                    'temperature_range' => '25-30°C',
                    'rainfall' => 'moderate to high',
                    'humidity' => '70-85%',
                    'sunlight' => 'abundant'
                ],
                'nutrient_requirements' => [
                    'nitrogen' => 'High for vegetative growth',
                    'phosphorus' => 'Moderate',
                    'potassium' => 'Moderate'
                ],
                'water_requirements' => [
                    'depth' => '3-5 cm water level',
                    'alternate_wetting_drying' => 'Optional for water saving'
                ],
                'common_problems' => [
                    'Brown planthopper',
                    'Stem borer',
                    'Bacterial leaf blight',
                    'Nutrient deficiency'
                ]
            ],
            [
                'name' => 'Panicle Initiation',
                'stage_code' => 'PANICLE_INIT',
                'description' => 'Reproductive phase begins with panicle development',
                'typical_duration_days' => 14,
                'order_sequence' => 5,
                'key_activities' => [
                    'Increased water management',
                    'Booster fertilizer application',
                    'Pest and disease control',
                    'Monitoring for stress'
                ],
                'weather_requirements' => [
                    'temperature_range' => '22-28°C',
                    'rainfall' => 'consistent',
                    'humidity' => '75-85%'
                ],
                'nutrient_requirements' => [
                    'nitrogen' => 'Moderate (panicle fertilizer)',
                    'phosphorus' => 'High for grain formation',
                    'potassium' => 'High for disease resistance'
                ],
                'water_requirements' => [
                    'depth' => '5-7 cm water level',
                    'consistency' => 'Very important - no water stress'
                ],
                'common_problems' => [
                    'Water stress',
                    'Blast disease',
                    'Sheath blight',
                    'Nutrient imbalance'
                ]
            ],
            [
                'name' => 'Flowering',
                'stage_code' => 'FLOWERING',
                'description' => 'Flowering and grain setting phase',
                'typical_duration_days' => 14,
                'order_sequence' => 6,
                'key_activities' => [
                    'Maintain water levels',
                    'Pest monitoring',
                    'Disease control',
                    'Weather monitoring'
                ],
                'weather_requirements' => [
                    'temperature_range' => '20-30°C',
                    'rainfall' => 'light (heavy rain damages flowers)',
                    'humidity' => '70-80%',
                    'wind' => 'gentle for pollination'
                ],
                'nutrient_requirements' => [
                    'potassium' => 'High for grain filling',
                    'micronutrients' => 'Zinc, boron for grain development'
                ],
                'water_requirements' => [
                    'depth' => '3-5 cm water level',
                    'temperature' => 'Cool water preferred'
                ],
                'common_problems' => [
                    'Poor pollination',
                    'Spikelet sterility',
                    'Blast disease',
                    'Extreme weather damage'
                ]
            ],
            [
                'name' => 'Grain Filling',
                'stage_code' => 'GRAIN_FILL',
                'description' => 'Grain development and maturation',
                'typical_duration_days' => 21,
                'order_sequence' => 7,
                'key_activities' => [
                    'Water management',
                    'Pest control',
                    'Disease monitoring',
                    'Harvest preparation'
                ],
                'weather_requirements' => [
                    'temperature_range' => '20-25°C',
                    'rainfall' => 'minimal',
                    'humidity' => '60-75%',
                    'sunlight' => 'abundant'
                ],
                'nutrient_requirements' => [
                    'potassium' => 'Moderate for grain quality',
                    'avoid_nitrogen' => 'Excess nitrogen delays maturity'
                ],
                'water_requirements' => [
                    'depth' => '2-3 cm water level',
                    'gradual_reduction' => 'Prepare for harvest'
                ],
                'common_problems' => [
                    'Grain discoloration',
                    'Lodging',
                    'Bird damage',
                    'Late season diseases'
                ]
            ],
            [
                'name' => 'Maturity & Harvest',
                'stage_code' => 'HARVEST',
                'description' => 'Crop maturation and harvesting',
                'typical_duration_days' => 7,
                'order_sequence' => 8,
                'key_activities' => [
                    'Field drainage',
                    'Harvest timing',
                    'Cutting and threshing',
                    'Drying and storage',
                    'Post-harvest handling'
                ],
                'weather_requirements' => [
                    'temperature_range' => '25-30°C',
                    'rainfall' => 'none (dry weather)',
                    'humidity' => '50-65%',
                    'wind' => 'gentle for drying'
                ],
                'nutrient_requirements' => [
                    'none' => 'No fertilizer application'
                ],
                'water_requirements' => [
                    'drainage' => 'Complete field drainage',
                    'timing' => '1-2 weeks before harvest'
                ],
                'common_problems' => [
                    'Lodging',
                    'Shattering',
                    'Weather delays',
                    'Post-harvest losses'
                ]
            ]
        ];

        foreach ($stages as $stage) {
            RiceGrowthStage::create($stage);
        }
    }
}