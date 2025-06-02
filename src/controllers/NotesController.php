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

    public function createNote($title, $content, $moduleId, $categoryId = null) {
        $studentId = $this->authController->getCurrentStudentId();
        if (!$studentId) {
            return false;
        }

        return $this->noteModel->createNote($title, $content, $studentId, $moduleId, $categoryId);
    }

    public function updateNote($noteId, $title, $content, $categoryId = null) {
        $studentId = $this->authController->getCurrentStudentId();
        if (!$studentId) {
            return false;
        }

        return $this->noteModel->updateNote($noteId, $title, $content, $studentId, $categoryId);
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

    public function getAllNotes() {
        $studentId = $this->authController->getCurrentStudentId();
        if (!$studentId) {
            return [];
        }

        return $this->noteModel->getAllNotesByStudent($studentId);
    }

    public function generateAISummary($noteId) {
        $studentId = $this->authController->getCurrentStudentId();
        if (!$studentId) {
            return false;
        }

        return $this->noteModel->generateAISummaryForNote($noteId, $studentId);
    }

    public function generateModuleSummaries($moduleId) {
        $studentId = $this->authController->getCurrentStudentId();
        if (!$studentId) {
            return false;
        }

        return $this->noteModel->generateModuleSummaries($moduleId, $studentId);
    }

    public function getNotesWithSummaries() {
        $studentId = $this->authController->getCurrentStudentId();
        if (!$studentId) {
            return [];
        }

        return $this->noteModel->getNotesWithSummaries($studentId);
    }

    public function createModule($name) {
        $studentId = $this->authController->getCurrentStudentId();
        if (!$studentId) {
            return false;
        }

        return $this->moduleModel->createModule($name, $studentId);
    }

    public function updateModule($moduleId, $name) {
        $studentId = $this->authController->getCurrentStudentId();
        if (!$studentId) {
            return false;
        }

        return $this->moduleModel->updateModule($moduleId, $name, $studentId);
    }

    public function deleteModule($moduleId) {
        $studentId = $this->authController->getCurrentStudentId();
        if (!$studentId) {
            return false;
        }

        return $this->moduleModel->deleteModule($moduleId, $studentId);
    }
}
