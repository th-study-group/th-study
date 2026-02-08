function initIntroPage() {
    const wrap = document.getElementById("wrap");
    if (!wrap) return;
    document.body.classList.add("intro-mode");
    setViewportHeightVar();

    const sections = Array.from(wrap.querySelectorAll(".section"));
    const dotnav = document.getElementById("dotnav");

    if (!sections.length || !dotnav) return;

    let idx = 0;
    let lock = false;

    sections.forEach((sec, i) => {
        const label = sec.getAttribute("data-label") || `S${i}`;
        const b = document.createElement("button");
        b.className = "dot" + (i === 0 ? " active" : "");
        b.type = "button";
        b.setAttribute("aria-label", label);
        b.setAttribute("data-label", label);
        b.addEventListener("click", () => go(i));
        dotnav.appendChild(b);
    });

    const heroBg = document.getElementById("heroBg");
    const parallaxImgs = Array.from(wrap.querySelectorAll(".parallax"));
    const isTouch = window.matchMedia("(pointer: coarse)").matches;
    let mouseX = 0;
    let mouseY = 0;

    function setViewportHeightVar() {
        const vh = window.innerHeight * 0.01;
        document.documentElement.style.setProperty("--intro-vh", `${vh}px`);
    }

    function getActiveSection() {
        return sections[idx] || null;
    }

    function canScrollUp(section) {
        if (!section) return false;
        return section.scrollTop > 24;
    }

    function canScrollDown(section) {
        if (!section) return false;
        return section.scrollHeight - (section.scrollTop + section.clientHeight) > 24;
    }

    function setActive(n) {
        sections.forEach((s, i) => s.classList.toggle("active", i === n));
        Array.from(dotnav.children).forEach((d, i) => d.classList.toggle("active", i === n));
        idx = n;
        requestAnimationFrame(updateParallax);
    }

    function go(n) {
        if (n < 0 || n >= sections.length) return;
        if (lock) return;

        lock = true;
        setActive(n);
        window.setTimeout(() => {
            lock = false;
        }, 700);
    }

    wrap.querySelectorAll("[data-goto]").forEach((a) => {
        a.addEventListener("click", (e) => {
            e.preventDefault();
            const n = parseInt(a.getAttribute("data-goto"), 10);
            if (!Number.isNaN(n)) go(n);
        });
    });

    function onWheel(e) {
        if (lock) return;

        const activeSection = getActiveSection();
        const delta = e.deltaY;
        if (Math.abs(delta) < 10) return;

        if (delta > 0) {
            if (canScrollDown(activeSection)) return;
            e.preventDefault();
            go(idx + 1);
            return;
        }

        if (canScrollUp(activeSection)) return;
        e.preventDefault();
        go(idx - 1);
    }

    window.addEventListener("wheel", onWheel, { passive: false });

    let startY = null;
    let startX = null;
    window.addEventListener(
        "touchstart",
        (e) => {
            if (!e.touches || !e.touches.length) return;
            startY = e.touches[0].clientY;
            startX = e.touches[0].clientX;
        },
        { passive: true }
    );

    // 모바일에서 섹션 내부 스크롤을 우선 허용하고, 경계에서만 섹션 전환
    wrap.addEventListener(
        "touchmove",
        (e) => {
            if (startY === null || !e.touches || !e.touches.length) return;

            const touch = e.touches[0];
            const diffY = startY - touch.clientY;
            const diffX = startX === null ? 0 : startX - touch.clientX;

            // 가로 스와이프는 무시
            if (Math.abs(diffX) > Math.abs(diffY)) return;

            const activeSection = getActiveSection();
            if (!activeSection) return;

            // 위로 스와이프(다음 섹션 방향)인데 아직 내부 내용이 남아 있으면 스크롤 허용
            if (diffY > 0 && canScrollDown(activeSection)) return;

            // 아래로 스와이프(이전 섹션 방향)인데 아직 위 내용이 남아 있으면 스크롤 허용
            if (diffY < 0 && canScrollUp(activeSection)) return;

            // 경계에서만 기본 동작 차단해 탄성 스크롤을 줄이고 섹션 전환 감각 유지
            e.preventDefault();
        },
        { passive: false }
    );

    window.addEventListener(
        "touchend",
        (e) => {
            if (startY === null) return;

            const endY = e.changedTouches && e.changedTouches[0] ? e.changedTouches[0].clientY : startY;
            const diff = startY - endY;
            startY = null;
            startX = null;

            if (lock) return;
            if (Math.abs(diff) < 40) return;

            const activeSection = getActiveSection();
            if (diff > 0) {
                if (canScrollDown(activeSection)) return;
                go(idx + 1);
                return;
            }

            if (canScrollUp(activeSection)) return;
            go(idx - 1);
        },
        { passive: true }
    );

    window.addEventListener("keydown", (e) => {
        if (lock) return;

        if (e.key === "ArrowDown" || e.key === "PageDown" || e.key === " ") {
            e.preventDefault();
            go(idx + 1);
        }

        if (e.key === "ArrowUp" || e.key === "PageUp") {
            e.preventDefault();
            go(idx - 1);
        }

        if (e.key === "Home") {
            e.preventDefault();
            go(0);
        }

        if (e.key === "End") {
            e.preventDefault();
            go(sections.length - 1);
        }
    });

    const typingEl = document.getElementById("typing");
    if (typingEl) {
        const typingText = "나를 뛰어넘는 개발자";
        let t = 0;

        function type() {
            typingEl.textContent = typingText.slice(0, t);
            if (t < typingText.length) {
                t += 1;
                setTimeout(type, 80);
            }
        }

        type();
    }

    window.addEventListener(
        "mousemove",
        (e) => {
            const cx = window.innerWidth / 2;
            const cy = window.innerHeight / 2;
            mouseX = (e.clientX - cx) / cx;
            mouseY = (e.clientY - cy) / cy;
            updateParallax();
        },
        { passive: true }
    );

    function updateParallax() {
        const activeSection = sections[idx];

        if (heroBg) {
            if (activeSection && activeSection.classList.contains("hero")) {
                const s = isTouch ? 8 : 14;
                heroBg.style.transform = `translateY(${mouseY * s}px) translateX(${mouseX * s}px) scale(1.08)`;
            } else {
                heroBg.style.transform = "translateY(0px) translateX(0px) scale(1.06)";
            }
        }

        parallaxImgs.forEach((img) => {
            const sec = img.closest(".section");
            if (!sec) return;

            const speed = parseFloat(img.dataset.speed || "0.18");
            if (sec.classList.contains("active")) {
                const s = (isTouch ? 6 : 12) * speed;
                img.style.transform = `translateY(${mouseY * s}px) scale(1.06)`;
            } else {
                img.style.transform = "translateY(0px) scale(1.06)";
            }
        });
    }

    function cloneIcon(templateId) {
        const t = document.getElementById(templateId);
        if (!t || !t.content || !t.content.firstElementChild) return null;
        return t.content.firstElementChild.cloneNode(true);
    }

    const aiTag = document.getElementById("aiTag");
    if (aiTag) {
        const icon = cloneIcon("icon-ai");
        if (icon) aiTag.appendChild(icon);
        aiTag.appendChild(document.createTextNode("AI / ChatGPT"));
    }

    const tistoryBtn = document.getElementById("tistoryBtn");
    if (tistoryBtn) {
        const slot = tistoryBtn.querySelector(".iconSlot");
        const icon = cloneIcon("icon-globe");
        if (slot && icon) slot.appendChild(icon);
    }

    const mailBtn = document.getElementById("mailBtn");
    if (mailBtn) {
        const slot = mailBtn.querySelector(".iconSlot");
        const icon = cloneIcon("icon-mail");
        if (slot && icon) slot.appendChild(icon);
    }

    updateParallax();

    window.addEventListener("resize", setViewportHeightVar, { passive: true });
    window.addEventListener("orientationchange", setViewportHeightVar, { passive: true });
    window.addEventListener("pageshow", setViewportHeightVar, { passive: true });
}

if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", initIntroPage, { once: true });
} else {
    initIntroPage();
}
