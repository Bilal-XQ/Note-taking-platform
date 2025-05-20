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

    public function generateAISummary($noteId, $studentId) {
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

    public function generateSummaryWithOpenRouter($content) {
        // OpenRouter API key
        $apiKey = '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';
        $url = 'https://openrouter.ai/api/v1/chat/completions';

        // Prepare the request data
        $data = [
            'model' => 'openai/gpt-3.5-turbo',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'You are a helpful assistant that generates concise summaries of notes.'
                ],
                [
                    'role' => 'user',
                    'content' => "Please generate a concise summary of the following note content. Focus on the key points and main ideas: \n\n" . $content
                ]
            ],
            'temperature' => 0.2,
            'max_tokens' => 800
        ];

        // Initialize cURL session
        $ch = curl_init($url);

        // Set cURL options
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $apiKey,
            'HTTP-Referer: https://localhost',
            'X-Title: StudyNotesApp'
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
        if (isset($responseData['choices'][0]['message']['content'])) {
            return $responseData['choices'][0]['message']['content'];
        } else {
            // If the response format is unexpected, return a fallback summary
            return "AI summary generation failed. Please try again later. Unexpected response format.";
        }
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
