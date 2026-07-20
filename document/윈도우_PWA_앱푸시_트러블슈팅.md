# PWA 앱 푸시 Windows에서 안 될 때 설정 정리

## 1. Windows 알림 설정 확인

Windows에서 Chrome 알림이 차단되어 있으면 PWA 푸시 발송이 정상적으로 처리되어도 알림이 표시되지 않을 수 있습니다.

Windows 설정에서 다음 경로로 이동합니다.

```text
설정
→ 시스템
→ 알림
```

다음 항목을 확인합니다.

```text
알림 사용: 켜짐
Google Chrome 알림: 켜짐
```

Chrome이 알림 앱 목록에 보이지 않는 경우 Chrome에서 먼저 알림 권한을 요청한 뒤 다시 확인합니다.

PWA를 Chrome에서 별도 앱으로 설치한 경우 설치된 PWA 앱의 알림도 차단되어 있지 않은지 확인합니다.

---

## 2. Chrome 사이트 알림 권한 확인

Chrome 주소창에 다음 주소를 입력합니다.

```text
chrome://settings/content/notifications
```

또는 다음 메뉴로 이동합니다.

```text
Chrome 설정
→ 개인정보 보호 및 보안
→ 사이트 설정
→ 알림
```

PWA를 실행하는 로컬 주소가 알림 허용 목록에 있는지 확인합니다.

```text
http://localhost:8000
```

차단 목록에 있다면 제거한 뒤 사이트에 다시 접속하여 알림을 허용합니다.

사이트별 권한은 주소창 왼쪽의 사이트 정보 아이콘을 눌러 확인할 수도 있습니다.

```text
사이트 설정
→ 알림
→ 허용
```

---

## 3. Service Worker 상태 확인

Chrome 개발자도구를 열고 다음 메뉴로 이동합니다.

```text
Application
→ Service Workers
```

Service Worker 상태가 다음과 같이 표시되는지 확인합니다.

```text
activated and running
```

Service Worker가 등록되지 않았거나 이전 파일이 남아 있다면 다음 순서로 초기화합니다.

```text
Application
→ Service Workers
→ Unregister
```

그다음 사이트 데이터를 삭제합니다.

```text
Application
→ Storage
→ Clear site data
```

페이지를 새로고침한 뒤 Service Worker를 다시 등록하고 알림 권한을 다시 허용합니다.

---

## 4. VAPID 키 확인

`.env`에 VAPID 키를 설정합니다.

```env
VAPID_PUBLIC_KEY=공개키
VAPID_PRIVATE_KEY=개인키
VAPID_SUBJECT=관리자이메일
```

VAPID 키는 프로젝트를 복제할 때마다 새로 생성하는 값이 아닙니다.

동일한 서비스에서 기존 브라우저 구독을 유지하려면 기존 VAPID 공개키와 개인키를 계속 사용합니다.

프로젝트에서 `config/services.php`를 사용한다면 다음과 같이 연결합니다.

```php
'webpush' => [
    'vapid_public_key' => env('VAPID_PUBLIC_KEY'),
    'vapid_private_key' => env('VAPID_PRIVATE_KEY'),
    'vapid_subject' => 'mailto:' . env('VAPID_SUBJECT'),
],
```

설정값을 확인합니다.

```bash
php artisan tinker
```

```php
config('services.webpush.vapid_public_key');
config('services.webpush.vapid_private_key');
config('services.webpush.vapid_subject');
```

값이 `null`로 나오면 Laravel 설정 캐시를 제거합니다.

```bash
php artisan optimize:clear
```

---

## 5. 사용 중인 php.ini 경로 확인

Windows에 PHP가 여러 개 설치되어 있으면 수정한 `php.ini`와 실제 CLI에서 읽는 `php.ini`가 다를 수 있습니다.

다음 명령으로 현재 사용 중인 파일을 확인합니다.

```powershell
php --ini
```

확인할 항목은 다음과 같습니다.

```text
Loaded Configuration File
```

예시

```text
Loaded Configuration File: C:\php8.2\php.ini
```

반드시 이 경로의 `php.ini`를 수정합니다.

---

## 6. php.ini 확장 모듈 설정

`php.ini`에서 확장 모듈 경로를 확인합니다.

```ini
extension_dir = "ext"
```

현재 설정에서 세미콜론이 제거되어 활성화된 확장 모듈은 다음과 같습니다.

```ini
extension=curl
extension=fileinfo
extension=gd
extension=mbstring
extension=exif
extension=mysqli
extension=openssl
extension=pdo_mysql
extension=sodium
```

PWA Web Push 처리에서 직접적으로 중요한 확장은 다음 두 개입니다.

```ini
extension=curl
extension=openssl
```

설정 후 다음 명령으로 확장 모듈이 로드되었는지 확인합니다.

```powershell
php -m | findstr /I "curl openssl"
```

정상 출력 예시

```text
curl
openssl
```

OpenSSL 버전도 확인합니다.

```powershell
php -r "echo OPENSSL_VERSION_TEXT, PHP_EOL;"
```

---

## 7. OpenSSL EC 키 생성 테스트

Laravel Web Push는 메시지 암호화 과정에서 EC 키를 생성합니다.

다음 명령으로 Windows PHP에서 EC 키가 정상적으로 생성되는지 확인합니다.

```powershell
php -r "$key = openssl_pkey_new(['private_key_type' => OPENSSL_KEYTYPE_EC, 'curve_name' => 'prime256v1']); var_dump($key); while ($e = openssl_error_string()) echo $e, PHP_EOL;"
```

정상이라면 다음과 비슷한 결과가 출력됩니다.

```text
object(OpenSSLAsymmetricKey)
```

다음과 같은 오류가 나오거나 결과가 `false`라면 OpenSSL 설정 파일을 찾지 못하는 상태일 수 있습니다.

```text
configuration file routines::no such file
BIO routines::no such file
```

Laravel Web Push에서는 이 상태에서 다음 오류가 발생할 수 있습니다.

```text
RuntimeException: Unable to create the local key.
```

---

## 8. openssl.cnf 파일 준비

PHP 설치 경로에 OpenSSL 설정 파일이 있는지 확인합니다.

예시 경로

```text
C:\php8.2\extras\ssl\openssl.cnf
```

파일이 없다면 PHP 배포 파일이나 OpenSSL 설치 경로에 포함된 `openssl.cnf`를 확인하여 해당 위치에 저장합니다.

최종 경로 예시

```text
C:\php8.2\extras\ssl\openssl.cnf
```

---

## 9. OPENSSL_CONF 환경변수 등록

Windows 검색에서 다음 항목을 실행합니다.

```text
시스템 환경 변수 편집
```

다음 순서로 이동합니다.

```text
고급
→ 환경 변수
→ 시스템 변수
→ 새로 만들기
```

변수 이름

```text
OPENSSL_CONF
```

변수 값

```text
C:\php8.2\extras\ssl\openssl.cnf
```

저장한 뒤 기존에 열려 있던 다음 프로그램을 모두 종료하고 다시 실행합니다.

```text
PowerShell
명령 프롬프트
VS Code
PHP 개발 서버
Queue Worker
```

적용 여부를 확인합니다.

```powershell
php -r "var_dump(getenv('OPENSSL_CONF'));"
```

정상 출력 예시

```text
string(38) "C:\php8.2\extras\ssl\openssl.cnf"
```

환경변수 적용 후 EC 키 생성 테스트를 다시 실행합니다.

```powershell
php -r "$key = openssl_pkey_new(['private_key_type' => OPENSSL_KEYTYPE_EC, 'curve_name' => 'prime256v1']); var_dump($key); while ($e = openssl_error_string()) echo $e, PHP_EOL;"
```

---

## 10. cacert.pem 다운로드

EC 키 생성 오류를 해결한 뒤 다음 오류가 발생할 수 있습니다.

```text
cURL error 60:
SSL certificate problem:
unable to get local issuer certificate
```

이 오류는 Windows PHP가 외부 HTTPS 서버의 인증서를 검증할 CA 인증서 목록을 찾지 못할 때 발생합니다.

공식 cURL CA 인증서 파일을 다음 주소에서 다운로드합니다.

```text
https://curl.se/ca/cacert.pem
```

다운로드한 파일을 다음 경로에 저장합니다.

```text
C:\php8.2\extras\ssl\cacert.pem
```

최종 폴더 구조 예시

```text
C:\php8.2
└─ extras
   └─ ssl
      ├─ openssl.cnf
      └─ cacert.pem
```

---

## 11. php.ini에 CA 인증서 경로 등록

현재 CLI에서 사용하는 `php.ini`를 열고 다음 설정을 추가하거나 기존 항목을 수정합니다.

```ini
curl.cainfo="C:\php8.2\extras\ssl\cacert.pem"
openssl.cafile="C:\php8.2\extras\ssl\cacert.pem"
```

설정 후 PowerShell, VS Code, PHP 서버 및 Queue Worker를 모두 다시 실행합니다.

적용 여부를 확인합니다.

```powershell
php -i | findstr /I "curl.cainfo openssl.cafile"
```

정상 출력 예시

```text
curl.cainfo => C:\php8.2\extras\ssl\cacert.pem => C:\php8.2\extras\ssl\cacert.pem
openssl.cafile => C:\php8.2\extras\ssl\cacert.pem => C:\php8.2\extras\ssl\cacert.pem
```

PHP 코드에서도 확인할 수 있습니다.

```powershell
php -r "echo ini_get('curl.cainfo'), PHP_EOL; echo ini_get('openssl.cafile'), PHP_EOL;"
```

정상 출력 예시

```text
C:\php8.2\extras\ssl\cacert.pem
C:\php8.2\extras\ssl\cacert.pem
```

---

## 12. Laravel 캐시 제거

PHP와 인증서 설정을 변경한 뒤 Laravel 캐시를 제거합니다.

```bash
php artisan optimize:clear
```

필요하면 개별 명령으로 제거할 수도 있습니다.

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

---

## 13. Queue Worker 재시작

이미 실행 중인 Queue Worker는 변경된 PHP 설정을 자동으로 다시 읽지 않습니다.

기존 Queue Worker를 `Ctrl + C`로 종료한 뒤 다시 실행합니다.

```bash
php artisan queue:work -vvv --tries=1
```

또는 Queue 재시작 명령을 실행합니다.

```bash
php artisan queue:restart
```

그다음 Worker를 다시 실행합니다.

```bash
php artisan queue:work
```

---

## 14. 최종 확인 명령

현재 사용 중인 PHP와 `php.ini`를 확인합니다.

```powershell
where php
php -v
php --ini
```

필수 확장을 확인합니다.

```powershell
php -m | findstr /I "curl openssl"
```

OpenSSL 설정 파일 환경변수를 확인합니다.

```powershell
php -r "var_dump(getenv('OPENSSL_CONF'));"
```

EC 키 생성을 확인합니다.

```powershell
php -r "$key = openssl_pkey_new(['private_key_type' => OPENSSL_KEYTYPE_EC, 'curve_name' => 'prime256v1']); var_dump($key); while ($e = openssl_error_string()) echo $e, PHP_EOL;"
```

CA 인증서 설정을 확인합니다.

```powershell
php -i | findstr /I "curl.cainfo openssl.cafile"
```

Laravel 캐시를 제거합니다.

```bash
php artisan optimize:clear
```

Queue Worker를 실행합니다.

```bash
php artisan queue:work -vvv --tries=1
```