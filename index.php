<?php
// filepath: c:\xampp\htdocs\GESTION_COMMANDE_FACTURATION\index.php
ob_start(); // Démarre la mise en mémoire tampon
session_start(); // Démarre la session
require_once './database.php'; // Assurez-vous que le chemin est correct

?>

<!doctype html>
<html lang="fr">
<head>
    <title>Gestion de Commandes</title>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <!-- Bootstrap CSS v5.2.1 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous" />
</head>
<body>
    <?php require_once './shared/navebar.php'; ?>

    <?php
    // Vérification de l'action dans l'URL
    if (isset($_GET['action'])) {
        // Déconnexion
        if ($_GET['action'] == "deconnecter") {
            session_destroy(); // Détruit la session
            header('Location: index.php'); // Redirige vers la page d'accueil
            exit();
        }

        // Si l'utilisateur est une secrétaire
        if (isset($_SESSION['role']) && $_SESSION['role'] === 'secretaire') {
            if ($_GET['action'] == 'gestionCommande') {
                require_once './pages/secretaire/gestionCommande.php';
            } elseif ($_GET['action'] == 'validationCommande') {
                require_once './pages/secretaire/validationCommande.php';
            }
        }
        // Si l'utilisateur est un administrateur
        elseif (isset($_SESSION['is_admin']) && $_SESSION['is_admin']) {
            // Actions liées aux clients
            if ($_GET['action'] == 'listClient') {
                require_once './pages/clients/list.php';
            } elseif ($_GET['action'] == 'addClient') {
                require_once './pages/clients/add.php';
            } elseif ($_GET['action'] == 'editClient') {
                require_once './pages/clients/edit.php';
            } elseif ($_GET['action'] == 'deleteClient') {
                require_once './pages/clients/delete.php';
            }
            
            // Actions liées aux produits
            elseif ($_GET['action'] == 'listProduit') {
                require_once './pages/produit/list.php';
            } elseif ($_GET['action'] == 'addProduit') {
                require_once './pages/produit/add.php';
            } elseif ($_GET['action'] == 'editProduit') {
                if (isset($_GET['id'])) {
                    $id = $_GET['id'];
                    $sql = "SELECT * FROM produit WHERE id=?";
                    $stmt = mysqli_prepare($connexion, $sql);
                    mysqli_stmt_bind_param($stmt, 'i', $id);
                    mysqli_stmt_execute($stmt);
                    $result = mysqli_stmt_get_result($stmt);
                    $produit = mysqli_fetch_assoc($result);
                    require_once './pages/produit/edit.php';
                }
            } elseif ($_GET['action'] == 'updateProduit') {
                if (isset($_POST['id'], $_POST['libelle'], $_POST['qte_stock'], $_POST['qte_seuil'], $_POST['prix'])) {
                    $id = $_POST['id'];
                    $libelle = $_POST['libelle'];
                    $qte_stock = $_POST['qte_stock'];
                    $qte_seuil = $_POST['qte_seuil'];
                    $prix = $_POST['prix'];
                    $sql = "UPDATE produit SET libelle=?, quantite_stock=?, quantite_seuil=?, prix=? WHERE id=?";
                    $stmt = mysqli_prepare($connexion, $sql);
                    mysqli_stmt_bind_param($stmt, 'siiis', $libelle, $qte_stock, $qte_seuil, $prix, $id);
                    $result = mysqli_stmt_execute($stmt);
                    echo $result ? "Produit modifié avec succès" : "Erreur de modification";
                }
                require_once './pages/produit/list.php';
            } elseif ($_GET['action'] == 'deleteProduit') {
                if (isset($_GET['id'])) {
                    $id = $_GET['id'];
                    $sql = "DELETE FROM produit WHERE id=?";
                    $stmt = mysqli_prepare($connexion, $sql);
                    mysqli_stmt_bind_param($stmt, 'i', $id);
                    $result = mysqli_stmt_execute($stmt);
                    echo $result ? "Produit supprimé avec succès" : "Erreur de suppression";
                }
                require_once './pages/produit/list.php';
            } elseif ($_GET['action'] == 'changerPassword') {
                require_once './pages/auth/changerPassword.php';
            }
        }
        // Si l'utilisateur est un client
        elseif (isset($_SESSION['user'])) {
            // Actions liées aux produits pour les clients
            if ($_GET['action'] == 'detailsProduit') {
                require_once './pages/produit/details.php';
            } elseif ($_GET['action'] == 'commande') {
                require_once './pages/commande/commande.php';
            } elseif ($_GET['action'] == 'recapulatif') {
                require_once './pages/recapulatif/recapulatif.php';
            } elseif ($_GET['action'] == 'accueil') {
                require_once 'accueil.php';
            } elseif ($_GET['action'] == 'search') {
                $query = $_GET['query'];
                // Ajoutez ici le code pour gérer la recherche
            } else {
                echo "<div class='alert alert-danger'>Accès non autorisé. Veuillez vous connecter.</div>";
                require_once './pages/auth/login.php';
            }
        } else {
            // Si l'utilisateur n'est pas connecté
            echo "<div class='alert alert-danger'>Accès non autorisé. Veuillez vous connecter.</div>";
            require_once './pages/auth/login.php';
        }
    } else {
        // Page par défaut : Page de connexion
        require_once './pages/auth/login.php';
    }
    ?>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQ+zqX6gSbd85u4mG4QzX+" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+" crossorigin="anonymous"></script>
</body>
</html>