<!DOCTYPE html>
<html lang="ko">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ ($code ?? 500) . ' | ' . ($title ?? '오류가 발생했어요') }}</title>
  <meta name="robots" content="noindex,nofollow">
  <style>
    :root {
      color-scheme: light;
      --error-bg: #eef4ff;
      --error-surface: rgba(255, 255, 255, 0.9);
      --error-surface-border: rgba(163, 184, 221, 0.34);
      --error-text: #152033;
      --error-subtle: #5c6b82;
      --error-accent: #1d4ed8;
      --error-accent-strong: #163ea7;
      --error-shadow: 0 28px 80px rgba(20, 36, 68, 0.16);
    }

    * {
      box-sizing: border-box;
    }

    html,
    body {
      height: 100%;
      margin: 0;
    }

    body {
      min-height: 100vh;
      font-family: "Pretendard", "Noto Sans KR", "Apple SD Gothic Neo", sans-serif;
      background:
        radial-gradient(circle at top left, rgba(29, 78, 216, 0.18), transparent 28%),
        radial-gradient(circle at bottom right, rgba(14, 165, 233, 0.14), transparent 24%),
        linear-gradient(180deg, #f6f9ff 0%, #edf3ff 100%);
      color: var(--error-text);
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 24px;
    }

    .error-shell {
      width: min(100%, 720px);
      border: 1px solid var(--error-surface-border);
      border-radius: 28px;
      background: var(--error-surface);
      box-shadow: var(--error-shadow);
      backdrop-filter: blur(14px);
      overflow: hidden;
    }

    .error-inner {
      padding: 34px 30px 30px;
      text-align: center;
    }

    .error-badge {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      min-width: 88px;
      height: 34px;
      padding: 0 16px;
      border-radius: 999px;
      border: 1px solid rgba(37, 99, 235, 0.16);
      background: rgba(255, 255, 255, 0.9);
      color: var(--error-accent);
      font-size: 13px;
      font-weight: 800;
      letter-spacing: .08em;
      text-transform: uppercase;
    }

    .error-code {
      margin: 20px 0 8px;
      font-size: clamp(84px, 19vw, 148px);
      line-height: .92;
      letter-spacing: -0.05em;
      font-weight: 900;
      color: #0f172a;
    }

    .error-title {
      margin: 0;
      font-size: clamp(22px, 4.2vw, 32px);
      line-height: 1.2;
      font-weight: 800;
      white-space: nowrap;
    }

    .error-message {
      margin: 12px 0 0;
      color: var(--error-subtle);
      font-size: clamp(14px, 2.7vw, 17px);
      line-height: 1.3;
      font-weight: 600;
      white-space: nowrap;
    }

    .error-actions {
      margin-top: 26px;
      display: flex;
      justify-content: center;
    }

    .error-home-link {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      min-width: 176px;
      height: 52px;
      padding: 0 22px;
      border-radius: 16px;
      background: linear-gradient(135deg, var(--error-accent) 0%, #2563eb 100%);
      color: #fff;
      text-decoration: none;
      font-size: 16px;
      font-weight: 800;
      box-shadow: 0 14px 28px rgba(29, 78, 216, 0.24);
      transition: transform .16s ease, box-shadow .16s ease, background-color .16s ease;
    }

    .error-home-link:hover {
      transform: translateY(-1px);
      box-shadow: 0 16px 32px rgba(29, 78, 216, 0.28);
      background: linear-gradient(135deg, var(--error-accent-strong) 0%, #1d4ed8 100%);
      color: #fff;
    }

    .error-home-link:focus-visible {
      outline: 3px solid rgba(37, 99, 235, 0.24);
      outline-offset: 4px;
    }

    @media (max-width: 640px) {
      body {
        padding: 16px;
      }

      .error-shell {
        border-radius: 24px;
      }

      .error-inner {
        padding: calc(env(safe-area-inset-top, 0px) + 28px) 18px calc(env(safe-area-inset-bottom, 0px) + 24px);
      }

      .error-badge {
        min-width: 80px;
        height: 32px;
        font-size: 12px;
      }

      .error-title,
      .error-message {
        overflow: hidden;
        text-overflow: ellipsis;
      }

      .error-home-link {
        width: 100%;
        min-width: 0;
        height: 50px;
      }
    }
  </style>
</head>
<body>
  <main class="error-shell" aria-labelledby="errorTitle">
    <div class="error-inner">
      <div class="error-badge">티에이치스터디</div>
      <div class="error-code">{{ $code ?? 500 }}</div>
      <h1 id="errorTitle" class="error-title">{{ $title ?? '오류가 발생했어요' }}</h1>
      <p class="error-message">{{ $message ?? '잠시 후 다시 시도해 주세요' }}</p>
      <div class="error-actions">
        <a href="{{ route('home') }}" class="error-home-link">메인으로 가기</a>
      </div>
    </div>
  </main>
</body>
</html>
