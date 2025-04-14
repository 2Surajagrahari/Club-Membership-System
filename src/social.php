<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Club Posts | Membership System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        indigo: {
                            400: '#818cf8',
                            500: '#6366f1',
                            600: '#4f46e5',
                            700: '#4338ca',
                        },
                        blue: {
                            400: '#60a5fa',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                        }
                    },
                    backgroundImage: {
                        'gradient-indigo-blue': 'linear-gradient(to right, #6366f1, #3b82f6)',
                    }
                }
            }
        }
    </script>
    <style>
        .gradient-text {
            background-clip: text;
            -webkit-background-clip: text;
            color: transparent;
            background-image: linear-gradient(to right, #6366f1, #3b82f6);
        }
    </style>
</head>
<body class="bg-gray-50 dark:bg-gray-900">
    <!-- Navigation Bar with Gradient -->
    <nav class="bg-gradient-to-r from-indigo-500 to-blue-500 fixed w-full z-10 shadow-lg">
        <div class="max-w-6xl mx-auto px-4 flex justify-between items-center h-14">
            <div class="flex items-center">
                <h1 class="text-white font-bold text-xl">ClubConnect</h1>
            </div>
            <div class="flex space-x-4 items-center">
                <button id="theme-toggle" class="text-white/80 hover:text-white rounded-lg p-2">
                    <i class="fas fa-moon dark:hidden"></i>
                    <i class="fas fa-sun hidden dark:block"></i>
                </button>
                <a href="index.php" class="text-white hover:text-white/80">
                    <i class="fas fa-home text-xl"></i>
                </a>
                <a href="#" class="text-white/80 hover:text-white">
                    <i class="fas fa-search text-xl"></i>
                </a>
                <a href="#" class="text-white/80 hover:text-white">
                    <i class="fas fa-plus-square text-xl"></i>
                </a>
                <a href="dashboard.php" class="text-white/80 hover:text-white">
                    <i class="fas fa-user text-xl"></i>
                </a>
            </div>
        </div>
    </nav>

    <!-- Main Content with Sidebar -->
    <div class="max-w-6xl mx-auto flex pt-16">
        <!-- Sidebar with Gradient Accent -->
        <aside class="w-64 hidden md:block fixed h-screen pt-4">
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4 overflow-y-auto h-full shadow-sm">
                <div class="mb-6">
                    <h2 class="text-lg font-semibold gradient-text dark:text-white">Club Menu</h2>
                    <ul class="mt-4 space-y-2">
                        <li>
                            <a href="#" class="flex items-center p-2 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-indigo-50 dark:hover:bg-gray-700 group">
                                <i class="fas fa-calendar-alt mr-3 text-indigo-500 group-hover:text-indigo-600 dark:text-indigo-400"></i>
                                <span>Events</span>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="flex items-center p-2 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-indigo-50 dark:hover:bg-gray-700 group">
                                <i class="fas fa-users mr-3 text-blue-500 group-hover:text-blue-600 dark:text-blue-400"></i>
                                <span>Members</span>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="flex items-center p-2 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-indigo-50 dark:hover:bg-gray-700 group">
                                <i class="fas fa-newspaper mr-3 text-indigo-500 group-hover:text-indigo-600 dark:text-indigo-400"></i>
                                <span>News</span>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="flex items-center p-2 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-indigo-50 dark:hover:bg-gray-700 group">
                                <i class="fas fa-cog mr-3 text-blue-500 group-hover:text-blue-600 dark:text-blue-400"></i>
                                <span>Settings</span>
                            </a>
                        </li>
                    </ul>
                </div>
                <div>
                    <h2 class="text-lg font-semibold gradient-text dark:text-white">Your Clubs</h2>
                    <ul class="mt-4 space-y-2">
                        <li>
                            <a href="#" class="flex items-center p-2 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-indigo-50 dark:hover:bg-gray-700">
                                <div class="w-6 h-6 rounded-full bg-gradient-to-r from-indigo-500 to-blue-500 mr-3 flex items-center justify-center text-white text-xs">S</div>
                                <span>Sports Club</span>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="flex items-center p-2 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-indigo-50 dark:hover:bg-gray-700">
                                <div class="w-6 h-6 rounded-full bg-gradient-to-r from-indigo-500 to-blue-500 mr-3 flex items-center justify-center text-white text-xs">A</div>
                                <span>Arts Society</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </aside>

        <!-- Main Content Area -->
        <main class="flex-1 md:ml-64 pb-20 px-4">
            <!-- Stories with Gradient Border -->
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg mb-6 p-4 overflow-x-auto shadow-sm">
                <div class="flex space-x-4">
                    <div class="flex flex-col items-center space-y-1">
                        <div class="w-16 h-16 rounded-full p-0.5 bg-gradient-to-r from-indigo-500 to-blue-500">
                            <div class="bg-white dark:bg-gray-800 w-full h-full rounded-full flex items-center justify-center">
                                <div class="bg-gray-200 dark:bg-gray-600 w-14 h-14 rounded-full"></div>
                            </div>
                        </div>
                        <span class="text-xs dark:text-gray-300">Your Story</span>
                    </div>
                    <!-- More story items would go here -->
                </div>
            </div>

            <!-- Post with Gradient Accents -->
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg mb-6 shadow-sm">
                <!-- Post Header -->
                <div class="flex items-center p-3 border-b border-gray-200 dark:border-gray-700">
                    <div class="w-8 h-8 rounded-full bg-gradient-to-r from-indigo-500 to-blue-500 p-0.5">
                        <div class="bg-white dark:bg-gray-800 w-full h-full rounded-full flex items-center justify-center">
                            <div class="bg-gray-200 dark:bg-gray-600 w-7 h-7 rounded-full"></div>
                        </div>
                    </div>
                    <div class="flex-1 ml-3">
                        <h3 class="font-semibold text-sm dark:text-white">Club Official</h3>
                        <p class="text-gray-500 dark:text-gray-400 text-xs">Sponsored</p>
                    </div>
                    <button class="text-gray-500 dark:text-gray-400 hover:text-indigo-500 dark:hover:text-indigo-400">
                        <i class="fas fa-ellipsis-h"></i>
                    </button>
                </div>

                <!-- Post Image -->
                <div class="bg-gradient-to-r from-indigo-100 to-blue-100 dark:from-indigo-900/30 dark:to-blue-900/30 w-full aspect-square flex items-center justify-center">
                    <span class="text-indigo-500 dark:text-indigo-300"><img src="leadership.png"></span>
                </div>

                <!-- Post Actions -->
                <div class="p-3">
                    <div class="flex justify-between mb-2">
                        <div class="flex space-x-4">
                            <button class="text-gray-700 dark:text-gray-300 hover:text-indigo-500 dark:hover:text-indigo-400 like-button">
                                <i class="far fa-heart text-xl"></i>
                            </button>
                            <button class="text-gray-700 dark:text-gray-300 hover:text-blue-500 dark:hover:text-blue-400">
                                <i class="far fa-comment text-xl"></i>
                            </button>
                            <button class="text-gray-700 dark:text-gray-300 hover:from-indigo-500 hover:to-blue-500">
                                <i class="far fa-paper-plane text-xl"></i>
                            </button>
                        </div>
                        <button class="text-gray-700 dark:text-gray-300 hover:text-indigo-500 dark:hover:text-indigo-400">
                            <i class="far fa-bookmark text-xl"></i>
                        </button>
                    </div>

                    <!-- Likes -->
                    <p class="font-semibold text-sm mb-1 dark:text-white">1,234 likes</p>

                    <!-- Caption -->
                    <p class="text-sm mb-1 dark:text-gray-300">
                        <span class="font-semibold gradient-text">Club Official</span> 
                        Join our exclusive membership program and get access to premium events, discounts, and member-only content! #ClubLife #MembersOnly
                    </p>

                    <!-- Comments -->
                    <p class="text-gray-500 dark:text-gray-400 text-sm mb-1">View all 56 comments</p>
                    <p class="text-sm mb-1 dark:text-gray-300">
                        <span class="font-semibold text-indigo-500 dark:text-indigo-400">member123</span> 
                        Can't wait for the next event!
                    </p>

                    <!-- Timestamp -->
                    <p class="text-gray-400 dark:text-gray-500 text-xs uppercase mt-2">2 hours ago</p>
                </div>

                <!-- Add Comment -->
                <div class="border-t border-gray-200 dark:border-gray-700 p-3 flex items-center">
                    <input 
                        type="text" 
                        placeholder="Add a comment..." 
                        class="flex-1 text-sm focus:outline-none bg-transparent dark:text-white dark:placeholder-gray-400"
                    >
                    <button class="text-indigo-500 dark:text-indigo-400 font-semibold text-sm ml-2 hover:text-indigo-600 dark:hover:text-indigo-300">Post</button>
                </div>
            </div>
        </main>
    </div>

    <!-- Bottom Navigation (Mobile) with Gradient -->
    <div class="fixed bottom-0 left-0 right-0 bg-gradient-to-r from-indigo-500 to-blue-500 md:hidden shadow-lg">
        <div class="flex justify-around py-3">
            <a href="index.php" class="text-white">
                <i class="fas fa-home text-xl"></i>
            </a>
            <a href="#" class="text-white/80 hover:text-white">
                <i class="fas fa-search text-xl"></i>
            </a>
            <a href="#" class="text-white/80 hover:text-white">
                <i class="fas fa-plus-square text-xl"></i>
            </a>
            <a href="#" class="text-white/80 hover:text-white">
                <i class="fas fa-heart text-xl"></i>
            </a>
            <a href="dashboard.php" class="text-white/80 hover:text-white">
                <i class="fas fa-user text-xl"></i>
            </a>
        </div>
    </div>

    <script>
        // Theme toggle functionality
        const themeToggle = document.getElementById('theme-toggle');
        themeToggle.addEventListener('click', () => {
            document.documentElement.classList.toggle('dark');
            localStorage.setItem('theme', document.documentElement.classList.contains('dark') ? 'dark' : 'light');
        });

        // Check for saved theme preference
        if (localStorage.getItem('theme') === 'dark' || (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }

        // Like button functionality
        document.querySelectorAll('.like-button').forEach(button => {
            button.addEventListener('click', function() {
                const icon = this.querySelector('i');
                icon.classList.toggle('far');
                icon.classList.toggle('fas');
                icon.classList.toggle('text-red-500');
            });
        });
    </script>
</body>
</html>