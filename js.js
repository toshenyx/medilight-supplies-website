document.addEventListener("DOMContentLoaded", function () {
    const slider = document.getElementById("heroSlider");
    const slides = Array.from(slider.querySelectorAll(".slide"));
    const prevBtn = slider.querySelector(".prev");
    const nextBtn = slider.querySelector(".next");
    const dotsWrap = slider.querySelector(".dots");
    let idx = 0, timer = null, delay = 5000;

    function createDots() {
        slides.forEach((_, i) => {
            const btn = document.createElement("button");
            btn.addEventListener("click", () => goTo(i));
            dotsWrap.appendChild(btn);
        });
        updateDots();
    }
    function updateDots() {
        Array.from(dotsWrap.children).forEach((d, i) =>
            d.classList.toggle("active", i === idx)
        );
    }
    function showSlide(i) {
        slides.forEach((s, j) => s.classList.toggle("active", j === i));
        idx = i; updateDots();
    }
    function next() { showSlide((idx + 1) % slides.length); restart(); }
    function prev() { showSlide((idx - 1 + slides.length) % slides.length); restart(); }
    function start() { stop(); timer = setInterval(next, delay); }
    function stop() { if (timer) clearInterval(timer); timer = null; }
    function restart() { stop(); start(); }

    prevBtn.addEventListener("click", prev);
    nextBtn.addEventListener("click", next);
    slider.addEventListener("mouseenter", stop);
    slider.addEventListener("mouseleave", start);

    createDots();
    showSlide(0);
    start();

    document.getElementById("year").textContent = new Date().getFullYear();

    // Search functionality
    const searchInput = document.querySelector(".search input");
    const allTextBlocks = document.querySelectorAll("h1, h2, h3, h4, p, li");

    searchInput.addEventListener("keypress", e => {
        if (e.key === "Enter") {
            e.preventDefault();
            const term = searchInput.value.toLowerCase().trim();
            if (!term) return;
            document.querySelectorAll(".highlight").forEach(el => el.classList.remove("highlight"));
            let found = false;
            allTextBlocks.forEach(el => {
                if (el.textContent.toLowerCase().includes(term)) {
                    el.classList.add("highlight");
                    if (!found) el.scrollIntoView({ behavior: "smooth", block: "center" });
                    found = true;
                }
            });
            if (!found) alert(`No results found for "${term}".`);
        }
    });
});
