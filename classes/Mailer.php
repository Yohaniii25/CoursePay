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
    private static $smtpUsername = 'no-reply@gjrti.gov.lk'; 
    private static $smtpPassword = ''; // Put your webmail email password here
    private static $smtpSecure   = PHPMailer::ENCRYPTION_SMTPS; // ENCRYPTION_SMTPS (465) or ENCRYPTION_STARTTLS (587)
    private static $smtpPort     = 465; 

    // Default Sender Details
    private static $fromEmail    = 'no-reply@gjrti.gov.lk';
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
}
