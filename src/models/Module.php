<?php
require_once __DIR__ . '/../../config/database.php';

class Module {
    private $conn;

    public function __construct() {
        $this->conn = getDBConnection();
    }

    public function getAllModules() {
        try {
            $stmt = $this->conn->prepare("SELECT id, name FROM modules ORDER BY name");
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            return [];
        }
    }

    public function getModuleById($id) {
        try {
            $stmt = $this->conn->prepare("SELECT id, name FROM modules WHERE id = :id");
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

    public function createModule($name, $studentId) {
        try {
            // Begin transaction
            $this->conn->beginTransaction();

            // First check if module with this name already exists
            $stmt = $this->conn->prepare("SELECT id FROM modules WHERE name = :name");
            $stmt->bindParam(':name', $name);
            $stmt->execute();

            $module = $stmt->fetch(PDO::FETCH_ASSOC);
            $moduleId = null;

            if ($module) {
                // Module exists, get its ID
                $moduleId = $module['id'];

                // Check if student already has this module
                $checkStmt = $this->conn->prepare("
                    SELECT 1 FROM student_modules 
                    WHERE student_id = :studentId AND module_id = :moduleId
                ");
                $checkStmt->bindParam(':studentId', $studentId);
                $checkStmt->bindParam(':moduleId', $moduleId);
                $checkStmt->execute();

                // If student doesn't have this module, assign it
                if ($checkStmt->rowCount() === 0) {
                    $assignStmt = $this->conn->prepare("
                        INSERT INTO student_modules (student_id, module_id)
                        VALUES (:studentId, :moduleId)
                    ");
                    $assignStmt->bindParam(':studentId', $studentId);
                    $assignStmt->bindParam(':moduleId', $moduleId);
                    $assignStmt->execute();
                } else {
                    // Module already exists and is assigned to student
                    $this->conn->commit();
                    return $moduleId;
                }
            } else {
                // Create new module
                $stmt = $this->conn->prepare("
                    INSERT INTO modules (name) 
                    VALUES (:name)
                ");
                $stmt->bindParam(':name', $name);
                $stmt->execute();

                $moduleId = $this->conn->lastInsertId();

                // Assign module to student
                $assignStmt = $this->conn->prepare("
                    INSERT INTO student_modules (student_id, module_id)
                    VALUES (:studentId, :moduleId)
                ");
                $assignStmt->bindParam(':studentId', $studentId);
                $assignStmt->bindParam(':moduleId', $moduleId);
                $assignStmt->execute();
            }

            // Commit transaction
            $this->conn->commit();
            return $moduleId;
        } catch(PDOException $e) {
            // Rollback on error
            $this->conn->rollBack();
            return false;
        }
    }

    public function deleteModule($moduleId, $studentId) {
        try {
            // Begin transaction
            $this->conn->beginTransaction();

            // First, delete student's notes for this module
            $noteStmt = $this->conn->prepare("
                DELETE FROM notes 
                WHERE student_id = :studentId AND module_id = :moduleId
            ");
            $noteStmt->bindParam(':studentId', $studentId);
            $noteStmt->bindParam(':moduleId', $moduleId);
            $noteStmt->execute();

            // Then, remove the module from the student's list
            $stmt = $this->conn->prepare("
                DELETE FROM student_modules 
                WHERE student_id = :studentId AND module_id = :moduleId
            ");
            $stmt->bindParam(':studentId', $studentId);
            $stmt->bindParam(':moduleId', $moduleId);
            $stmt->execute();

            // Commit transaction
            $this->conn->commit();
            return true;
        } catch(PDOException $e) {
            // Rollback on error
            $this->conn->rollBack();
            return false;
        }
    }
}
?>
