<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Field;
use App\Models\Planting;
use App\Models\Task;
use App\Models\Laborer;
use App\Models\InventoryItem;
use App\Models\WeatherLog;
use App\Models\Harvest;
use App\Models\Expense;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // First seed rice varieties, growth stages, task templates, and inventory categories
        $this->call([
            RiceVarietySeeder::class,
            RiceGrowthStageSeeder::class,
            TaskTemplateSeeder::class,
            InventoryCategorySeeder::class,
        ]);

        // Create admin user
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@ricefarm.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'phone' => '+63-917-1234567',
            'address' => [
                'street' => '123 Admin Street',
                'city' => 'Manila',
                'state' => 'Metro Manila',
                'country' => 'Philippines',
                'postal_code' => '1000'
            ]
        ]);

        // Create farmers
        $farmer1 = User::create([
            'name' => 'Juan dela Cruz',
            'email' => 'juan@ricefarm.com',
            'password' => Hash::make('password'),
            'role' => 'farmer',
            'phone' => '+63-917-2345678',
            'address' => [
                'street' => 'Barangay San Isidro',
                'city' => 'Nueva Ecija',
                'state' => 'Central Luzon',
                'country' => 'Philippines',
                'postal_code' => '3100'
            ]
        ]);

        $farmer2 = User::create([
            'name' => 'Maria Santos',
            'email' => 'maria@ricefarm.com',
            'password' => Hash::make('password'),
            'role' => 'farmer',
            'phone' => '+63-917-3456789',
            'address' => [
                'street' => 'Barangay Maligaya',
                'city' => 'Cabanatuan',
                'state' => 'Nueva Ecija',
                'country' => 'Philippines',
                'postal_code' => '3120'
            ]
        ]);

        // Create buyers
        $buyer1 = User::create([
            'name' => 'Rice Merchant Co.',
            'email' => 'buyer@ricemerchant.com',
            'password' => Hash::make('password'),
            'role' => 'buyer',
            'phone' => '+63-917-4567890',
            'address' => [
                'street' => '456 Market Street',
                'city' => 'Quezon City',
                'state' => 'Metro Manila',
                'country' => 'Philippines',
                'postal_code' => '1100'
            ]
        ]);

        $buyer2 = User::create([
            'name' => 'Golden Grain Trading',
            'email' => 'orders@goldengrain.com',
            'password' => Hash::make('password'),
            'role' => 'buyer',
            'phone' => '+63-917-5678901',
            'address' => [
                'street' => '789 Commerce Ave',
                'city' => 'Makati City',
                'state' => 'Metro Manila',
                'country' => 'Philippines',
                'postal_code' => '1200'
            ]
        ]);

        // Get rice varieties for farms and plantings
        $ir64 = \App\Models\RiceVariety::where('variety_code', 'IR64')->first();
        $psbRc82 = \App\Models\RiceVariety::where('variety_code', 'PSB_RC82')->first();
        $nsicRc222 = \App\Models\RiceVariety::where('variety_code', 'NSIC_RC222')->first();

        // Create farms for farmers
        $farm1 = \App\Models\Farm::create([
            'user_id' => $farmer1->id,
            'name' => 'Juan\'s Rice Farm',
            'location' => 'Barangay San Isidro, Nueva Ecija',
            'description' => 'Traditional rice farm specializing in IR64 variety',
            'total_area' => 5.0, // 5 hectares
            'cultivated_area' => 4.5,
            'soil_type' => 'clay_loam',
            'soil_ph' => 6.2,
            'water_source' => 'irrigation',
            'irrigation_type' => 'flood',
            'primary_rice_variety_id' => $ir64->id,
            'farm_coordinates' => [
                'latitude' => 15.5784,
                'longitude' => 120.9721
            ],
            'elevation' => 45.0,
            'slope' => 1.5,
            'drainage_system' => 'good',
            'is_setup_complete' => true,
        ]);

        $farm2 = \App\Models\Farm::create([
            'user_id' => $farmer2->id,
            'name' => 'Maria\'s Organic Rice Farm',
            'location' => 'Barangay Maligaya, Cabanatuan',
            'description' => 'Organic rice farming using PSB Rc82 variety',
            'total_area' => 3.5, // 3.5 hectares
            'cultivated_area' => 3.2,
            'soil_type' => 'loam',
            'soil_ph' => 6.5,
            'water_source' => 'river',
            'irrigation_type' => 'flood',
            'primary_rice_variety_id' => $psbRc82->id,
            'farm_coordinates' => [
                'latitude' => 15.4817,
                'longitude' => 120.9660
            ],
            'elevation' => 52.0,
            'slope' => 2.0,
            'drainage_system' => 'good',
            'is_setup_complete' => true,
        ]);

        // Create fields for farms
        $field1 = Field::create([
            'user_id' => $farmer1->id,
            'farm_id' => $farm1->id,
            'name' => 'North Paddy',
            'location' => [
                'description' => 'Northern section of the farm'
            ],
            'field_coordinates' => [
                'latitude' => 15.5790,
                'longitude' => 120.9725
            ],
            'soil_type' => 'clay_loam',
            'size' => 2.5, // hectares
            'water_access' => 'excellent',
            'drainage_quality' => 'good'
        ]);

        $field2 = Field::create([
            'user_id' => $farmer1->id,
            'farm_id' => $farm1->id,
            'name' => 'South Paddy',
            'location' => [
                'description' => 'Southern section of the farm'
            ],
            'field_coordinates' => [
                'latitude' => 15.5778,
                'longitude' => 120.9717
            ],
            'soil_type' => 'clay_loam',
            'size' => 2.0, // hectares
            'water_access' => 'excellent',
            'drainage_quality' => 'good'
        ]);

        $field3 = Field::create([
            'user_id' => $farmer2->id,
            'farm_id' => $farm2->id,
            'name' => 'Main Rice Field',
            'location' => [
                'description' => 'Primary rice cultivation area'
            ],
            'field_coordinates' => [
                'latitude' => 15.4820,
                'longitude' => 120.9665
            ],
            'soil_type' => 'loam',
            'size' => 3.2, // hectares
            'water_access' => 'good',
            'drainage_quality' => 'excellent'
        ]);

        // Create laborers
        $laborer1 = Laborer::create([
            'name' => 'Pedro Mangubat',
            'contact' => '+63-917-6789012',
            'hourly_rate' => 250.00 // PHP per day
        ]);

        $laborer2 = Laborer::create([
            'name' => 'Rosa Magtanim',
            'contact' => '+63-917-7890123',
            'hourly_rate' => 280.00 // PHP per day
        ]);

        $laborer3 = Laborer::create([
            'name' => 'Jose Ani',
            'contact' => '+63-917-8901234',
            'hourly_rate' => 300.00 // PHP per day
        ]);

        // Create rice plantings
        $planting1 = Planting::create([
            'field_id' => $field1->id,
            'rice_variety_id' => $ir64->id,
            'crop_type' => 'rice',
            'planting_date' => now()->subDays(45),
            'expected_harvest_date' => now()->addDays(70), // IR64 is 115 days total
            'status' => 'growing',
            'planting_method' => 'transplanting',
            'seed_rate' => 120.0, // kg per hectare
            'area_planted' => 2.5,
            'season' => 'wet',
            'notes' => 'First crop of wet season, using IR64 variety'
        ]);

        $planting2 = Planting::create([
            'field_id' => $field2->id,
            'rice_variety_id' => $ir64->id,
            'crop_type' => 'rice',
            'planting_date' => now()->subDays(30),
            'expected_harvest_date' => now()->addDays(85), // IR64 is 115 days total
            'status' => 'growing',
            'planting_method' => 'transplanting',
            'seed_rate' => 120.0, // kg per hectare
            'area_planted' => 2.0,
            'season' => 'wet',
            'notes' => 'Second field planting, slightly later start'
        ]);

        $planting3 = Planting::create([
            'field_id' => $field3->id,
            'rice_variety_id' => $psbRc82->id,
            'crop_type' => 'rice',
            'planting_date' => now()->subDays(60),
            'expected_harvest_date' => now()->addDays(60), // PSB Rc82 is 120 days total
            'status' => 'ready',
            'planting_method' => 'transplanting',
            'seed_rate' => 100.0, // kg per hectare
            'area_planted' => 3.2,
            'season' => 'wet',
            'notes' => 'Organic rice using PSB Rc82, ready for harvest'
        ]);

        // Create a harvested planting from previous season
        $planting4 = Planting::create([
            'field_id' => $field1->id,
            'rice_variety_id' => $nsicRc222->id,
            'crop_type' => 'rice',
            'planting_date' => now()->subDays(120),
            'expected_harvest_date' => now()->subDays(10),
            'actual_harvest_date' => now()->subDays(5),
            'status' => 'harvested',
            'planting_method' => 'direct_seeding',
            'seed_rate' => 140.0, // kg per hectare
            'area_planted' => 2.5,
            'season' => 'dry',
            'notes' => 'Previous dry season crop, successfully harvested'
        ]);

        // Create rice farming tasks
        Task::create([
            'planting_id' => $planting1->id,
            'task_type' => 'watering',
            'due_date' => now()->addDays(2),
            'description' => 'Water management for North Paddy - maintain 3-5cm water level',
            'status' => 'pending',
            'assigned_to' => $laborer1->id
        ]);

        Task::create([
            'planting_id' => $planting1->id,
            'task_type' => 'fertilizing',
            'due_date' => now()->addDays(5),
            'description' => 'Apply panicle fertilizer (urea) for tillering stage',
            'status' => 'pending',
            'assigned_to' => $laborer2->id
        ]);

        Task::create([
            'planting_id' => $planting2->id,
            'task_type' => 'weeding',
            'due_date' => now()->addDays(1),
            'description' => 'Manual weeding of South Paddy rice field',
            'status' => 'pending',
            'assigned_to' => $laborer1->id
        ]);

        Task::create([
            'planting_id' => $planting3->id,
            'task_type' => 'harvesting',
            'due_date' => now()->addDays(3),
            'description' => 'Harvest PSB Rc82 rice - grain moisture at 20-22%',
            'status' => 'pending',
            'assigned_to' => $laborer3->id
        ]);

        Task::create([
            'planting_id' => $planting2->id,
            'task_type' => 'pest_control',
            'due_date' => now()->addDays(7),
            'description' => 'Monitor and control brown planthopper in rice field',
            'status' => 'pending',
            'assigned_to' => $laborer2->id
        ]);

        // Create completed task
        Task::create([
            'planting_id' => $planting4->id,
            'task_type' => 'harvesting',
            'due_date' => now()->subDays(10),
            'description' => 'Harvest NSIC Rc222 rice - completed successfully',
            'status' => 'completed',
            'assigned_to' => $laborer2->id
        ]);

        // Get inventory categories
        $seedsCategory = \App\Models\InventoryCategory::where('code', 'SEEDS')->first();
        $fertilizerCategory = \App\Models\InventoryCategory::where('code', 'FERTILIZER')->first();
        $pesticidesCategory = \App\Models\InventoryCategory::where('code', 'PESTICIDE')->first();
        $produceCategory = \App\Models\InventoryCategory::where('code', 'PRODUCE')->first();

        // Create rice farming inventory items
        InventoryItem::create([
            'name' => 'IR64 Rice Seeds',
            'category' => 'seeds',
            'category_id' => $seedsCategory->id,
            'sku' => 'SEED-IR64-001',
            'brand' => 'PhilRice',
            'quantity' => 200.0,
            'price' => 85.00,
            'cost_per_unit' => 75.00,
            'unit' => 'kg',
            'min_stock' => 50.0,
            'reorder_point' => 75.0,
            'max_stock' => 500.0,
            'shelf_life_days' => 365,
            'storage_requirements' => [
                'temperature' => '15-20°C',
                'humidity' => 'Below 14%',
                'ventilation' => 'Good air circulation required'
            ],
            'supplier' => 'PhilRice Seed Center',
            'supplier_contact' => '+63-44-456-0285',
            'lead_time_days' => 7.0,
        ]);

        InventoryItem::create([
            'name' => 'PSB Rc82 Rice Seeds',
            'category' => 'seeds',
            'quantity' => 150.0,
            'price' => 95.00,
            'unit' => 'kg',
            'min_stock' => 30.0
        ]);

        InventoryItem::create([
            'name' => 'Urea Fertilizer (46-0-0)',
            'category' => 'fertilizer',
            'quantity' => 20.0,
            'price' => 1250.00,
            'unit' => 'bags',
            'min_stock' => 5.0
        ]);

        InventoryItem::create([
            'name' => 'Complete Fertilizer (14-14-14)',
            'category' => 'fertilizer',
            'quantity' => 15.0,
            'price' => 1350.00,
            'unit' => 'bags',
            'min_stock' => 3.0
        ]);

        InventoryItem::create([
            'name' => 'Rice Pesticide (Cypermethrin)',
            'category' => 'pesticide',
            'quantity' => 10.0,
            'price' => 450.00,
            'unit' => 'liters',
            'min_stock' => 3.0
        ]);

        InventoryItem::create([
            'name' => 'Herbicide (Butachlor)',
            'category' => 'pesticide',
            'quantity' => 8.0,
            'price' => 380.00,
            'unit' => 'liters',
            'min_stock' => 2.0
        ]);

        InventoryItem::create([
            'name' => 'Premium White Rice',
            'category' => 'produce',
            'quantity' => 2500.0,
            'price' => 45.00,
            'unit' => 'kg',
            'min_stock' => 0.0
        ]);

        InventoryItem::create([
            'name' => 'Organic Brown Rice',
            'category' => 'produce',
            'quantity' => 800.0,
            'price' => 65.00,
            'unit' => 'kg',
            'min_stock' => 0.0
        ]);

        InventoryItem::create([
            'name' => 'Rice Bran',
            'category' => 'produce',
            'quantity' => 500.0,
            'price' => 18.00,
            'unit' => 'kg',
            'min_stock' => 0.0
        ]);

        // Create rice harvests
        $harvest1 = Harvest::create([
            'planting_id' => $planting4->id,
            'yield' => 11250.0, // 4.5 tons per hectare * 2.5 hectares
            'harvest_date' => now()->subDays(7),
            'quality' => 'excellent'
        ]);

        // Create rice farming expenses
        Expense::create([
            'description' => 'IR64 rice seeds purchase for wet season',
            'amount' => 10200.00, // PHP
            'category' => 'seeds',
            'date' => now()->subDays(50),
            'planting_id' => $planting1->id
        ]);

        Expense::create([
            'description' => 'Urea fertilizer application - tillering stage',
            'amount' => 3750.00, // PHP
            'category' => 'fertilizer',
            'date' => now()->subDays(35),
            'planting_id' => $planting1->id
        ]);

        Expense::create([
            'description' => 'Labor costs - transplanting and weeding',
            'amount' => 8400.00, // PHP (3 workers x 7 days x 400 PHP/day)
            'category' => 'labor',
            'date' => now()->subDays(20),
            'planting_id' => $planting2->id
        ]);

        Expense::create([
            'description' => 'Pesticide application for brown planthopper control',
            'amount' => 1800.00, // PHP
            'category' => 'pesticide',
            'date' => now()->subDays(15),
            'planting_id' => $planting1->id
        ]);

        Expense::create([
            'description' => 'Irrigation system maintenance',
            'amount' => 2500.00, // PHP
            'category' => 'maintenance',
            'date' => now()->subDays(40),
            'planting_id' => $planting1->id
        ]);

        // Create weather logs for rice fields
        $fields = [$field1, $field2, $field3];
        $conditions = ['clear', 'cloudy', 'rainy', 'stormy', 'foggy'];
        
        foreach ($fields as $field) {
            for ($i = 0; $i < 14; $i++) { // 2 weeks of weather data
                WeatherLog::create([
                    'field_id' => $field->id,
                    'temperature' => rand(22, 32) + (rand(0, 9) / 10), // Tropical climate
                    'humidity' => rand(65, 90), // Higher humidity for rice
                    'wind_speed' => rand(3, 15) + (rand(0, 9) / 10),
                    'conditions' => $conditions[array_rand($conditions)],
                    'recorded_at' => now()->subDays($i)->setTime(rand(6, 18), 0, 0)
                ]);
            }
        }

        // Initialize growth stages for active plantings
        foreach ([$planting1, $planting2, $planting3] as $planting) {
            $planting->initializeGrowthStages();
        }

        echo "Rice Farming Database seeded successfully!\n";
        echo "Users created:\n";
        echo "- Admin: admin@ricefarm.com (password: password)\n";
        echo "- Farmer 1: juan@ricefarm.com (password: password)\n";
        echo "- Farmer 2: maria@ricefarm.com (password: password)\n";
        echo "- Buyer 1: buyer@ricemerchant.com (password: password)\n";
        echo "- Buyer 2: orders@goldengrain.com (password: password)\n";
        echo "\nRice varieties seeded: 5 varieties (IR64, PSB Rc82, NSIC Rc222, NSIC Rc216, Jasmine)\n";
        echo "Rice growth stages seeded: 8 stages (Land Prep to Harvest)\n";
        echo "Farms created: 2 rice farms with complete setup\n";
        echo "Fields created: 3 rice paddies\n";
        echo "Plantings created: 4 rice plantings (3 active, 1 harvested)\n";
    }
}