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