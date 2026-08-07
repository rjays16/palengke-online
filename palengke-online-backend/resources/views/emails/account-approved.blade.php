<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="color-scheme" content="light">
  <meta name="supported-color-schemes" content="light">
</head>
<body style="margin:0;padding:0;background:#f4f4f4;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;">
  <table width="100%" cellpadding="0" cellspacing="0">
    <tr>
      <td align="center" style="padding:40px 16px;">
        <table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,0.08);max-width:100%;">

          <tr>
            <td align="center" style="padding:48px 48px 24px;">
              <img src="{{ $message->embed(public_path('images/logo.png')) }}" alt="Palengke Online" style="width:160px;height:auto;display:block;margin:0 auto;" width="160">
            </td>
          </tr>

          <tr>
            <td align="center" style="padding:8px 48px 8px;">
              <h1 style="margin:0;font-size:26px;color:#1a1a2e;font-weight:800;letter-spacing:-0.5px;">Account Approved!</h1>
            </td>
          </tr>

          <tr>
            <td align="center" style="padding:8px 48px 16px;">
              <p style="margin:0;font-size:15px;color:#64748b;line-height:1.6;">
                Hi {{ $firstName }},
                <br><br>
                Great news! Your account for <strong style="color:#1a1a2e;">{{ $storeName }}</strong> has been approved!
              </p>
            </td>
          </tr>

          <tr>
            <td align="center" style="padding:8px 48px 24px;">
              <table cellpadding="0" cellspacing="0" style="margin:0 auto;">
                <tr>
                  <td align="center" style="background:#f0fdf4;border-radius:16px;padding:32px 48px;border:2px solid #bbf7d0;">
                    <p style="margin:0 0 8px;font-size:13px;color:#16a34a;font-weight:600;text-transform:uppercase;letter-spacing:1px;">Status: Approved</p>
                    <p style="margin:0;font-size:15px;color:#166534;line-height:1.6;">
                      You can now log in and start selling on Palengke Online!
                    </p>
                  </td>
                </tr>
              </table>
            </td>
          </tr>

          <tr>
            <td align="center" style="padding:0 48px 8px;">
              <p style="margin:0;font-size:14px;color:#64748b;line-height:1.6;text-align:left;">
                <strong style="color:#1a1a2e;">Get Started:</strong><br><br>
                &bull; Log in to your seller account.<br>
                &bull; Complete your store profile.<br>
                &bull; Add your first products.<br>
                &bull; Start receiving orders!
              </p>
            </td>
          </tr>

          <tr>
            <td align="center" style="padding:24px 48px;">
              <table width="100%" cellpadding="0" cellspacing="0">
                <tr>
                  <td style="border-bottom:1px solid #e2e8f0;"></td>
                </tr>
              </table>
            </td>
          </tr>

          <tr>
            <td align="center" style="padding:0 48px 32px;">
              <p style="margin:0 0 12px;font-size:13px;color:#94a3b8;line-height:1.5;">
                Need help? Contact us at support@palengkeonline.com
              </p>
              <p style="margin:0;font-size:12px;color:#cbd5e1;">
                &copy; {{ date('Y') }} Palengke Online. All rights reserved.
              </p>
            </td>
          </tr>

        </table>

        <table width="600" cellpadding="0" cellspacing="0" style="max-width:100%;">
          <tr>
            <td align="center" style="padding:24px 16px 0;">
              <p style="margin:0;font-size:11px;color:#cbd5e1;line-height:1.5;">
                Palengke Online &bull; Your fresh market, delivered!
              </p>
            </td>
          </tr>
        </table>

      </td>
    </tr>
  </table>
</body>
</html>
