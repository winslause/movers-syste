<?php
function sendSMTPEmail($to, $subject, $message) {
    $smtpHost = 'smtp.gmail.com';
    $smtpPort = 587;
    $smtpUsername = 'wenbusale383@gmail.com';
    $smtpPassword = 'gllr irpd caku blzy';

    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=UTF-8\r\n";
    $headers .= "From: Rheaspark <wenbusale383@gmail.com>\r\n";
    $headers .= "Reply-To: wenbusale383@gmail.com\r\n";

    // Use mail() with SMTP if configured, or try to send directly
    // For Gmail SMTP, need proper setup

    // For now, use mail() and hope it's configured
    return mail($to, $subject, $message, $headers);
}
?>