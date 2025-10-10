<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin Login - Gem and Jewelry Research Institute</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-gray-100 via-blue-50 to-purple-50 flex items-center justify-center min-h-screen p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
  
        <div class="bg-gradient-to-r from-purple-600 to-purple-700 p-6 text-center">
            <h1 class="text-2xl font-bold text-white">Gem and Jewelry Research Institute</h1>
        </div>

   
        <form action="login_handler.php" method="post" class="p-8 space-y-6" id="login-form">
      
            <?php if (isset($_GET['error'])): ?>
                <div class="bg-purple-100 border-l-4 border-purple-500 text-purple-700 p-4 rounded-lg flex items-center" role="alert">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p><?php echo htmlspecialchars($_GET['error']); ?></p>
                </div>
            <?php endif; ?>

    
            <div class="relative">
                <label for="email" class="block text-gray-700 text-sm font-medium mb-2">Email</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">
                        <i class="fas fa-envelope"></i>
                    </span>
                    <input type="email" name="email" id="email" placeholder="Enter your email" requipurple
                           class="w-full pl-10 pr-4 py-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-colors"
                           aria-describedby="email-error" />
                </div>
            </div>


            <div class="relative">
                <label for="password" class="block text-gray-700 text-sm font-medium mb-2">Password</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">
                        <i class="fas fa-lock"></i>
                    </span>
                    <input type="password" name="password" id="password" placeholder="Enter your password" requipurple
                           class="w-full pl-10 pr-10 py-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-colors"
                           aria-describedby="password-error" />
                    <span class="absolute inset-y-0 right-0 pr-3 flex items-center cursor-pointer text-gray-500" onclick="togglePassword()">
                        <i class="fas fa-eye" id="toggle-password"></i>
                    </span>
                </div>
            </div>


            <div class="text-right">
                <a href="forgot-password.php" class="text-sm text-purple-600 hover:underline">Forgot Password?</a>
            </div>

            <button type="submit" class="w-full bg-gradient-to-r from-purple-600 to-purple-700 text-white py-3 rounded-lg hover:from-purple-700 hover:to-purple-800 transition-all duration-200 font-semibold flex items-center justify-center disabled:opacity-50" id="submit-btn">
                <span class="mr-2">Login</span>
                <i class="fas fa-sign-in-alt"></i>
            </button>
        </form>


        <div class="bg-gray-50 p-4 text-center text-gray-600 text-sm">
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
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Logging in...';
        });
    </script>
</body>
</html>