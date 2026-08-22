# TH-STUDY 프론트엔드 개발 규칙

## 1. 적용 원칙

- 현재 Blade, public CSS/JS, Bootstrap 5 구현을 우선한다.
- 기능 전용 UI 규칙은 `board.md`, `note.md`에 유지한다. 이 문서는 Layout, Blade, 폼, 목록, 공통 JS처럼 여러 화면에서 쓰이는 패턴만 다룬다.
- Bootstrap 5를 우선 사용하되, 이미 존재하는 `board.css`, `blog.css`, `intro/*` 등 프로젝트 CSS와 충돌하지 않게 확장한다.

## 2. Layout과 Blade 구조

- 일반 화면은 `@extends('layouts.app')`을 사용한다.
- `layouts.app`은 header, menu, note sidebar, footer, back-to-top, splash, PWA popup을 include하고 `@yield('content')`로 본문을 렌더링한다.
- 페이지 제목은 `@section('title', '...')`로 설정한다. SEO/OG 값이 필요한 화면은 Layout에 있는 `meta_*`, `og_*` section을 사용한다.
- 페이지별 stylesheet/script 외부 파일은 `@push('styles')`, `@push('scripts')`로 추가한다. Layout의 `@stack()` 위치를 따른다.
- 화면별 동작은 `@section('script')` inline script로도 구현되어 있다. 기존 화면을 수정할 때는 해당 화면의 방식과 기능 복잡도를 기준으로 유지한다.

## 3. 공통 자산과 CSS / JS 파일

- 공통 자산은 `partials/head-styles.blade.php`, `partials/head-scripts.blade.php`에서 관리한다.
- Bootstrap 5, jQuery, `common.js`, PWA script, Toast UI Editor, Flatpickr가 실제 공통 자산으로 로드된다.
- 공통 스타일은 `public/css/common.css` 등, 기능별 스타일은 `board.css`, `blog.css`, `intro/*.css`처럼 분리되어 있다. 기능 전용 스타일은 기존 파일이 있으면 그 파일에 추가한다.
- `<style>` 태그는 blog 목록, readme, MCP 로그인 등 일부 화면에 존재한다. 따라서 전면 금지하지 않으며, 공통화 가능한 스타일 또는 규모 있는 기능 스타일은 기존 CSS 파일로 분리할지 먼저 검토한다.
- Vue/React 사용처는 현재 확인되지 않았다. Blade + public JS/jQuery 방식을 기본 전제로 한다.

## 4. Bootstrap Layout과 반응형

- Layout 본문은 `container-fluid`, `row`, Bootstrap grid를 사용한다. 관리자·게시판 중심 화면은 `col-12 col-lg-8 mx-auto` 구성이 반복된다.
- Header와 side menu는 desktop/mobile을 분리한다. desktop에는 `d-none d-lg-*`, mobile에는 navbar/offcanvas를 사용한다.
- 목록 테이블의 모바일 대응은 `table-responsive`보다 `board-table-wrap`과 공통 JS의 가로 드래그 처리를 주로 사용한다.
- board 계열에서 모바일 숨김은 `board-col-hidden`, 말줄임은 `board-ellipsis`/`board-ellipsis-mobile`, 줄바꿈 방지는 `text-nowrap`를 사용한다. 이 클래스는 board 계열 UI에 한정해 사용한다.

## 5. 목록 / 상세 / 폼 UI

- board 계열 목록은 대체로 `board-card` → 제목/검색 영역 → `board-table-wrap` + Bootstrap table → `board-pagination` 순서다.
- 목록은 `@forelse`/`@empty`로 빈 상태를 출력하고, `{{ $items->links() }}`를 pagination nav 안에 렌더링한다.
- 상세 화면은 Bootstrap spacing, `form-label`, `bg-light`, `rounded-3` 등을 조합해 읽기 전용 필드를 구성한다.
- 등록/수정은 공용 Blade를 쓰는 기능이 있다. 유사 기능이 이미 공용 Blade라면 mode 변수와 action/method 분기로 같은 구조를 유지한다.
- 버튼은 Bootstrap `btn` 계열을 사용하고, 상태는 `badge`와 기존 도메인 class(`use-flag` 등)를 함께 사용한다.

## 6. Form / Validation Message

- Blade form에는 `@csrf`를 사용하고 PUT/PATCH/DELETE는 필요한 `@method()`를 추가한다.
- 기존 입력값은 `old('field', $model->field ?? '')` 형태로 유지한다.
- 필드 오류는 `@error('field')`, `is-invalid`, `invalid-feedback`을 조합해 표시하는 패턴이 많다.
- 검색·등록/수정 화면에는 `$errors->any()` alert가 사용되며, 성공 메시지는 주로 `session('status')` alert로 표시한다.
- form label은 `form-label`, 보조 설명은 `small text-secondary` 조합이 반복된다.

## 7. JavaScript / AJAX

- 일반 이벤트 바인딩은 jQuery `$(function () {})`와 `.on('click', ...)`가 주류다.
- AJAX는 `common.js`의 `window.requestAjax()` 래퍼를 우선 사용한다. URL, method, data, `X-CSRF-TOKEN`, success/error callback을 기존 호출 형태에 맞춰 전달한다.
- 공통 `requestAjax()`는 loading UI를 연동한다. 새 AJAX에서 별도 loading 구현을 만들기 전 기존 래퍼 사용 가능 여부를 확인한다.
- 저장 form은 화면별 `isSubmitting` 상태와 submit button disable로 중복 제출을 막는 구현이 있다. 유사한 저장 form에는 기존 패턴을 따른다.
- 삭제·공개 여부 변경 등 위험한 동작은 기존 화면처럼 `confirm()` 후 AJAX 또는 form submit을 수행한다.
- `common.js`에는 loading modal, table 가로 드래그, 날짜 선택기, offcanvas/overlay 정리, 공유 기능이 있다. 같은 동작은 새 중복 구현보다 기존 helper 사용을 우선 검토한다.

## 8. 이미지와 기능 전용 UI

- 노트 목록은 `thumbnail_path`가 없을 때 `images/no_image.png`를 기본 이미지로 사용한다.
- 노트 등록/수정은 파일 input, 기존 이미지 정보, AJAX 썸네일 삭제, Toast UI Editor를 사용한다. 이는 노트 전용 구현이므로 다른 화면에 공통 규칙으로 강제하지 않는다.
- 블로그 목록/상세의 AJAX 더 보기·상세 modal은 `blog.js`와 관련 Blade의 기존 payload 형식을 따른다.

## 9. 접근성

- 공통 화면에는 `aria-label`, `aria-expanded`, `aria-controls`, `role="alert"`, table `scope="col"`, modal의 `aria-*` 속성이 사용된다.
- 새 버튼, collapse, modal, table을 추가할 때는 같은 Bootstrap 접근성 속성과 label 패턴을 적용할 수 있는지 확인한다.
- 현재 전 화면에 동일한 접근성 구현이 적용된 것은 아니므로, 확인되지 않은 별도 표준을 강제 규칙으로 추가하지 않는다.

## 10. PWA / BFCache 페이지 복원 상태

- PWA에서 스와이프로 이전 페이지로 돌아갈 때 페이지가 새로 로드되지 않고 BFCache에서 복원될 수 있으므로, 목록 화면의 이동 상태를 확인한다.
- 목록에서 중복 이동 방지를 위해 `*NavigationStarted` 플래그를 사용하는 경우 `pageshow` 이벤트에서 해당 플래그를 `false`로 초기화한다.
- 목록 → 상세/수정 → PWA 스와이프 뒤로가기 → 다른 행 클릭 흐름을 반드시 테스트한다.
