let thSplashSessionKey = "th_pwa_splash_session_v1";
let thSplashFallbackKey = "th_pwa_splash_fallback_last_shown_at";
let thSplashFallbackCooldownMs = 10 * 60 * 1000;

function thIsPwaMode() {
    if (typeof navigator.standalone !== "undefined" && navigator.standalone) {
        return true;
    }

    if (window.matchMedia) {
        if (window.matchMedia("(display-mode: standalone)").matches) return true;
    }

    if (document.referrer && document.referrer.indexOf("android-app://") === 0) {
        return true;
    }

    return false;
}

function thShouldShowSplashInSession() {
    try {
        if (sessionStorage.getItem(thSplashSessionKey) === "1") {
            return false;
        }
        
        sessionStorage.setItem(thSplashSessionKey, "1");
        
        return true;
    } catch (e) {
        var now = Date.now();
        var lastShownAt = 0;

        try {
            lastShownAt = Number(localStorage.getItem(thSplashFallbackKey) || "0");
        } catch (ignore) {}

        if (lastShownAt > 0 && (now - lastShownAt) < thSplashFallbackCooldownMs) {
            return false;
        }

        try {
            localStorage.setItem(thSplashFallbackKey, String(now));
        } catch (ignore) {}

        return true;
    }
}

function thShowPwaSplash() {
    if (!thIsPwaMode()) {
        return;
    }

    if (!thShouldShowSplashInSession()) {
        return;
    }

    var splash = document.getElementById("th-pwa-splash");
    if (!splash) {
        return;
    }

    window.__thSplashVisible = true;
    window.dispatchEvent(new CustomEvent("th:splash:show"));

    splash.classList.remove("th-pwa-splash-hide");
    splash.style.display = "flex";
    splash.style.pointerEvents = "auto";

    setTimeout(function () {
        splash.classList.add("th-pwa-splash-hide");
        
        setTimeout(function () {
            splash.style.display = "none";
            splash.style.pointerEvents = "none";
            window.__thSplashVisible = false;
            window.dispatchEvent(new CustomEvent("th:splash:hidden"));
        }, 260);
    }, 1200);
}

if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", thShowPwaSplash);
} else {
    thShowPwaSplash();
}
