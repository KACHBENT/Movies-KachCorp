<?php

namespace App\Libraries;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Mailer
{
    public function send(string $toEmail, string $toName, string $subject, string $htmlBody, ?string $replyToEmail = null, ?string $replyToName = null): array
    {
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = getenv('SMTP_HOST');
            $mail->Port       = (int) getenv('SMTP_PORT');

            $secure = getenv('SMTP_SECURE') ?: 'tls';
            $mail->SMTPSecure = ($secure === 'ssl')
                ? PHPMailer::ENCRYPTION_SMTPS
                : PHPMailer::ENCRYPTION_STARTTLS;

            $mail->SMTPAuth   = true;
            $mail->Username   = getenv('SMTP_USER');
            $mail->Password   = getenv('SMTP_PASS');

            $fromEmail = getenv('SMTP_FROM_EMAIL') ?: getenv('SMTP_USER');
            $fromName  = getenv('SMTP_FROM_NAME') ?: 'Consultoria';

            $mail->setFrom($fromEmail, $fromName);

            if ($replyToEmail) {
                $mail->addReplyTo($replyToEmail, $replyToName ?: $replyToEmail);
            }

            $mail->addAddress($toEmail, $toName ?: $toEmail);

            $mail->isHTML(true);
            $mail->CharSet = 'UTF-8';
            $mail->Subject = $subject;
            $mail->Body    = $htmlBody;

            $mail->send();

            return ['ok' => true, 'error' => null];
        } catch (Exception $e) {
            return ['ok' => false, 'error' => $mail->ErrorInfo ?: $e->getMessage()];
        }
    }
}
