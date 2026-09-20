# TH-STUDY Google AdSense 운영 규칙

## 1. 적용 범위

Google AdSense 로더, 자동광고, 앵커·전면광고, 자동 in-page 광고, iOS Safari 및 standalone PWA에서 광고와 레이아웃이 충돌하는 작업에 적용한다.

화면·CSS·JavaScript 변경이 있으면 `agent_rules/frontend.md`도 함께 확인한다.

## 2. 현재 구조

- AdSense 공통 스크립트는 `resources/views/layouts/app.blade.php`에서 `ADSENSE_ID` 설정값이 있을 때 로드한다.
- AdSense 발급자 ID는 `config/services.php`의 `ADSENSE_ID`로, 광고 단위는 `config/adsense.php`의 `units`에서 AdSense 화면의 이름을 키로 관리한다. 수동 광고 슬롯은 Controller가 해당 키의 최종 값을 전달하고, `resources/views/components/adsense.blade.php`의 `<x-adsense>`가 `ad-slot`, `format`으로 출력한다. 인피드·멀티플렉스에 필요한 `data-ad-layout`, `data-ad-layout-key` 등의 추가 속성은 컴포넌트 호출부에서 전달한다.
- 개별 Blade에 Google 로더 스크립트를 중복 삽입하지 않는다. 커스텀 모달 등 동적 영역은 표시된 뒤 `requestAnimationFrame` 기준으로 광고 `<ins>`의 실제 폭이 0보다 큰 것을 확인한 뒤에만 초기화한다. flex 레이아웃 안의 수동 광고는 전용 래퍼와 광고 `<ins>`에 `width: 100%`를 적용해 빈 flex item이 폭 0으로 축소되지 않게 한다. `data-adsbygoogle-status` 또는 별도 초기화 표시가 있는 슬롯은 다시 push하지 않으며, 초기화된 슬롯이나 Google 로더를 다시 초기화하지 않는다.
- 모달에서 큰 자동 포맷이 부적절하면 `<ins>` 슬롯 자체에 화면 폭별 가로형 크기를 지정하고 `data-ad-format="auto"` 및 `data-full-width-responsive`를 함께 제거한다. iframe을 CSS로 자르거나 변형하지 않으며, 배너형 슬롯의 폭·높이는 광고 슬롯 자체에만 적용한다.
- `resources/views/components/adfit.blade.php`의 광고는 Kakao AdFit이므로 AdSense 자동광고와 혼동하지 않는다.

## 3. 금지 사항

- Google이 생성한 iframe, 광고 DOM, 광고의 닫기·접기 버튼을 CSS 또는 JavaScript로 숨기거나 이동·크기 변경·클릭 영역 변경하지 않는다.
- 광고 표시 여부나 위치를 추측해 `z-index`, `overflow`, 고정 여백을 전역으로 강제하지 않는다.
- 광고 종료 이벤트를 대상으로 body 스크롤을 제어하지 않는다.

## 4. iOS/PWA 레이아웃 기준

- `viewport-fit=cover`를 사용하는 화면은 top/bottom safe-area 처리가 이미 있는지 먼저 확인하고 중복 적용하지 않는다.
- fixed header는 `env(safe-area-inset-top)`과 실제 헤더 높이만큼 본문 시작 위치를 확보한다.
- 하단 fixed UI는 `env(safe-area-inset-bottom)`을 반영한다.
- 일반 문서 화면의 최소 높이는 `100vh` fallback과 `100svh`, `100dvh`를 함께 검토한다. 전체 화면 전용 UI의 `overflow: hidden`은 별도로 검증한다.
- 회전, Safari 주소창 변화, BFCache 복원 뒤 header offset과 자체 modal/offcanvas의 body overflow 잔류를 확인한다.

## 5. 자동광고 배치 관리

- AdSense 콘솔 `Excluded areas`는 미리보기에서 Google이 제시한 광고 후보 영역만 제외할 수 있다. 표·코드블록처럼 임의의 콘텐츠 블록을 직접 선택할 수 없는 경우에는 해당 화면의 정보 구조를 단순화하거나 `Page exclusions`를 사용한다.
- 특정 화면의 자동광고 정책은 AdSense 콘솔 `Page exclusions`에서 관리한다. 코드로 Google 광고를 숨기는 방식은 사용하지 않는다.
- 앵커·전면광고의 노출 여부, 위치, 동적 크기, 빈도는 AdSense 콘솔의 Auto ads 설정에서 조정한다.
- 하단 앵커광고가 만드는 외부 배경·접힘 영역은 사이트 CSS로 수정하지 않는다. 사이트 자체 fixed UI만 safe-area 기준으로 배치한다.

## 6. 검증

- AdSense 스크립트가 한 페이지에 한 번만 로드되는지 확인한다.
- 모달·동적 영역의 수동 광고는 최초 표시와 다른 콘텐츠를 연속으로 열 때 콘솔 오류나 동일 슬롯의 중복 초기화가 없는지 확인한다.
- 수동 광고가 보이지 않으면 해당 `<ins>`의 `offsetWidth`, `offsetHeight`, `data-ad-status`, `data-adsbygoogle-status`를 함께 확인한다. `filled`는 Google 렌더링 완료, `unfilled`는 광고 미배정, 상태 속성 부재는 미초기화 또는 초기화 대기 상태로 구분한다.
- iPhone Safari와 standalone PWA에서 status bar/header, home indicator/fixed UI, 세로·가로 회전을 확인한다.
- 광고 표시·종료 뒤에도 사이트 자체 modal/offcanvas가 없으면 body의 `overflow`, `touch-action`, `modal-open`이 남지 않는지 확인한다.
- 데스크톱에서 footer와 하단 앵커광고가 동시에 보일 때 사이트 UI가 가려지지 않는지 확인한다.
