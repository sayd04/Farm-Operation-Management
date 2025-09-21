<?php

namespace Database\Seeders;

use App\Models\TaskTemplate;
use App\Models\RiceGrowthStage;
use Illuminate\Database\Seeder;

class TaskTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $stages = RiceGrowthStage::all()->keyBy('stage_code');

        $templates = [
            // Land Preparation Stage
            [
                'rice_growth_stage_id' => $stages['LAND_PREP']->id,
                'name' => 'Primary Tillage (Plowing)',
                'description' => 'Plow the field to break and turn over soil for {field_name} - {area}',
                'task_type' => 'maintenance',
                'days_from_stage_start' => 0,
                'estimated_duration_hours' => 8,
                'priority' => 'high',
                'weather_conditions' => [
                    'avoid_conditions' => ['rainy', 'stormy'],
                    'max_wind_speed' => 15,
                ],
                'required_materials' => ['Tractor', 'Plow', 'Fuel'],
                'instructions' => [
                    'Check soil moisture - should be at field capacity',
                    'Plow to 15-20cm depth',
                    'Ensure complete soil turnover',
                    'Avoid plowing when soil is too wet or too dry',
                ],
                'safety_notes' => ['Wear protective equipment', 'Check machinery before use'],
                'is_weather_dependent' => true,
            ],
            [
                'rice_growth_stage_id' => $stages['LAND_PREP']->id,
                'name' => 'Field Leveling and Bunding',
                'description' => 'Level the field and repair bunds for {field_name}',
                'task_type' => 'maintenance',
                'days_from_stage_start' => 3,
                'estimated_duration_hours' => 6,
                'priority' => 'high',
                'weather_conditions' => [
                    'avoid_conditions' => ['rainy', 'stormy'],
                ],
                'required_materials' => ['Leveling board', 'Shovel', 'Measuring tools'],
                'instructions' => [
                    'Level field to ensure uniform water distribution',
                    'Repair and strengthen bunds',
                    'Create proper drainage channels',
                    'Check for proper slope (1-2cm per 100m)',
                ],
                'is_weather_dependent' => true,
            ],

            // Seed Preparation & Sowing Stage
            [
                'rice_growth_stage_id' => $stages['SEED_SOW']->id,
                'name' => 'Seed Treatment and Preparation',
                'description' => 'Treat and prepare {variety_name} seeds for {field_name}',
                'task_type' => 'maintenance',
                'days_from_stage_start' => 0,
                'estimated_duration_hours' => 2,
                'priority' => 'critical',
                'required_materials' => ['Rice seeds', 'Fungicide', 'Water', 'Containers'],
                'instructions' => [
                    'Select healthy, uniform seeds',
                    'Treat seeds with fungicide',
                    'Soak seeds for 24 hours',
                    'Incubate for pre-germination',
                ],
                'safety_notes' => ['Wear gloves when handling chemicals'],
                'is_weather_dependent' => false,
            ],
            [
                'rice_growth_stage_id' => $stages['SEED_SOW']->id,
                'name' => 'Nursery Establishment',
                'description' => 'Establish nursery for {variety_name} seedlings',
                'task_type' => 'maintenance',
                'days_from_stage_start' => 1,
                'estimated_duration_hours' => 4,
                'priority' => 'critical',
                'weather_conditions' => [
                    'avoid_conditions' => ['stormy'],
                    'temperature_min' => 20,
                    'temperature_max' => 35,
                ],
                'required_materials' => ['Nursery area', 'Organic matter', 'Water source'],
                'instructions' => [
                    'Prepare nursery bed (1m x 10m per hectare)',
                    'Apply organic matter',
                    'Maintain 2-3cm water depth',
                    'Sow pre-germinated seeds evenly',
                ],
                'is_weather_dependent' => true,
            ],

            // Transplanting Stage
            [
                'rice_growth_stage_id' => $stages['TRANSPLANT']->id,
                'name' => 'Seedling Transplanting',
                'description' => 'Transplant {variety_name} seedlings to {field_name}',
                'task_type' => 'maintenance',
                'days_from_stage_start' => 0,
                'estimated_duration_hours' => 16,
                'priority' => 'critical',
                'weather_conditions' => [
                    'avoid_conditions' => ['stormy'],
                    'temperature_max' => 32,
                    'max_wind_speed' => 10,
                ],
                'required_materials' => ['Healthy seedlings', 'Transplanting tools'],
                'instructions' => [
                    'Use 21-25 day old seedlings',
                    'Transplant 2-3 seedlings per hill',
                    'Maintain 20cm x 20cm spacing',
                    'Plant at proper depth (2-3cm)',
                    'Ensure roots are well-covered',
                ],
                'safety_notes' => ['Work during cooler hours', 'Stay hydrated'],
                'is_weather_dependent' => true,
            ],

            // Tillering Stage
            [
                'rice_growth_stage_id' => $stages['TILLERING']->id,
                'name' => 'First Nitrogen Application',
                'description' => 'Apply first dose of nitrogen fertilizer for tillering',
                'task_type' => 'fertilizing',
                'days_from_stage_start' => 7,
                'estimated_duration_hours' => 3,
                'priority' => 'high',
                'weather_conditions' => [
                    'avoid_conditions' => ['rainy', 'stormy'],
                    'max_wind_speed' => 10,
                ],
                'required_materials' => ['Urea fertilizer', 'Broadcasting equipment'],
                'instructions' => [
                    'Apply 40kg urea per hectare',
                    'Broadcast evenly across field',
                    'Apply when field has 2-3cm water',
                    'Incorporate into soil',
                ],
                'safety_notes' => ['Wear protective equipment', 'Avoid skin contact'],
                'is_weather_dependent' => true,
            ],
            [
                'rice_growth_stage_id' => $stages['TILLERING']->id,
                'name' => 'Weed Control',
                'description' => 'Control weeds in {field_name} during tillering stage',
                'task_type' => 'weeding',
                'days_from_stage_start' => 14,
                'estimated_duration_hours' => 6,
                'priority' => 'high',
                'weather_conditions' => [
                    'avoid_conditions' => ['rainy', 'stormy'],
                    'max_wind_speed' => 12,
                ],
                'required_materials' => ['Herbicide', 'Sprayer', 'Water'],
                'instructions' => [
                    'Scout field for weed types and density',
                    'Apply appropriate herbicide',
                    'Ensure proper coverage',
                    'Maintain water level during application',
                ],
                'safety_notes' => ['Use protective clothing', 'Follow label instructions'],
                'is_weather_dependent' => true,
            ],

            // Panicle Initiation Stage
            [
                'rice_growth_stage_id' => $stages['PANICLE_INIT']->id,
                'name' => 'Panicle Fertilizer Application',
                'description' => 'Apply panicle fertilizer for grain development',
                'task_type' => 'fertilizing',
                'days_from_stage_start' => 3,
                'estimated_duration_hours' => 3,
                'priority' => 'critical',
                'weather_conditions' => [
                    'avoid_conditions' => ['rainy', 'stormy'],
                    'max_wind_speed' => 10,
                ],
                'required_materials' => ['Complete fertilizer (NPK)', 'Broadcasting equipment'],
                'instructions' => [
                    'Apply complete fertilizer (14-14-14) at 2 bags per hectare',
                    'Ensure even distribution',
                    'Apply when field has standing water',
                    'Time application with weather forecast',
                ],
                'is_weather_dependent' => true,
            ],

            // Flowering Stage
            [
                'rice_growth_stage_id' => $stages['FLOWERING']->id,
                'name' => 'Water Management During Flowering',
                'description' => 'Critical water management during flowering period',
                'task_type' => 'watering',
                'days_from_stage_start' => 0,
                'estimated_duration_hours' => 2,
                'priority' => 'critical',
                'required_materials' => ['Water source', 'Irrigation equipment'],
                'instructions' => [
                    'Maintain 3-5cm water depth consistently',
                    'Never allow field to dry during flowering',
                    'Monitor water quality',
                    'Ensure continuous water supply',
                ],
                'is_weather_dependent' => false,
                'is_mandatory' => true,
            ],

            // Grain Filling Stage
            [
                'rice_growth_stage_id' => $stages['GRAIN_FILL']->id,
                'name' => 'Pest Monitoring',
                'description' => 'Monitor for late-season pests during grain filling',
                'task_type' => 'pest_control',
                'days_from_stage_start' => 7,
                'estimated_duration_hours' => 2,
                'priority' => 'medium',
                'required_materials' => ['Monitoring tools', 'Record sheets'],
                'instructions' => [
                    'Scout for brown planthopper',
                    'Check for stem borer damage',
                    'Monitor for disease symptoms',
                    'Record pest levels and damage',
                ],
                'is_weather_dependent' => false,
            ],

            // Harvest Stage
            [
                'rice_growth_stage_id' => $stages['HARVEST']->id,
                'name' => 'Pre-Harvest Field Drainage',
                'description' => 'Drain field in preparation for harvest',
                'task_type' => 'watering',
                'days_from_stage_start' => 0,
                'estimated_duration_hours' => 1,
                'priority' => 'critical',
                'required_materials' => ['Drainage tools'],
                'instructions' => [
                    'Drain field completely',
                    'Allow soil to firm up',
                    'Check accessibility for harvest equipment',
                    'Ensure proper drainage channels',
                ],
                'is_weather_dependent' => false,
                'is_mandatory' => true,
            ],
            [
                'rice_growth_stage_id' => $stages['HARVEST']->id,
                'name' => 'Rice Harvesting',
                'description' => 'Harvest {variety_name} rice from {field_name}',
                'task_type' => 'harvesting',
                'days_from_stage_start' => 5,
                'estimated_duration_hours' => 12,
                'priority' => 'critical',
                'weather_conditions' => [
                    'avoid_conditions' => ['rainy', 'stormy'],
                    'required_conditions' => ['clear', 'cloudy'],
                ],
                'required_materials' => ['Harvester', 'Storage bags', 'Transport'],
                'instructions' => [
                    'Check grain moisture (20-22% for optimal harvest)',
                    'Cut rice when 80-85% of grains are mature',
                    'Harvest during dry weather',
                    'Handle carefully to minimize losses',
                    'Transport to drying area immediately',
                ],
                'safety_notes' => ['Use proper machinery safety', 'Avoid working alone'],
                'is_weather_dependent' => true,
                'is_mandatory' => true,
            ],
        ];

        foreach ($templates as $template) {
            TaskTemplate::create($template);
        }
    }
}