<?php
require_once '../config.php';
header('Content-Type: application/json');

$action = $_POST['action'] ?? '';

if ($action === 'register') {
    $firstName = trim($_POST['first_name'] ?? '');
    $lastName  = trim($_POST['last_name']  ?? '');
    $email     = trim($_POST['email']      ?? '');
    $pass      = $_POST['password']        ?? '';

    if (!$firstName || !$lastName || !$email || !$pass) {
        echo json_encode(['success' => false, 'message' => 'All fields are required.']);
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Invalid email format.']);
        exit;
    }

    $hash = password_hash($pass, PASSWORD_DEFAULT);

    try {
        $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, password_hash) VALUES (?, ?, ?, ?)");
        $stmt->execute([$firstName, $lastName, $email, $hash]);
        echo json_encode(['success' => true, 'message' => 'Account created! You can now sign in.']);
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            echo json_encode(['success' => false, 'message' => 'An account with this email already exists.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Registration failed. Please try again.']);
        }
    }

} elseif ($action === 'login') {
    $email = trim($_POST['email']    ?? '');
    $pass  = $_POST['password']      ?? '';

    if (!$email || !$pass) {
        echo json_encode(['success' => false, 'message' => 'Email and password are required.']);
        exit;
    }

    try {
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($pass, $user['password_hash'])) {
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid email or password.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Server error. Please try again.']);
    }

} elseif ($action === 'logout') {
    session_destroy();
    echo json_encode(['success' => true]);

} else {
    echo json_encode(['success' => false, 'message' => 'Invalid action.']);
}
?>
