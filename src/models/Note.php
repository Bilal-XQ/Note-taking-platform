<?php
require_once __DIR__ . '/../../config/database.php';

class Note {
    private $conn;

    public function __construct() {
        $this->conn = getDBConnection();
    }

    /**
     * Static method to handle static calls to generateAISummary()
     * This redirects static calls to an instance method
     * 
     * @param int $noteId The ID of the note to generate a summary for
     * @return bool True if successful, false otherwise
     */
    public static function generateAISummary($noteId) {
        // Start a session if not already started
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $instance = new self();
        $studentId = isset($_SESSION['student_id']) ? $_SESSION['student_id'] : null;
        if (!$studentId) {
            return false;
        }

        // Call the instance method with a different name to avoid recursion
        return $instance->_generateAISummaryImpl($noteId, $studentId);
    }

    public function getNotesByStudentAndModule($studentId, $moduleId) {
        try {
            $stmt = $this->conn->prepare("
                SELECT id, title, content, ai_summary, created_at, updated_at 
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
                SELECT id, title, content, ai_summary, module_id, category_id, created_at, updated_at 
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

    public function createNote($title, $content, $studentId, $moduleId, $categoryId = null) {
        try {
            $stmt = $this->conn->prepare("
                INSERT INTO notes (title, content, student_id, module_id, category_id) 
                VALUES (:title, :content, :studentId, :moduleId, :categoryId)
            ");
            $stmt->bindParam(':title', $title);
            $stmt->bindParam(':content', $content);
            $stmt->bindParam(':studentId', $studentId);
            $stmt->bindParam(':moduleId', $moduleId);
            $stmt->bindParam(':categoryId', $categoryId);

            return $stmt->execute();
        } catch(PDOException $e) {
            return false;
        }
    }

    public function updateNote($noteId, $title, $content, $studentId, $categoryId = null) {
        try {
            $stmt = $this->conn->prepare("
                UPDATE notes 
                SET title = :title, content = :content, category_id = :categoryId, updated_at = NOW() 
                WHERE id = :noteId AND student_id = :studentId
            ");
            $stmt->bindParam(':noteId', $noteId);
            $stmt->bindParam(':title', $title);
            $stmt->bindParam(':content', $content);
            $stmt->bindParam(':studentId', $studentId);
            $stmt->bindParam(':categoryId', $categoryId);

            return $stmt->execute();
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

            return $stmt->execute();
        } catch(PDOException $e) {
            return false;
        }
    }

    public function getAllNotesByStudent($studentId) {
        try {
            $stmt = $this->conn->prepare("
                SELECT n.id, n.title, n.content, n.ai_summary, n.module_id, n.created_at, n.updated_at, m.name as module_name
                FROM notes n
                JOIN modules m ON n.module_id = m.id
                WHERE n.student_id = :studentId
                ORDER BY n.module_id, n.updated_at DESC
            ");
            $stmt->bindParam(':studentId', $studentId);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            return [];
        }
    }

    /**
     * Implementation of AI summary generation
     * This is called by the static method generateAISummary
     * 
     * @param int $noteId The ID of the note to generate a summary for
     * @param int $studentId The ID of the student who owns the note
     * @return bool True if successful, false otherwise
     */
    public function _generateAISummaryImpl($noteId, $studentId) {
        $note = $this->getNoteById($noteId, $studentId);
        if (!$note) {
            return false;
        }

        // Use OpenRouter API to generate summary
        $summary = $this->generateSummaryWithOpenRouter($note['content']);

        try {
            $stmt = $this->conn->prepare("
                UPDATE notes 
                SET ai_summary = :summary, summary_generated_at = NOW() 
                WHERE id = :noteId AND student_id = :studentId
            ");
            $stmt->bindParam(':noteId', $noteId);
            $stmt->bindParam(':summary', $summary);
            $stmt->bindParam(':studentId', $studentId);

            return $stmt->execute();
        } catch(PDOException $e) {
            return false;
        }
    }

    /**
     * Instance method for generating AI summaries
     * This is a wrapper around _generateAISummaryImpl for backward compatibility
     * 
     * @param int $noteId The ID of the note to generate a summary for
     * @param int $studentId The ID of the student who owns the note
     * @return bool True if successful, false otherwise
     */
    public function generateAISummaryForNote($noteId, $studentId) {
        return $this->_generateAISummaryImpl($noteId, $studentId);
    }

    public function generateSummaryWithGemini($content) {
        // Get API key from environment variables
        $apiKey = $_ENV['GEMINI_API_KEY'] ?? getenv('GEMINI_API_KEY');
        $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent';

        // Prepare the request data
        $data = [
            'contents' => [
                [
                    'parts' => [
                        [
                            'text' => "Please generate a concise summary of the following note content. Focus on the key points and main ideas: \n\n" . $content
                        ]
                    ]
                ]
            ],
            'generationConfig' => [
                'temperature' => 0.2,
                'maxOutputTokens' => 800,
                'topP' => 0.8,
                'topK' => 40
            ]
        ];

        // Initialize cURL session
        $ch = curl_init($url . '?key=' . $apiKey);

        // Set cURL options
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Disable SSL verification to fix certificate issues
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json'
        ]);

        // Execute cURL session and get the response
        $response = curl_exec($ch);

        // Check for errors
        if (curl_errno($ch)) {
            // If there's an error, return a fallback summary
            curl_close($ch);
            return "AI summary generation failed. Please try again later. Error: " . curl_error($ch);
        }

        // Close cURL session
        curl_close($ch);

        // Decode the response
        $responseData = json_decode($response, true);

        // Extract the summary from the response
        if (isset($responseData['candidates'][0]['content']['parts'][0]['text'])) {
            return $responseData['candidates'][0]['content']['parts'][0]['text'];
        } else {
            // If the response format is unexpected, return a fallback summary
            return "AI summary generation failed. Please try again later. Unexpected response format.";
        }
    }

    public function generateSummaryWithOpenRouter($content) {
        // For backward compatibility, now calls the Gemini API
        return $this->generateSummaryWithGemini($content);
    }

    public function generateModuleSummaries($moduleId, $studentId) {
        try {
            // Get all notes for the module
            $stmt = $this->conn->prepare("
                SELECT id, title, content
                FROM notes
                WHERE module_id = :moduleId AND student_id = :studentId
            ");
            $stmt->bindParam(':moduleId', $moduleId);
            $stmt->bindParam(':studentId', $studentId);
            $stmt->execute();

            $notes = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $successCount = 0;

            // Generate summary for each note
            foreach ($notes as $note) {
                $summary = $this->generateSummaryWithOpenRouter($note['content']);

                // Update the note with the summary
                $updateStmt = $this->conn->prepare("
                    UPDATE notes 
                    SET ai_summary = :summary, summary_generated_at = NOW() 
                    WHERE id = :noteId AND student_id = :studentId
                ");
                $updateStmt->bindParam(':noteId', $note['id']);
                $updateStmt->bindParam(':summary', $summary);
                $updateStmt->bindParam(':studentId', $studentId);

                if ($updateStmt->execute()) {
                    $successCount++;
                }
            }

            return [
                'total' => count($notes),
                'success' => $successCount
            ];
        } catch(PDOException $e) {
            return false;
        }
    }

    public function getNotesWithSummaries($studentId) {
        try {
            $stmt = $this->conn->prepare("
                SELECT n.id, n.title, n.content, n.ai_summary, n.summary_generated_at, n.module_id, m.name as module_name
                FROM notes n
                JOIN modules m ON n.module_id = m.id
                WHERE n.student_id = :studentId AND n.ai_summary IS NOT NULL
                ORDER BY n.summary_generated_at DESC
            ");
            $stmt->bindParam(':studentId', $studentId);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            return [];
        }
    }
}
