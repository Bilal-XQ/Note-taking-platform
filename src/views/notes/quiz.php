<?php
// quiz.php: Displays a quiz for a given note_id
require_once __DIR__ . '/../../../src/controllers/NotesController.php';

$noteId = isset($_GET['note_id']) ? (int)$_GET['note_id'] : 0;
$note = null;
if ($noteId) {
    $notesController = new NotesController();
    $note = $notesController->getNote($noteId);
}

?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz | StudyNotes</title>
    <link rel="stylesheet" href="/main/public/css/modern-dashboard.css">
    <link rel="stylesheet" href="/main/public/css/notes-list-fix.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body { font-family: 'Inter', Arial, sans-serif; background: #f5f7fa; margin: 0; }
        .quiz-container { max-width: 700px; margin: 48px auto; background: #fff; border-radius: 14px; box-shadow: 0 4px 24px rgba(0,0,0,0.08); padding: 36px 32px; }
        .quiz-title { font-size: 2rem; font-weight: 700; margin-bottom: 18px; color: #2c3e50; }
        .quiz-loading { text-align: center; padding: 40px 0; color: #3498db; font-size: 1.2rem; }
        .quiz-error { color: #e74c3c; background: #fdf3f2; border-left: 4px solid #e74c3c; padding: 18px; border-radius: 8px; margin-bottom: 24px; text-align: center; }
        .quiz-question { margin-bottom: 32px; }
        .quiz-question-title { font-weight: 600; font-size: 1.1rem; margin-bottom: 14px; }
        .quiz-options { display: flex; flex-direction: column; gap: 10px; }
        .quiz-option-label { background: #f8fafc; border-radius: 6px; padding: 12px 16px; cursor: pointer; border: 1px solid #e9ecef; transition: background 0.2s, border 0.2s; display: flex; align-items: center; }
        .quiz-option-label input[type="radio"] { margin-right: 12px; }
        .quiz-option-label.selected { background: #e3f2fd; border-color: #3498db; }
        .quiz-submit-btn { display: block; width: 100%; margin: 24px 0 0 0; padding: 14px 0; background: #3498db; color: #fff; font-size: 1.1rem; font-weight: 600; border: none; border-radius: 8px; cursor: pointer; transition: background 0.2s; }
        .quiz-submit-btn:disabled { background: #b3c6d6; cursor: not-allowed; }
        .quiz-score { margin-top: 24px; font-size: 1.2rem; font-weight: 600; color: #27ae60; text-align: center; }
        .quiz-spinner { border: 5px solid #e3eaf2; border-top: 5px solid #3498db; border-radius: 50%; width: 48px; height: 48px; animation: spin 1s linear infinite; margin: 0 auto 18px auto; }
        @keyframes spin { 0% { transform: rotate(0deg);} 100% { transform: rotate(360deg);} }
        .quiz-back-link { display: inline-block; margin-bottom: 24px; color: #3498db; text-decoration: none; font-weight: 500; }
        .quiz-back-link:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="quiz-container">
        <a href="/main/student/home.php" class="quiz-back-link"><i class="fas fa-arrow-left"></i> Back to Notes</a>
        <div class="quiz-title">Quiz for Note #<?php echo htmlspecialchars($noteId); ?></div>
        <?php if (!$note): ?>
            <div class="quiz-error">This quiz could not be loaded. Please check your note or try again.</div>
        <?php else: ?>
            <div id="quiz-content">
                <div class="quiz-loading"><div class="quiz-spinner"></div>Loading quiz...</div>
            </div>
        <?php endif; ?>
    </div>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var noteId = <?php echo (int)$noteId; ?>;
        var noteContent = <?php echo json_encode($note ? $note['content'] : ''); ?>;
        var quizContent = document.getElementById('quiz-content');
        if (!noteId || !noteContent) return;
        fetch('/main/src/views/notes/generate_quiz_api.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ content: noteContent })
        })
        .then(res => res.json())
        .then(data => {
            if (data.error) {
                quizContent.innerHTML = '<div class="quiz-error">Oops! Couldn\'t load quiz. Please try again later.</div>';
            } else if (data.quiz && Array.isArray(data.quiz)) {
                const quiz = data.quiz.slice(0, 5);
                let html = '<form id="quiz-form"><h3 style="margin-bottom:24px;">Quiz</h3>';
                quiz.forEach((q, idx) => {
                    html += `<div class='quiz-question'><div class='quiz-question-title'>Q${idx+1}: ${q.question}</div><div class='quiz-options'>`;
                    q.options.forEach((opt, oidx) => {
                        html += `<label class='quiz-option-label'><input type='radio' name='q${idx}' value='${opt}' required> ${opt}</label>`;
                    });
                    html += '</div></div>';
                });
                html += '<button type="submit" class="quiz-submit-btn">Submit</button>';
                html += '<div class="quiz-score" style="display:none;"></div>';
                html += '</form>';
                quizContent.innerHTML = html;
                // Add form submit handler
                const form = document.getElementById('quiz-form');
                form.addEventListener('submit', function(ev) {
                    ev.preventDefault();
                    let score = 0;
                    quiz.forEach((q, idx) => {
                        const selected = form.querySelector('input[name="q' + idx + '"]:checked');
                        if (selected && selected.value === q.answer) score++;
                    });
                    // Disable all inputs
                    form.querySelectorAll('input[type="radio"]').forEach(inp => inp.disabled = true);
                    form.querySelector('.quiz-submit-btn').disabled = true;
                    // Show score
                    const scoreDiv = form.querySelector('.quiz-score');
                    scoreDiv.style.display = 'block';
                    scoreDiv.textContent = `You got ${score}/5 correct.`;
                });
            } else {
                quizContent.innerHTML = '<div class="quiz-error">Oops! Couldn\'t load quiz. Please try again later.</div>';
            }
        })
        .catch(() => {
            quizContent.innerHTML = '<div class="quiz-error">Oops! Couldn\'t load quiz. Please try again later.</div>';
        });
    });
    </script>
</body>
</html>

