<?php
require_once __DIR__ . '/../../config/database.php';

class Quiz {
    private $conn;

    public function __construct() {
        $this->conn = getDBConnection();
    }

    /**
     * Create a new quiz for a note
     * 
     * @param int $noteId The ID of the note
     * @param string $title The title of the quiz
     * @param string $description The description of the quiz
     * @return int|bool The ID of the new quiz or false on failure
     */
    public function createQuiz($noteId, $title, $description = '') {
        try {
            $stmt = $this->conn->prepare("
                INSERT INTO quizzes (note_id, title, description) 
                VALUES (:noteId, :title, :description)
            ");
            $stmt->bindParam(':noteId', $noteId);
            $stmt->bindParam(':title', $title);
            $stmt->bindParam(':description', $description);

            if ($stmt->execute()) {
                return $this->conn->lastInsertId();
            }
            return false;
        } catch(PDOException $e) {
            return false;
        }
    }

    /**
     * Add a question to a quiz
     * 
     * @param int $quizId The ID of the quiz
     * @param string $questionText The text of the question
     * @param string $questionType The type of question (multiple_choice, true_false, short_answer)
     * @param string $difficulty The difficulty of the question (easy, medium, hard)
     * @return int|bool The ID of the new question or false on failure
     */
    public function addQuestion($quizId, $questionText, $questionType = 'multiple_choice', $difficulty = 'medium') {
        try {
            $stmt = $this->conn->prepare("
                INSERT INTO quiz_questions (quiz_id, question_text, question_type, difficulty) 
                VALUES (:quizId, :questionText, :questionType, :difficulty)
            ");
            $stmt->bindParam(':quizId', $quizId);
            $stmt->bindParam(':questionText', $questionText);
            $stmt->bindParam(':questionType', $questionType);
            $stmt->bindParam(':difficulty', $difficulty);

            if ($stmt->execute()) {
                return $this->conn->lastInsertId();
            }
            return false;
        } catch(PDOException $e) {
            return false;
        }
    }

    /**
     * Add an answer to a question
     * 
     * @param int $questionId The ID of the question
     * @param string $answerText The text of the answer
     * @param bool $isCorrect Whether the answer is correct
     * @param string $explanation The explanation for the answer
     * @return bool Whether the operation was successful
     */
    public function addAnswer($questionId, $answerText, $isCorrect = false, $explanation = '') {
        try {
            $stmt = $this->conn->prepare("
                INSERT INTO quiz_answers (question_id, answer_text, is_correct, explanation) 
                VALUES (:questionId, :answerText, :isCorrect, :explanation)
            ");
            $stmt->bindParam(':questionId', $questionId);
            $stmt->bindParam(':answerText', $answerText);
            $stmt->bindParam(':isCorrect', $isCorrect, PDO::PARAM_BOOL);
            $stmt->bindParam(':explanation', $explanation);

            return $stmt->execute();
        } catch(PDOException $e) {
            return false;
        }
    }

    /**
     * Get a quiz by ID
     * 
     * @param int $quizId The ID of the quiz
     * @return array|bool The quiz data or false on failure
     */
    public function getQuizById($quizId) {
        try {
            $stmt = $this->conn->prepare("
                SELECT * FROM quizzes WHERE id = :quizId
            ");
            $stmt->bindParam(':quizId', $quizId);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            return false;
        }
    }

    /**
     * Get all quizzes for a note
     * 
     * @param int $noteId The ID of the note
     * @return array The quizzes for the note
     */
    public function getQuizzesByNoteId($noteId) {
        try {
            $stmt = $this->conn->prepare("
                SELECT * FROM quizzes WHERE note_id = :noteId ORDER BY created_at DESC
            ");
            $stmt->bindParam(':noteId', $noteId);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            return [];
        }
    }

    /**
     * Get all questions for a quiz
     * 
     * @param int $quizId The ID of the quiz
     * @return array The questions for the quiz
     */
    public function getQuestionsByQuizId($quizId) {
        try {
            $stmt = $this->conn->prepare("
                SELECT * FROM quiz_questions WHERE quiz_id = :quizId
            ");
            $stmt->bindParam(':quizId', $quizId);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            return [];
        }
    }

    /**
     * Get all answers for a question
     * 
     * @param int $questionId The ID of the question
     * @return array The answers for the question
     */
    public function getAnswersByQuestionId($questionId) {
        try {
            $stmt = $this->conn->prepare("
                SELECT * FROM quiz_answers WHERE question_id = :questionId
            ");
            $stmt->bindParam(':questionId', $questionId);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            return [];
        }
    }

    /**
     * Get a complete quiz with questions and answers
     * 
     * @param int $quizId The ID of the quiz
     * @return array|bool The complete quiz data or false on failure
     */
    public function getCompleteQuiz($quizId) {
        $quiz = $this->getQuizById($quizId);
        if (!$quiz) {
            return false;
        }

        $questions = $this->getQuestionsByQuizId($quizId);
        $quiz['questions'] = [];

        foreach ($questions as $question) {
            $answers = $this->getAnswersByQuestionId($question['id']);
            $question['answers'] = $answers;
            $quiz['questions'][] = $question;
        }

        return $quiz;
    }

    /**
     * Delete a quiz and all its questions and answers
     * 
     * @param int $quizId The ID of the quiz
     * @return bool Whether the operation was successful
     */
    public function deleteQuiz($quizId) {
        try {
            $stmt = $this->conn->prepare("
                DELETE FROM quizzes WHERE id = :quizId
            ");
            $stmt->bindParam(':quizId', $quizId);

            return $stmt->execute();
        } catch(PDOException $e) {
            return false;
        }
    }

    /**
     * Record a quiz attempt
     * 
     * @param int $studentId The ID of the student
     * @param int $quizId The ID of the quiz
     * @param float $score The score achieved
     * @param int $totalQuestions The total number of questions
     * @return bool Whether the operation was successful
     */
    public function recordAttempt($studentId, $quizId, $score, $totalQuestions) {
        try {
            $stmt = $this->conn->prepare("
                INSERT INTO quiz_attempts (student_id, quiz_id, score, total_questions) 
                VALUES (:studentId, :quizId, :score, :totalQuestions)
            ");
            $stmt->bindParam(':studentId', $studentId);
            $stmt->bindParam(':quizId', $quizId);
            $stmt->bindParam(':score', $score);
            $stmt->bindParam(':totalQuestions', $totalQuestions);

            return $stmt->execute();
        } catch(PDOException $e) {
            return false;
        }
    }

    /**
     * Get all attempts for a student and quiz
     * 
     * @param int $studentId The ID of the student
     * @param int $quizId The ID of the quiz
     * @return array The attempts
     */
    public function getAttemptsByStudentAndQuiz($studentId, $quizId) {
        try {
            $stmt = $this->conn->prepare("
                SELECT * FROM quiz_attempts 
                WHERE student_id = :studentId AND quiz_id = :quizId 
                ORDER BY completed_at DESC
            ");
            $stmt->bindParam(':studentId', $studentId);
            $stmt->bindParam(':quizId', $quizId);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            return [];
        }
    }

    /**
     * Get the latest attempt for a student and quiz
     * 
     * @param int $studentId The ID of the student
     * @param int $quizId The ID of the quiz
     * @return array|bool The latest attempt or false if none exists
     */
    public function getLatestAttemptByStudentAndQuiz($studentId, $quizId) {
        try {
            $stmt = $this->conn->prepare("
                SELECT * FROM quiz_attempts 
                WHERE student_id = :studentId AND quiz_id = :quizId 
                ORDER BY completed_at DESC
                LIMIT 1
            ");
            $stmt->bindParam(':studentId', $studentId);
            $stmt->bindParam(':quizId', $quizId);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            return false;
        }
    }

    /**
     * Get all quizzes for a student with note and module information
     * 
     * @param int $studentId The ID of the student
     * @return array The quizzes for the student
     */
    public function getAllQuizzesByStudent($studentId) {
        try {
            $stmt = $this->conn->prepare("
                SELECT q.*, n.title as note_title, n.module_id, m.name as module_name
                FROM quizzes q
                JOIN notes n ON q.note_id = n.id
                JOIN modules m ON n.module_id = m.id
                WHERE n.student_id = :studentId
                ORDER BY q.created_at DESC
            ");
            $stmt->bindParam(':studentId', $studentId);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            return [];
        }
    }

    /**
     * Generate a quiz using the Gemini API
     * 
     * @param string $noteContent The content of the note
     * @param string $noteTitle The title of the note
     * @return array|bool The generated quiz data or false on failure
     */
    public function generateQuizWithGemini($noteContent, $noteTitle) {
        // OpenRouter API key
        $apiKey = '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';
        $url = 'https://openrouter.ai/api/v1/chat/completions';

        // Prepare the request data
        $prompt = "Generate a quiz based on the following note content. The note title is: '$noteTitle'. 
        Create 3-5 multiple-choice questions that test understanding of the key concepts in the note.

        For each question:
        1. Provide the question text
        2. Provide 4 possible answers (labeled A, B, C, D)
        3. Indicate which answer is correct
        4. Provide a brief explanation for why the answer is correct

        Format your response as a JSON object with this structure:
        {
          \"questions\": [
            {
              \"question\": \"Question text here\",
              \"answers\": [
                {\"text\": \"Answer A\", \"isCorrect\": false},
                {\"text\": \"Answer B\", \"isCorrect\": true},
                {\"text\": \"Answer C\", \"isCorrect\": false},
                {\"text\": \"Answer D\", \"isCorrect\": false}
              ],
              \"explanation\": \"Explanation for the correct answer\"
            }
          ]
        }

        Here's the note content:
        $noteContent";

        $data = [
            'model' => 'openchat/openchat-3.5-1210',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'You are a helpful assistant that generates quizzes based on note content.'
                ],
                [
                    'role' => 'user',
                    'content' => $prompt
                ]
            ],
            'temperature' => 0.2,
            'max_tokens' => 1024
        ];

        // Initialize cURL session
        $ch = curl_init($url);

        // Set cURL options
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $apiKey
        ]);

        // Execute cURL session and get the response
        $response = curl_exec($ch);

        // Check for errors
        if (curl_errno($ch)) {
            curl_close($ch);
            return false;
        }

        // Close cURL session
        curl_close($ch);

        // Decode the response
        $responseData = json_decode($response, true);

        // Extract the quiz data from the response
        if (isset($responseData['choices'][0]['message']['content'])) {
            $quizText = $responseData['choices'][0]['message']['content'];

            // Extract JSON from the response (it might be wrapped in markdown code blocks)
            preg_match('/\{.*\}/s', $quizText, $matches);

            if (!empty($matches)) {
                $quizJson = $matches[0];
                $quizData = json_decode($quizJson, true);

                if (json_last_error() === JSON_ERROR_NONE && isset($quizData['questions'])) {
                    return $quizData;
                }
            }
        }

        return false;
    }
}
