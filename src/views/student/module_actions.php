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

// Check if this is an AJAX request or a direct form submission
$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

// Set content type based on request type
if ($isAjax) {
    header('Content-Type: application/json');
}

// Get the action type
$action = '';
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action'])) {
    $action = $_GET['action'];
} elseif (isset($_POST['action'])) {
    $action = $_POST['action'];
}

// For debugging
$debug = [
    'received_post' => $_POST,
    'received_get' => $_GET,
    'action' => $action
];

// Initialize the NotesController
$notesController = new NotesController();

// Process the request based on action
switch ($action) {
    case 'add':
        // Create a new module
        $moduleName = isset($_POST['name']) ? trim($_POST['name']) : '';

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
                if ($isAjax) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'Module created successfully',
                        'module_id' => $moduleId,
                        'debug' => $debug
                    ]);
                } else {
                    // Redirect to modules page or home page with success message
                    header('Location: /main/src/views/student/modules.php?message=' . urlencode('Module created successfully') . '&type=success');
                    exit;
                }
            } else {
                if ($isAjax) {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Failed to create module',
                        'debug' => $debug
                    ]);
                } else {
                    // Redirect back with error message
                    header('Location: /main/src/views/student/modules.php?message=' . urlencode('Failed to create module') . '&type=error');
                    exit;
                }
            }
        } catch (Exception $e) {
            if ($isAjax) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Error: ' . $e->getMessage(),
                    'debug' => $debug
                ]);
            } else {
                // Redirect back with error message
                header('Location: /main/src/views/student/modules.php?message=' . urlencode('Error: ' . $e->getMessage()) . '&type=error');
                exit;
            }
        }
        break;

    case 'edit':
        // Edit an existing module
        $moduleId = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        $moduleName = isset($_POST['name']) ? trim($_POST['name']) : '';

        // Debug
        $debug['module_id'] = $moduleId;
        $debug['module_name'] = $moduleName;

        if (empty($moduleId) || empty($moduleName)) {
            echo json_encode([
                'success' => false,
                'message' => 'Module ID and name are required',
                'debug' => $debug
            ]);
            exit;
        }

        try {
            $result = $notesController->updateModule($moduleId, $moduleName);

            if ($result) {
                if ($isAjax) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'Module updated successfully',
                        'debug' => $debug
                    ]);
                } else {
                    // Redirect to modules page with success message
                    header('Location: /main/src/views/student/modules.php?message=' . urlencode('Module updated successfully') . '&type=success');
                    exit;
                }
            } else {
                if ($isAjax) {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Failed to update module',
                        'debug' => $debug
                    ]);
                } else {
                    // Redirect back with error message
                    header('Location: /main/src/views/student/modules.php?message=' . urlencode('Failed to update module') . '&type=error');
                    exit;
                }
            }
        } catch (Exception $e) {
            if ($isAjax) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Error: ' . $e->getMessage(),
                    'debug' => $debug
                ]);
            } else {
                // Redirect back with error message
                header('Location: /main/src/views/student/modules.php?message=' . urlencode('Error: ' . $e->getMessage()) . '&type=error');
                exit;
            }
        }
        break;

    case 'delete':
        // Delete a module
        // Check if the request is GET or POST
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $moduleId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        } else {
            $moduleId = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        }

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
                if ($isAjax) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'Module deleted successfully',
                        'debug' => $debug
                    ]);
                } else {
                    // Redirect to modules page with success message
                    header('Location: /main/src/views/student/modules.php?message=' . urlencode('Module deleted successfully') . '&type=success');
                    exit;
                }
            } else {
                if ($isAjax) {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Failed to delete module',
                        'debug' => $debug
                    ]);
                } else {
                    // Redirect back with error message
                    header('Location: /main/src/views/student/modules.php?message=' . urlencode('Failed to delete module') . '&type=error');
                    exit;
                }
            }
        } catch (Exception $e) {
            if ($isAjax) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Error: ' . $e->getMessage(),
                    'debug' => $debug
                ]);
            } else {
                // Redirect back with error message
                header('Location: /main/src/views/student/modules.php?message=' . urlencode('Error: ' . $e->getMessage()) . '&type=error');
                exit;
            }
        }
        break;

    default:
        if ($isAjax) {
            echo json_encode([
                'success' => false,
                'message' => 'Invalid action',
                'debug' => $debug
            ]);
        } else {
            // Redirect back with error message
            header('Location: /main/src/views/student/modules.php?message=' . urlencode('Invalid action') . '&type=error');
            exit;
        }
}
