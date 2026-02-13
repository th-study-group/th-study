<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Broadcast Test (npm)</title>

    {{-- Vite로 JS 로드 --}}
    @vite(['resources/js/app.js'])
</head>
<body>

<h2>실시간 메시지 (npm + Echo)</h2>

<input id="msg">
<button onclick="send()">전송</button>

<ul id="list"></ul>

<script>
console.log("start..");

document.addEventListener('DOMContentLoaded', () => {

    Echo.channel('chat-channel')
        .listen('MessageSent', (e) => {
            const li = document.createElement('li');
            li.innerText = e.message;
            document.getElementById('list').appendChild(li);
        });

});

function send() {
    fetch('/_dev/send', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            message: document.getElementById('msg').value
        })
    });
}
</script>

</body>
</html>