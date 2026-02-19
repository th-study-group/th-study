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
    const TRANSITION_LOCK_MS = 950;

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
        if (section.classList.contains("hero")) return false;
        return section.scrollTop > 6;
    }

    function canScrollDown(section) {
        if (!section) return false;
        if (section.classList.contains("hero")) return false;
        return section.scrollHeight - (section.scrollTop + section.clientHeight) > 6;
    }

    function isAtTop(section) {
        if (!section) return true;
        return !canScrollUp(section);
    }

    function isAtBottom(section) {
        if (!section) return true;
        return !canScrollDown(section);
    }

    function setActive(n) {
        stopVideosOutsideSection(sections[n]);
        sections.forEach((s, i) => s.classList.toggle("active", i === n));
        Array.from(dotnav.children).forEach((d, i) => d.classList.toggle("active", i === n));
        idx = n;
        const active = sections[idx];
        if (active) active.scrollTop = 0;
        requestAnimationFrame(updateParallax);
    }

    function stopVideosOutsideSection(activeSection) {
        wrap.querySelectorAll(".yt-lite[data-video-id]").forEach((el) => {
            if (activeSection && activeSection.contains(el)) return;
            resetLiteYouTubeEmbed(el);
        });
    }

    function go(n) {
        if (n < 0 || n >= sections.length) return;
        if (lock) return;

        lock = true;
        setActive(n);
        window.setTimeout(() => {
            lock = false;
        }, TRANSITION_LOCK_MS);
    }

    wrap.querySelectorAll("[data-goto]").forEach((a) => {
        a.addEventListener("click", (e) => {
            e.preventDefault();
            const n = parseInt(a.getAttribute("data-goto"), 10);
            if (!Number.isNaN(n)) go(n);
        });
    });

    function onWheel(e) {
        if (lock) {
            e.preventDefault();
            return;
        }

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
    let lastY = null;
    let boundaryPull = 0;
    let gestureConsumed = false;
    let startAtTop = false;
    let startAtBottom = false;
    let startOnInteractive = false;

    function isInteractiveTarget(target) {
        if (!target || !(target instanceof Element)) return false;
        return Boolean(
            target.closest(
                "iframe, .video-wrap, .video-ratio, a, button, input, textarea, select, label, [role='button'], [contenteditable='true']"
            )
        );
    }

    wrap.addEventListener(
        "touchstart",
        (e) => {
            if (!e.touches || !e.touches.length) return;
            startOnInteractive = isInteractiveTarget(e.target);
            startY = e.touches[0].clientY;
            startX = e.touches[0].clientX;
            lastY = startY;
            boundaryPull = 0;
            gestureConsumed = false;
            const activeSection = getActiveSection();
            startAtTop = isAtTop(activeSection);
            startAtBottom = isAtBottom(activeSection);
        },
        { passive: true }
    );

    // 모바일에서 섹션 내부 스크롤을 우선 허용하고, 경계에서만 섹션 전환
    wrap.addEventListener(
        "touchmove",
        (e) => {
            if (startY === null || !e.touches || !e.touches.length) return;
            if (startOnInteractive) return;
            if (lock) {
                e.preventDefault();
                return;
            }
            if (gestureConsumed) return;

            const touch = e.touches[0];
            const diffY = startY - touch.clientY;
            const diffX = startX === null ? 0 : startX - touch.clientX;
            const stepY = lastY === null ? 0 : touch.clientY - lastY;
            lastY = touch.clientY;

            // 가로 스와이프는 무시
            if (Math.abs(diffX) > Math.abs(diffY)) return;

            const activeSection = getActiveSection();
            if (!activeSection) return;

            // 위로 스와이프(다음 섹션 방향)인데 아직 내부 내용이 남아 있으면 스크롤 허용
            if (diffY > 0 && canScrollDown(activeSection)) {
                boundaryPull = 0;
                return;
            }

            // 아래로 스와이프(이전 섹션 방향)인데 아직 위 내용이 남아 있으면 스크롤 허용
            if (diffY < 0 && canScrollUp(activeSection)) {
                boundaryPull = 0;
                return;
            }

            // 경계에서만 기본 동작 차단해 탄성 스크롤을 줄이고 섹션 전환 감각 유지
            e.preventDefault();

            if (diffY > 0 && !startAtBottom) return;
            if (diffY < 0 && !startAtTop) return;

            boundaryPull += Math.abs(stepY);
            if (boundaryPull < 26) return;

            if (diffY > 0) {
                go(idx + 1);
            } else if (diffY < 0) {
                go(idx - 1);
            }

            gestureConsumed = true;
            boundaryPull = 0;
        },
        { passive: false }
    );

    wrap.addEventListener(
        "touchend",
        (e) => {
            if (startY === null) return;

            const endY = e.changedTouches && e.changedTouches[0] ? e.changedTouches[0].clientY : startY;
            const diff = startY - endY;
            startY = null;
            startX = null;
            lastY = null;
            boundaryPull = 0;
            if (startOnInteractive) {
                startOnInteractive = false;
                return;
            }

            if (lock) return;
            if (gestureConsumed) {
                gestureConsumed = false;
                return;
            }
            if (Math.abs(diff) < 40) return;

            const activeSection = getActiveSection();
            if (diff > 0) {
                if (canScrollDown(activeSection)) return;
                if (!startAtBottom) return;
                go(idx + 1);
                return;
            }

            if (canScrollUp(activeSection)) return;
            if (!startAtTop) return;
            go(idx - 1);
        },
        { passive: true }
    );

    wrap.addEventListener(
        "touchcancel",
        () => {
            startY = null;
            startX = null;
            lastY = null;
            boundaryPull = 0;
            gestureConsumed = false;
            startAtTop = false;
            startAtBottom = false;
            startOnInteractive = false;
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

    initLiteYouTubeEmbeds(wrap);

    updateParallax();

    window.addEventListener("resize", setViewportHeightVar, { passive: true });
    window.addEventListener("orientationchange", setViewportHeightVar, { passive: true });
    window.addEventListener("pageshow", setViewportHeightVar, { passive: true });
}

function initLiteYouTubeEmbeds(root = document) {
    const targets = root.querySelectorAll(".yt-lite[data-video-id]");
    targets.forEach((el) => {
        if (el.dataset.bound === "Y") return;
        el.dataset.bound = "Y";
        let lastActivatedAt = 0;
        const initialImg = el.querySelector("img");
        el.dataset.thumbSrc = initialImg?.getAttribute("src") || `https://i.ytimg.com/vi/${el.dataset.videoId}/hqdefault.jpg`;
        el.dataset.thumbAlt = initialImg?.getAttribute("alt") || "YouTube 영상 썸네일";

        const activate = () => {
            const videoId = el.dataset.videoId;
            if (!videoId) return;

            if (el.querySelector("iframe")) return;

            const iframe = document.createElement("iframe");
            iframe.src = `https://www.youtube.com/embed/${videoId}?autoplay=1&controls=1&playsinline=1&rel=0&modestbranding=1&fs=1&enablejsapi=1`;
            iframe.title = "YouTube video player";
            iframe.loading = "lazy";
            iframe.allow = "accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share";
            iframe.allowFullscreen = true;

            el.innerHTML = "";
            el.appendChild(iframe);

            const ctrlBtn = document.createElement("button");
            ctrlBtn.type = "button";
            ctrlBtn.className = "yt-control-btn";
            ctrlBtn.textContent = "일시정지";
            ctrlBtn.setAttribute("aria-label", "영상 일시정지");
            ctrlBtn.dataset.playing = "Y";

            ctrlBtn.addEventListener("click", (evt) => {
                evt.preventDefault();
                evt.stopPropagation();
                const playing = ctrlBtn.dataset.playing === "Y";
                if (iframe.contentWindow) {
                    iframe.contentWindow.postMessage(
                        JSON.stringify({
                            event: "command",
                            func: playing ? "pauseVideo" : "playVideo",
                            args: [],
                        }),
                        "*"
                    );
                }
                ctrlBtn.dataset.playing = playing ? "N" : "Y";
                ctrlBtn.textContent = playing ? "재생" : "일시정지";
                ctrlBtn.setAttribute("aria-label", playing ? "영상 재생" : "영상 일시정지");
            });

            el.appendChild(ctrlBtn);
        };

        const activateFromEvent = (e) => {
            if (e) {
                e.preventDefault();
                e.stopPropagation();
            }
            const now = Date.now();
            if (now - lastActivatedAt < 450) return;
            lastActivatedAt = now;
            activate();
        };

        el.addEventListener("click", activateFromEvent);
        el.addEventListener("touchend", activateFromEvent, { passive: false });
        el.addEventListener("keydown", (e) => {
            if (e.key === "Enter" || e.key === " ") {
                e.preventDefault();
                activate();
            }
        });
    });
}

function resetLiteYouTubeEmbed(el) {
    if (!el || !(el instanceof Element)) return;
    if (!el.matches(".yt-lite[data-video-id]")) return;
    if (!el.querySelector("iframe")) return;

    const videoId = el.dataset.videoId;
    const thumbSrc = el.dataset.thumbSrc || `https://i.ytimg.com/vi/${videoId}/hqdefault.jpg`;
    const thumbAlt = el.dataset.thumbAlt || "YouTube 영상 썸네일";

    el.innerHTML = "";

    const img = document.createElement("img");
    img.src = thumbSrc;
    img.alt = thumbAlt;
    img.loading = "lazy";

    const play = document.createElement("span");
    play.className = "yt-play-btn";
    play.setAttribute("aria-hidden", "true");

    el.appendChild(img);
    el.appendChild(play);
}

if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", initIntroPage, { once: true });
} else {
    initIntroPage();
}
