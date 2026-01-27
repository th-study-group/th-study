<!DOCTYPE html>
<html lang="ko">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>비밀번호 변경 완료 안내</title>
    </head>
    <body style="margin: 0; padding: 0; background: #f5f7fb; font-family: 'Apple SD Gothic Neo', 'Malgun Gothic', Arial, sans-serif; color: #1f2937;">
        <div style="max-width: 600px; margin: 0 auto; padding: 28px 16px 40px;">
            <div style="background: #ffffff; border: 1px solid #e5e7eb; border-radius: 12px; padding: 28px 24px; box-shadow: 0 6px 20px rgba(15, 23, 42, 0.06);">
                <h1 style="font-size: 20px; margin: 0 0 12px; font-weight: 700;">비밀번호 변경 완료 안내</h1>
                <p style="font-size: 14px; line-height: 1.6; margin: 0 0 12px;">
                    {{ $name }}님, 비밀번호 변경이 정상적으로 완료되었습니다.
                </p>
                <p style="font-size: 14px; line-height: 1.6; margin: 0 0 12px;">
                    보안을 위해 로그인하여 변경된 비밀번호로 정상 접속되는지 확인해 주세요.
                </p>
                <p style="font-size: 14px; line-height: 1.6; margin: 0;">
                    본인이 아닌 경우 이 메일은 무시하셔도 됩니다. 의심되는 경우 홈페이지의 문의하기를 통해 알려주세요.
                </p>

                <div style="text-align: center; margin: 20px 0 0;">
                    <a href="{{ $siteUrl }}" target="_blank" rel="noopener" style="display: inline-block; background: #2563eb; color: #ffffff !important; text-decoration: none; padding: 12px 18px; border-radius: 8px; font-weight: 600; font-size: 14px;">
                        로그인 화면으로 이동
                    </a>
                </div>
            </div>

            <div style="text-align: center; font-size: 12px; color: #6b7280; margin-top: 18px;">
                본 메일은 비밀번호 변경 완료 안내를 위해 발송되었습니다.
            </div>
        </div>
    </body>
</html>
