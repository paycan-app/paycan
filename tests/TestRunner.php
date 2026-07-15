<?php

namespace Tests;

/**
 * E2E Test Runner for Laravel 12 + Vue 3 Payment System
 *
 * Usage: php tests/TestRunner.php [test_group]
 *
 * Available test groups:
 * - all: Run all E2E tests
 * - subscription: Subscription flow tests
 * - physical: Physical product tests
 * - digital: Digital product tests
 * - service: Service product tests
 * - validation: Validation and error tests
 */
class TestRunner
{
    private $testGroups = [
        'subscription' => [
            'tests/Feature/SubscriptionFlowTest.php',
        ],
        'physical' => [
            'tests/Feature/PhysicalProductFlowTest.php',
        ],
        'digital' => [
            'tests/Feature/DigitalProductFlowTest.php',
        ],
        'service' => [
            'tests/Feature/ServiceProductFlowTest.php',
        ],
        'validation' => [
            'tests/Feature/ApiValidationAndErrorTest.php',
        ],
    ];

    public function run($group = 'all')
    {
        echo "🚀 Starting E2E Test Suite for Laravel 12 + Vue 3 Payment System\n";
        echo "═══════════════════════════════════════════════════════════════\n\n";

        if ($group === 'all') {
            $this->runAllTests();
        } elseif (isset($this->testGroups[$group])) {
            $this->runTestGroup($group);
        } else {
            $this->showUsage();

            return;
        }

        echo "\n✅ E2E Test Suite Complete!\n";
    }

    private function runAllTests()
    {
        foreach ($this->testGroups as $groupName => $tests) {
            $this->runTestGroup($groupName);
        }
    }

    private function runTestGroup($groupName)
    {
        echo "📋 Running {$groupName} tests...\n";
        echo "─────────────────────────────────\n";

        $tests = $this->testGroups[$groupName];

        foreach ($tests as $testFile) {
            $this->runTest($testFile);
        }

        echo "\n";
    }

    private function runTest($testFile)
    {
        $command = "php artisan test {$testFile} --colors=always";
        echo "▶️  Running: {$testFile}\n";

        $output = shell_exec($command.' 2>&1');
        echo $output;
    }

    private function showUsage()
    {
        echo "Usage: php tests/TestRunner.php [test_group]\n\n";
        echo "Available test groups:\n";
        echo "- all: Run all E2E tests\n";

        foreach ($this->testGroups as $group => $tests) {
            echo "- {$group}: ".$this->getGroupDescription($group)."\n";
        }

        echo "\nExample: php tests/TestRunner.php subscription\n";
    }

    private function getGroupDescription($group)
    {
        $descriptions = [
            'subscription' => 'Test subscription lifecycle (register → subscribe → cancel → resume)',
            'physical' => 'Test physical product purchase and shipping',
            'digital' => 'Test digital product purchase and instant delivery',
            'service' => 'Test service product booking and fulfillment',
            'validation' => 'Test API validation, security, and error handling',
        ];

        return $descriptions[$group] ?? 'Test group';
    }
}

// Run the test runner
$group = $argv[1] ?? 'all';
$runner = new \Tests\TestRunner;
$runner->run($group);
