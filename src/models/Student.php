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
            $stmt = $this->conn->prepare("SELECT id, full_name, username FROM students WHERE id = :id");
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            return false;
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
}
?>
