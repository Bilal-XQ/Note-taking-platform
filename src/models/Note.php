<?php
require_once __DIR__ . '/../../config/database.php';

class Note {
    private $conn;
    
    public function __construct() {
        $this->conn = getDBConnection();
    }
    
    public function getNotesByStudentAndModule($studentId, $moduleId) {
        try {
            $stmt = $this->conn->prepare("
                SELECT id, content, created_at, updated_at 
                FROM notes 
                WHERE student_id = :studentId AND module_id = :moduleId 
                ORDER BY updated_at DESC
            ");
            $stmt->bindParam(':studentId', $studentId);
            $stmt->bindParam(':moduleId', $moduleId);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            return [];
        }
    }
    
    public function getNoteById($noteId, $studentId) {
        try {
            $stmt = $this->conn->prepare("
                SELECT id, content, module_id, created_at, updated_at 
                FROM notes 
                WHERE id = :noteId AND student_id = :studentId
            ");
            $stmt->bindParam(':noteId', $noteId);
            $stmt->bindParam(':studentId', $studentId);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            return false;
        }
    }
    
    public function createNote($content, $studentId, $moduleId) {
        try {
            $stmt = $this->conn->prepare("
                INSERT INTO notes (content, student_id, module_id) 
                VALUES (:content, :studentId, :moduleId)
            ");
            $stmt->bindParam(':content', $content);
            $stmt->bindParam(':studentId', $studentId);
            $stmt->bindParam(':moduleId', $moduleId);
            $stmt->execute();
            
            return $this->conn->lastInsertId();
        } catch(PDOException $e) {
            return false;
        }
    }
    
    public function updateNote($noteId, $content, $studentId) {
        try {
            $stmt = $this->conn->prepare("
                UPDATE notes 
                SET content = :content 
                WHERE id = :noteId AND student_id = :studentId
            ");
            $stmt->bindParam(':content', $content);
            $stmt->bindParam(':noteId', $noteId);
            $stmt->bindParam(':studentId', $studentId);
            $stmt->execute();
            
            return $stmt->rowCount() > 0;
        } catch(PDOException $e) {
            return false;
        }
    }
    
    public function deleteNote($noteId, $studentId) {
        try {
            $stmt = $this->conn->prepare("
                DELETE FROM notes 
                WHERE id = :noteId AND student_id = :studentId
            ");
            $stmt->bindParam(':noteId', $noteId);
            $stmt->bindParam(':studentId', $studentId);
            $stmt->execute();
            
            return $stmt->rowCount() > 0;
        } catch(PDOException $e) {
            return false;
        }
    }
}
?>
