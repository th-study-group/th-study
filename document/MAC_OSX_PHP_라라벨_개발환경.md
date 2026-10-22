# 티에이치스터디 macOS 개발환경 구축

## 1. Homebrew 설치 확인

```bash
brew --version
```

Homebrew가 없다면 설치합니다.

```text
https://brew.sh/
```

---

## 2. PHP 8.2 설치

```bash
brew update
brew install php@8.2
```

Apple Silicon 기준 PATH를 등록합니다.

```bash
echo 'export PATH="/opt/homebrew/opt/php@8.2/bin:$PATH"' >> ~/.zshrc
source ~/.zshrc
```

Intel Mac은 `/opt/homebrew` 대신 `/usr/local` 경로를 사용합니다.

---

## 3. PHP 설정

현재 `php.ini` 위치를 확인합니다.

```bash
php --ini
```

확인된 `php.ini`에 아래 값을 설정합니다.

```ini
memory_limit=512M
upload_max_filesize=50M
post_max_size=50M
max_execution_time=60
max_input_time=60
date.timezone=Asia/Seoul
session.upload_progress.enabled=Off
```

`session.upload_progress.enabled`는 파일 업로드 진행 상태를 세션에 기록하는 기능입니다. 로컬 개발환경에서도 불필요한 업로드 진행 세션 처리를 막기 위해 `Off`로 설정합니다. 일반적인 이미지·엑셀 파일 업로드에는 영향이 없습니다. 이 설정은 임시 보안 완화 조치이므로 사용하는 PHP 버전의 보안 패치도 별도로 적용해야 합니다.

Homebrew 설치 위치와 PHP 버전에 따라 `php --ini`에서 확인한 실제 `php.ini`를 수정합니다. PHP-FPM을 별도로 실행하는 경우에는 CLI가 읽는 설정과 FPM이 읽는 설정이 다를 수 있으므로 FPM 설정 파일에도 같은 값을 적용해야 합니다.

필수 확장을 확인합니다.

```bash
php -m | grep -Ei 'curl|fileinfo|gd|intl|mbstring|mysqli|openssl|pdo_mysql|zip'
php -i | grep -i 'session.upload_progress.enabled'
```

---

## 4. Composer 설치

```bash
brew install composer
```

확인

```bash
composer -V
```

---

## 5. Node.js 20 설치

```bash
brew install node@20
```

Apple Silicon 기준 PATH를 등록합니다.

```bash
echo 'export PATH="/opt/homebrew/opt/node@20/bin:$PATH"' >> ~/.zshrc
source ~/.zshrc
```

확인

```bash
node -v
npm -v
```

---

## 6. MySQL 8 설치

```bash
brew install mysql@8.0
brew services start mysql@8.0
```

확인

```bash
mysql --version
```

---

## 7. 프로젝트 실행

```bash
cd ~/th-study

composer install
npm install

cp .env.example .env

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

---

## 8. 최종 확인

```bash
php -v
php --ini
composer -V
node -v
mysql --version
php artisan --version
```
