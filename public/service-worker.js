self.addEventListener("install", function () { 
    self.skipWaiting(); 
}); // # 즉시적용

self.addEventListener("activate", function (event) { 
    event.waitUntil(self.clients.claim()); 
}); // # 바로제어