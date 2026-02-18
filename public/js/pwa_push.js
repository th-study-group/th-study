$(function () {
    autoSyncOnLogin();
    bindLogoutPushCleanup();
    bindFirstInteractionSubscribe();
    debugPushEnvironmentByAlert();
});

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
    var key = 'push_last_ping_' + endpoint;
    var now = Date.now();
    var last = Number(localStorage.getItem(key) || '0');

    if (now - last < 24 * 60 * 60 * 1000) {
        return $.Deferred().resolve().promise();
    }

    return requestPushApi('/push/ping', { endpoint: endpoint }).then(function () {
        localStorage.setItem(key, String(now));
    });
}

function subscribeAndSave() {
    return navigator.serviceWorker.ready
        .then(function (registration) {
            if (!('Notification' in window)) {
                throw new Error('notification_not_supported');
            }

            return Notification.requestPermission().then(function (permission) {
                if (permission !== 'granted') {
                    throw new Error('notification_denied');
                }

                var vapid = (window.VAPID_PUBLIC_KEY || '').trim();
                if (!vapid) {
                    throw new Error('missing_vapid_key');
                }

                return registration.pushManager.subscribe({
                    userVisibleOnly: true,
                    applicationServerKey: urlBase64ToUint8Array(vapid)
                });
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
            if (!subscription) {
                if (requiresIosGestureSubscribe()) {
                    return;
                }

                return subscribeAndSave().then(function (newSubscription) {
                    return pingOncePerDay(newSubscription.endpoint);
                });
            }

            if (requiresIosGestureSubscribe() && !isStandalonePwa()) {
                return;
            }

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
        })
        .catch(function () {
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

function bindFirstInteractionSubscribe() {
    if (!window.IS_LOGGED_IN) {
        return;
    }

    if (!requiresIosGestureSubscribe()) {
        return;
    }

    if (!isStandalonePwa()) {
        return;
    }

    if (!('serviceWorker' in navigator) || !('PushManager' in window)) {
        return;
    }

    var triggered = false;
    var handler = function () {
        if (triggered) {
            return;
        }
        triggered = true;
        cleanup();

        navigator.serviceWorker.ready
            .then(function (registration) {
                return registration.pushManager.getSubscription();
            })
            .then(function (subscription) {
                if (subscription) {
                    return;
                }

                return subscribeAndSave()
                    .then(function (newSubscription) {
                        return pingOncePerDay(newSubscription.endpoint);
                    });
            })
            .catch(function () {
                // 첫 상호작용 기반 자동 구독 실패는 사용자 흐름을 막지 않는다.
            });
    };

    var cleanup = function () {
        document.removeEventListener('pointerdown', handler, true);
        document.removeEventListener('touchstart', handler, true);
        document.removeEventListener('keydown', handler, true);
    };

    document.addEventListener('pointerdown', handler, true);
    document.addEventListener('touchstart', handler, true);
    document.addEventListener('keydown', handler, true);
}

function clearPushPingCache(endpoint) {
    if (!endpoint) {
        return;
    }

    try {
        localStorage.removeItem('push_last_ping_' + endpoint);
    } catch (e) {}
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
                window.location.href = href;
            });
    });
}

window.unsubscribeCurrentPush = unsubscribeCurrentPush;

function debugPushEnvironmentByAlert() {
    if (!window.IS_LOGGED_IN) {
        return;
    }

    if (!('serviceWorker' in navigator)) {
        alert('serviceWorker 미지원');
        return;
    }

    var summary = [];
    summary.push('isSecureContext: ' + String(window.isSecureContext));
    summary.push('standalone(iOS): ' + String(window.navigator.standalone === true));
    summary.push('display-mode standalone: ' + String(window.matchMedia && window.matchMedia('(display-mode: standalone)').matches));
    summary.push('Notification 지원: ' + String('Notification' in window));
    summary.push('PushManager 지원: ' + String('PushManager' in window));
    summary.push('permission: ' + (window.Notification ? Notification.permission : 'N/A'));

    navigator.serviceWorker.getRegistrations()
        .then(function (regs) {
            summary.push('SW 등록 개수: ' + regs.length);
            for (var i = 0; i < regs.length; i++) {
                summary.push((i + 1) + ') scope: ' + regs[i].scope);
            }
            alert(summary.join('\n'));
        })
        .catch(function (err) {
            summary.push('getRegistrations 실패: ' + (err && err.message ? err.message : err));
            alert(summary.join('\n'));
        });
}
