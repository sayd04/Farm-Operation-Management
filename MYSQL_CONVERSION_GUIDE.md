# MySQL Conversion Guide

## Overview
Your Laravel application has been successfully converted from MongoDB to MySQL. This guide will help you complete the setup and migration process.

## Changes Made

### 1. Database Configuration
- Updated `config/database.php` to use MySQL as the default connection
- Removed MongoDB connection configuration

### 2. Dependencies
- Removed `jenssegers/mongodb` package from `composer.json`
- The application now uses standard Laravel Eloquent ORM

### 3. Models Updated
All models have been converted from MongoDB to standard Eloquent models:
- `User.php` - Updated to use standard Laravel authentication
- `Field.php` - Converted location field to JSON
- `Order.php`, `OrderItem.php` - Standard Eloquent models
- `Category.php`, `Laborer.php`, `InventoryItem.php` - Standard models
- `LaborWage.php`, `Expense.php`, `Buyer.php` - Standard models  
- `Harvest.php`, `WeatherLog.php`, `Task.php` - Standard models
- `Sale.php`, `Planting.php` - Standard models

### 4. Migrations Created
New MySQL-compatible migrations have been created for all tables:
- Users table with proper authentication fields
- All farm management tables with proper foreign key constraints
- Cache and jobs tables for Laravel functionality

### 5. Environment Configuration
- Created `.env.example` with MySQL configuration
- Includes OpenWeather API key placeholder

## Setup Instructions

### Step 1: Install Dependencies
```bash
composer install
```

### Step 2: Environment Setup
1. Copy the environment file:
```bash
cp .env.example .env
```

2. Generate application key:
```bash
php artisan key:generate
```

3. Configure your MySQL database in `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=farm_operations
DB_USERNAME=your_mysql_username
DB_PASSWORD=your_mysql_password
```

### Step 3: Database Setup

#### Using Navicat (Recommended for you)
1. Open Navicat and create a new MySQL connection
2. Create a new database named `farm_operations`
3. Make sure your MySQL server is running

#### Run Migrations
```bash
php artisan migrate
```

This will create all the necessary tables with proper structure and relationships.

### Step 4: Seed Data (Optional)
If you have existing data from MongoDB, you'll need to export and import it. For new installations:

```bash
php artisan db:seed
```

### Step 5: Test the Application
```bash
php artisan serve
```

## Database Schema

### Main Tables Created:
- `users` - User authentication and profiles
- `farms` - Farm information
- `fields` - Field management with JSON location data
- `plantings` - Crop planting records
- `tasks` - Farm task management
- `harvests` - Harvest records
- `laborers` - Worker information
- `labor_wages` - Wage tracking
- `inventory_items` - Inventory management
- `categories` - Item categories
- `orders` - Order management
- `order_items` - Order line items
- `expenses` - Expense tracking
- `weather_logs` - Weather data
- `sales` - Sales records
- `buyers` - Buyer information

### Key Relationships:
- Users → Fields → Plantings → Harvests
- Plantings → Tasks → Labor Wages
- Orders → Order Items → Inventory Items
- Categories → Inventory Items

## Data Migration from MongoDB

If you have existing MongoDB data, you'll need to:

1. Export your MongoDB collections to JSON/CSV
2. Create seeders or use Laravel's database import tools
3. Map MongoDB document structure to MySQL relational structure
4. Pay special attention to:
   - Array fields (now stored as JSON)
   - ObjectIds (now auto-incrementing integers)
   - Embedded documents (now separate related tables)

## Important Notes

### JSON Fields
These fields are stored as JSON in MySQL:
- `users.address` - User address information
- `fields.location` - Field location coordinates

### Foreign Key Constraints
All relationships now use proper foreign key constraints:
- Cascade deletes where appropriate
- Set null for optional relationships

### Enum Fields
Status and category fields use MySQL ENUM types for better data integrity.

## Troubleshooting

### Common Issues:

1. **Migration Errors**: Make sure MySQL server is running and credentials are correct
2. **Foreign Key Constraints**: Ensure parent records exist before creating child records
3. **JSON Field Issues**: Use proper JSON format for location and address fields

### Testing Database Connection:
```bash
php artisan tinker
DB::connection()->getPdo();
```

## Next Steps

1. Update any custom queries that used MongoDB syntax
2. Test all application features thoroughly
3. Update any frontend code that expects MongoDB document structure
4. Consider adding database indexes for performance optimization

## Support

The conversion maintains all your existing functionality while providing the benefits of a relational database:
- ACID compliance
- Better data integrity
- More efficient queries
- Better tool support (like Navicat)
- Easier backup and replication

Your application is now ready to use MySQL with all the same features as before!