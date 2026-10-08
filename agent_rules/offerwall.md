# TH-STUDY Offerwall 운영 규칙

## 적용 범위

Google Funding Choices Offerwall 메시지를 일반 AdSense 광고와 분리해 제어하는 작업에 적용한다.

## 현재 구현

- `app/Support/OfferwallGuard.php`는 `user.level === 'admin'` 또는 `config('traffic.access_log_excluded_ips')`에 포함된 IP를 Offerwall 차단 대상으로 판별한다.
- `app/Http/Middleware/ShareOfferwallSettings.php`가 요청별 차단 상태를 View에 공유한다.
- `resources/views/layouts/app.blade.php`는 AdSense 스크립트보다 먼저 `googlefc.controlledMessagingFunction`을 정의한다.
- 차단 시 `message.proceed(false, [window.googlefc.MessageTypeEnum.OFFERWALL])`을 사용해 Offerwall만 억제한다. 일반 광고와 다른 메시지 유형은 차단하지 않는다.
- 기존 `TrafficTrackingGuard`, GA4, access log, conversion log 로직은 Offerwall 때문에 변경하지 않는다.
- 기존 `config/adsense.php`의 광고 제외 Route에서는 AdSense 스크립트가 로드되지 않으므로 Offerwall도 표시되지 않는다.

## 변경 규칙

- 지정 IP 목록은 `config/traffic.php`의 기존 `access_log_excluded_ips`를 재사용한다.
- Offerwall 조건을 `TrafficTrackingGuard`에 추가하지 않는다. 추적 제외 정책과 Offerwall 표시 정책을 분리한다.
- `controlledMessagingFunction`은 Google 광고 스크립트보다 먼저 정의한다.
- Offerwall 차단이 일반 AdSense 광고 차단으로 확대되지 않았는지 브라우저에서 관리자, 지정 IP, 일반 회원, 비로그인 방문자를 각각 확인한다.
- 테스트는 게시된 Funding Choices 메시지와 `fc=alwaysshow&fctype=monetization`을 사용한다. 정적 검사만으로 실제 메시지 노출 여부를 확정하지 않는다.

