<?php
require_once __DIR__ . '/../models/Note.php';
require_once __DIR__ . '/../models/Module.php';
require_once __DIR__ . '/../models/Student.php';
require_once __DIR__ . '/AuthController.php';

class NotesController {
    private $noteModel;
    private $moduleModel;
    private $authController;

    public function __construct() {
        $this->noteModel = new Note();
        $this->moduleModel = new Module();
        $this->authController = new AuthController();
    }

    public function getNotesByModule($moduleId) {
        $studentId = $this->authController->getCurrentStudentId();
        if (!$studentId) {
            return false;
        }

        return $this->noteModel->getNotesByStudentAndModule($studentId, $moduleId);
    }

    public function getNote($noteId) {
        $studentId = $this->authController->getCurrentStudentId();
        if (!$studentId) {
            return false;
        }

        return $this->noteModel->getNoteById($noteId, $studentId);
    }

    public function createNote($content, $moduleId) {
        $studentId = $this->authController->getCurrentStudentId();
        if (!$studentId) {
            return false;
        }

        return $this->noteModel->createNote($content, $studentId, $moduleId);
    }

    public function updateNote($noteId, $content) {
        $studentId = $this->authController->getCurrentStudentId();
        if (!$studentId) {
            return false;
        }

        return $this->noteModel->updateNote($noteId, $content, $studentId);
    }

    public function deleteNote($noteId) {
        $studentId = $this->authController->getCurrentStudentId();
        if (!$studentId) {
            return false;
        }

        return $this->noteModel->deleteNote($noteId, $studentId);
    }

    public function getModules() {
        $studentId = $this->authController->getCurrentStudentId();
        if (!$studentId) {
            return [];
        }

        return $this->moduleModel->getStudentModules($studentId);
    }

    public function createModule($name) {
        $studentId = $this->authController->getCurrentStudentId();
        if (!$studentId || empty($name)) {
            return false;
        }

        return $this->moduleModel->createModule($name, $studentId);
    }

    public function deleteModule($moduleId) {
        $studentId = $this->authController->getCurrentStudentId();
        if (!$studentId) {
            return false;
        }

        return $this->moduleModel->deleteModule($moduleId, $studentId);
    }
}
?>
