// Show hero and mission/vision sections with fade effect on scroll
document.addEventListener("DOMContentLoaded", () => {
    const hero = document.querySelector(".hero-left");
    const mvSections = document.querySelectorAll(".mv");

    const revealOnScroll = () => {
        const windowHeight = window.innerHeight;
        const scrollTop = window.scrollY;

        // reveal hero (only if exists)
        if (hero) {
            const heroPos = hero.getBoundingClientRect().top + scrollTop;
            if (scrollTop + windowHeight > heroPos + 100) {
                hero.classList.add("show");
            }
        }

        // reveal mission/vision sections (if exist)
        mvSections.forEach(sec => {
            const secPos = sec.getBoundingClientRect().top + scrollTop;
            if (scrollTop + windowHeight > secPos + 50) {
                sec.classList.add("show");
            }
        });
    };

    window.addEventListener("scroll", revealOnScroll);
    revealOnScroll(); // initial call

    // Interactive core values (safe check)
    const values = document.querySelectorAll(".val");
    values.forEach(v => {
        v.addEventListener("click", () => {
            const value = v.dataset.value || "Unknown";
            alert(`Core Value: ${value}`);
        });
    });

    // Learn More button animation (safe check)
    const learnBtn = document.getElementById("learnBtn");
    if (learnBtn) {
        learnBtn.addEventListener("click", (e) => {
            e.preventDefault();
            learnBtn.textContent = "Thanks for your interest!";
            learnBtn.style.background = "#00b3b3";
            learnBtn.style.color = "#fff";
            setTimeout(() => {
                learnBtn.textContent = "Learn More";
                learnBtn.style.background = "#fff";
                learnBtn.style.color = "#16374a";
            }, 2000);
        });
    }
});
