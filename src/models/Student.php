<?php
require_once __DIR__ . '/../../config/database.php';

class Student {
    private $conn;

    public function __construct() {
        $this->conn = getDBConnection();
    }

    public function login($username, $password) {
        try {
            $stmt = $this->conn->prepare("SELECT id, full_name, password FROM students WHERE username = :username");
            $stmt->bindParam(':username', $username);
            $stmt->execute();

            if($stmt->rowCount() > 0) {
                $student = $stmt->fetch(PDO::FETCH_ASSOC);

                // Simple plain text password comparison
                if($password === $student['password']) {
                    return [
                        'id' => $student['id'],
                        'full_name' => $student['full_name']
                    ];
                }
            }
            return false;
        } catch(PDOException $e) {
            return false;
        }
    }

    public function getStudentById($id) {
        try {
            $stmt = $this->conn->prepare("SELECT id, full_name, username, last_login FROM students WHERE id = :id");
            $stmt->bindParam(':id', $id);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            return false;
        }
    }

    public function getLastLogin($id) {
        try {
            $stmt = $this->conn->prepare("SELECT last_login FROM students WHERE id = :id");
            $stmt->bindParam(':id', $id);
            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ? $result['last_login'] : null;
        } catch(PDOException $e) {
            return null;
        }
    }

    public function getStudentModules($studentId) {
        try {
            $stmt = $this->conn->prepare("
                SELECT m.id, m.name 
                FROM modules m 
                INNER JOIN student_modules sm ON m.id = sm.module_id 
                WHERE sm.student_id = :studentId
                ORDER BY m.name
            ");
            $stmt->bindParam(':studentId', $studentId);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            return [];
        }
    }

    public function updateStudentInfo($studentId, $fullName, $username) {
        try {
            // Check if username is already taken by another student
            $checkStmt = $this->conn->prepare("
                SELECT id FROM students WHERE username = :username AND id != :studentId
            ");
            $checkStmt->bindParam(':username', $username);
            $checkStmt->bindParam(':studentId', $studentId);
            $checkStmt->execute();

            if ($checkStmt->rowCount() > 0) {
                return [
                    'success' => false,
                    'message' => 'Username is already taken by another student'
                ];
            }

            // Update student information
            $stmt = $this->conn->prepare("
                UPDATE students 
                SET full_name = :fullName, username = :username, updated_at = NOW() 
                WHERE id = :studentId
            ");
            $stmt->bindParam(':fullName', $fullName);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':studentId', $studentId);

            if ($stmt->execute()) {
                return [
                    'success' => true,
                    'message' => 'Student information updated successfully'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Failed to update student information'
                ];
            }
        } catch(PDOException $e) {
            return [
                'success' => false,
                'message' => 'Database error: ' . $e->getMessage()
            ];
        }
    }

    public function updateStudentPassword($studentId, $currentPassword, $newPassword) {
        try {
            // Verify current password
            $checkStmt = $this->conn->prepare("
                SELECT password FROM students WHERE id = :studentId
            ");
            $checkStmt->bindParam(':studentId', $studentId);
            $checkStmt->execute();

            $student = $checkStmt->fetch(PDO::FETCH_ASSOC);
            if (!$student) {
                return [
                    'success' => false,
                    'message' => 'Student not found'
                ];
            }

            // Simple plain text password comparison
            if ($currentPassword !== $student['password']) {
                return [
                    'success' => false,
                    'message' => 'Current password is incorrect'
                ];
            }

            // Update password
            $stmt = $this->conn->prepare("
                UPDATE students 
                SET password = :newPassword, updated_at = NOW() 
                WHERE id = :studentId
            ");
            $stmt->bindParam(':newPassword', $newPassword);
            $stmt->bindParam(':studentId', $studentId);

            if ($stmt->execute()) {
                return [
                    'success' => true,
                    'message' => 'Password updated successfully'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Failed to update password'
                ];
            }
        } catch(PDOException $e) {
            return [
                'success' => false,
                'message' => 'Database error: ' . $e->getMessage()
            ];
        }
    }
}
?>
