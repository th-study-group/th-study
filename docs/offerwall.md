# Offerwall 구현 메모

TH Study는 Google Funding Choices API의 `googlefc.controlledMessagingFunction`을 사용해 Offerwall만 선택적으로 제어한다.

최고관리자(`user.level === 'admin'`) 또는 `config/traffic.php`의 기존 지정 IP에 해당하면 다음 방식으로 Offerwall만 차단한다.

```js
const applicableMessageTypes = [
    window.googlefc.MessageTypeEnum.OFFERWALL
];
message.proceed(false, applicableMessageTypes);
```

일반 회원과 일반 방문자는 `message.proceed(true)`로 처리한다. 일반 AdSense 광고 로더와 기존 GA4·유입 로그 로직은 별도로 유지한다.

`controlledMessagingFunction`은 `adsbygoogle.js`보다 먼저 선언해야 한다. 기존 로그인·회원가입·관리자·작성·수정 Route는 `config/adsense.php`의 공통 광고 제외 패턴으로 AdSense 스크립트가 로드되지 않으므로 Offerwall도 표시되지 않는다.

관련 구현:

- `app/Support/OfferwallGuard.php`
- `app/Http/Middleware/ShareOfferwallSettings.php`
- `resources/views/layouts/app.blade.php`

공식 참고: [Funding Choices API](https://developers.google.com/funding-choices/fc-api-docs?hl=ko)
