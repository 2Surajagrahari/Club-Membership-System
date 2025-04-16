<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<style>
    .checkmark__circle {
        stroke-dasharray: 166;
        stroke-dashoffset: 166;
        stroke-width: 3;
        stroke-miterlimit: 10;
        animation: stroke 0.6s cubic-bezier(0.65, 0, 0.45, 1) forwards;
    }
    
    .checkmark__check {
        transform-origin: 50% 50%;
        stroke-dasharray: 48;
        stroke-dashoffset: 48;
        animation: stroke 0.3s cubic-bezier(0.65, 0, 0.45, 1) 0.6s forwards;
    }
    
    @keyframes stroke {
        100% { stroke-dashoffset: 0; }
    }
</style>
<body>
<div class="bg-gradient-to-br from-white to-blue-50 bg-opacity-95 backdrop-blur-lg p-8 rounded-2xl shadow-xl w-full lg:w-1/2 border border-blue-100 mx-auto mt-10 transform transition-all duration-500 hover:shadow-2xl hover:-translate-y-1">
    <!-- Animated checkmark -->
    <div class="flex justify-center mb-6">
        <svg class="checkmark animate-draw" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52" width="80" height="80">
            <circle class="checkmark__circle" cx="26" cy="26" r="25" fill="none" stroke="#4CAF50" stroke-width="3"/>
            <path class="checkmark__check" fill="none" stroke="#4CAF50" stroke-width="4" stroke-linecap="round" d="M14.1 27.2l7.1 7.2 16.7-16.8"/>
        </svg>
    </div>
    
    <h2 class="text-4xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-green-600 to-blue-600 mb-6 text-center tracking-tight">
        Application Received!
    </h2>
    
    <div class="text-center mb-8 space-y-3">
        <p class="text-xl font-medium text-gray-700">Thank you for joining our community!</p>
        <p class="text-gray-600">We've sent a confirmation to <span class="font-semibold text-blue-600">your email</span>.</p>
        
        <!-- Animated progress indicator -->
        <div class="pt-4">
            <div class="relative pt-1">
                <div class="flex mb-2 items-center justify-between">
                    <div>
                        <span class="text-xs font-semibold inline-block py-1 px-2 uppercase rounded-full text-blue-600 bg-blue-200">
                            Processing
                        </span>
                    </div>
                    <div class="text-right">
                        <span class="text-xs font-semibold inline-block text-blue-600">
                            1/3 steps
                        </span>
                    </div>
                </div>
                <div class="overflow-hidden h-2 mb-4 text-xs flex rounded bg-blue-200">
                    <div style="width:33%" class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-gradient-to-r from-blue-500 to-blue-400 animate-pulse"></div>
                </div>
            </div>
        </div>
        
        <p class="text-sm text-gray-500 italic">Our team will review your application within 24-48 hours.</p>
    </div>
    
    <!-- Confetti button -->
    <a><button onclick="confetti()" class="relative overflow-hidden bg-gradient-to-r from-blue-600 to-blue-500 text-white px-8 py-4 rounded-xl shadow-lg hover:shadow-xl hover:from-blue-700 hover:to-blue-600 transition-all duration-300 w-full flex items-center justify-center gap-3 group">
        <span class="relative z-10 flex items-center">
            <i class="fas fa-home mr-2 transition-transform group-hover:scale-110"></i>
            <span class="font-medium tracking-wide">Return Home</span>
        
        <span class="absolute inset-0 bg-gradient-to-r from-blue-700 to-blue-600 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></span>
    </button></a>
    
    <!-- Social sharing -->
    <div class="mt-6 text-center">
        <p class="text-sm text-gray-500 mb-3">Share with friends</p>
        <div class="flex justify-center space-x-4">
            <a href="#" class="w-10 h-10 rounded-full bg-blue-100 hover:bg-blue-200 flex items-center justify-center text-blue-600 transition-colors">
                <i class="fab fa-facebook-f"></i>
            </a>
            <a href="#" class="w-10 h-10 rounded-full bg-blue-100 hover:bg-blue-200 flex items-center justify-center text-blue-400 transition-colors">
                <i class="fab fa-twitter"></i>
            </a>
            <a href="#" class="w-10 h-10 rounded-full bg-blue-100 hover:bg-blue-200 flex items-center justify-center text-red-500 transition-colors">
                <i class="fab fa-instagram"></i>
            </a>
        </div>
    </div>
</div>

<!-- Confetti library -->
<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.5.1/dist/confetti.browser.min.js"></script>
<script>
    function confetti() {
        confetti({
            particleCount: 100,
            spread: 70,
            origin: { y: 0.6 }
        });
        setTimeout(() => {
            window.location.href = "index.php";
        }, 800);
    }
    
    // Auto-trigger confetti on page load
    window.addEventListener('load', () => {
        setTimeout(confetti, 300);
    });
</script>


</body>
</html>