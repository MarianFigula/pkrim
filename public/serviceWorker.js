/* eslint-disable no-restricted-globals */

const cache_container = "static_v1";
const files = [
    "/",
    "/index.html",
    "/logo.png",
    "/logo192.png",
    "/logo512.png"
]


// install service worker and add all files to cache
self.addEventListener('install', function (event){
    event.waitUntil(
        caches.open(cache_container).then(cache => {
            cache.addAll(files)
        })
    )
})

self.addEventListener('activate', function (event){
    console.log("service worker activated", event);
})

addEventListener('fetch', event => {
    // Prevent the default, and handle the request ourselves.
    event.respondWith(async function() {
        // Try to get the response from a cache.
        const cachedResponse = await caches.match(event.request);
        // Return it if we found one.
        if (cachedResponse) return cachedResponse;
        // If we didn't find a match in the cache, use the network.
        return fetch(event.request);
    }());
});