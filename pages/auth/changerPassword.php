<?php
// filepath: /c:/xampp/htdocs/GESTION_COMMANDE_FACTURATION/pages/auth/changerPassword.php

session_start();
require_once __DIR__ . '/../../database.php';
require_once __DIR__ . '/../../vendor/autoload.php'; // PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

$message = "";
$uploaded_image_path = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_password = mysqli_real_escape_string($connexion, trim($_POST['new_password']));
    $confirm_password = mysqli_real_escape_string($connexion, trim($_POST['confirm_password']));

    if (empty($new_password) || empty($confirm_password)) {
        $message = "<div class='alert alert-danger'>Veuillez remplir tous les champs.</div>";
    } elseif ($new_password !== $confirm_password) {
        $message = "<div class='alert alert-danger'>Les mots de passe ne correspondent pas.</div>";
    } else {
        // Gestion du téléchargement de l'image
        if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
            $upload_dir = __DIR__ . '/../../uploads/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            // Vérification du type de fichier
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            if (!in_array($_FILES['image']['type'], $allowed_types)) {
                $message = "<div class='alert alert-danger'>Type de fichier non autorisé. Seules les images JPEG, PNG et GIF sont autorisées.</div>";
            } else {
                // Vérification de la taille du fichier (max 2MB)
                if ($_FILES['image']['size'] > 2 * 1024 * 1024) {
                    $message = "<div class='alert alert-danger'>La taille du fichier ne doit pas dépasser 2MB.</div>";
                } else {
                    $uploaded_image_path = $upload_dir . basename($_FILES['image']['name']);
                    if (move_uploaded_file($_FILES['image']['tmp_name'], $uploaded_image_path)) {
                        $uploaded_image_path = 'uploads/' . basename($_FILES['image']['name']);
                    } else {
                        $message = "<div class='alert alert-danger'>Erreur lors du téléchargement de l'image.</div>";
                    }
                }
            }
        }

        $user_id = $_SESSION['user']['id'];
        $user_email = $_SESSION['user']['email'];
        $user_nom = $_SESSION['user']['nom'];

        // Mise à jour du mot de passe sans hachage
        $sql = "UPDATE client SET password = ?, etat = 1 WHERE id = ?";
        $stmt = mysqli_prepare($connexion, $sql);
        mysqli_stmt_bind_param($stmt, "si", $new_password, $user_id);

        if (mysqli_stmt_execute($stmt)) {
            // Envoi de l'email de confirmation
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'sandbox.smtp.mailtrap.io';
                $mail->SMTPAuth = true;
                $mail->Username = '22cad0f44d19f1';
                $mail->Password = 'your_mailtrap_password';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 2525;

                $mail->setFrom('no-reply@votreentreprise.com', 'Votre Entreprise');
                $mail->addAddress($user_email);

                $mail->isHTML(true);
                $mail->Subject = 'Confirmation de changement de mot de passe';
                $mail->Body = "
                    <p>Bonjour <b>$user_nom</b>,</p>
                    <p>Votre mot de passe a été changé avec succès.</p>
                    <p>Si ce n'était pas vous, contactez notre support immédiatement.</p>
                    <p>Cordialement,<br><b>Votre Entreprise</b></p>";

                $mail->send();
            } catch (Exception $e) {
                $message .= "<div class='alert alert-warning'>Email non envoyé : {$mail->ErrorInfo}</div>";
            }

            // Redirection après succès
            $message = "<div class='alert alert-success'>Mot de passe changé avec succès. Un email de confirmation a été envoyé.</div>";
            header('Location: ../../index.php?action=listProduit');
            exit();
        } else {
            $message = "<div class='alert alert-danger'>Erreur SQL : " . mysqli_stmt_error($stmt) . "</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Changer le mot de passe</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container col-md-6">
        <div class="card">
            <div class="card-header text-center">
                <h3>Changer le mot de passe</h3>
                <!-- Image ajoutée ici -->
                <?php if (!empty($uploaded_image_path)): ?>
                    <img src="<?php echo htmlspecialchars($uploaded_image_path); ?>" alt="Image de confirmation" class="img-fluid mb-3" style="max-width: 100px;">
                <?php endif; ?>
            </div>
            <div class="card-body">
                <?php echo $message; ?>
                <form action="#" method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="new_password" class="form-label">Nouveau mot de passe</label>
                        <input type="password" name="new_password" id="new_password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Confirmer le mot de passe</label>
                        <input type="password" name="confirm_password" id="confirm_password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="image" class="form-label">Télécharger une image</label>
                        <input type="file" name="image" id="image" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Changer</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>