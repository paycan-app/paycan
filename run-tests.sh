#!/bin/bash

echo "🧪 PayCan Test Suite Runner"
echo "=========================="
echo ""

# Check if PHP and Artisan are available
if ! command -v php &> /dev/null; then
    echo "❌ PHP is not installed or not in PATH"
    exit 1
fi

if [ ! -f "artisan" ]; then
    echo "❌ Laravel artisan not found. Make sure you're in the project root."
    exit 1
fi

echo "🔧 Setting up test environment..."

# Copy .env.example to .env.testing if it doesn't exist
if [ ! -f ".env.testing" ]; then
    cp .env.example .env.testing
    echo "✅ Created .env.testing file"
fi

# Generate application key for testing
php artisan key:generate --env=testing --force

echo "🗄️  Preparing test database..."
php artisan migrate:fresh --env=testing --force

echo ""
echo "🚀 Running comprehensive test suite..."
echo ""

# Run tests with different options based on arguments
case "${1:-all}" in
    "quick")
        echo "⚡ Running quick test suite..."
        php artisan test --testsuite=Feature --stop-on-failure
        ;;
    "coverage")
        echo "📊 Running tests with coverage..."
        php artisan test --coverage --min=80
        ;;
    "parallel")
        echo "🔄 Running tests in parallel..."
        php artisan test --parallel
        ;;
    "api")
        echo "🌐 Running API tests only..."
        php artisan test tests/Feature/Api/
        ;;
    "portal")
        echo "🚪 Running Portal tests only..."
        php artisan test tests/Feature/Portal/
        ;;
    "all"|*)
        echo "🎯 Running all tests..."
        php artisan test
        ;;
esac

echo ""
echo "✅ Test suite completed!"
echo ""
echo "📋 Available test commands:"
echo "  ./run-tests.sh quick     - Quick test run (stop on first failure)"
echo "  ./run-tests.sh coverage  - Run with coverage report"
echo "  ./run-tests.sh parallel  - Run tests in parallel"
echo "  ./run-tests.sh api       - Run API tests only"
echo "  ./run-tests.sh portal    - Run Portal tests only"
echo "  ./run-tests.sh all       - Run all tests (default)"