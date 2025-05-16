<?php
// session_start(); // Removed to avoid duplicate session_start warning
if (!isset($_SESSION['admin_id'])) {
    header('Location: /main/index.php');
    exit;
}
require_once __DIR__ . '/../models/Student.php';
$studentModel = new Student();
$action = $_GET['action'] ?? 'list';

switch ($action) {
    case 'add':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $studentModel->add($_POST);
            header('Location: /admin/dashboard.php');
            exit;
        }
        break;
    case 'edit':
        $id = $_GET['id'] ?? null;
        if (!$id) break;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $studentModel->edit($id, $_POST);
            header('Location: /admin/dashboard.php');
            exit;
        }
        $student = $studentModel->get($id);
        break;
    case 'delete':
        $id = $_GET['id'] ?? null;
        if ($id) {
            $studentModel->delete($id);
        }
        header('Location: /admin/dashboard.php');
        exit;
    case 'list':
    default:
        $students = $studentModel->all();
        break;
} 