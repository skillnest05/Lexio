<?php
require_once '../config.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized. Please sign in.']);
    exit;
}

$senderName    = trim($_POST['sender_name']    ?? '');
$recipientName = trim($_POST['recipient_name'] ?? '');
$tone          = trim($_POST['tone']           ?? 'professional');
$length        = trim($_POST['length']         ?? 'medium');
$description   = trim($_POST['description']    ?? '');

if (empty($description)) {
    echo json_encode(['success' => false, 'message' => 'Please provide a description of what the email should say.']);
    exit;
}

// Map length to instruction
$lengthMap = [
    'short'  => 'Keep it concise — around 3-5 sentences.',
    'medium' => 'Write a moderate-length email — around 2-3 short paragraphs.',
    'long'   => 'Write a detailed, comprehensive email — around 4-6 paragraphs.',
];
$lengthInstruction = $lengthMap[$length] ?? $lengthMap['medium'];

// Map tone
$toneMap = [
    'professional' => 'professional and formal',
    'casual'       => 'casual and friendly',
    'persuasive'   => 'persuasive and compelling',
];
$toneLabel = $toneMap[$tone] ?? 'professional and formal';

$senderPart    = $senderName    ? "The email is from: $senderName." : '';
$recipientPart = $recipientName ? "The email is addressed to: $recipientName." : '';

$prompt = "You are an expert email writer. Write a $toneLabel email based on the following details.\n"
        . "$senderPart $recipientPart\n"
        . "$lengthInstruction\n\n"
        . "Email context or key points:\n$description\n\n"
        . "Provide the output in two clearly labeled sections:\n"
        . "SUBJECT: (a concise email subject line)\n"
        . "BODY:\n(the email body)";

$url  = "https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent?key=" . $gemini_api_key;
$data = [
    "contents" => [
        ["parts" => [["text" => $prompt]]]
    ],
    "generationConfig" => [
        "temperature" => 0.7
    ]
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

$response = curl_exec($ch);
$curlError = curl_error($ch);
curl_close($ch);

if ($response === false) {
    echo json_encode(['success' => false, 'message' => 'Could not connect to AI: ' . $curlError]);
    exit;
}

$result = json_decode($response, true);

if (!isset($result['candidates'][0]['content']['parts'][0]['text'])) {
    echo json_encode(['success' => false, 'message' => 'Unexpected AI response. Please try again.']);
    exit;
}

$rawText = trim($result['candidates'][0]['content']['parts'][0]['text']);

// Parse subject and body
$subject = '';
$body    = $rawText;

if (preg_match('/SUBJECT:\s*(.+?)(?:\n|$)/i', $rawText, $subMatch)) {
    $subject = trim($subMatch[1]);
}
if (preg_match('/BODY:\s*([\s\S]+)/i', $rawText, $bodyMatch)) {
    $body = trim($bodyMatch[1]);
}

// Save to DB
try {
    $stmt = $conn->prepare("INSERT INTO emails (user_id, sender_name, recipient_name, tone, length, prompt_text, generated_email, subject_line) VALUES (?,?,?,?,?,?,?,?)");
    $stmt->execute([
        $_SESSION['user_id'],
        $senderName,
        $recipientName,
        $tone,
        $length,
        $description,
        $body,
        $subject
    ]);
} catch (PDOException $e) {
    // Don't fail the request for a save error
}

echo json_encode([
    'success' => true,
    'subject' => $subject,
    'body'    => $body
]);
?>
