<?php
require_once __DIR__ . '/../models/Quiz.php';
require_once __DIR__ . '/AuthController.php';
require_once __DIR__ . '/NotesController.php';

class QuizController {
    private $quizModel;
    private $authController;
    private $notesController;

    public function __construct() {
        $this->quizModel = new Quiz();
        $this->authController = new AuthController();
        $this->notesController = new NotesController();
    }

    /**
     * Generate a quiz for a note
     * 
     * @param int $noteId The ID of the note
     * @return int|bool The ID of the new quiz or false on failure
     */
    public function generateQuiz($noteId) {
        $studentId = $this->authController->getCurrentStudentId();
        if (!$studentId) {
            error_log('QuizController: No student ID');
            echo '<div style="color:red">Error: Not logged in.</div>';
            return false;
        }

        // Get the note
        $note = $this->notesController->getNote($noteId);
        if (!$note) {
            error_log('QuizController: Note not found for ID ' . $noteId);
            echo '<div style="color:red">Error: Note not found.</div>';
            return false;
        }

        // Generate quiz data using OpenRouter API
        $quizData = $this->quizModel->generateQuizWithOpenRouter($note['content'], $note['title']);
        if (!$quizData || !isset($quizData['questions']) || empty($quizData['questions'])) {
            error_log('QuizController: Quiz data not generated or empty for note ID ' . $noteId);
            echo '<div style="color:red">Error: Quiz data not generated or empty.</div>';
            return false;
        }

        // Create the quiz
        $quizTitle = "Quiz on " . $note['title'];
        $quizDescription = "Generated quiz based on your note: " . $note['title'];
        $quizId = $this->quizModel->createQuiz($noteId, $quizTitle, $quizDescription);

        if (!$quizId) {
            error_log('QuizController: Failed to create quiz in DB for note ID ' . $noteId);
            echo '<div style="color:red">Error: Failed to create quiz in database.</div>';
            return false;
        }

        // Add questions and answers
        foreach ($quizData['questions'] as $questionData) {
            $questionText = $questionData['question'];
            $explanation = $questionData['explanation'] ?? '';

            // Add the question
            $questionId = $this->quizModel->addQuestion($quizId, $questionText);

            if (!$questionId) {
                error_log('QuizController: Failed to add question to DB for quiz ID ' . $quizId);
                echo '<div style="color:red">Error: Failed to add question to database.</div>';
                continue;
            }

            // Add the answers
            foreach ($questionData['answers'] as $answerData) {
                $answerText = $answerData['text'];
                $isCorrect = $answerData['isCorrect'];

                $result = $this->quizModel->addAnswer($questionId, $answerText, $isCorrect, $explanation);
                if (!$result) {
                    error_log('QuizController: Failed to add answer to DB for question ID ' . $questionId);
                    echo '<div style="color:red">Error: Failed to add answer to database.</div>';
                }
            }
        }

        return $quizId;
    }

    /**
     * Generate a quiz from multiple notes
     * 
     * @param array $notes Array of notes
     * @param int $numQuestions Number of questions to generate
     * @return int|bool The ID of the new quiz or false on failure
     */
    public function generateQuizFromMultipleNotes($notes, $numQuestions = 5) {
        $studentId = $this->authController->getCurrentStudentId();
        if (!$studentId || empty($notes)) {
            error_log('QuizController: No student ID or notes array empty');
            echo '<div style="color:red">Error: Not logged in or no notes selected.</div>';
            return false;
        }

        // Generate quiz data using the Quiz model
        $quizData = $this->quizModel->generateQuizFromMultipleNotes($notes, $numQuestions);
        if (!$quizData || !isset($quizData['questions']) || empty($quizData['questions'])) {
            error_log('QuizController: Quiz data not generated or empty for multiple notes');
            echo '<div style="color:red">Error: Quiz data not generated or empty.</div>';
            return false;
        }

        // Create a combined title from the notes
        $titles = [];
        foreach ($notes as $note) {
            $titles[] = $note['title'];
        }

        $combinedTitle = "Quiz on " . implode(", ", $titles);
        if (count($titles) > 3) {
            $combinedTitle = "Quiz on " . $titles[0] . ", " . $titles[1] . ", " . $titles[2] . " and " . (count($titles) - 3) . " more";
        }

        $quizDescription = "Generated quiz based on multiple notes";

        // Use the first note's ID for the quiz (we'll need to associate it with a note)
        $noteId = $notes[0]['id'];

        // Create the quiz
        $quizId = $this->quizModel->createQuiz($noteId, $combinedTitle, $quizDescription);

        if (!$quizId) {
            error_log('QuizController: Failed to create quiz in DB for multiple notes');
            echo '<div style="color:red">Error: Failed to create quiz in database.</div>';
            return false;
        }

        // Add questions and answers
        foreach ($quizData['questions'] as $questionData) {
            $questionText = $questionData['question'];
            $explanation = $questionData['explanation'] ?? '';

            // Add the question
            $questionId = $this->quizModel->addQuestion($quizId, $questionText);

            if (!$questionId) {
                error_log('QuizController: Failed to add question to DB for quiz ID ' . $quizId);
                echo '<div style="color:red">Error: Failed to add question to database.</div>';
                continue;
            }

            // Add the answers
            foreach ($questionData['answers'] as $answerData) {
                $answerText = $answerData['text'];
                $isCorrect = $answerData['isCorrect'];

                $result = $this->quizModel->addAnswer($questionId, $answerText, $isCorrect, $explanation);
                if (!$result) {
                    error_log('QuizController: Failed to add answer to DB for question ID ' . $questionId);
                    echo '<div style="color:red">Error: Failed to add answer to database.</div>';
                }
            }
        }

        return $quizId;
    }

    /**
     * Get a quiz by ID
     * 
     * @param int $quizId The ID of the quiz
     * @return array|bool The quiz data or false on failure
     */
    public function getQuiz($quizId) {
        $studentId = $this->authController->getCurrentStudentId();
        if (!$studentId) {
            return false;
        }

        // Get the quiz
        $quiz = $this->quizModel->getCompleteQuiz($quizId);
        if (!$quiz) {
            return false;
        }

        // Get the note to verify ownership
        $note = $this->notesController->getNote($quiz['note_id']);
        if (!$note) {
            return false;
        }

        return $quiz;
    }

    /**
     * Get all quizzes for a note
     * 
     * @param int $noteId The ID of the note
     * @return array The quizzes for the note
     */
    public function getQuizzesByNote($noteId) {
        $studentId = $this->authController->getCurrentStudentId();
        if (!$studentId) {
            return [];
        }

        // Verify note ownership
        $note = $this->notesController->getNote($noteId);
        if (!$note) {
            return [];
        }

        return $this->quizModel->getQuizzesByNoteId($noteId);
    }

    /**
     * Delete a quiz
     * 
     * @param int $quizId The ID of the quiz
     * @return bool Whether the operation was successful
     */
    public function deleteQuiz($quizId) {
        $studentId = $this->authController->getCurrentStudentId();
        if (!$studentId) {
            return false;
        }

        // Get the quiz
        $quiz = $this->quizModel->getQuizById($quizId);
        if (!$quiz) {
            return false;
        }

        // Verify note ownership
        $note = $this->notesController->getNote($quiz['note_id']);
        if (!$note) {
            return false;
        }

        return $this->quizModel->deleteQuiz($quizId);
    }

    /**
     * Record a quiz attempt
     * 
     * @param int $quizId The ID of the quiz
     * @param array $answers The answers submitted by the student (question_id => answer_id)
     * @return array The results of the attempt
     */
    public function submitQuizAttempt($quizId, $answers) {
        $studentId = $this->authController->getCurrentStudentId();
        if (!$studentId) {
            return ['success' => false, 'message' => 'Not authenticated'];
        }

        // Get the quiz
        $quiz = $this->quizModel->getCompleteQuiz($quizId);
        if (!$quiz) {
            return ['success' => false, 'message' => 'Quiz not found'];
        }

        // Verify note ownership
        $note = $this->notesController->getNote($quiz['note_id']);
        if (!$note) {
            return ['success' => false, 'message' => 'Note not found'];
        }

        // Calculate score
        $score = 0;
        $totalQuestions = count($quiz['questions']);
        $results = [];

        foreach ($quiz['questions'] as $question) {
            $questionId = $question['id'];
            $selectedAnswerId = isset($answers[$questionId]) ? (int)$answers[$questionId] : null;
            $correctAnswer = null;
            $selectedAnswer = null;

            // Find the correct answer and the selected answer
            foreach ($question['answers'] as $answer) {
                if ($answer['is_correct']) {
                    $correctAnswer = $answer;
                }
                if ($answer['id'] == $selectedAnswerId) {
                    $selectedAnswer = $answer;
                }
            }

            $isCorrect = false;
            if ($selectedAnswer && $selectedAnswer['is_correct']) {
                $isCorrect = true;
                $score++;
            }

            $results[] = [
                'question_id' => $questionId,
                'question_text' => $question['question_text'],
                'selected_answer_id' => $selectedAnswerId,
                'correct_answer_id' => $correctAnswer ? $correctAnswer['id'] : null,
                'is_correct' => $isCorrect,
                'explanation' => $correctAnswer ? $correctAnswer['explanation'] : ''
            ];
        }

        // Record the attempt
        $scorePercent = $totalQuestions > 0 ? ($score / $totalQuestions) * 100 : 0;
        $this->quizModel->recordAttempt($studentId, $quizId, $scorePercent, $totalQuestions);

        return [
            'success' => true,
            'score' => $score,
            'total_questions' => $totalQuestions,
            'percentage' => $scorePercent,
            'results' => $results
        ];
    }

    /**
     * Get all quizzes for the current student
     * 
     * @return array The quizzes for the student
     */
    public function getAllQuizzes() {
        $studentId = $this->authController->getCurrentStudentId();
        if (!$studentId) {
            return [];
        }

        $quizzes = $this->quizModel->getAllQuizzesByStudent($studentId);

        // Get the latest attempt for each quiz
        foreach ($quizzes as &$quiz) {
            $latestAttempt = $this->quizModel->getLatestAttemptByStudentAndQuiz($studentId, $quiz['id']);
            $quiz['latest_attempt'] = $latestAttempt;
        }

        return $quizzes;
    }

    /**
     * Get the latest attempt for a quiz
     * 
     * @param int $quizId The ID of the quiz
     * @return array|bool The latest attempt or false if none exists
     */
    public function getLatestAttempt($quizId) {
        $studentId = $this->authController->getCurrentStudentId();
        if (!$studentId) {
            return false;
        }

        return $this->quizModel->getLatestAttemptByStudentAndQuiz($studentId, $quizId);
    }
}
