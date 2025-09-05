#!/bin/bash

echo "🌾 Farm Operations Management System - Installation Script"
echo "========================================================="

# Check if PHP is installed
if ! command -v php &> /dev/null; then
    echo "❌ PHP is not installed. Please install PHP 8.2 or higher."
    exit 1
fi

# Check if Composer is installed
if ! command -v composer &> /dev/null; then
    echo "❌ Composer is not installed. Please install Composer."
    exit 1
fi

# Check if Node.js is installed
if ! command -v node &> /dev/null; then
    echo "❌ Node.js is not installed. Please install Node.js 18 or higher."
    exit 1
fi

# Check if NPM is installed
if ! command -v npm &> /dev/null; then
    echo "❌ NPM is not installed. Please install NPM."
    exit 1
fi

echo "✅ All prerequisites are installed"
echo ""

# Install PHP dependencies
echo "📦 Installing PHP dependencies..."
composer install

if [ $? -ne 0 ]; then
    echo "❌ Failed to install PHP dependencies"
    exit 1
fi

# Install Node.js dependencies
echo "📦 Installing Node.js dependencies..."
npm install

if [ $? -ne 0 ]; then
    echo "❌ Failed to install Node.js dependencies"
    exit 1
fi

# Copy environment file
if [ ! -f .env ]; then
    echo "📝 Creating environment file..."
    cp .env.example .env
    echo "✅ Environment file created. Please edit .env with your configurations."
else
    echo "⚠️  Environment file already exists."
fi

# Generate application key
echo "🔑 Generating application key..."
php artisan key:generate

# Create storage link (if needed)
echo "🔗 Creating storage link..."
php artisan storage:link 2>/dev/null || echo "Storage link already exists or not needed"

echo ""
echo "🎉 Installation completed successfully!"
echo ""
echo "📋 Next steps:"
echo "1. Edit .env file with your MongoDB Atlas connection string"
echo "2. Add your OpenWeatherMap API key to .env"
echo "3. Run 'php artisan db:seed' to populate sample data"
echo "4. Run 'npm run dev' to build frontend assets"
echo "5. Run 'php artisan serve' to start the development server"
echo ""
echo "📚 Default login credentials (after seeding):"
echo "   Admin: admin@farmops.com / password"
echo "   Farmer: john@farmops.com / password"
echo "   Buyer: alice@farmops.com / password"
echo ""
echo "🌐 Visit http://localhost:8000 to access the application"