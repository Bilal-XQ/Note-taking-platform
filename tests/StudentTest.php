<?php
// Include the Student model
require_once __DIR__ . '/../src/models/Student.php';
// Create a mock Student class that extends the real Student class
class MockStudent extends Student {
    // Override the constructor to use a mock database connection
    public function __construct() {
        // Create a mock PDO object
        $this->conn = new class extends PDO {
            private $prepareReturnValue;
            private $mockData = [];

            public function __construct() {
                // Call parent constructor with SQLite in-memory database
                parent::__construct('sqlite::memory:');
            }

            // Mock the prepare method to return a custom statement
            public function prepare($statement, $options = null) {
                $this->prepareReturnValue = new class($this) {
                    private $parent;
                    private $params = [];
                    private $mockResult = [];
                    private $rowCount = 0;

                    public function __construct($parent) {
                        $this->parent = $parent;
                    }

                    public function bindParam($param, &$value) {
                        $this->params[$param] = $value;
                        return true;
                    }

                    public function execute() {
                        // Simulate different query results based on the parameters
                        if (strpos($this->parent->lastQuery, 'SELECT id, full_name, password FROM students') !== false) {
                            if ($this->params[':username'] === 'testuser') {
                                $this->mockResult = [
                                    'id' => 1,
                                    'full_name' => 'Test User',
                                    'password' => 'password123'
                                ];
                                $this->rowCount = 1;
                            } else {
                                $this->mockResult = [];
                                $this->rowCount = 0;
                            }
                        }
                        return true;
                    }

                    public function fetch() {
                        return $this->mockResult ?: false;
                    }

                    public function fetchAll() {
                        return [$this->mockResult];
                    }

                    public function rowCount() {
                        return $this->rowCount;
                    }
                };

                $this->lastQuery = $statement;
                return $this->prepareReturnValue;
            }
        };
    }
}

// Test class
class StudentTest {
    private $student;

    public function __construct() {
        // Use the mock student class instead of the real one
        $this->student = new MockStudent();
    }

    public function testLogin() {
        // Test successful login
        $result = $this->student->login('testuser', 'password123');

        if ($result && $result['id'] === 1 && $result['full_name'] === 'Test User') {
            echo "✅ Login test passed: Successfully logged in with valid credentials\n";
        } else {
            echo "❌ Login test failed: Could not log in with valid credentials\n";
        }

        // Test failed login with invalid username
        $result = $this->student->login('invaliduser', 'password123');

        if ($result === false) {
            echo "✅ Login test passed: Correctly rejected invalid username\n";
        } else {
            echo "❌ Login test failed: Accepted invalid username\n";
        }
    }

    public function runAllTests() {
        echo "Running Student model tests...\n";
        $this->testLogin();
        echo "All tests completed.\n";
    }
}

// Run the tests
$tester = new StudentTest();
$tester->runAllTests();
?>
