<?php
// lib/Mailer.php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/phpmailer/src/PHPMailer.php';
require_once __DIR__ . '/phpmailer/src/SMTP.php';
require_once __DIR__ . '/phpmailer/src/Exception.php';

class Mailer {
    private $cfg;

    public function __construct() {
        $this->cfg = require __DIR__ . '/../config/mail.php';
    }

    private function sendMail($toEmail, $subject, $htmlBody, array $attachments = []) {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = $this->cfg['host'];
            $mail->SMTPAuth = true;
            $mail->Username = $this->cfg['username'];
            $mail->Password = $this->cfg['password'];
            $mail->SMTPSecure = $this->cfg['encryption'];
            $mail->Port = $this->cfg['port'];

            $mail->setFrom($this->cfg['from_email'], $this->cfg['from_name']);
            $mail->addAddress($toEmail);

            foreach ($attachments as $filePath) {
                if (is_file($filePath)) $mail->addAttachment($filePath);
            }

            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $htmlBody;

            $mail->send();
            return true;
        } catch (Exception $e) {
            // Optionally log $e->getMessage()
            return false;
        }
    }

    public function sendToCustomer($to, $subject, $html, array $attachments = []) {
        return $this->sendMail($to, $subject, $html, $attachments);
    }

    public function sendToAdmin($subject, $html, array $attachments = []) {
        return $this->sendMail($this->cfg['admin_notify_email'], $subject, $html, $attachments);
    }
}
