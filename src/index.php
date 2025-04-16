<!DOCTYPE html>
<?php 
session_start(); 

// Database Connection
include 'databases.php'; // This includes your database connection

// Fetch upcoming events
$today = date('Y-m-d');
$sql = "SELECT * FROM events WHERE event_date >= ? ORDER BY event_date ASC LIMIT 6"; // Limit to 6 events
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $today);
$stmt->execute();
$result = $stmt->get_result();
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ClubSphere-Club Membership System</title>
    <link rel="shortcut icon" href="favicon.png" type="image/x-icon">
    <!-- Font Awesome CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    animation: {
                        fadeIn: "fadeIn 2s ease-in-out",
                        bounceSlow: "bounce 3s infinite"
                    },
                    keyframes: {
                        fadeIn: {
                            '0%': { opacity: '0' },
                            '100%': { opacity: '1' }
                        }
                    }
                }
            }
        };
    </script>
    <style>
        .cursor {
        font-weight: bold;
        font-size: inherit;
        display: inline-block;
        animation: blink 0.8s infinite;
    }
    @keyframes blink {
        0%, 100% { opacity: 1; }
        50% { opacity: 0; }
    }

        html{
            scroll-behavior: smooth;
        }

         /* Preloader Fullscreen */
        #preloader {
            position: fixed;
            width: 100%;
            height: 100%;
            background: #ffffff;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            z-index: 9999;
        }

        /* Spinner Animation */
        .spin {
            width: 30px;
            height: 30px;
            margin: 5px;
            border-radius: 50%;
            display: inline-block;
            animation: spin 1.2s linear infinite;
        }
        
        /* Different Colors */
        .spin1 { background-color: #1e3a8a; animation-delay: -0.3s; } /* Dark Blue */
        .spin2 { background-color: #3b82f6; animation-delay: -0.6s; } /* Light Blue */
        .spin3 { background-color: #facc15; animation-delay: -0.9s; } /* Yellow */

        /* Keyframes for Rotation */
        @keyframes spin {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.5); }
        }

        /* Fade-out effect */
        .fade-out {
            opacity: 0;
            transition: opacity 1s ease-out;
            pointer-events: none;
        }

         /* ClubSphere Text Animation */
        .animated-text {
            display: flex;
            font-size: 24px;
            font-weight: bold;
            color: #1e3a8a;
        }

        .letter {
            display: inline-block;
            animation: bounce 1.5s infinite ease-in-out;
        }

        .nav{
            backdrop-filter: blur(10px);
            background-color: black;
            backface-visibility: visible;
        }
        .letter:nth-child(1) { animation-delay: 0s; }
        .letter:nth-child(2) { animation-delay: 0.1s; }
        .letter:nth-child(3) { animation-delay: 0.2s; }
        .letter:nth-child(4) { animation-delay: 0.3s; }
        .letter:nth-child(5) { animation-delay: 0.4s; }
        .letter:nth-child(6) { animation-delay: 0.5s; }
        .letter:nth-child(7) { animation-delay: 0.6s; }
        .letter:nth-child(8) { animation-delay: 0.7s; }
        .letter:nth-child(9) { animation-delay: 0.8s; }
        .letter:nth-child(10) { animation-delay: 0.9s; }

        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        .line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

    </style>
</head>
<body class="bg-gray-100">
    <!-- Preloader -->
    <div id="preloader">
        <div class="flex">
            <div class="spin spin1"></div>
            <div class="spin spin2"></div>
            <div class="spin spin3"></div>
        </div>
        <h1 class="mt-4 animated-text">
            <span class="letter">C</span>
            <span class="letter">l</span>
            <span class="letter">u</span>
            <span class="letter">b</span>
            <span class="letter">S</span>
            <span class="letter">p</span>
            <span class="letter">h</span>
            <span class="letter">e</span>
            <span class="letter">r</span>
            <span class="letter">e</span>
        </h1>
    </div>

 <!-- Navbar -->
<nav class="fixed top-0 left-0 w-full z-50 bg-white text-gray-600 font-small p-4 shadow-lg transition-all duration-300">
    <div class="container mx-auto flex justify-between items-center">
        
        <!-- Logo -->
        <h1 class="text-4xl font-semibold pl-5 text-black transition duration-300">
        <i class="fas fa-globe text-4xl text-primary-300"></i>
            ClubSphere
        </h1>

        <!-- Navigation Links -->
        <ul class="hidden md:flex pr-6 space-x-6 text-2xl gap-4">
            <li class="relative group">
                <a href="#membership" class="relative pb-2 hover:text-black transition">
                    Membership
                    <span class="absolute left-1/2 bottom-0 w-0 h-1 bg-blue-600 rounded-full transition-all duration-300 ease-in-out transform -translate-x-1/2 group-hover:w-full"></span>
                </a>
            </li>
            <li class="relative group">
                <a href="#dues" class="relative pb-2 hover:text-black transition">
                    Dues
                    <span class="absolute left-1/2 bottom-0 w-0 h-1 bg-blue-600 rounded-full transition-all duration-300 ease-in-out transform -translate-x-1/2 group-hover:w-full"></span>
                </a>
            </li>
            <li class="relative group">
                <a href="#events" class="relative pb-2 hover:text-black transition">
                    Events
                    <span class="absolute left-1/2 bottom-0 w-0 h-1 bg-blue-600 rounded-full transition-all duration-300 ease-in-out transform -translate-x-1/2 group-hover:w-full"></span>
                </a>
            </li>
            <li class="relative group">
                <a href="#committees" class="relative pb-2 hover:text-black transition">
                    Committees
                    <span class="absolute left-1/2 bottom-0 w-0 h-1 bg-blue-600 rounded-full transition-all duration-300 ease-in-out transform -translate-x-1/2 group-hover:w-full"></span>
                </a>
            </li>
        </ul>

        <!-- Login Button -->
        

<!-- Show Profile Icon if Logged In -->
<?php if (isset($_SESSION["user"])): ?>
    <a href="dashboard.php">
        <img src="<?php echo isset($_SESSION['profile_image']) ? $_SESSION['profile_image'] : 'uploads/default.png'; ?>"  class="w-12 h-12 rounded-full border-2 border-white">
    </a>
<?php else: ?>
    <!-- Login Button -->
    <button class="px-5 py-3 bg-gradient-to-r from-blue-500 to-indigo-500 text-white text-lg font-semibold rounded-full shadow-lg hover:scale-105 hover:shadow-xl transition-transform duration-300 flex items-center justify-center space-x-2">
        <i class="fa-solid fa-right-to-bracket text-xl"></i>
        <a href="login.php" class="ml-2">Login / Sign Up</a>
    </button>
<?php endif; ?>



        <!-- Mobile Menu Button -->
        <button id="menu-toggle" class="text-white text-2xl md:hidden focus:outline-none ml-4">
            ☰
        </button>
    </div>
    
    <!-- Mobile Menu (Hidden by Default) -->
    <div id="mobile-menu" class="hidden md:hidden absolute top-16 left-0 w-full bg-black bg-opacity-80 text-center py-4">
        <a href="#membership" class="block py-2 text-white text-lg hover:bg-blue-500">Membership</a>
        <a href="#dues" class="block py-2 text-white text-lg hover:bg-blue-500">Dues</a>
        <a href="#events" class="block py-2 text-white text-lg hover:bg-blue-500">Events</a>
        <a href="#committees" class="block py-2 text-white text-lg hover:bg-blue-500">Committees</a>
    </div>
</nav>

<script>
    // Toggle Mobile Menu
    document.getElementById("menu-toggle").addEventListener("click", function () {
        document.getElementById("mobile-menu").classList.toggle("hidden");
    });
</script>




<!-- Hero Section with Poster Slideshow -->
<section class="relative h-screen w-full overflow-hidden hero-section">
    <!-- Poster Slideshow Container -->
    <div id="posterSlideshow" class="absolute inset-0 w-full h-full bg-blue-500 transition-all duration-500">
        <!-- Posters will be inserted dynamically -->
    </div>

    <!-- Overlay Content (Welcome Message + Button) -->
    <div class="absolute inset-0 flex flex-col items-center justify-center text-center text-white z-10 bg-black bg-opacity-40">
        <h2 class="text-6xl font-bold drop-shadow-lg">
            <span id="typingText"></span><span class="cursor">|</span>
        </h2>
        <p class="mt-4 text-3xl drop-shadow-lg">Join us today and be part of an amazing community</p>
        <a href="#membership" 
           class="mt-6 inline-block bg-white text-blue-600 px-6 py-3 rounded-lg font-bold shadow-md hover:bg-gray-100 transition duration-300 relative group w-40 text-center">
            <span class="inline-block transition-opacity duration-300 group-hover:opacity-0 applyButton">Apply now</span>
            <i class="fas fa-file-alt absolute inset-0 flex items-center justify-center opacity-0 transition-opacity duration-300 group-hover:opacity-100 text-2xl"></i>
        </a>
    </div>
</section>

<script>
    let slideInterval; // Store interval reference

    function loadPostersFullScreen() {
        let posters = JSON.parse(localStorage.getItem("posters")) || [];
        console.log("Loading posters:", posters); // Debugging

        let slideshow = document.getElementById("posterSlideshow");
        let heroSection = document.querySelector(".hero-section");

        if (posters.length === 0) {
            heroSection.classList.remove("poster-active");
            slideshow.innerHTML = `<div class="w-full h-full flex items-center justify-center text-white text-2xl"></div>`;
            clearInterval(slideInterval); // Stop existing slideshow
            return;
        }

        heroSection.classList.add("poster-active");
        slideshow.innerHTML = "";

        posters.forEach((poster, index) => {
            let slide = document.createElement("div");
            slide.classList.add("absolute", "inset-0", "w-full", "h-full", "opacity-0", "transition-opacity", "duration-1000");
            slide.style.background = `url('${poster.image}') center/cover no-repeat`;
            slideshow.appendChild(slide);
        });

        startSlideshow();
    }

    let index = 0;
    function startSlideshow() {
        let slides = document.getElementById("posterSlideshow").children;
        if (!slides || slides.length === 0) return;

        clearInterval(slideInterval); // Clear any existing interval

        slides[index].classList.add("opacity-100");

        slideInterval = setInterval(() => {
            for (let i = 0; i < slides.length; i++) {
                slides[i].classList.remove("opacity-100");
            }
            index = (index + 1) % slides.length;
            slides[index].classList.add("opacity-100");
        }, 5000);
    }

    document.addEventListener("DOMContentLoaded", loadPostersFullScreen);
</script>



    <!-- Why Join Us Section -->
<section id="why-join" class="container mx-auto p-12 text-center bg-gray-200">
    <h2 class="text-4xl font-bold mb-6">Why Join Us?</h2>
    <span class="block w-20 h-2 bg-blue-500  mx-auto mb-2 rounded-full transition-transform duration-300 hover:w-24 hover:bg-blue-600"></span>
    <p class="text-lg text-gray-600 mb-8">Be part of something bigger! Gain access to amazing opportunities and experiences.</p>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- Feature 1 -->
        <div class="bg-white p-6 rounded-lg shadow-lg transform transition-transform duration-300 hover:scale-105">
            <div class="text-blue-500 text-5xl mb-4">
                <i class="fas fa-calendar-alt"></i> <!-- Font Awesome Icon -->
            </div>
            <h3 class="text-xl font-bold">Exclusive Events</h3>
            <p class="mt-2 text-gray-600">Attend special club events, networking meetups, and skill-building workshops.</p>
        </div>

        <!-- Feature 2 -->
        <div class="bg-white p-6 rounded-lg shadow-lg transform transition-transform duration-300 hover:scale-105">
            <div class="text-blue-500 text-5xl mb-4">
                <i class="fas fa-users"></i>
            </div>
            <h3 class="text-xl font-bold">Strong Community</h3>
            <p class="mt-2 text-gray-600">Connect with like-minded individuals and build lasting friendships.</p>
        </div>

        <!-- Feature 3 -->
        <div class="bg-white p-6 rounded-lg shadow-lg transform transition-transform duration-300 hover:scale-105">
            <div class="text-blue-500 text-5xl mb-4">
                <i class="fas fa-lightbulb"></i>
            </div>
            <h3 class="text-xl font-bold">Skill Development</h3>
            <p class="mt-2 text-gray-600">Grow your leadership, teamwork, and communication skills.</p>
        </div>

        <!-- Feature 4 -->
        <div class="bg-white p-6 rounded-lg shadow-lg transform transition-transform duration-300 hover:scale-105">
            <div class="text-blue-500 text-5xl mb-4">
                <i class="fas fa-award"></i>
            </div>
            <h3 class="text-xl font-bold">Recognition & Rewards</h3>
            <p class="mt-2 text-gray-600">Get rewarded for your contributions and achievements in the club.</p>
        </div>

        <!-- Feature 5 -->
        <div class="bg-white p-6 rounded-lg shadow-lg transform transition-transform duration-300 hover:scale-105">
            <div class="text-blue-500 text-5xl mb-4">
                <i class="fas fa-chalkboard-teacher"></i>
            </div>
            <h3 class="text-xl font-bold">Workshops & Training</h3>
            <p class="mt-2 text-gray-600">Gain hands-on experience through industry-specific workshops.</p>
        </div>

        <!-- Feature 6 -->
        <div class="bg-white p-6 rounded-lg shadow-lg transform transition-transform duration-300 hover:scale-105">
            <div class="text-blue-500 text-5xl mb-4">
                <i class="fas fa-handshake"></i>
            </div>
            <h3 class="text-xl font-bold">Career Opportunities</h3>
            <p class="mt-2 text-gray-600">Get access to internships, job referrals, and professional guidance.</p>
        </div>
    </div>
</section>


<!-- Membership Application -->
    <section id="membership" class="w-full flex items-center justify-center px-10 py-12 bg-gray-100">

    
    <div class="container mx-auto flex flex-wrap lg:flex-nowrap items-center gap-10">
        
        <!-- Left: Form -->
        <div class="bg-white bg-opacity-90 backdrop-blur-lg p-8 rounded-lg shadow-lg w-full lg:w-1/2 border border-gray-300">
    <h2 class="text-3xl font-bold text-blue-600 mb-6 text-center">Membership Application</h2>
    <span class="block w-20 h-1 bg-blue-500 mx-auto mb-2 rounded-full transition-transform duration-300 hover:w-24 hover:bg-blue-600"></span>
    
    <form action="process_membership.php" method="POST" enctype="multipart/form-data">
        <label class="block mb-2 font-medium text-gray-700">Name</label>
        <input type="text" name="name" class="w-full p-2 border rounded mb-4 focus:ring-2 focus:ring-blue-500" required>
        
        <label class="block mb-2 font-medium text-gray-700">Email</label>
        <input type="email" name="email" class="w-full p-2 border rounded mb-4 focus:ring-2 focus:ring-blue-500" required>

        <label class="block mb-2 font-medium text-gray-700">Why do you want to join?</label>
        <textarea name="message" class="w-full p-2 border rounded mb-4 focus:ring-2 focus:ring-blue-500" required></textarea>
        
        <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded-md shadow-md hover:bg-blue-700 transition duration-300 w-full flex items-center justify-center gap-2">
            <i class="fas fa-paper-plane"></i> Submit
        </button>
    </form>
</div>

        <!-- Right: Image -->
        <div class="w-full lg:w-1/2 flex justify-center">
            <img src="form.jpg" alt="Membership" class="max-w-full h-auto object-cover rounded-lg shadow-lg">
        </div>

    </div>
</section>



<!-- Dues Collection -->
<section id="dues" class="w-full min-h-screen flex flex-col items-center justify-center bg-gray-100 px-6 py-12">
    <h2 class="text-4xl font-extrabold text-blue-700 mb-4 ">Dues Collection</h2>
    <span class="block w-20 h-2 bg-blue-500  mx-auto mb-2 rounded-full transition-transform duration-300 hover:w-24 hover:bg-blue-600"></span>
    <p class="text-lg text-gray-700 mb-10 text-center max-w-2xl">
        Securely manage and pay your membership dues. Choose a plan that suits you!
    </p>

    <div class="container mx-auto grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-10">
        
        <!-- Basic Plan -->
        <div class="bg-white p-8 rounded-lg shadow-lg text-center transform hover:scale-105 transition duration-300 relative">
            <i class="fas fa-user text-blue-600 text-4xl absolute top-4 left-4"></i>
            <h3 class="text-2xl font-semibold text-gray-800 mt-4">Basic Membership</h3>
            <p class="text-lg text-gray-600 mt-2">$10 / Month</p>
            <hr class="my-4">
            <ul class="text-gray-600 space-y-2">
                <li>✅ Access to events</li>
                <li>✅ Monthly newsletter</li>
                <li>❌ Priority support</li>
            </ul>
            <a href="pay.php"><button class="mt-6 bg-blue-600 text-white px-6 py-3 rounded-lg shadow-md hover:bg-blue-700 transition duration-300 w-full flex items-center justify-center gap-2">
                <i class="fas fa-credit-card"></i>Pay Now
            </button></a>
        </div>

        <!-- Standard Plan (Highlighted) -->
        <div class="bg-blue-600 text-white p-8 rounded-lg shadow-lg text-center transform hover:scale-105 transition duration-300 relative">
            <span class="absolute top-4 right-4 bg-yellow-400 text-black text-xs px-3 py-1 rounded-full">Most Popular</span>
            <i class="fas fa-star text-yellow-400 text-4xl absolute top-4 left-4"></i>
            <h3 class="text-2xl font-semibold mt-4">Standard Membership</h3>
            <p class="text-lg mt-2">$25 / Month</p>
            <hr class="my-4 border-white">
            <ul class="space-y-2">
                <li>✅ Access to events</li>
                <li>✅ Monthly newsletter</li>
                <li>✅ Priority support</li>
            </ul>
            <a href="pay.php"><button class="mt-6 bg-white text-blue-600 px-6 py-3 rounded-lg shadow-md hover:bg-gray-200 transition duration-300 w-full flex items-center justify-center gap-2">
                <i class="fas fa-credit-card"></i>Pay Now
            </button></a>
        </div>

        <!-- Premium Plan -->
        <div class="bg-white p-8 rounded-lg shadow-lg text-center transform hover:scale-105 transition duration-300 relative">
            <i class="fas fa-crown text-yellow-500 text-4xl absolute top-4 left-4"></i>
            <h3 class="text-2xl font-semibold text-gray-800 mt-4">Premium Membership</h3>
            <p class="text-lg text-gray-600 mt-2">$50 / Month</p>
            <hr class="my-4">
            <ul class="text-gray-600 space-y-2">
                <li>✅ All Standard features</li>
                <li>✅ VIP event invitations</li>
                <li>✅ 24/7 premium support</li>
            </ul>
            <a href="pay.php"><button class="mt-6 bg-blue-600 text-white px-6 py-3 rounded-lg shadow-md hover:bg-blue-700 transition duration-300 w-full flex items-center justify-center gap-2">
                <i class="fas fa-credit-card"></i>Pay Now
            </button></a>
        </div>

    </div>
</section>

<!-- Upcoming Events Section -->
<section id="events" class="container mx-auto py-16 px-4">
    <h2 class="text-4xl font-bold text-center mb-4">🎉 Upcoming Events</h2>
    <span class="block w-20 h-2 bg-blue-500 mx-auto mb-6 rounded-full transition-transform duration-300 hover:w-24 hover:bg-blue-600"></span>
    <p class="text-lg text-gray-600 text-center mb-12">Don't miss out on our exciting events! Join us and stay connected.</p>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="bg-white rounded-xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2">
                    <div class="relative h-48 overflow-hidden">
                        <img src="<?php echo htmlspecialchars($row['event_image']); ?>" alt="<?php echo htmlspecialchars($row['event_name']); ?>" 
                             class="w-full h-full object-cover transition-transform duration-500 hover:scale-110">
                        <div class="absolute top-0 right-0 bg-blue-600 text-white px-3 py-1 m-3 rounded-full text-sm font-semibold">
                            <?php 
                                // Format date to display nicely
                                $event_date = new DateTime($row['event_date']);
                                echo $event_date->format('M d, Y'); 
                            ?>
                        </div>
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-800 mb-2"><?php echo htmlspecialchars($row['event_name']); ?></h3>
                        <p class="text-gray-600 mb-4 line-clamp-2"><?php echo htmlspecialchars($row['event_description']); ?></p>
                        <div class="flex items-center mb-4">
                            <i class="fas fa-map-marker-alt text-red-500 mr-2"></i>
                            <span class="text-gray-700"><?php echo htmlspecialchars($row['event_location']); ?></span>
                        </div>
                        <a href="event_details.php?id=<?php echo $row['id']; ?>" class="block text-center bg-gradient-to-r from-blue-500 to-blue-700 text-white py-2 rounded-lg hover:from-blue-600 hover:to-blue-800 transition duration-300">
                            View Details
                        </a>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-span-full text-center py-8">
                <div class="text-6xl mb-4 opacity-30">📅</div>
                <h3 class="text-2xl font-semibold text-gray-700 mb-2">No Upcoming Events</h3>
                <p class="text-gray-500">Check back soon for new events!</p>
            </div>
        <?php endif; ?>
    </div>

    <?php if ($result->num_rows > 0): ?>
        <!-- <div class="text-center mt-12">
            <a href="all_events.php" class="inline-block px-8 py-3 bg-white text-blue-600 border-2 border-blue-600 rounded-lg hover:bg-blue-50 transition duration-300 font-semibold">
                <i class="fas fa-calendar-alt mr-2"></i>View All Events
            </a>
        </div> -->
    <?php endif; ?>
</section>


    
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            loadEvents();
        });
    
        // function loadEvents() {
        //     let events = JSON.parse(localStorage.getItem("events")) || [];
        //     let today = new Date().toISOString().split("T")[0];
    
        //     // Remove past events
        //     events = events.filter(event => event.date >= today);
        //     localStorage.setItem("events", JSON.stringify(events));
    
        //     let eventContainer = document.getElementById("upcomingEvents");
        //     eventContainer.innerHTML = ""; // Clear old events
    
        //     if (events.length === 0) {
        //         eventContainer.innerHTML = "<p class='text-center text-gray-500 col-span-3'>No upcoming events.</p>";
        //         return;
        //     }
    
        //     events.forEach(event => {
        //         let eventCard = document.createElement("div");
        //         eventCard.classList.add("bg-white", "p-6", "rounded-lg", "shadow-lg", "hover:shadow-2xl", "transition", "duration-300", "transform", "hover:scale-105");
    
        //         eventCard.innerHTML = `
        //             <img src="${event.image}" alt="Event Image" class="w-full h-40 object-cover rounded-md">
        //             <h3 class="text-xl font-bold mt-4">${event.title}</h3>
        //             <p class="text-gray-600">${event.description}</p>
        //             <p class="text-blue-500 font-semibold mt-2">📅 ${event.date} | 📍 ${event.location}</p>
        //             <a href="register.html" class="block mt-4 text-white bg-blue-600 px-4 py-2 rounded-md hover:bg-blue-700">Register Now</a>
        //         `;
    
        //         eventContainer.appendChild(eventCard);
        //     });
        // }
    </script>
    




    <!-- Committee Management Section -->
<!-- Committee Management Section -->
<?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
<section id="committees" class="bg-gray-100 py-16">
    <div class="container mx-auto text-center">
        <h2 class="text-4xl font-bold text-gray-800 mb-4">Committee Management</h2>
        <div class="w-20 h-1 bg-blue-600 mx-auto mb-8"></div>
        <p class="text-lg text-gray-600 mb-12">Join a committee and contribute to the success of our club.</p>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
            <!-- Committee 1 -->
            <div class="relative bg-white rounded-lg shadow-lg overflow-hidden p-6 transition duration-300 transform hover:scale-105 hover:shadow-2xl">
                <h3 class="text-2xl font-semibold text-gray-800"> Event Planning</h3>
                <p class="text-gray-600 mt-2">Help organize and manage club events.</p>
                <button class="mt-4 bg-blue-600 text-white px-5 py-2 rounded-md font-medium hover:bg-blue-700 transition duration-300"><a href="event_planning.php">
                    Customize Now</a>
                </button>
            </div>

            <!-- Committee 2 -->
            <div class="relative bg-white rounded-lg shadow-lg overflow-hidden p-6 transition duration-300 transform hover:scale-105 hover:shadow-2xl">
                <h3 class="text-2xl font-semibold text-gray-800">Design & Marketing</h3>
                <p class="text-gray-600 mt-2">Create posters and promote club activities.</p>
                <button class="mt-4 bg-blue-600 text-white px-5 py-2 rounded-md font-medium hover:bg-blue-700 transition duration-300">
                    <a href="design.php">
                        Customize Now</a>
                </button>
            </div>

            <!-- Committee 3 -->
            <div class="relative bg-white rounded-lg shadow-lg overflow-hidden p-6 transition duration-300 transform hover:scale-105 hover:shadow-2xl">
                <h3 class="text-2xl font-semibold text-gray-800">Finance & Budgeting</h3>
                <p class="text-gray-600 mt-2">Manage club funds and financial planning.</p>
                <button class="mt-4 bg-blue-600 text-white px-5 py-2 rounded-md font-medium hover:bg-blue-700 transition duration-300">
                    <a href="finance.php">Customize now</a>
                </button>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>
<button id="scrollToTop" class="fixed bottom-6 right-6 w-12 h-12 bg-blue-600 text-white rounded-full flex items-center justify-center  transition duration-300 shadow-[0px_5px_15px_rgba(0,0,0,0.35)] p-6  hover:bg-blue-700">
    ^
</button>


<footer class="bg-blue-500 text-white py-8 mt-10">
    <div class="container mx-auto flex flex-col md:flex-row items-center justify-between px-6">

        <!-- Left Side: Contact Info & Copyright -->
        <div class="text-left space-y-3">
            <p class="flex items-center space-x-2 text-sm">
                <i class="fas fa-envelope text-lg text-yellow-300"></i>
                <span class="hover:text-yellow-300 transition duration-300">surajagrahari265@gmail.com</span>
            </p>
            <p class="flex items-center space-x-2 text-sm">
                <i class="fas fa-map-marker-alt text-lg text-red-300"></i>
                <span class="hover:text-red-300 transition duration-300">Lovely Professional University Jalandhar, Punjab</span>
            </p>
            <p class="text-lg mt-2">&copy; 2025 ClubSphere. All rights reserved.</p>
        </div>

        <!-- Center: Website Name & Slogan -->
        <div class="text-center">
            <h2 class="text-3xl font-bold tracking-wide">ClubSphere</h2>
            <p class="text-lg mt-2">Connect • Collaborate • Create</p>
        </div>

        <!-- Right Side: Social Media Icons -->
        <div class="flex space-x-6 pr-12">
            <a href="https://github.com/2Surajagrahari" class="text-white text-xl transition-transform transform hover:scale-125">
                <i class="fab fa-github"></i>
            </a>
            <a href="https://www.instagram.com/surajagr_01/" class="text-white text-xl transition-transform transform hover:scale-125">
                <i class="fab fa-instagram"></i>
            </a>
            <a href="https://x.com/surajagrahari01" class="text-white text-xl transition-transform transform hover:scale-125">
                <i class="fab fa-twitter"></i>
            </a>
            <a href="https://www.linkedin.com/in/suraj-agraharii/" class="text-white text-xl transition-transform transform hover:scale-125">
                <i class="fab fa-linkedin"></i>
            </a>
        </div>

    </div>
</footer>

    <script src="index.js"></script>
</body>
</html>
