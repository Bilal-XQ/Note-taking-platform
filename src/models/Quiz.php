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
     * Generate a quiz using the OpenRouter API
     * 
     * @param string $noteContent The content of the note
     * @param string $noteTitle The title of the note
     * @return array|bool The generated quiz data or false on failure
     */
    public function generateQuizWithOpenRouter($noteContent, $noteTitle) {
        // For backward compatibility, now calls the Gemini API
        return $this->generateQuizWithGemini($noteContent, $noteTitle);
    }

    /**
     * Generate a quiz using the Gemini API
     * 
     * @param string $noteContent The content of the note
     * @param string $noteTitle The title of the note
     * @param int $numQuestions The number of questions to generate (default: 5)
     * @return array|bool The generated quiz data or false on failure
     */
    public function generateQuizWithGemini($noteContent, $noteTitle, $numQuestions = 5) {
        // Get API key from environment variables
        $apiKey = $_ENV['GEMINI_API_KEY'] ?? getenv('GEMINI_API_KEY');
        $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent';

        $prompt = "Generate a quiz based on the following note content. The note title is: '$noteTitle'. 
        Create $numQuestions multiple-choice questions that test understanding of the key concepts in the note.\n\nFor each question:\n1. Provide the question text\n2. Provide 4 possible answers (labeled A, B, C, D)\n3. Indicate which answer is correct\n4. Provide a brief explanation for why the answer is correct\n\nFormat your response as a JSON object with this structure:\n{\n  \"questions\": [\n    {\n      \"question\": \"Question text here\",\n      \"answers\": [\n        {\"text\": \"Answer A\", \"isCorrect\": false},\n        {\"text\": \"Answer B\", \"isCorrect\": true},\n        {\"text\": \"Answer C\", \"isCorrect\": false},\n        {\"text\": \"Answer D\", \"isCorrect\": false}\n      ],\n      \"explanation\": \"Explanation for the correct answer\"\n    }\n  ]\n}\n\nHere's the note content:\n$noteContent";

        $data = [
            'contents' => [
                [
                    'parts' => [
                        [ 'text' => $prompt ]
                    ]
                ]
            ],
            'generationConfig' => [
                'temperature' => 0.2,
                'maxOutputTokens' => 2048,
                'topP' => 0.8,
                'topK' => 40
            ]
        ];

        error_log("Quiz generation request for note: $noteTitle");
        error_log("Request data: " . json_encode($data));

        $ch = curl_init($url . '?key=' . $apiKey);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [ 'Content-Type: application/json' ]);
        $response = curl_exec($ch);
        
        if (curl_errno($ch)) {
            $error = curl_error($ch);
            error_log("Curl error in quiz generation: $error");
            curl_close($ch);
            return $this->fallbackQuiz($noteTitle);
        }
        
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        error_log("Quiz generation response code: $httpCode");
        error_log("Quiz generation response: " . $response);
        
        curl_close($ch);
        
        if ($httpCode >= 400) {
            error_log("HTTP error in quiz generation: $httpCode");
            return $this->fallbackQuiz($noteTitle);
        }
        
        $responseData = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log("JSON decode error in quiz generation: " . json_last_error_msg());
            return $this->fallbackQuiz($noteTitle);
        }
        
        if (isset($responseData['candidates'][0]['content']['parts'][0]['text'])) {
            $quizText = $responseData['candidates'][0]['content']['parts'][0]['text'];
            error_log("Quiz text received: " . $quizText);
            
            preg_match('/\{.*\}/s', $quizText, $matches);
            if (!empty($matches)) {
                $quizJson = $matches[0];
                $quizData = json_decode($quizJson, true);
                if (json_last_error() === JSON_ERROR_NONE && isset($quizData['questions'])) {
                    error_log("Quiz data successfully parsed");
                    return $quizData;
                } else {
                    error_log("Failed to parse quiz JSON: " . json_last_error_msg());
                }
            } else {
                error_log("No JSON object found in response");
            }
        } else {
            error_log("No quiz text found in response");
        }
        
        error_log("Falling back to default quiz");
        return $this->fallbackQuiz($noteTitle);
    }

    private function fallbackQuiz($noteTitle) {
        return [
            "questions" => [
                [
                    "question" => "What is the main topic of the note titled '$noteTitle'?",
                    "answers" => [
                        ["text" => "The main topic is found in the title", "isCorrect" => true],
                        ["text" => "The main topic is not related to the title", "isCorrect" => false],
                        ["text" => "The main topic is always in the conclusion", "isCorrect" => false],
                        ["text" => "The main topic is not important", "isCorrect" => false]
                    ],
                    "explanation" => "The main topic of a note is typically reflected in its title."
                ],
                [
                    "question" => "Which of the following is a good study practice when reviewing notes?",
                    "answers" => [
                        ["text" => "Regular review and practice", "isCorrect" => true],
                        ["text" => "Cramming the night before", "isCorrect" => false],
                        ["text" => "Reading once and never reviewing", "isCorrect" => false],
                        ["text" => "Skipping difficult sections", "isCorrect" => false]
                    ],
                    "explanation" => "Regular review and practice helps reinforce learning and improve retention."
                ],
                [
                    "question" => "What is the purpose of taking notes?",
                    "answers" => [
                        ["text" => "To record and organize important information", "isCorrect" => true],
                        ["text" => "To waste time", "isCorrect" => false],
                        ["text" => "To show off to others", "isCorrect" => false],
                        ["text" => "To avoid studying", "isCorrect" => false]
                    ],
                    "explanation" => "Notes help record and organize important information for future reference and study."
                ],
                [
                    "question" => "How can you make your notes more effective?",
                    "answers" => [
                        ["text" => "By organizing information clearly and using your own words", "isCorrect" => true],
                        ["text" => "By copying everything word for word", "isCorrect" => false],
                        ["text" => "By writing as fast as possible", "isCorrect" => false],
                        ["text" => "By using only abbreviations", "isCorrect" => false]
                    ],
                    "explanation" => "Organizing information clearly and using your own words helps with understanding and retention."
                ],
                [
                    "question" => "What should you do if you don't understand something in your notes?",
                    "answers" => [
                        ["text" => "Review the material and ask for clarification", "isCorrect" => true],
                        ["text" => "Ignore it and move on", "isCorrect" => false],
                        ["text" => "Assume it's not important", "isCorrect" => false],
                        ["text" => "Skip that section entirely", "isCorrect" => false]
                    ],
                    "explanation" => "Seeking clarification helps ensure you understand the material correctly."
                ]
            ]
        ];
    }

    /**
     * Generate a quiz from multiple notes using the Gemini API
     * 
     * @param array $notes Array of notes, each containing 'content' and 'title'
     * @param int $numQuestions The number of questions to generate
     * @return array|bool The generated quiz data or false on failure
     */
    public function generateQuizFromMultipleNotes($notes, $numQuestions = 5) {
        if (empty($notes)) {
            return false;
        }

        // Combine note contents and create a title
        $combinedContent = "";
        $titles = [];

        foreach ($notes as $note) {
            $combinedContent .= "Note: " . $note['title'] . "\n\n" . $note['content'] . "\n\n---\n\n";
            $titles[] = $note['title'];
        }

        $combinedTitle = "Quiz on " . implode(", ", $titles);
        if (count($titles) > 3) {
            $combinedTitle = "Quiz on " . $titles[0] . ", " . $titles[1] . ", " . $titles[2] . " and " . (count($titles) - 3) . " more";
        }

        // Generate quiz using the combined content
        return $this->generateQuizWithGemini($combinedContent, $combinedTitle, $numQuestions);
    }
}
