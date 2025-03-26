<?php
// filepath: c:\xampp\htdocs\GESTION_COMMANDE_FACTURATION\pages\auth\login.php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../../database.php';

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['connecter'])) {
    extract($_POST);

    $login = mysqli_real_escape_string($connexion, trim($login));
    $password = trim($password);

    if (empty($login) || empty($password)) {
        $message = "<div class='alert alert-danger'>Veuillez remplir tous les champs.</div>";
    } else {
        // Vérification des informations de l'administrateur
        $admin_sql = "SELECT * FROM rs WHERE login = ?";
        $admin_stmt = mysqli_prepare($connexion, $admin_sql);
        if ($admin_stmt) {
            mysqli_stmt_bind_param($admin_stmt, "s", $login);
            mysqli_stmt_execute($admin_stmt);
            $admin_result = mysqli_stmt_get_result($admin_stmt);
            $admin = mysqli_fetch_assoc($admin_result);

            if ($admin && $password === $admin['password']) { // Remplacez par password_verify() si les mots de passe sont hachés
                $_SESSION['user'] = [
                    'id' => $admin['id'],
                    'login' => $admin['login'],
                    'nom' => $admin['nom'],
                    'prenom' => $admin['prenom'],
                    'telephone' => $admin['telephone']
                ];
                $_SESSION['is_admin'] = true;
                $_SESSION['role'] = 'admin'; // Stocker le rôle dans la session
                header('Location: ./index.php?action=listClient'); // Redirection admin
                exit();
            }
        } else {
            $message = "<div class='alert alert-danger'>Erreur de préparation de la requête pour l'administrateur.</div>";
        }

        // Vérification des informations de la secrétaire
        $secretaire_sql = "SELECT * FROM secretaire WHERE login = ?";
        $secretaire_stmt = mysqli_prepare($connexion, $secretaire_sql);
        if ($secretaire_stmt) {
            mysqli_stmt_bind_param($secretaire_stmt, "s", $login);
            mysqli_stmt_execute($secretaire_stmt);
            $secretaire_result = mysqli_stmt_get_result($secretaire_stmt);
            $secretaire = mysqli_fetch_assoc($secretaire_result);

            if ($secretaire && $password === $secretaire['password']) { // Remplacez par password_verify() si les mots de passe sont hachés
                $_SESSION['user'] = [
                    'id' => $secretaire['id'],
                    'login' => $secretaire['login'],
                    'nom' => $secretaire['nom'],
                    'prenom' => $secretaire['prenom']
                ];
                $_SESSION['is_admin'] = false;
                $_SESSION['role'] = 'secretaire'; // Stocker le rôle dans la session
                header('Location: ./index.php?action=statut_commande'); // Redirection secrétaire
                exit();
            }
        } else {
            $message = "<div class='alert alert-danger'>Erreur de préparation de la requête pour la secrétaire.</div>";
        }

        // Vérification des informations des clients
        $client_sql = "SELECT * FROM client WHERE login = ?";
        $client_stmt = mysqli_prepare($connexion, $client_sql);
        if ($client_stmt) {
            mysqli_stmt_bind_param($client_stmt, "s", $login);
            mysqli_stmt_execute($client_stmt);
            $client_result = mysqli_stmt_get_result($client_stmt);
            $client = mysqli_fetch_assoc($client_result);

            if ($client && $password === $client['password']) { // Remplacez par password_verify() si les mots de passe sont hachés
                $_SESSION['user'] = [
                    'id' => $client['id'],
                    'login' => $client['login'],
                    'email' => $client['email'],
                    'nom' => $client['nom'],
                    'prenom' => $client['prenom'],
                    'photo' => $client['photo']
                ];
                $_SESSION['is_admin'] = false;
                $_SESSION['role'] = 'client'; // Stocker le rôle dans la session

                // Vérification si c'est la première connexion
                if ($client['etat'] == 0) {
                    header('Location: ./index.php?action=changerPassword');
                } else {
                    header('Location: ./index.php?action=detailsProduit');
                }
                exit();
            } else {
                $message = "<div class='alert alert-danger'>Login ou mot de passe incorrect.</div>";
            }
        } else {
            $message = "<div class='alert alert-danger'>Erreur de préparation de la requête pour le client.</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container col-md-6">
        <div class="card mt-5">
            <div class="card-header text-center">
                <h3>Veuillez vous authentifier</h3>
            </div>
            <div class="card-body">
                <?php echo $message; ?>
                <form action="#" method="POST">
                    <div class="mb-3">
                        <label for="login" class="form-label">Login</label>
                        <input type="text" name="login" id="login" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Mot de passe</label>
                        <input type="password" name="password" id="password" class="form-control" required>
                    </div>
                    <div class="d-flex justify-content-between">
                        <button type="submit" name="connecter" class="btn btn-primary">Se connecter</button>
                        <button type="reset" class="btn btn-danger">Annuler</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>