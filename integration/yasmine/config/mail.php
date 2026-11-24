<?php
/**
 * config/mail.php
 * Envoi d'emails. Les vrais identifiants SMTP vont dans config/mail.local.php (non versionné sur GitHub).
 */
if (is_file(__DIR__ . '/mail.local.php')) {
    require __DIR__ . '/mail.local.php';
}
defined('APP_URL')        or define('APP_URL', rtrim(amirConfig()['base_url'],'/'));
defined('MAIL_DEBUG')     or define('MAIL_DEBUG', true);   // true = écrit le mail dans storage/mails.log au lieu de l'envoyer
defined('MAIL_HOST')      or define('MAIL_HOST', 'smtp.gmail.com');
defined('MAIL_PORT')      or define('MAIL_PORT', 587);
defined('MAIL_USER')      or define('MAIL_USER', '');
defined('MAIL_PASS')      or define('MAIL_PASS', '');
defined('MAIL_FROM')      or define('MAIL_FROM', 'no-reply@aquaflow.local');
defined('MAIL_FROM_NAME') or define('MAIL_FROM_NAME', 'AquaFlow');

class Mailer
{
    public static function send(string $to, string $subject, string $html): bool
    {
        if (MAIL_DEBUG) {
            $dir = __DIR__ . '/../storage';
            if (!is_dir($dir)) {
                mkdir($dir, 0775, true);
            }
            $entry = "=== " . date('Y-m-d H:i:s') . " ===\nTo: $to\nSubject: $subject\n$html\n\n";
            return file_put_contents($dir . '/mails.log', $entry, FILE_APPEND) !== false;
        }

        // Production : PHPMailer (soit via Composer, soit copié dans lib/PHPMailer/src)
        if (is_file(__DIR__ . '/../../vendor/autoload.php')) {
            require_once __DIR__ . '/../../vendor/autoload.php';
        } else {
            require_once __DIR__ . '/../lib/PHPMailer/src/Exception.php';
            require_once __DIR__ . '/../lib/PHPMailer/src/PHPMailer.php';
            require_once __DIR__ . '/../lib/PHPMailer/src/SMTP.php';
        }
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = MAIL_HOST;
            $mail->SMTPAuth = true;
            $mail->Username = MAIL_USER;
            $mail->Password = MAIL_PASS;
            $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = MAIL_PORT;
            $mail->CharSet = 'UTF-8';
            $mail->setFrom(MAIL_FROM, MAIL_FROM_NAME);
            $mail->addAddress($to);
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $html;
            $mail->AltBody = strip_tags($html);
            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log('Mail error: ' . $mail->ErrorInfo);
            return false;
        }
    }
}
