$(function () {
    clearPushClientCacheIfJustLoggedOut();
    autoSyncOnLogin();
    bindLogoutPushCleanup();
    openNativePushPermissionPrompt();
});

function pushDebugAlert(message) {
    var text = '[PUSH DEBUG] ' + message;

    try {
        console.log(text);
    } catch (e) {}

    //pushDebugPanel(text);

    try {
        //alert(text);
    } catch (e) {}
}

function pushDebugPanel(text) {
    var panelId = 'pushDebugPanel';
    var lineId = 'pushDebugPanelLines';
    var panel = document.getElementById(panelId);

    if (!panel) {
        panel = document.createElement('div');
        panel.id = panelId;
        panel.style.position = 'fixed';
        panel.style.left = '8px';
        panel.style.right = '8px';
        panel.style.bottom = '8px';
        panel.style.zIndex = '100000';
        panel.style.background = 'rgba(17,24,39,0.94)';
        panel.style.color = '#f9fafb';
        panel.style.padding = '10px';
        panel.style.borderRadius = '10px';
        panel.style.fontSize = '12px';
        panel.style.lineHeight = '1.45';
        panel.style.maxHeight = '34vh';
        panel.style.overflowY = 'auto';
        panel.style.boxShadow = '0 6px 24px rgba(0,0,0,0.28)';
        panel.innerHTML = ''
            + '<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px;">'
            + '<strong style="font-size:12px;">PUSH DEBUG LOG</strong>'
            + '<button type="button" id="pushDebugPanelClose" style="border:0;background:transparent;color:#d1d5db;cursor:pointer;font-size:12px;">닫기</button>'
            + '</div>'
            + '<div id="' + lineId + '"></div>';
        document.body.appendChild(panel);

        var closeButton = document.getElementById('pushDebugPanelClose');
        if (closeButton) {
            closeButton.onclick = function () {
                panel.style.display = 'none';
            };
        }
    }

    panel.style.display = 'block';

    var lineWrap = document.getElementById(lineId);
    if (!lineWrap) {
        return;
    }

    var row = document.createElement('div');
    row.textContent = new Date().toLocaleTimeString() + ' ' + text;
    row.style.borderTop = '1px solid rgba(255,255,255,0.12)';
    row.style.paddingTop = '4px';
    row.style.marginTop = '4px';
    lineWrap.appendChild(row);

    while (lineWrap.children.length > 12) {
        lineWrap.removeChild(lineWrap.firstChild);
    }
}

function csrfToken() {
    return window.CSRF_TOKEN || '';
}

function urlBase64ToUint8Array(base64String) {
    var padding = new Array((4 - base64String.length % 4) % 4 + 1).join('=');
    var base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
    var rawData = window.atob(base64);
    var outputArray = new Uint8Array(rawData.length);

    for (var i = 0; i < rawData.length; i++) {
        outputArray[i] = rawData.charCodeAt(i);
    }

    return outputArray;
}

function requestPushApi(url, data) {
    if (typeof window.requestAjax !== 'function') {
        return $.Deferred().reject().promise();
    }

    return window.requestAjax({
        url: url,
        method: 'POST',
        dataType: 'json',
        data: data,
        headers: {
            'X-CSRF-TOKEN': csrfToken()
        },
        showLoading: false
    });
}

function pingOncePerDay(endpoint) {
    return requestPushApi('/push/ping', { endpoint: endpoint });
}

function subscribeAndSave() {
    return navigator.serviceWorker.ready
        .then(function (registration) {
            if (!('Notification' in window)) {
                throw new Error('notification_not_supported');
            }

            if (Notification.permission !== 'granted') {
                throw new Error('notification_not_granted');
            }

            var vapid = (window.VAPID_PUBLIC_KEY || '').trim();
            if (!vapid) {
                throw new Error('missing_vapid_key');
            }

            return registration.pushManager.subscribe({
                userVisibleOnly: true,
                applicationServerKey: urlBase64ToUint8Array(vapid)
            });
        })
        .then(function (subscription) {
            var payload = subscription.toJSON ? subscription.toJSON() : subscription;
            return requestPushApi('/push/subscribe', payload).then(function () {
                return subscription;
            });
        });
}

function autoSyncOnLogin() {
    if (!window.IS_LOGGED_IN) {
        return;
    }

    if (!('serviceWorker' in navigator) || !('PushManager' in window)) {
        return;
    }

    navigator.serviceWorker.ready
        .then(function (registration) {
            return registration.pushManager.getSubscription();
        })
        .then(function (subscription) {
            if (subscription) {
                return requestPushApi('/push/exists', { endpoint: subscription.endpoint })
                    .then(function (response) {
                        if (!response || response.exists !== true) {
                            var payload = subscription.toJSON ? subscription.toJSON() : subscription;
                            return requestPushApi('/push/subscribe', payload);
                        }
                    })
                    .then(function () {
                        return pingOncePerDay(subscription.endpoint);
                    });
            }

            if (!('Notification' in window)) {
                pushDebugAlert('autoSync skip: notification_not_supported');
                return;
            }

            if (Notification.permission !== 'granted') {
                pushDebugAlert('autoSync skip: permission=' + Notification.permission);
                return;
            }

            if (isIosDevice() && !isStandalonePwa()) {
                pushDebugAlert('autoSync skip: ios_not_standalone');
                return;
            }

            return subscribeAndSave().then(function (newSubscription) {
                return pingOncePerDay(newSubscription.endpoint);
            });
        })
        .catch(function (err) {
            pushDebugAlert('autoSync fail: ' + (err && err.message ? err.message : err));
            // 자동 동기화 실패는 사용자 흐름을 막지 않는다.
        });
}

function isIosDevice() {
    return /iPhone|iPad|iPod/i.test(window.navigator.userAgent || '');
}

function isStandalonePwa() {
    var isIosStandalone = window.navigator.standalone === true;
    var isDisplayModeStandalone = window.matchMedia && window.matchMedia('(display-mode: standalone)').matches;

    return isIosStandalone || isDisplayModeStandalone;
}

function requiresIosGestureSubscribe() {
    return isIosDevice();
}

function clearPushPingCache(endpoint) {
    if (!endpoint) {
        return;
    }

    try {
        localStorage.removeItem('push_last_ping_' + endpoint);
    } catch (e) {}
}

function clearPushClientCacheAll() {
    try {
        localStorage.removeItem('push_permission_prompt_snooze_until');

        var keysToDelete = [];
        for (var i = 0; i < localStorage.length; i++) {
            var key = localStorage.key(i);
            if (key && key.indexOf('push_last_ping_') === 0) {
                keysToDelete.push(key);
            }
        }

        keysToDelete.forEach(function (key) {
            localStorage.removeItem(key);
        });
    } catch (e) {}
}

function clearPushClientCacheIfJustLoggedOut() {
    if (!window.JUST_LOGGED_OUT) {
        return;
    }

    clearPushClientCacheAll();
}

function unsubscribeCurrentPush() {
    if (!('serviceWorker' in navigator) || !('PushManager' in window)) {
        return Promise.resolve(false);
    }

    return navigator.serviceWorker.ready
        .then(function (registration) {
            return registration.pushManager.getSubscription();
        })
        .then(function (subscription) {
            if (!subscription) {
                return false;
            }

            var endpoint = subscription.endpoint;

            return requestPushApi('/push/unsubscribe', { endpoint: endpoint })
                .catch(function () {
                    return null;
                })
                .then(function () {
                    return subscription.unsubscribe().catch(function () {
                        return false;
                    });
                })
                .then(function () {
                    clearPushPingCache(endpoint);
                    return true;
                });
        })
        .catch(function () {
            return false;
        });
}

function bindLogoutPushCleanup() {
    $(document).on('click', 'a[data-push-logout="1"]', function (e) {
        var href = this.href;
        e.preventDefault();

        Promise.resolve(unsubscribeCurrentPush())
            .catch(function () {
                return false;
            })
            .finally(function () {
                clearPushClientCacheAll();
                window.location.href = href;
            });
    });
}

window.unsubscribeCurrentPush = unsubscribeCurrentPush;

function openNativePushPermissionPrompt() {
    if (!window.IS_LOGGED_IN) {
        pushDebugAlert('중단: 비로그인 상태');
        return;
    }

    if (!isStandalonePwa()) {
        pushDebugAlert('중단: standalone(PWA 앱 실행) 아님');
        return;
    }

    if (!('serviceWorker' in navigator) || !('PushManager' in window) || !('Notification' in window)) {
        pushDebugAlert('중단: serviceWorker/PushManager/Notification 미지원');
        return;
    }

    if (Notification.permission !== 'default') {
        pushDebugAlert('중단: Notification.permission=' + Notification.permission);
        return;
    }

    if (isPromptSnoozed()) {
        pushDebugAlert('중단: snooze 상태');
        return;
    }

    pushDebugAlert('진입: 팝업 노출 조건 통과');

    navigator.serviceWorker.ready
        .then(function (registration) {
            pushDebugAlert('serviceWorker.ready 성공');
            return registration.pushManager.getSubscription();
        })
        .then(function (subscription) {
            if (subscription) {
                pushDebugAlert('중단: 기존 구독 존재');
                return;
            }

            pushDebugAlert('팝업 표시 시도');
            renderNativePushPrompt();
        })
        .catch(function (err) {
            pushDebugAlert('실패: openNativePushPermissionPrompt catch - ' + (err && err.message ? err.message : err));
            // 팝업 노출 실패는 사용자 흐름을 막지 않는다.
        });
}

function renderNativePushPrompt() {
    var popup = document.getElementById('nativePushPermissionPopup');
    var closeButton = document.getElementById('btnNativePushPromptLater');
    var allowButton = document.getElementById('btnNativePushPromptAllow');

    if (!popup || !closeButton || !allowButton) {
        pushDebugAlert('중단: popup DOM 없음');
        return;
    }

    popup.style.display = 'flex';
    popup.style.pointerEvents = 'auto';
    popup.setAttribute('aria-hidden', 'false');
    pushDebugAlert('성공: 팝업 표시됨');

    closeButton.onclick = function () {
        pushDebugAlert('동작: 나중에 클릭');
        setPromptSnooze();
        hideNativePushPrompt(popup);
    };

    allowButton.onclick = function () {
        pushDebugAlert('동작: 허용 클릭, requestPermission 호출');
        allowButton.disabled = true;

        Notification.requestPermission()
            .then(function (permission) {
                pushDebugAlert('결과: requestPermission=' + permission);
                if (permission !== 'granted') {
                    setPromptSnooze();
                    hideNativePushPrompt(popup);
                    return null;
                }

                return subscribeAndSave()
                    .then(function (subscription) {
                        return pingOncePerDay(subscription.endpoint);
                    })
                    .then(function () {
                        pushDebugAlert('성공: 구독 저장 완료');
                        hideNativePushPrompt(popup);
                    });
            })
            .catch(function (err) {
                pushDebugAlert('실패: 허용 처리 catch - ' + (err && err.message ? err.message : err));
                setPromptSnooze();
                hideNativePushPrompt(popup);
            })
            .finally(function () {
                allowButton.disabled = false;
            });
    };
}

function hideNativePushPrompt(popup) {
    popup.style.display = 'none';
    popup.style.pointerEvents = 'none';
    popup.setAttribute('aria-hidden', 'true');
}

function isPromptSnoozed() {
    var key = 'push_permission_prompt_snooze_until';
    var snoozeUntil = Number(localStorage.getItem(key) || '0');

    return snoozeUntil > Date.now();
}

function setPromptSnooze() {
    var key = 'push_permission_prompt_snooze_until';
    var oneDayMs = 24 * 60 * 60 * 1000;
    localStorage.setItem(key, String(Date.now() + oneDayMs));
}
