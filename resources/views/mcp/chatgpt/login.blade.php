<!doctype html>
<html lang="ko">
<head>
    <meta charset="utf-8">
    <title>티에이치스터디 OAuth 로그인</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        :root {
            --th-bg: #050b18;
            --th-bg-soft: #07111f;
            --th-card: #0f172a;
            --th-card-body: #111827;
            --th-purple: #7c3aed;
            --th-purple-soft: #8b5cf6;
            --th-blue: #38bdf8;
            --th-blue-soft: #60a5fa;
            --th-text: #f8fafc;
            --th-text-muted: #cbd5e1;
            --th-text-faded: #94a3b8;
            --th-border: rgba(148, 163, 184, 0.25);
            --th-shadow: 0 30px 80px rgba(5, 11, 24, 0.55);
            --th-radius-xl: 28px;
            --th-radius-lg: 20px;
            --th-radius-md: 14px;
        }

        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            min-height: 100%;
            font-family: "Segoe UI", "Noto Sans KR", "Malgun Gothic", sans-serif;
            color: var(--th-text);
            background:
                linear-gradient(rgba(148, 163, 184, 0.05) 1px, transparent 1px),
                linear-gradient(90deg, rgba(148, 163, 184, 0.05) 1px, transparent 1px),
                radial-gradient(circle at top left, rgba(124, 58, 237, 0.18), transparent 32%),
                radial-gradient(circle at bottom right, rgba(56, 189, 248, 0.12), transparent 30%),
                var(--th-bg);
            background-size: 32px 32px, 32px 32px, auto, auto, auto;
        }

        body {
            position: relative;
        }

        body::before {
            content: "";
            position: fixed;
            inset: 0;
            pointer-events: none;
            background:
                radial-gradient(circle at 20% 20%, rgba(96, 165, 250, 0.12), transparent 22%),
                radial-gradient(circle at 80% 25%, rgba(139, 92, 246, 0.12), transparent 24%);
            opacity: 0.9;
        }

        .th-auth-page {
            position: relative;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            z-index: 1;
        }

        .th-auth-navbar {
            position: sticky;
            top: 0;
            z-index: 2;
            display: flex;
            align-items: center;
            min-height: 76px;
            padding: 18px 24px;
            border-bottom: 1px solid rgba(148, 163, 184, 0.12);
            background: rgba(7, 17, 31, 0.82);
            backdrop-filter: blur(16px);
        }

        .th-auth-brand {
            display: inline-flex;
            align-items: center;
            gap: 14px;
            text-decoration: none;
            color: inherit;
        }

        .th-auth-logo-mark {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 46px;
            height: 46px;
            border-radius: 15px;
            font-size: 1.2rem;
            font-weight: 800;
            letter-spacing: 0.08em;
            color: #ffffff;
            background: linear-gradient(135deg, var(--th-purple) 0%, var(--th-blue) 100%);
            box-shadow: 0 16px 36px rgba(76, 101, 255, 0.3);
        }

        .th-auth-brand-text {
            display: flex;
            flex-direction: column;
            gap: 3px;
        }

        .th-auth-brand-title {
            font-size: 1.45rem;
            font-weight: 800;
            letter-spacing: -0.03em;
        }

        .th-auth-brand-subtitle {
            font-size: 0.82rem;
            color: var(--th-text-muted);
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }

        .th-auth-main {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 48px 20px 32px;
        }

        .th-auth-card {
            width: min(100%, 560px);
            border: 1px solid rgba(99, 102, 241, 0.22);
            border-radius: var(--th-radius-xl);
            overflow: hidden;
            background: rgba(15, 23, 42, 0.92);
            box-shadow: var(--th-shadow);
            backdrop-filter: blur(14px);
        }

        .th-auth-card-header {
            position: relative;
            padding: 34px 34px 30px;
            background: linear-gradient(135deg, #111827 0%, #312e81 45%, #7c3aed 100%);
        }

        .th-auth-card-header::after {
            content: "";
            position: absolute;
            inset: auto -10% -38% auto;
            width: 240px;
            height: 240px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(96, 165, 250, 0.32), transparent 62%);
            pointer-events: none;
        }

        .th-auth-badge {
            display: inline-flex;
            align-items: center;
            padding: 8px 14px;
            border: 1px solid rgba(248, 250, 252, 0.2);
            border-radius: 999px;
            background: rgba(15, 23, 42, 0.22);
            color: #eef2ff;
            font-size: 0.82rem;
            font-weight: 700;
            letter-spacing: -0.01em;
        }

        .th-auth-card-header h1 {
            margin: 18px 0 14px;
            max-width: 420px;
            font-size: clamp(2rem, 4vw, 2.55rem);
            line-height: 1.18;
            letter-spacing: -0.045em;
        }

        .th-auth-card-header p {
            margin: 0;
            max-width: 430px;
            color: rgba(248, 250, 252, 0.86);
            font-size: 1rem;
            line-height: 1.75;
        }

        .th-auth-card-body {
            padding: 30px 34px 32px;
            background:
                linear-gradient(180deg, rgba(17, 24, 39, 0.98) 0%, rgba(15, 23, 42, 0.98) 100%);
        }

        .th-auth-alert,
        .th-auth-errors {
            margin-bottom: 22px;
            padding: 16px 18px;
            border-radius: var(--th-radius-md);
            border: 1px solid rgba(139, 92, 246, 0.28);
            background: rgba(124, 58, 237, 0.12);
            color: var(--th-text-muted);
            font-size: 0.95rem;
            line-height: 1.7;
        }

        .th-auth-errors {
            border-color: rgba(248, 113, 113, 0.32);
            background: rgba(127, 29, 29, 0.28);
            color: #fecaca;
        }

        .th-auth-errors ul {
            margin: 0;
            padding-left: 18px;
        }

        .th-auth-form-group + .th-auth-form-group {
            margin-top: 18px;
        }

        .th-auth-label {
            display: inline-block;
            margin-bottom: 9px;
            color: var(--th-text);
            font-size: 0.95rem;
            font-weight: 700;
            letter-spacing: -0.015em;
        }

        .th-auth-input {
            width: 100%;
            height: 56px;
            padding: 0 18px;
            border: 1px solid var(--th-border);
            border-radius: 16px;
            outline: none;
            background: rgba(15, 23, 42, 0.78);
            color: var(--th-text);
            font-size: 1rem;
            transition: border-color 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
        }

        .th-auth-input::placeholder {
            color: rgba(148, 163, 184, 0.72);
        }

        .th-auth-input:focus {
            border-color: rgba(96, 165, 250, 0.72);
            background: rgba(15, 23, 42, 0.96);
            box-shadow: 0 0 0 4px rgba(96, 165, 250, 0.14);
        }

        .btn_oauth_login {
            width: 100%;
            margin-top: 24px;
            height: 58px;
            border: 0;
            border-radius: 18px;
            cursor: pointer;
            color: #ffffff;
            font-size: 1rem;
            font-weight: 800;
            letter-spacing: -0.015em;
            background: linear-gradient(135deg, #1d4ed8 0%, #38bdf8 35%, #7c3aed 100%);
            box-shadow: 0 18px 36px rgba(59, 130, 246, 0.28);
            transition: transform 0.18s ease, box-shadow 0.18s ease, filter 0.18s ease;
        }

        .btn_oauth_login:hover {
            transform: translateY(-1px);
            box-shadow: 0 22px 40px rgba(76, 101, 255, 0.32);
            filter: saturate(1.08);
        }

        .btn_oauth_login:active {
            transform: translateY(0);
        }

        .th-auth-actions {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-top: 24px;
        }

        .th-auth-link-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            min-height: 54px;
            padding: 0 18px;
            border: 1px solid rgba(148, 163, 184, 0.28);
            border-radius: 18px;
            color: var(--th-text);
            font-size: 0.98rem;
            font-weight: 700;
            letter-spacing: -0.015em;
            text-decoration: none;
            background: rgba(15, 23, 42, 0.7);
            transition: transform 0.18s ease, border-color 0.18s ease, background 0.18s ease;
        }

        .th-auth-link-button:hover {
            transform: translateY(-1px);
            border-color: rgba(96, 165, 250, 0.5);
            background: rgba(30, 41, 59, 0.92);
        }

        .th-auth-help {
            margin: 18px 0 0;
            color: var(--th-text-faded);
            font-size: 0.9rem;
            line-height: 1.7;
            text-align: center;
        }

        .th-auth-footer {
            padding: 0 20px 24px;
            color: var(--th-text-faded);
            font-size: 0.86rem;
            text-align: center;
        }

        @media (max-width: 640px) {
            .th-auth-navbar {
                min-height: 68px;
                padding: 14px 16px;
            }

            .th-auth-logo-mark {
                width: 40px;
                height: 40px;
                border-radius: 13px;
                font-size: 1rem;
            }

            .th-auth-brand-title {
                font-size: 1.2rem;
            }

            .th-auth-brand-subtitle {
                font-size: 0.74rem;
            }

            .th-auth-main {
                padding: 24px 14px 24px;
                align-items: flex-start;
            }

            .th-auth-card-header,
            .th-auth-card-body {
                padding-left: 22px;
                padding-right: 22px;
            }

            .th-auth-card-header {
                padding-top: 28px;
                padding-bottom: 24px;
            }

            .th-auth-card-body {
                padding-top: 24px;
                padding-bottom: 26px;
            }

            .th-auth-input,
            .btn_oauth_login {
                height: 54px;
            }
        }
    </style>
</head>
<body>
    <div class="th-auth-page">
        <header class="th-auth-navbar">
            <div class="th-auth-brand" aria-label="티에이치스터디 인증">
                <div class="th-auth-logo-mark">TH</div>
                <div class="th-auth-brand-text">
                    <div class="th-auth-brand-title">티에이치스터디</div>
                    <div class="th-auth-brand-subtitle">ChatGPT MCP Authentication</div>
                </div>
            </div>
        </header>

        <main class="th-auth-main">
            <section class="th-auth-card" aria-labelledby="th-auth-title">
                <div class="th-auth-card-header">
                    <div class="th-auth-badge">ChatGPT MCP 연결</div>
                    <h1 id="th-auth-title">티에이치스터디 데이터를 안전하게 연결합니다</h1>
                    <p>로그인 후 ChatGPT가 티에이치스터디의 블로그, 포트폴리오, 학습 기록을 필요한 범위 안에서 조회할 수 있습니다.</p>
                </div>

                <div class="th-auth-card-body">
                    <div class="th-auth-alert">
                        이 화면은 티에이치스터디 계정 인증을 위한 로그인 화면입니다. 로그인한 사용자 권한에 따라 접근 가능한 데이터만 연결됩니다.
                    </div>

                    @if ($errors->any())
                        <div class="th-auth-errors">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form id="form-oauth-login" method="post" action="{{ route('mcp.oauth.login') }}">
                        @csrf

                        <input type="hidden" name="client_id" value="{{ $client_id }}">
                        <input type="hidden" name="redirect_uri" value="{{ $redirect_uri }}">
                        <input type="hidden" name="state" value="{{ $state }}">
                        <input type="hidden" name="code_challenge" value="{{ $code_challenge }}">
                        <input type="hidden" name="code_challenge_method" value="{{ $code_challenge_method }}">

                        <div class="th-auth-form-group">
                            <label class="th-auth-label" for="email">이메일</label>
                            <input
                                type="email"
                                id="email"
                                name="email"
                                class="th-auth-input"
                                value="{{ old('email') }}"
                                placeholder="example@email.com"
                                required
                                autofocus
                            >
                        </div>

                        <div class="th-auth-form-group">
                            <label class="th-auth-label" for="password">비밀번호</label>
                            <input
                                type="password"
                                id="password"
                                name="password"
                                class="th-auth-input"
                                placeholder="비밀번호를 입력해주세요."
                                required
                            >
                        </div>

                        <div class="th-auth-actions">
                            <button type="button" id="btn_oauth_login" class="btn_oauth_login">
                                계정 연결하기
                            </button>

                            <a href="{{ route('register.form') }}" class="th-auth-link-button">
                                회원가입
                            </a>
                        </div>
                    </form>

                    <p class="th-auth-help">
                        연결 후 발급된 인증 토큰은 ChatGPT의 티에이치스터디 데이터 요청에만 사용됩니다.
                    </p>
                </div>
            </section>
        </main>

        <footer class="th-auth-footer">
            © 티에이치스터디 OAuth Login
        </footer>
    </div>

    <script src="{{ asset('js/jquery-3.7.1.min.js') }}"></script>
    <script>
        $(function () {
            $('#form-oauth-login').on('keydown', 'input', function (event) {
                if (event.key === 'Enter') {
                    event.preventDefault();
                }
            });

            $('#form-oauth-login').on('submit', function (event) {
                event.preventDefault();
            });

            $('#btn_oauth_login').on('click', function () {
                const email = $.trim($('#email').val());
                const password = $.trim($('#password').val());

                if (!email) {
                    alert('이메일을 입력해주세요.');
                    $('#email').focus();
                    return;
                }

                if (!password) {
                    alert('비밀번호를 입력해주세요.');
                    $('#password').focus();
                    return;
                }

                $('#form-oauth-login')[0].submit();
            });
        });
    </script>
</body>
</html>
