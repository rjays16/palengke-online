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
              <h1 style="margin:0;font-size:26px;color:#1a1a2e;font-weight:800;letter-spacing:-0.5px;">Welcome to Palengke Online!</h1>
            </td>
          </tr>

          <tr>
            <td align="center" style="padding:8px 48px 24px;">
              <p style="margin:0 0 16px;font-size:15px;color:#64748b;line-height:1.6;text-align:left;">
                @if($recipientName)
                  Hi {{ $recipientName }},
                @else
                  Hi there,
                @endif
              </p>
              <p style="margin:0 0 16px;font-size:15px;color:#64748b;line-height:1.6;text-align:left;">
                Your admin account has been created for <strong style="color:#1a1a2e;">Palengke Online</strong>. You can now log in to the Admin Portal using the credentials provided by your administrator.
              </p>
            </td>
          </tr>

          <tr>
            <td align="center" style="padding:24px 48px 32px;">
              <table cellpadding="0" cellspacing="0" style="margin:0 auto;">
                <tr>
                  <td align="center" style="background:#059669;border-radius:12px;padding:0;">
                    <a href="{{ $loginUrl }}" target="_blank" style="display:inline-block;padding:16px 40px;font-size:16px;font-weight:700;color:#ffffff;text-decoration:none;letter-spacing:0.3px;">
                      Log In to Admin Portal
                    </a>
                  </td>
                </tr>
              </table>
            </td>
          </tr>

          <tr>
            <td align="center" style="padding:0 48px 24px;">
              <p style="margin:0;font-size:13px;color:#94a3b8;line-height:1.6;text-align:left;word-break:break-all;">
                Or copy and paste this link into your browser:<br>
                <span style="color:#059669;">{{ $loginUrl }}</span>
              </p>
            </td>
          </tr>

          <tr>
            <td align="center" style="padding:0 48px;">
              <table width="100%" cellpadding="0" cellspacing="0">
                <tr>
                  <td style="border-bottom:1px solid #e2e8f0;"></td>
                </tr>
              </table>
            </td>
          </tr>

          <tr>
            <td align="center" style="padding:24px 48px 32px;">
              <p style="margin:0 0 12px;font-size:13px;color:#94a3b8;line-height:1.5;text-align:left;">
                If you didn't expect this email, please contact the system administrator.
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
