<!DOCTYPE html>
<html lang="ko">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>비밀번호 변경 요청 안내</title>
    </head>
    <body style="margin: 0; padding: 0; background: #f5f7fb; font-family: 'Apple SD Gothic Neo', 'Malgun Gothic', Arial, sans-serif; color: #1f2937;">
        <div style="max-width: 600px; margin: 0 auto; padding: 28px 16px 40px;">
            <div style="background: #ffffff; border: 1px solid #e5e7eb; border-radius: 12px; padding: 28px 24px; box-shadow: 0 6px 20px rgba(15, 23, 42, 0.06);">
                <h1 style="font-size: 20px; margin: 0 0 12px; font-weight: 700;">비밀번호 변경 요청 안내</h1>
                <p style="font-size: 14px; line-height: 1.6; margin: 0 0 12px;">
                    {{ $name }}님, 비밀번호 변경 요청이 정상적으로 접수되었습니다.
                </p>
                <p style="font-size: 14px; line-height: 1.6; margin: 0 0 12px;">
                    보안을 위해 로그인 후 비밀번호 변경 화면에서 새 비밀번호를 설정해 주세요.
                </p>
                <p style="font-size: 14px; line-height: 1.6; margin: 0;">
                    아래 버튼을 눌러 홈페이지로 이동하신 뒤, 로그인 후 비밀번호 변경을 진행해 주세요.
                </p>

                <div style="text-align: center; margin: 20px 0 0;">
                    <a href="{{ $siteUrl }}" target="_blank" rel="noopener" style="display: inline-block; background: #2563eb; color: #ffffff !important; text-decoration: none; padding: 12px 18px; border-radius: 8px; font-weight: 600; font-size: 14px;">
                        홈페이지로 이동
                    </a>
                </div>
            </div>

            <div style="text-align: center; font-size: 12px; color: #6b7280; margin-top: 18px;">
                본 메일은 비밀번호 변경 요청 안내를 위해 발송되었습니다.
            </div>
        </div>
    </body>
</html>
