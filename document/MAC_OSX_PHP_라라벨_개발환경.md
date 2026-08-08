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
```

필수 확장을 확인합니다.

```bash
php -m | grep -Ei 'curl|fileinfo|gd|intl|mbstring|mysqli|openssl|pdo_mysql|zip'
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