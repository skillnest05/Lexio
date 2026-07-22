<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lexio — AI Email Draft Helper</title>
    <meta name="description" content="Lexio helps you draft professional, casual, or persuasive emails in seconds using AI.">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<div class="auth-wrapper">
    <div class="auth-card">

        <!-- Brand -->
        <div class="brand-logo">
            <div class="brand-name">✦ Lexio</div>
            <div class="brand-tagline">Your AI Email Drafting Assistant</div>
        </div>

        <!-- ALERT BANNERS -->
        <div id="loginError"    class="alert-banner"></div>
        <div id="registerError" class="alert-banner"></div>

        <!-- ── LOGIN FORM ── -->
        <div id="loginBox">
            <div class="auth-title">Welcome back</div>
            <form id="loginForm" novalidate>
                <div class="mb-3">
                    <label class="form-label" for="loginEmail">Email address</label>
                    <input type="email" id="loginEmail" name="email" class="form-control" placeholder="you@example.com" required>
                </div>
                <div class="mb-4">
                    <label class="form-label" for="loginPassword">Password</label>
                    <input type="password" id="loginPassword" name="password" class="form-control" placeholder="••••••••" required>
                </div>
                <button type="submit" class="btn-primary-custom">Sign In</button>
            </form>
            <p class="auth-footer mt-3">Don't have an account? <a href="#" id="showRegister">Sign Up</a></p>
        </div>

        <!-- ── REGISTER FORM ── -->
        <div id="registerBox" class="d-none">
            <div class="auth-title">Create your account</div>
            <form id="registerForm" novalidate>
                <div class="row g-3 mb-3">
                    <div class="col-6">
                        <label class="form-label" for="regFirstName">First Name</label>
                        <input type="text" id="regFirstName" name="first_name" class="form-control" placeholder="John" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label" for="regLastName">Last Name</label>
                        <input type="text" id="regLastName" name="last_name" class="form-control" placeholder="Doe" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="regEmail">Email address</label>
                    <input type="email" id="regEmail" name="email" class="form-control" placeholder="you@example.com" required>
                </div>
                <div class="mb-4">
                    <label class="form-label" for="regPassword">Password</label>
                    <input type="password" id="regPassword" name="password" class="form-control" placeholder="Min. 8 characters" required>
                </div>
                <button type="submit" class="btn-primary-custom">Create Account</button>
            </form>
            <p class="auth-footer mt-3">Already have an account? <a href="#" id="showLogin">Sign In</a></p>
        </div>

    </div>
</div>

<script src="js/app.js"></script>
</body>
</html>
