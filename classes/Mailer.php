<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/autoload.php';

class Mailer {
    /* 
    ====================================================================
    WEBMAIL (cPanel) SMTP CONFIGURATION EXAMPLE:
    --------------------------------------------------------------------
    private static $smtpHost     = 'mail.sltdigital.site'; // or 'mail.yourdomain.com' / 'localhost'
    private static $smtpAuth     = true;
    private static $smtpUsername = 'no-reply@sltdigital.site'; // Full Webmail Address
    private static $smtpPassword = 'your_webmail_password';   // Webmail Password
    private static $smtpSecure   = PHPMailer::ENCRYPTION_SMTPS; // PHPMailer::ENCRYPTION_SMTPS (for 465) or PHPMailer::ENCRYPTION_STARTTLS (for 587)
    private static $smtpPort     = 465; // 465 (SSL) or 587 (TLS)
    ====================================================================
    */

    // Set your Webmail or SMTP Credentials below:
    private static $smtpHost     = 'mail.gjrti.gov.lk'; 
    private static $smtpAuth     = true;
    private static $smtpUsername = 'test@gjrti.gov.lk'; 
    private static $smtpPassword = '?hD7^2,DIC1}gV5B'; // Put your webmail email password here
    private static $smtpSecure   = PHPMailer::ENCRYPTION_SMTPS; // ENCRYPTION_SMTPS (465) or ENCRYPTION_STARTTLS (587)
    private static $smtpPort     = 465; 

    // Default Sender Details
    private static $fromEmail    = 'test@gjrti.gov.lk';
    private static $fromName     = 'GJRTI CoursePay';

    /**
     * Send email using PHPMailer with Webmail/SMTP credentials (with native mail() fallback)
     * 
     * @param string $to Recipient email address
     * @param string $subject Email subject
     * @param string $body Email content (HTML or Plain text)
     * @param bool $isHtml Whether the content is HTML
     * @param string|null $replyTo Reply-to email address
     * @return bool True if email sent successfully, false otherwise
     */
    public static function send($to, $subject, $body, $isHtml = false, $replyTo = null) {
        if (empty($to)) {
            self::logError("Empty recipient email address provided for subject: '$subject'");
            return false;
        }

        $mail = new PHPMailer(true);

        try {
            // If SMTP Credentials are set, send via SMTP
            if (!empty(self::$smtpHost) && !empty(self::$smtpUsername) && !empty(self::$smtpPassword)) {
                $mail->isSMTP();
                $mail->Host       = self::$smtpHost;
                $mail->SMTPAuth   = self::$smtpAuth;
                $mail->Username   = self::$smtpUsername;
                $mail->Password   = self::$smtpPassword;
                $mail->SMTPSecure = self::$smtpSecure;
                $mail->Port       = self::$smtpPort;
                $mail->SMTPOptions = [
                    'ssl' => [
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                        'allow_self_signed' => true
                    ]
                ];
            } else {
                // Otherwise use server mail() transport
                $mail->isMail();
            }

            // Sender and Recipient
            $mail->setFrom(self::$fromEmail, self::$fromName);
            $mail->addAddress($to);

            if (!empty($replyTo)) {
                $mail->addReplyTo($replyTo);
            }

            // Message Content
            $mail->isHTML($isHtml);
            $mail->Subject = $subject;
            $mail->Body    = $body;

            if (!$isHtml) {
                $mail->AltBody = strip_tags($body);
            }

            $mail->send();
            return true;
        } catch (Exception $e) {
            $errorMsg = "PHPMailer error sending to {$to}: {$mail->ErrorInfo} (Exception: {$e->getMessage()})";
            self::logError($errorMsg);

            // Fallback to PHP native mail()
            $headers = "From: " . self::$fromName . " <" . self::$fromEmail . ">\r\n";
            if ($replyTo) {
                $headers .= "Reply-To: " . $replyTo . "\r\n";
            }
            if ($isHtml) {
                $headers .= "MIME-Version: 1.0\r\nContent-Type: text/html; charset=UTF-8\r\n";
            } else {
                $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
            }

            $mailResult = @mail($to, $subject, $body, $headers);
            if (!$mailResult) {
                self::logError("Native mail() fallback also failed for recipient {$to}");
            }
            return $mailResult;
        }
    }

    /**
     * Log delivery errors into logs/mail_errors.log
     */
    private static function logError($message) {
        $logDir = __DIR__ . '/../logs';
        if (!is_dir($logDir)) {
            @mkdir($logDir, 0777, true);
        }
        $logFile = $logDir . '/mail_errors.log';
        $entry = date('[Y-m-d H:i:s]') . " " . $message . "\n";
        @file_put_contents($logFile, $entry, FILE_APPEND);
    }
    /**
     * Generate an attractive, responsive HTML email template
     */
    public static function buildTemplate($title, $badgeText, $badgeColor, $greeting, $messageHtml, $details = [], $buttonText = '', $buttonUrl = '', $footerNote = '') {
        $badgeBg = '#e0e7ff';
        $badgeFg = '#3730a3';
        if ($badgeColor === 'green') {
            $badgeBg = '#dcfce7'; $badgeFg = '#166534';
        } elseif ($badgeColor === 'amber' || $badgeColor === 'orange') {
            $badgeBg = '#fef3c7'; $badgeFg = '#92400e';
        } elseif ($badgeColor === 'red') {
            $badgeBg = '#fee2e2'; $badgeFg = '#991b1b';
        }

        $rowsHtml = '';
        if (!empty($details)) {
            $rowsHtml .= '<table style="width:100%; border-collapse:collapse; margin:20px 0; background:#f9fafb; border-radius:8px; overflow:hidden; border:1px solid #e5e7eb;">';
            foreach ($details as $label => $value) {
                if ($value === null || $value === '') continue;
                $rowsHtml .= '<tr style="border-bottom:1px solid #edf0f3;">';
                $rowsHtml .= '<td style="padding:12px 16px; font-weight:600; color:#4b5563; font-size:14px; width:40%;">' . htmlspecialchars($label) . '</td>';
                $rowsHtml .= '<td style="padding:12px 16px; color:#111827; font-size:14px; font-weight:500;">' . $value . '</td>';
                $rowsHtml .= '</tr>';
            }
            $rowsHtml .= '</table>';
        }

        $buttonHtml = '';
        if (!empty($buttonText) && !empty($buttonUrl)) {
            $buttonHtml = '
            <div style="text-align:center; margin:28px 0 16px;">
                <a href="' . htmlspecialchars($buttonUrl) . '" style="display:inline-block; background:linear-gradient(135deg, #25116F 0%, #4338ca 100%); color:#ffffff; font-size:15px; font-weight:700; text-decoration:none; padding:14px 32px; border-radius:8px; box-shadow:0 4px 12px rgba(37, 17, 111, 0.25); letter-spacing:0.3px;">
                    ' . htmlspecialchars($buttonText) . ' &rarr;
                </a>
            </div>';
        }

        $footerNoteHtml = '';
        if (!empty($footerNote)) {
            $footerNoteHtml = '
            <div style="background:#f0fdf4; border-left:4px solid #22c55e; padding:12px 16px; border-radius:6px; margin:20px 0; font-size:13px; color:#166534; line-height:1.5;">
                ' . $footerNote . '
            </div>';
        }

        $badgeHtml = '';
        if (!empty($badgeText)) {
            $badgeHtml = '<span style="display:inline-block; background:' . $badgeBg . '; color:' . $badgeFg . '; font-size:12px; font-weight:700; padding:4px 12px; border-radius:9999px; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:12px;">' . htmlspecialchars($badgeText) . '</span><br>';
        }

        $year = date('Y');
        return "<!DOCTYPE html>
<html>
<head>
<meta charset=\"UTF-8\">
<meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
<title>" . htmlspecialchars($title) . "</title>
</head>
<body style=\"margin:0; padding:0; background-color:#f1f5f9; font-family:-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; -webkit-font-smoothing:antialiased;\">
<table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\" style=\"background-color:#f1f5f9; padding:30px 15px;\">
    <tr>
        <td align=\"center\">
            <table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\" style=\"max-width:600px; background:#ffffff; border-radius:16px; overflow:hidden; box-shadow:0 10px 25px rgba(0,0,0,0.06); border:1px solid #e2e8f0;\">
                
                <!-- HEADER BANNER -->
                <tr>
                    <td style=\"background:linear-gradient(135deg, #1b0d54 0%, #2e1065 50%, #4338ca 100%); padding:28px 24px; text-align:center;\">
                        <img src=\"https://www.gjrti.gov.lk/wp-content/uploads/2025/06/GJRT-1.png\" alt=\"GJRTI Logo\" style=\"height:65px; width:auto; margin-bottom:10px; display:inline-block; background:#ffffff; padding:6px 12px; border-radius:8px;\">
                        <h1 style=\"color:#ffffff; margin:6px 0 2px; font-size:20px; font-weight:700; letter-spacing:0.3px;\">Gem & Jewellery Research and Training Institute</h1>
                        <p style=\"color:#e0e7ff; margin:0; font-size:12px; font-weight:500; text-transform:uppercase; letter-spacing:1px;\">Official Notification</p>
                    </td>
                </tr>

                <!-- MAIN CARD BODY -->
                <tr>
                    <td style=\"padding:32px 28px;\">
                        {$badgeHtml}
                        <h2 style=\"color:#0f172a; margin:0 0 16px; font-size:21px; font-weight:700;\">" . htmlspecialchars($title) . "</h2>
                        
                        <p style=\"color:#334155; font-size:15px; line-height:1.6; margin:0 0 16px;\">
                            <strong>" . htmlspecialchars($greeting) . "</strong>
                        </p>
                        
                        <div style=\"color:#475569; font-size:14px; line-height:1.6; margin-bottom:18px;\">
                            {$messageHtml}
                        </div>

                        {$rowsHtml}
                        {$buttonHtml}
                        {$footerNoteHtml}

                        <p style=\"color:#64748b; font-size:13px; line-height:1.6; margin-top:28px; border-top:1px solid #f1f5f9; padding-top:16px;\">
                            Warm regards,<br>
                            <strong style=\"color:#1e293b;\">GJRTI Admissions Division</strong><br>
                            Gem and Jewellery Research and Training Institute
                        </p>
                    </td>
                </tr>

                <!-- FOOTER -->
                <tr>
                    <td style=\"background:#f8fafc; border-top:1px solid #e2e8f0; padding:20px 24px; text-align:center; font-size:12px; color:#64748b; line-height:1.6;\">
                        <p style=\"margin:0 0 6px;\">Questions? Contact us at <a href=\"mailto:info@gjrti.gov.lk\" style=\"color:#4338ca; text-decoration:none; font-weight:600;\">info@gjrti.gov.lk</a></p>
                        <p style=\"margin:0; font-size:11px; color:#94a3b8;\">&copy; {$year} Gem and Jewellery Research and Training Institute. All rights reserved.</p>
                    </td>
                </tr>

            </table>
        </td>
    </tr>
</table>
</body>
</html>";
    }
}