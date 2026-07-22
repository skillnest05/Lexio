<?php
require_once '../config.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

try {
    $stmt = $conn->prepare(
        "SELECT id, sender_name, recipient_name, tone, length, prompt_text, generated_email, subject_line, created_at
         FROM emails
         WHERE user_id = ?
         ORDER BY created_at DESC
         LIMIT 30"
    );
    $stmt->execute([$_SESSION['user_id']]);
    $history = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['success' => true, 'history' => $history]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Could not load history.']);
}
?>
