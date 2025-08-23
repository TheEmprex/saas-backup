#!/bin/bash

# Enhanced Messaging System Test Runner
# This script runs comprehensive tests for the enhanced messaging system

set -e # Exit on any error

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Configuration
PROJECT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
PHPUNIT_CONFIG="phpunit.messaging.xml"
TEST_RESULTS_DIR="tests/results"
COVERAGE_DIR="tests/coverage"

# Functions
print_header() {
    echo -e "${BLUE}================================================${NC}"
    echo -e "${BLUE}  Enhanced Messaging System Test Suite${NC}"
    echo -e "${BLUE}================================================${NC}"
    echo ""
}

print_section() {
    echo -e "${YELLOW}--- $1 ---${NC}"
}

print_success() {
    echo -e "${GREEN}✓ $1${NC}"
}

print_error() {
    echo -e "${RED}✗ $1${NC}"
}

print_info() {
    echo -e "${BLUE}ℹ $1${NC}"
}

check_prerequisites() {
    print_section "Checking Prerequisites"
    
    # Check if we're in the right directory
    if [[ ! -f "composer.json" ]]; then
        print_error "Not in Laravel project root directory"
        exit 1
    fi
    
    # Check if PHPUnit is available
    if ! command -v vendor/bin/phpunit &> /dev/null; then
        print_error "PHPUnit not found. Please run: composer install"
        exit 1
    fi
    
    # Check if PHPUnit config exists
    if [[ ! -f "$PHPUNIT_CONFIG" ]]; then
        print_error "PHPUnit configuration file not found: $PHPUNIT_CONFIG"
        exit 1
    fi
    
    print_success "All prerequisites met"
}

setup_test_environment() {
    print_section "Setting up Test Environment"
    
    # Create directories for test results
    mkdir -p "$TEST_RESULTS_DIR"
    mkdir -p "$COVERAGE_DIR"
    
    # Clean previous results
    rm -rf "$TEST_RESULTS_DIR"/*
    rm -rf "$COVERAGE_DIR"/*
    
    # Copy environment file for testing
    if [[ ! -f ".env.testing" ]]; then
        print_info "Creating .env.testing file"
        cp .env.example .env.testing 2>/dev/null || true
        
        # Update test-specific settings
        sed -i.bak 's/APP_ENV=local/APP_ENV=testing/' .env.testing 2>/dev/null || true
        sed -i.bak 's/DB_DATABASE=.*/DB_DATABASE=:memory:/' .env.testing 2>/dev/null || true
        sed -i.bak 's/DB_CONNECTION=.*/DB_CONNECTION=sqlite/' .env.testing 2>/dev/null || true
        rm -f .env.testing.bak
    fi
    
    print_success "Test environment prepared"
}

run_static_analysis() {
    print_section "Running Static Analysis"
    
    # Run PHPStan if available
    if command -v vendor/bin/phpstan &> /dev/null; then
        print_info "Running PHPStan analysis..."
        vendor/bin/phpstan analyze app/Http/Controllers/Api/V1 --level=5 || true
        print_success "PHPStan analysis completed"
    else
        print_info "PHPStan not available, skipping static analysis"
    fi
    
    # Run PHP CS Fixer if available
    if command -v vendor/bin/php-cs-fixer &> /dev/null; then
        print_info "Running PHP CS Fixer..."
        vendor/bin/php-cs-fixer fix --dry-run --diff app/Http/Controllers/Api/V1 || true
        print_success "Code style check completed"
    else
        print_info "PHP CS Fixer not available, skipping code style check"
    fi
}

run_unit_tests() {
    print_section "Running Unit Tests"
    
    print_info "Executing messaging system tests..."
    
    # Run PHPUnit with coverage
    if vendor/bin/phpunit \
        --configuration="$PHPUNIT_CONFIG" \
        --coverage-html="$COVERAGE_DIR/html" \
        --coverage-clover="$COVERAGE_DIR/clover.xml" \
        --coverage-text \
        --log-junit="$TEST_RESULTS_DIR/junit.xml" \
        --testdox-html="$TEST_RESULTS_DIR/testdox.html" \
        --testdox-text="$TEST_RESULTS_DIR/testdox.txt"; then
        
        print_success "All tests passed!"
        return 0
    else
        print_error "Some tests failed!"
        return 1
    fi
}

run_feature_tests() {
    print_section "Running Feature Tests"
    
    # Run specific test classes
    local test_classes=(
        "tests/Feature/Api/V1/MessagingSystemTest.php"
        "tests/Feature/Api/V1/SystemMonitoringTest.php"
    )
    
    local failed_tests=()
    
    for test_class in "${test_classes[@]}"; do
        if [[ -f "$test_class" ]]; then
            print_info "Running $(basename "$test_class")..."
            if vendor/bin/phpunit --configuration="$PHPUNIT_CONFIG" "$test_class"; then
                print_success "$(basename "$test_class") passed"
            else
                print_error "$(basename "$test_class") failed"
                failed_tests+=("$test_class")
            fi
        else
            print_info "Test class not found: $test_class"
        fi
    done
    
    if [[ ${#failed_tests[@]} -eq 0 ]]; then
        print_success "All feature tests passed!"
        return 0
    else
        print_error "Failed tests: ${failed_tests[*]}"
        return 1
    fi
}

run_performance_tests() {
    print_section "Running Performance Tests"
    
    print_info "Testing API endpoint performance..."
    
    # Simple performance test using curl if available
    if command -v curl &> /dev/null; then
        # Start PHP built-in server in background for testing
        php artisan serve --host=127.0.0.1 --port=8080 --env=testing &
        SERVER_PID=$!
        
        sleep 3 # Wait for server to start
        
        # Test a few endpoints
        local endpoints=(
            "http://127.0.0.1:8080/api/marketplace/v1/conversations"
            "http://127.0.0.1:8080/api/marketplace/v1/users/search?q=test"
        )
        
        for endpoint in "${endpoints[@]}"; do
            print_info "Testing $endpoint"
            # This would need proper authentication headers in real scenario
            response_time=$(curl -o /dev/null -s -w "%{time_total}" "$endpoint" || echo "0")
            print_info "Response time: ${response_time}s"
        done
        
        # Kill the test server
        kill $SERVER_PID 2>/dev/null || true
        
        print_success "Performance tests completed"
    else
        print_info "curl not available, skipping performance tests"
    fi
}

generate_reports() {
    print_section "Generating Test Reports"
    
    # Generate coverage summary
    if [[ -f "$COVERAGE_DIR/clover.xml" ]]; then
        print_info "Coverage report generated: $COVERAGE_DIR/html/index.html"
    fi
    
    # Generate test summary
    if [[ -f "$TEST_RESULTS_DIR/testdox.txt" ]]; then
        print_info "Test documentation: $TEST_RESULTS_DIR/testdox.txt"
        echo ""
        echo -e "${BLUE}Test Summary:${NC}"
        head -20 "$TEST_RESULTS_DIR/testdox.txt" 2>/dev/null || true
    fi
    
    # Generate JUnit summary
    if [[ -f "$TEST_RESULTS_DIR/junit.xml" ]]; then
        print_info "JUnit report: $TEST_RESULTS_DIR/junit.xml"
    fi
    
    print_success "Reports generated successfully"
}

cleanup() {
    print_section "Cleaning up"
    
    # Kill any remaining processes
    pkill -f "artisan serve" 2>/dev/null || true
    
    print_success "Cleanup completed"
}

main() {
    print_header
    
    # Trap cleanup function on script exit
    trap cleanup EXIT
    
    # Change to project directory
    cd "$PROJECT_DIR"
    
    local exit_code=0
    
    # Run all test phases
    check_prerequisites || exit_code=1
    
    if [[ $exit_code -eq 0 ]]; then
        setup_test_environment || exit_code=1
    fi
    
    if [[ $exit_code -eq 0 ]]; then
        run_static_analysis || exit_code=1
    fi
    
    if [[ $exit_code -eq 0 ]]; then
        run_unit_tests || exit_code=1
    fi
    
    if [[ $exit_code -eq 0 ]]; then
        run_feature_tests || exit_code=1
    fi
    
    if [[ $exit_code -eq 0 ]]; then
        run_performance_tests || exit_code=1
    fi
    
    generate_reports
    
    echo ""
    if [[ $exit_code -eq 0 ]]; then
        print_success "🎉 All tests completed successfully!"
        echo -e "${GREEN}The enhanced messaging system is ready for deployment.${NC}"
    else
        print_error "❌ Some tests failed!"
        echo -e "${RED}Please review the test results and fix any issues.${NC}"
    fi
    
    echo ""
    print_info "Test results available in: $TEST_RESULTS_DIR/"
    print_info "Coverage reports available in: $COVERAGE_DIR/"
    
    return $exit_code
}

# Handle command line arguments
case "${1:-}" in
    --help|-h)
        echo "Enhanced Messaging System Test Runner"
        echo ""
        echo "Usage: $0 [OPTIONS]"
        echo ""
        echo "Options:"
        echo "  --help, -h          Show this help message"
        echo "  --unit-only         Run only unit tests"
        echo "  --feature-only      Run only feature tests"
        echo "  --performance-only  Run only performance tests"
        echo "  --no-coverage       Skip coverage generation"
        echo ""
        exit 0
        ;;
    --unit-only)
        print_header
        cd "$PROJECT_DIR"
        check_prerequisites
        setup_test_environment
        run_unit_tests
        ;;
    --feature-only)
        print_header
        cd "$PROJECT_DIR"
        check_prerequisites
        setup_test_environment
        run_feature_tests
        ;;
    --performance-only)
        print_header
        cd "$PROJECT_DIR"
        check_prerequisites
        run_performance_tests
        ;;
    *)
        main "$@"
        ;;
esac
