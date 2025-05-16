<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../../controllers/NotesController.php';
require_once __DIR__ . '/../../controllers/AuthController.php';

// Ensure the user is logged in
$authController = new AuthController();
if (!$authController->isLoggedIn() || $authController->isAdmin()) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Authentication required'
    ]);
    exit;
}

// All responses will be in JSON format
header('Content-Type: application/json');

// For debugging
$debug = [
    'received_post' => $_POST,
    'action' => isset($_POST['action']) ? $_POST['action'] : 'none'
];

// Get the action type
$action = isset($_POST['action']) ? $_POST['action'] : '';

// Initialize the NotesController
$notesController = new NotesController();

// Process the request based on action
switch ($action) {
    case 'create':
        // Create a new module
        $moduleName = isset($_POST['module_name']) ? trim($_POST['module_name']) : '';

        // Debug
        $debug['module_name'] = $moduleName;

        if (empty($moduleName)) {
            echo json_encode([
                'success' => false,
                'message' => 'Module name is required',
                'debug' => $debug
            ]);
            exit;
        }

        try {
            $moduleId = $notesController->createModule($moduleName);

            if ($moduleId) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Module created successfully',
                    'module_id' => $moduleId,
                    'debug' => $debug
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Failed to create module',
                    'debug' => $debug
                ]);
            }
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
                'debug' => $debug
            ]);
        }
        break;

    case 'delete':
        // Delete a module
        $moduleId = isset($_POST['module_id']) ? (int)$_POST['module_id'] : 0;

        // Debug
        $debug['module_id'] = $moduleId;

        if (empty($moduleId)) {
            echo json_encode([
                'success' => false,
                'message' => 'Module ID is required',
                'debug' => $debug
            ]);
            exit;
        }

        try {
            $result = $notesController->deleteModule($moduleId);

            if ($result) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Module deleted successfully',
                    'debug' => $debug
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Failed to delete module',
                    'debug' => $debug
                ]);
            }
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
                'debug' => $debug
            ]);
        }
        break;

    default:
        echo json_encode([
            'success' => false,
            'message' => 'Invalid action',
            'debug' => $debug
        ]);
}
