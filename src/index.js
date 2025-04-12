const scrollToTopBtn = document.getElementById("scrollToTop");

    function toggleScrollButton() {
        if (window.scrollY > 300) {
            scrollToTopBtn.classList.remove("hidden");
        } else {
            scrollToTopBtn.classList.add("hidden");
        }
    }

    window.addEventListener("scroll", toggleScrollButton);
    
    scrollToTopBtn.addEventListener("click", () => {
        window.scrollTo({ top: 0, behavior: "smooth" });
    });

    // Hide preloader and show content after loading
    window.onload = function () {
        setTimeout(() => {
            document.getElementById("preloader").classList.add("fade-out");
            document.getElementById("main-content").classList.remove("hidden");
        }, 2000); // Preloader duration
    };


    const texts = [
        "Welcome to ClubSphere...!",
        "Empowering the Future of Clubs",
        "Join Us and Unleash Your Potential",
        "Explore. Innovate. Connect."
    ];

    let textIndex = 0;
    let charIndex = 0;
    const typingSpeed = 120; // Typing speed
    const erasingSpeed = 60; // Erasing speed
    const delayBetweenTexts = 1500; // Delay before erasing
    const typingTextElement = document.getElementById("typingText");

    function typeEffect() {
        if (charIndex < texts[textIndex].length) {
            typingTextElement.innerHTML += texts[textIndex].charAt(charIndex);
            charIndex++;
            setTimeout(typeEffect, typingSpeed);
        } else {
            setTimeout(eraseEffect, delayBetweenTexts);
        }
    }

    function eraseEffect() {
        if (charIndex > 0) {
            typingTextElement.innerHTML = texts[textIndex].substring(0, charIndex - 1);
            charIndex--;
            setTimeout(eraseEffect, erasingSpeed);
        } else {
            textIndex = (textIndex + 1) % texts.length; // Move to next text
            setTimeout(typeEffect, typingSpeed);
        }
    }

    document.addEventListener("DOMContentLoaded", () => {
        setTimeout(typeEffect, 500); // Start typing effect after delay
    });

    