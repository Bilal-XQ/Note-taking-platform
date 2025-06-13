<?php
/**
 * Simple Test Example
 * 
 * This file demonstrates a basic approach to testing PHP code without external dependencies.
 * It uses a simple function-based approach rather than a full testing framework.
 */

// Function to be tested
function validateUsername($username) {
    // Username must be between 3 and 20 characters
    if (strlen($username) < 3 || strlen($username) > 20) {
        return false;
    }
    
    // Username must contain only alphanumeric characters and underscores
    if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        return false;
    }
    
    return true;
}

// Test function
function testValidateUsername() {
    $testCases = [
        // Valid usernames
        ['username' => 'user123', 'expected' => true, 'description' => 'Valid alphanumeric username'],
        ['username' => 'john_doe', 'expected' => true, 'description' => 'Valid username with underscore'],
        ['username' => 'admin', 'expected' => true, 'description' => 'Valid short username'],
        
        // Invalid usernames
        ['username' => 'ab', 'expected' => false, 'description' => 'Too short username'],
        ['username' => 'this_username_is_way_too_long', 'expected' => false, 'description' => 'Too long username'],
        ['username' => 'user@name', 'expected' => false, 'description' => 'Username with special characters'],
        ['username' => 'user name', 'expected' => false, 'description' => 'Username with space']
    ];
    
    $passedTests = 0;
    $totalTests = count($testCases);
    
    echo "Running username validation tests...\n";
    
    foreach ($testCases as $index => $test) {
        $result = validateUsername($test['username']);
        $passed = $result === $test['expected'];
        
        echo ($passed ? "✅" : "❌") . " Test " . ($index + 1) . ": " . $test['description'] . "\n";
        echo "   Input: '" . $test['username'] . "'\n";
        echo "   Expected: " . ($test['expected'] ? 'true' : 'false') . ", Got: " . ($result ? 'true' : 'false') . "\n";
        
        if ($passed) {
            $passedTests++;
        }
    }
    
    echo "\nTest Results: $passedTests/$totalTests tests passed\n";
    
    return $passedTests === $totalTests;
}

// Run the test
$testsPassed = testValidateUsername();
echo "\nOverall Test Result: " . ($testsPassed ? "All tests passed! 🎉" : "Some tests failed! 😢") . "\n";
?>