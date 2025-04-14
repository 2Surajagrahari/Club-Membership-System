<?php

session_start();


$preserved_email = isset($_GET['email']) ? htmlspecialchars($_GET['email']) : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- TailwindCSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <title>ClubSphere</title>

    <style>
        /* Preloader Styling */
        #preloader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            z-index: 1000;
            transition: opacity 0.5s ease-out;
        }
        .spin {
            width: 15px;
            height: 15px;
            margin: 5px;
            border-radius: 50%;
            background-color: #3498db;
            animation: bounce 1.5s infinite ease-in-out;
        }
        .spin1 { animation-delay: -0.3s; }
        .spin2 { animation-delay: -0.2s; }
        .spin3 { animation-delay: -0.1s; }

        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        /* Fade-out effect for Preloader */
        .fade-out {
            opacity: 0;
            visibility: hidden;
        }

        /* Form transitions */
        .form-container {
            transition: all 0.4s ease-in-out;
        }
        .form-hidden {
            opacity: 0;
            height: 0;
            overflow: hidden;
            transform: translateY(20px);
        }
        .form-visible {
            opacity: 1;
            height: auto;
            transform: translateY(0);
        }

        /* Input focus effects */
        .input-field:focus {
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
        }

        /* Error animation */
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
            20%, 40%, 60%, 80% { transform: translateX(5px); }
        }
        .shake {
            animation: shake 0.5s;
        }

        /* Error message styling */
.error-message {
    position: fixed;
    bottom: 20px;
    right: 20px;
    background-color: #fee2e2;
    border: 1px solid #ef4444;
    color: #b91c1c;
    padding: 1rem;
    border-radius: 0.5rem;
    max-width: 300px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    z-index: 1000;
    animation: slideIn 0.5s forwards, fadeOut 0.5s forwards 3s;
}

.error-message button {
    background: none;
    border: none;
    color: #b91c1c;
    cursor: pointer;
    font-size: 1.2rem;
    margin-left: 10px;
}

/* Animation for sliding in from right */
@keyframes slideIn {
    from { transform: translateX(100%); opacity: 0; }
    to { transform: translateX(0); opacity: 1; }
}

/* Animation for fading out */
@keyframes fadeOut {
    from { opacity: 1; }
    to { opacity: 0; }
}
    </style>
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100">
<?php if (isset($_SESSION['error'])): ?>
        <div class="error-message">
            <span><?php echo $_SESSION['error']; ?></span>
            <button onclick="this.parentElement.remove()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <!-- Preloader -->
    <div id="preloader">
        <div class="flex">
            <div class="spin spin1"></div>
            <div class="spin spin2"></div>
            <div class="spin spin3"></div>
        </div>
        <h1 class="mt-4 text-xl font-bold text-blue-600 animate-bounce">ClubSphere</h1>
    </div>

    <nav class="bg-gradient-to-r from-blue-600 to-indigo-700 text-white p-4 shadow-lg">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-3xl font-bold flex items-center">
                <i class="fas fa-users mr-2"></i> ClubSphere
            </h1>
            <a href="index.php" class="text-lg bg-white text-blue-600 px-4 py-2 rounded-md font-semibold hover:bg-gray-100 transition flex items-center">
                <i class="fas fa-home mr-2"></i> Back to Home
            </a>
        </div>
    </nav>

    <div class="flex flex-col items-center justify-center min-h-screen p-6">
        <!-- Login / Signup Form Container -->
        <div class="bg-white p-8 rounded-2xl shadow-xl w-full max-w-md">
        <!--  -->
                    
            <!-- Form Toggle Buttons -->
            <div class="flex mb-6 border-b">
                <button id="loginTab" class="flex-1 py-2 font-medium text-blue-600 border-b-2 border-blue-600 focus:outline-none">
                    Login
                </button>
                <button id="signupTab" class="flex-1 py-2 font-medium text-gray-500 focus:outline-none">
                    Sign Up
                </button>
            </div>

            <!-- Error Message Container -->
            <div id="formError" class="hidden mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded-lg"></div>

            <!-- Login Form -->
            <div id="loginForm" class="form-container form-visible">
                <form id="loginFormElement" action="databases.php" method="POST">
                    <div class="mb-4">
                        <label for="loginEmail" class="block text-gray-700 text-sm font-medium mb-1">Email</label>
                        <div class="relative">
                        <input type="email" id="loginEmail" name="email" placeholder="your@email.com" required 
           value="<?php echo $preserved_email; ?>"
           class="w-full p-3 pl-10 border rounded-lg input-field focus:border-blue-500 transition">
                            <i class="fas fa-envelope absolute left-3 top-3.5 text-gray-400"></i>
                        </div>
                        <p id="loginEmailError" class="hidden mt-1 text-xs text-red-500"></p>
                    </div>

                    <div class="mb-6">
                        <label for="loginPassword" class="block text-gray-700 text-sm font-medium mb-1">Password</label>
                        <div class="relative">
                            <input type="password" id="loginPassword" name="password" placeholder="••••••••" required 
                                   class="w-full p-3 pl-10 border rounded-lg input-field focus:border-blue-500 transition">
                            <i class="fas fa-lock absolute left-3 top-3.5 text-gray-400"></i>
                            <button type="button" class="absolute right-3 top-3.5 text-gray-400 hover:text-gray-600" onclick="togglePassword('loginPassword')">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <p id="loginPasswordError" class="hidden mt-1 text-xs text-red-500"></p>
                    </div>

                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <input type="checkbox" id="rememberMe" name="remember" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="rememberMe" class="ml-2 block text-sm text-gray-700">Remember me</label>
                        </div>
                        <a href="#" class="text-sm text-blue-600 hover:underline">Forgot password?</a>
                    </div>

                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-lg shadow-md transition duration-300 flex items-center justify-center" name="login">
                        <i class="fas fa-sign-in-alt mr-2"></i> Login
                    </button>
                </form>

                <div class="mt-6 text-center">
                    <p class="text-gray-600">Don't have an account? 
                        <button onclick="showSignupForm()" class="text-blue-600 hover:underline font-medium">Sign Up</button>
                    </p>
                </div>
            </div>

            <!-- Sign Up Form -->
            <div id="signUpForm" class="form-container form-hidden">
                <form id="signupFormElement" action="databases.php" method="POST">
                    <div class="mb-4">
                        <label for="signupName" class="block text-gray-700 text-sm font-medium mb-1">Full Name</label>
                        <div class="relative">
                            <input type="text" id="signupName" name="name" placeholder="John Doe" required 
                                   class="w-full p-3 pl-10 border rounded-lg input-field focus:border-indigo-500 transition">
                            <i class="fas fa-user absolute left-3 top-3.5 text-gray-400"></i>
                        </div>
                        <p id="signupNameError" class="hidden mt-1 text-xs text-red-500"></p>
                    </div>

                    <div class="mb-4">
                        <label for="signupEmail" class="block text-gray-700 text-sm font-medium mb-1">Email</label>
                        <div class="relative">
                            <input type="email" id="signupEmail" name="email" placeholder="your@email.com" required 
                                   class="w-full p-3 pl-10 border rounded-lg input-field focus:border-indigo-500 transition">
                            <i class="fas fa-envelope absolute left-3 top-3.5 text-gray-400"></i>
                        </div>
                        <p id="signupEmailError" class="hidden mt-1 text-xs text-red-500"></p>
                    </div>

                    <div class="mb-4">
                        <label for="signupPassword" class="block text-gray-700 text-sm font-medium mb-1">Password</label>
                        <div class="relative">
                            <input type="password" id="signupPassword" name="password" placeholder="••••••••" required 
                                   class="w-full p-3 pl-10 border rounded-lg input-field focus:border-indigo-500 transition"
                                   minlength="6">
                            <i class="fas fa-lock absolute left-3 top-3.5 text-gray-400"></i>
                            <button type="button" class="absolute right-3 top-3.5 text-gray-400 hover:text-gray-600" onclick="togglePassword('signupPassword')">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <p id="signupPasswordError" class="hidden mt-1 text-xs text-red-500"></p>
                        <p class="mt-1 text-xs text-gray-500">Minimum 6 characters</p>
                    </div>

                    <div class="mb-6">
                        <label for="signupRole" class="block text-gray-700 text-sm font-medium mb-1">Role</label>
                        <div class="relative">
                            <select id="signupRole" name="role" class="w-full p-3 pl-10 border rounded-lg input-field focus:border-indigo-500 appearance-none">
                                <option value="user">User</option>
                                <option value="admin">Admin</option>
                            </select>
                            <i class="fas fa-user-tag absolute left-3 top-3.5 text-gray-400"></i>
                            <i class="fas fa-chevron-down absolute right-3 top-3.5 text-gray-400"></i>
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-3 px-4 rounded-lg shadow-md transition duration-300 flex items-center justify-center" name="register">
                        <i class="fas fa-user-plus mr-2"></i> Sign Up
                    </button>
                </form>

                <div class="mt-6 text-center">
                    <p class="text-gray-600">Already have an account? 
                        <button onclick="showLoginForm()" class="text-blue-600 hover:underline font-medium">Login</button>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Preloader Hide After Load
        window.onload = function() {
            document.getElementById("preloader").classList.add("fade-out");
            setTimeout(() => document.getElementById("preloader").style.display = "none", 500);

            <?php if (isset($_GET['email'])): ?>
                document.getElementById("loginEmail").focus();
            <?php endif; ?>
        };

        // Toggle between login and signup forms
        function showLoginForm() {       
            document.getElementById("loginForm").classList.remove("form-hidden");
            document.getElementById("loginForm").classList.add("form-visible");
            document.getElementById("signUpForm").classList.remove("form-visible");
            document.getElementById("signUpForm").classList.add("form-hidden");
            
            document.getElementById("loginTab").classList.add("border-b-2", "border-blue-600", "text-blue-600");
            document.getElementById("loginTab").classList.remove("text-gray-500");
            document.getElementById("signupTab").classList.remove("border-b-2", "border-blue-600", "text-blue-600");
            document.getElementById("signupTab").classList.add("text-gray-500");
            
            clearErrors();
        }

        function showSignupForm() {
            document.getElementById("signUpForm").classList.remove("form-hidden");
            document.getElementById("signUpForm").classList.add("form-visible");
            document.getElementById("loginForm").classList.remove("form-visible");
            document.getElementById("loginForm").classList.add("form-hidden");
            
            document.getElementById("signupTab").classList.add("border-b-2", "border-indigo-600", "text-indigo-600");
            document.getElementById("signupTab").classList.remove("text-gray-500");
            document.getElementById("loginTab").classList.remove("border-b-2", "border-indigo-600", "text-indigo-600");
            document.getElementById("loginTab").classList.add("text-gray-500");
            
            clearErrors();
        }

        // Tab button event listeners
        document.getElementById("loginTab").addEventListener("click", showLoginForm);
        document.getElementById("signupTab").addEventListener("click", showSignupForm);

        // Toggle password visibility
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const icon = field.nextElementSibling.querySelector("i");
            
            if (field.type === "password") {
                field.type = "text";
                icon.classList.remove("fa-eye");
                icon.classList.add("fa-eye-slash");
            } else {
                field.type = "password";
                icon.classList.remove("fa-eye-slash");
                icon.classList.add("fa-eye");
            }
        }

        // Form validation
        function validateEmail(email) {
            const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return re.test(email);
        }

        function clearErrors() {
            document.getElementById("formError").classList.add("hidden");
            
            // Clear all field-specific errors
            const errorElements = document.querySelectorAll("[id$='Error']");
            errorElements.forEach(el => {
                el.classList.add("hidden");
                el.textContent = "";
            });
        }

        function showError(message) {
            const errorContainer = document.getElementById("formError");
            errorContainer.textContent = message;
            errorContainer.classList.remove("hidden");
            errorContainer.classList.add("shake");
            
            // Remove shake animation after it completes
            setTimeout(() => {
                errorContainer.classList.remove("shake");
            }, 500);
        }

        function showFieldError(fieldId, message) {
            const errorElement = document.getElementById(fieldId + "Error");
            if (errorElement) {
                errorElement.textContent = message;
                errorElement.classList.remove("hidden");
                
                // Highlight the problematic field
                const inputField = document.getElementById(fieldId);
                inputField.classList.add("border-red-500");
                inputField.addEventListener("input", function() {
                    inputField.classList.remove("border-red-500");
                    errorElement.classList.add("hidden");
                }, { once: true });
            }
        }

        // Login form validation
        document.getElementById("loginFormElement").addEventListener("submit", function(e) {
            const email = document.getElementById("loginEmail").value;
            const password = document.getElementById("loginPassword").value;
            
            // Clear previous errors
            document.querySelectorAll('.error-message').forEach(el => el.remove());
            
            // Simple validation
            if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                e.preventDefault();
                showError("Please enter a valid email address");
                return;
            }
            
            if (!password) {
                e.preventDefault();
                showError("Please enter your password");
                return;
            }
            
            // If validation passes, let the form submit to databases.php
        });

        function showError(message) {
            const errorDiv = document.createElement('div');
            errorDiv.className = 'error-message';
            errorDiv.innerHTML = `
                <span>${message}</span>
                <button onclick="this.parentElement.remove()">
                    <i class="fas fa-times"></i>
                </button>
            `;
            document.querySelector('body').prepend(errorDiv);
        }

        // Signup form validation
        document.getElementById("signupFormElement").addEventListener("submit", function(e) {
            clearErrors();
            
            const name = document.getElementById("signupName").value;
            const email = document.getElementById("signupEmail").value;
            const password = document.getElementById("signupPassword").value;
            let isValid = true;
            
            if (!name) {
                showFieldError("signupName", "Name is required");
                isValid = false;
            }
            
            if (!email) {
                showFieldError("signupEmail", "Email is required");
                isValid = false;
            } else if (!validateEmail(email)) {
                showFieldError("signupEmail", "Please enter a valid email");
                isValid = false;
            }
            
            if (!password) {
                showFieldError("signupPassword", "Password is required");
                isValid = false;
            } else if (password.length < 6) {
                showFieldError("signupPassword", "Password must be at least 6 characters");
                isValid = false;
            }
            
            if (!isValid) {
                e.preventDefault();
                showError("Please fix the errors in the form");
            }
        });

document.addEventListener('DOMContentLoaded', function() {
    const errorMessage = document.querySelector('.error-message');
    if (errorMessage) {
        setTimeout(() => {
            errorMessage.style.animation = 'fadeOut 0.5s forwards';
            setTimeout(() => errorMessage.remove(), 500);
        }, 5000);
    }
});
    </script>
</body>
</html>