<?php
require 'vendor/autoload.php'; // Vérifie que le chemin est correct

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);

try {
    // Configuration du serveur SMTP (Mailtrap)
    $mail->isSMTP();
    $mail->Host = 'sandbox.smtp.mailtrap.io'; 
    $mail->SMTPAuth = true;
    $mail->Username = '22cad0f44d19f1'; // Ton Mailtrap Username
    $mail->Password = '555337fe7e4591'; // Ton Mailtrap Password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; 
    $mail->Port = 2525;

    // Destinataire
    $mail->setFrom('mamethiorodieng3@gmail.com', 'Votre Entreprise');
    $mail->addAddress('1dbae734e6-c9553e@inbox.mailtrap.io'); // Mets ton email Mailtrap ici

    // Contenu du mail
    $mail->isHTML(true);
    $mail->Subject = 'Test Mailtrap';
    $mail->Body = '<h3>Félicitations 🎉 !</h3><p>Votre test d\'envoi d\'email via Mailtrap fonctionne.</p>';

    if ($mail->send()) {
        echo "✅ Email envoyé avec succès ! Vérifie ta boîte Mailtrap.";
    } else {
        echo "❌ Échec de l'envoi : " . $mail->ErrorInfo;
    }
} catch (Exception $e) {
    echo "❌ Erreur : " . $mail->ErrorInfo;
}
?>
