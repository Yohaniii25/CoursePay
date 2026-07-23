<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin Login - Gem and Jewelry Research Institute</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="login-body">
    <div class="login-card">
        <div class="login-header">
            <h1>Gem and Jewelry Research Institute</h1>
        </div>

        <form action="login_handler.php" method="post" class="login-form" id="login-form">
            <?php if (isset($_GET['error'])): ?>
                <div class="alert-error" role="alert">
                    <i class="fas fa-exclamation-circle"></i>
                    <p><?php echo htmlspecialchars($_GET['error']); ?></p>
                </div>
            <?php endif; ?>

            <div class="input-field-group">
                <label for="email" class="input-label">Email</label>
                <div class="input-icon-wrapper">
                    <span class="input-icon-left">
                        <i class="fas fa-envelope"></i>
                    </span>
                    <input type="email" name="email" id="email" placeholder="Enter your email" required class="login-input" />
                </div>
            </div>

            <div class="input-field-group">
                <label for="password" class="input-label">Password</label>
                <div class="input-icon-wrapper">
                    <span class="input-icon-left">
                        <i class="fas fa-lock"></i>
                    </span>
                    <input type="password" name="password" id="password" placeholder="Enter your password" required class="login-input has-right-icon" />
                    <span class="input-icon-right" onclick="togglePassword()">
                        <i class="fas fa-eye" id="toggle-password"></i>
                    </span>
                </div>
            </div>

            <div class="login-options">
                <a href="forgot-password.php" class="forgot-link">Forgot Password?</a>
            </div>

            <button type="submit" class="login-submit-btn" id="submit-btn">
                <span>Login</span>
                <i class="fas fa-sign-in-alt"></i>
            </button>
        </form>

        <div class="login-footer">
            <p>&copy; <?php echo date('Y'); ?> Gem and Jewelry Research Institute</p>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggle-password');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }

        document.getElementById('login-form').addEventListener('submit', function(e) {
            const submitBtn = document.getElementById('submit-btn');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Logging in...';
        });
    </script>
</body>
</html>