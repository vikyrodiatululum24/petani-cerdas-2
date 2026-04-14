<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
</head>
<body style="margin:0; padding:0; background-color:#f4f8f3; font-family:Arial, Helvetica, sans-serif; color:#1f2937;">
    <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="background-color:#f4f8f3; padding:24px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="max-width:620px; background:#ffffff; border:1px solid #e5e7eb; border-radius:14px; overflow:hidden;">
                    <tr>
                        <td style="background:linear-gradient(120deg,#1b5e20,#2e7d32,#66bb6a); padding:24px; color:#ffffff;">
                            <p style="margin:0; font-size:12px; letter-spacing:1.4px; text-transform:uppercase; opacity:0.95;">Petani Cerdas</p>
                            <h1 style="margin:8px 0 0 0; font-size:24px; line-height:1.25;">Reset Password Akun Anda</h1>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:24px;">
                            <p style="margin:0 0 12px 0; font-size:15px; line-height:1.7;">Halo {{ $name }},</p>
                            <p style="margin:0 0 12px 0; font-size:15px; line-height:1.7;">
                                Kami menerima permintaan untuk mereset password akun <strong>Petani Cerdas</strong> Anda.
                            </p>
                            <p style="margin:0 0 12px 0; font-size:15px; line-height:1.7;">
                                Klik tombol berikut untuk membuat password baru. Tautan ini berlaku selama <strong>{{ $expireMinutes }} menit</strong>.
                            </p>

                            <table role="presentation" cellpadding="0" cellspacing="0" border="0" style="margin:20px 0;">
                                <tr>
                                    <td align="center" style="border-radius:10px; background-color:#2e7d32;">
                                        <a href="{{ $resetUrl }}" style="display:inline-block; padding:12px 20px; color:#ffffff; text-decoration:none; font-size:14px; font-weight:700;">
                                            Reset Password Sekarang
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin:0 0 10px 0; font-size:13px; line-height:1.7; color:#4b5563;">
                                Jika tombol di atas tidak dapat diklik, salin dan buka tautan berikut di browser Anda:
                            </p>
                            <p style="margin:0 0 12px 0; font-size:12px; line-height:1.6; word-break:break-all; color:#1f2937;">
                                <a href="{{ $resetUrl }}" style="color:#1b5e20;">{{ $resetUrl }}</a>
                            </p>

                            <p style="margin:0; font-size:13px; line-height:1.7; color:#6b7280;">
                                Jika Anda tidak meminta reset password, abaikan email ini dan akun Anda tetap aman.
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:16px 24px; border-top:1px solid #e5e7eb; background:#f9fafb;">
                            <p style="margin:0; font-size:12px; color:#6b7280;">
                                Salam hangat,<br>
                                Tim Petani Cerdas
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
