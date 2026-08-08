# 티에이치스터디 Windows 개발환경 구축

## 1. PHP 8.2 설치

Windows PHP 공식 사이트에서 아래 버전을 다운로드합니다.

```text
PHP 8.2 x64 Non Thread Safe (NTS)
https://windows.php.net/download/
```

압축 해제 위치

```text
C:\php82
```

---

## 2. php.ini 생성

```text
C:\php82\php.ini-development
→
C:\php82\php.ini
```

`php.ini`에서 필요한 확장을 활성화합니다.

```ini
extension=curl
extension=fileinfo
extension=gd
extension=intl
extension=mbstring
extension=mysqli
extension=openssl
extension=pdo_mysql
extension=zip
```

기본 설정

```ini
memory_limit=512M
upload_max_filesize=50M
post_max_size=50M
max_execution_time=60
max_input_time=60
date.timezone=Asia/Seoul
```

---

## 3. SSL 인증서 설정

Windows에서 OpenAI API 호출 시 `cURL error 60`이 발생하면 인증서를 설정합니다.

```text
https://curl.se/ca/cacert.pem
```

저장 위치

```text
C:\php82\extras\ssl\cacert.pem
```

`php.ini`

```ini
curl.cainfo="C:\php82\extras\ssl\cacert.pem"
openssl.cafile="C:\php82\extras\ssl\cacert.pem"
```

---

## 4. PATH 등록

Windows 환경변수 `Path`에 추가합니다.

```text
C:\php82
```

---

## 5. Composer 설치

```text
https://getcomposer.org/
```

설치 시 PHP 경로를 지정합니다.

```text
C:\php82\php.exe
```

---

## 6. 설치 확인

```bash
php -v
php --ini
php -m
composer -V
```

필수 확장 확인

```bash
php -m | findstr /i "curl fileinfo gd intl mbstring mysqli openssl pdo_mysql zip"
```

---

## 7. 프로젝트 실행

```bash
cd th-study

composer install
npm install

copy .env.example .env

php artisan key:generate
php artisan migrate

php artisan serve
```

별도 터미널에서 Vite를 실행합니다.

```bash
npm run dev
```

접속

```text
http://127.0.0.1:8000
```