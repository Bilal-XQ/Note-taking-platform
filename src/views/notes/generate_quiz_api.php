<?php
// File: generate_quiz_api.php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
if (!isset($data['content']) || empty($data['content'])) {
    http_response_code(400);
    echo json_encode(['error' => 'No note content provided.']);
    exit;
}

$content = $data['content'];

// Get API key from environment variables
$apiKey = $_ENV['GEMINI_API_KEY'] ?? getenv('GEMINI_API_KEY');
$url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent';

// Prepare the request data for quiz generation
$requestData = [
    'contents' => [
        [
            'parts' => [
                [
                    'text' => "Generate a quiz (at least 3 questions, each with 4 options and the correct answer marked) based on the following note content. Format the response as JSON with an array of questions, each having 'question', 'options', and 'answer':\n\n" . $content
                ]
            ]
        ]
    ],
    'generationConfig' => [
        'temperature' => 0.3,
        'maxOutputTokens' => 1200,
        'topP' => 0.8,
        'topK' => 40
    ]
];

$ch = curl_init($url . '?key=' . $apiKey);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestData));
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json'
]);

$response = curl_exec($ch);
if (curl_errno($ch)) {
    http_response_code(500);
    echo json_encode(['error' => 'Quiz generation failed: ' . curl_error($ch)]);
    curl_close($ch);
    exit;
}
curl_close($ch);

// Debug: log the full Gemini API response for troubleshooting
file_put_contents(__DIR__ . '/gemini_api_debug.log', $response . PHP_EOL, FILE_APPEND);

$responseData = json_decode($response, true);
if (!isset($responseData['candidates'][0]['content']['parts'][0]['text'])) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Unexpected response from Gemini API.',
        'debug' => $responseData // Add debug info for troubleshooting
    ]);
    exit;
}

// Try to extract the JSON quiz from the response text
$quizText = $responseData['candidates'][0]['content']['parts'][0]['text'];
$quizJson = null;
// Remove markdown code block if present
$quizText = preg_replace('/^```json|^```/m', '', $quizText); // Remove opening code block
$quizText = preg_replace('/```$/m', '', $quizText); // Remove closing code block
$quizText = trim($quizText);
$quizJson = json_decode($quizText, true);

if (!$quizJson || !isset($quizJson['questions'])) {
    http_response_code(500);
    echo json_encode(['error' => 'Could not parse quiz from Gemini response.']);
    exit;
}

echo json_encode(['quiz' => $quizJson['questions']]);
