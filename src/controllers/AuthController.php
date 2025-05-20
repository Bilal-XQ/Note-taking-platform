<?php
require_once __DIR__ . '/../models/Student.php';
require_once __DIR__ . '/../models/Module.php';

class AuthController {
    private $studentModel;

    public function __construct() {
        $this->studentModel = new Student();
    }

    // Helper function to start session safely
    private function startSession() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function login($username, $password) {
        // First check if it's an admin
        $admin = $this->checkAdminLogin($username, $password);
        if ($admin) {
            $this->startSession();
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            $_SESSION['is_admin'] = true;
            $_SESSION['is_logged_in'] = true;
            return 'admin';
        }

        // If not admin, check if it's a student
        $student = $this->studentModel->login($username, $password);
        if ($student) {
            // Update the last_login time
            try {
                $conn = getDBConnection();
                $stmt = $conn->prepare("UPDATE students SET last_login = CURRENT_TIMESTAMP WHERE id = :id");
                $stmt->bindParam(':id', $student['id']);
                $stmt->execute();
            } catch (PDOException $e) {
                // Continue even if this fails (the column might not exist yet)
            }

            $this->startSession();
            $_SESSION['student_id'] = $student['id'];
            $_SESSION['student_name'] = $student['full_name'];
            $_SESSION['is_admin'] = false;
            $_SESSION['is_logged_in'] = true;
            return 'student';
        }

        return false;
    }

    private function checkAdminLogin($username, $password) {
        try {
            $conn = getDBConnection();
            $stmt = $conn->prepare("SELECT id, username, password FROM admins WHERE username = :username");
            $stmt->bindParam(':username', $username);
            $stmt->execute();

            if($stmt->rowCount() > 0) {
                $admin = $stmt->fetch(PDO::FETCH_ASSOC);

                // Simple plain text password comparison
                if($password === $admin['password']) {
                    return [
                        'id' => $admin['id'],
                        'username' => $admin['username']
                    ];
                }
            }
            return false;
        } catch(PDOException $e) {
            return false;
        }
    }

    public function logout() {
        $this->startSession();
        $_SESSION = array();
        session_destroy();

        // Clear remember me cookie if exists
        if(isset($_COOKIE['remember_user'])) {
            setcookie('remember_user', '', time() - 3600, "/");
        }

        return true;
    }

    public function isLoggedIn() {
        $this->startSession();
        return isset($_SESSION['is_logged_in']) && $_SESSION['is_logged_in'] === true;
    }

    public function isAdmin() {
        $this->startSession();
        return isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true;
    }

    public function getCurrentStudentId() {
        $this->startSession();
        return isset($_SESSION['student_id']) ? $_SESSION['student_id'] : null;
    }

    public function getCurrentStudentName() {
        $this->startSession();
        return isset($_SESSION['student_name']) ? $_SESSION['student_name'] : null;
    }

    public function getLastLoginTime() {
        $studentId = $this->getCurrentStudentId();
        if (!$studentId) {
            return null;
        }

        return $this->studentModel->getLastLogin($studentId);
    }

    public function getCurrentStudent() {
        $studentId = $this->getCurrentStudentId();
        if (!$studentId) {
            return null;
        }

        return $this->studentModel->getStudentById($studentId);
    }

    public function updateStudentInfo($fullName, $username) {
        $studentId = $this->getCurrentStudentId();
        if (!$studentId) {
            return [
                'success' => false,
                'message' => 'You must be logged in to update your information'
            ];
        }

        $result = $this->studentModel->updateStudentInfo($studentId, $fullName, $username);

        if ($result['success']) {
            // Update session with new name
            $this->startSession();
            $_SESSION['student_name'] = $fullName;
        }

        return $result;
    }

    public function updateStudentPassword($currentPassword, $newPassword) {
        $studentId = $this->getCurrentStudentId();
        if (!$studentId) {
            return [
                'success' => false,
                'message' => 'You must be logged in to update your password'
            ];
        }

        return $this->studentModel->updateStudentPassword($studentId, $currentPassword, $newPassword);
    }
}
?>
