self.addEventListener("install", function () { 
    self.skipWaiting(); 
}); // 즉시적용

self.addEventListener("activate", function (event) { 
    event.waitUntil(self.clients.claim()); 
}); // 바로제어

self.addEventListener("push", function (event) { // # 푸시 수신
  var data = { title: "알림", body: "새 메시지", url: "/" }; // # 기본
  try { 
    if (event.data) {
      data = Object.assign(data, event.data.json()); 
    }
  } catch (e) {}
  
  event.waitUntil(
    self.registration.showNotification(data.title, {
      body: data.body,
      icon: "/images/favicon/th_favicon_and_192.png",
      badge: "/images/favicon/th_favicon_and_192.png",
      data: { url: data.url } // 클릭 시 열 URL
    })
  );
});
  
self.addEventListener("notificationclick", function (event) { // 클릭 이동
  event.notification.close();
  event.waitUntil(clients.openWindow((event.notification.data && event.notification.data.url) ? event.notification.data.url : "/"));
});