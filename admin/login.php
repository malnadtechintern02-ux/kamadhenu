<?php
/**
 * Admin Login
 * Kamadenu Goushala
 */

define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/config/config.php';

// Redirect if already logged in
if (isAdminLoggedIn()) {
    redirect(ADMIN_URL . '/index.php');
}

$error = '';

if (isPost()) {
    requireCsrfToken();
    
    $username = getParam('username', '', 'POST');
    $password = getParam('password', '', 'POST');
    
    if (empty($username) || empty($password)) {
        $error = 'Please enter both username and password.';
    } elseif (attemptLogin($username, $password)) {
        setFlash('success', 'Welcome back, ' . ($_SESSION['admin_name'] ?? 'Admin') . '!');
        redirect(ADMIN_URL . '/index.php');
    } else {
        $error = 'Invalid username or password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | Kamadenu Goushala</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --clr-primary: #2E7D32;
            --clr-primary-dark: #1B5E20;
            --clr-cream: #FFFDF5;
            --clr-brown: #4E342E;
        }
        body { font-family: 'Inter', sans-serif; background: linear-gradient(135deg, var(--clr-primary-dark) 0%, var(--clr-primary) 50%, var(--clr-brown) 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .login-card { background: white; border-radius: 1rem; box-shadow: 0 20px 60px rgba(0,0,0,0.3); max-width: 420px; width: 100%; padding: 2.5rem; }
        .login-logo { width: 60px; height: 60px; background: linear-gradient(135deg, var(--clr-primary), var(--clr-primary-dark)); border-radius: 0.75rem; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.5rem; margin: 0 auto 1rem; }
        .login-title { font-family: 'Playfair Display', serif; color: var(--clr-brown); text-align: center; font-size: 1.5rem; margin-bottom: 0.25rem; }
        .login-subtitle { text-align: center; color: #8D6E63; font-size: 0.88rem; margin-bottom: 2rem; }
        .form-control { border-radius: 0.5rem; padding: 0.65rem 0.9rem; border: 1px solid #E0D5C7; }
        .form-control:focus { border-color: var(--clr-primary); box-shadow: 0 0 0 0.15rem rgba(46,125,50,0.15); }
        .btn-login { background: var(--clr-primary); color: white; border: none; border-radius: 0.5rem; padding: 0.7rem; font-weight: 600; width: 100%; transition: all 0.3s; }
        .btn-login:hover { background: var(--clr-primary-dark); color: white; transform: translateY(-1px); }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-logo"><i class="bi bi-flower2"></i></div>
        <h1 class="login-title">Admin Panel</h1>
        <p class="login-subtitle">Kamadenu Goushala Management</p>
        
        <?php if ($error): ?>
        <div class="alert alert-danger d-flex align-items-center py-2" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <div><?= e($error) ?></div>
        </div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <?= csrfField() ?>
            <div class="mb-3">
                <label for="username" class="form-label fw-medium">Username or Email</label>
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="bi bi-person"></i></span>
                    <input type="text" class="form-control" id="username" name="username" required autofocus>
                </div>
            </div>
            <div class="mb-4">
                <label for="password" class="form-label fw-medium">Password</label>
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="bi bi-lock"></i></span>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
            </div>
            <button type="submit" class="btn btn-login">
                <i class="bi bi-box-arrow-in-right me-1"></i> Sign In
            </button>
        </form>
        <p class="text-center mt-3 small text-muted">
            <a href="<?= SITE_URL ?>/" class="text-decoration-none"><i class="bi bi-arrow-left me-1"></i> Back to Website</a>
        </p>
    </div>
</body>
</html>
