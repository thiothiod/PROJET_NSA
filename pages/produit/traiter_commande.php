<?php
// Récupérer les données du panier depuis le formulaire
$cartData = isset($_POST['cart']) ? json_decode($_POST['cart'], true) : [];

if (empty($cartData)) {
    die("Votre panier est vide.");
}

// Connexion à la base de données
require_once __DIR__ . '/../../database.php';

// Démarrer une transaction pour garantir l'intégrité des données
mysqli_begin_transaction($connexion);

try {
    // Insérer chaque produit du panier dans la table `commande`
    foreach ($cartData as $product) {
        $produitId = $product['id'];
        $quantite = $product['quantity'];
        $prixTotal = $product['prix'] * $quantite;

        // Vérifier si le produit existe et si le stock est suffisant
        $sqlCheckStock = "SELECT quantite_stock FROM produit WHERE id = ?";
        $stmtCheckStock = mysqli_prepare($connexion, $sqlCheckStock);
        mysqli_stmt_bind_param($stmtCheckStock, "i", $produitId);
        mysqli_stmt_execute($stmtCheckStock);
        mysqli_stmt_bind_result($stmtCheckStock, $quantiteStock);
        mysqli_stmt_fetch($stmtCheckStock);
        mysqli_stmt_close($stmtCheckStock);

        if ($quantiteStock < $quantite) {
            throw new Exception("Stock insuffisant pour le produit ID $produitId.");
        }

        // Requête SQL pour insérer la commande
        $sql = "INSERT INTO commande (produit_id, quantite, prix_total) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($connexion, $sql);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "iid", $produitId, $quantite, $prixTotal);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        } else {
            throw new Exception("Erreur lors de la préparation de la requête : " . mysqli_error($connexion));
        }
    }

    // Valider la transaction
    mysqli_commit($connexion);

    // Vider le panier (via JavaScript)
    echo "<script>localStorage.removeItem('cart');</script>";

    // Afficher un message de succès
    echo "<h1>Commande passée avec succès !</h1>";
    echo "<a href='details.php'>Retour à la liste des produits</a>";
} catch (Exception $e) {
    // Annuler la transaction en cas d'erreur
    mysqli_rollback($connexion);

    // Afficher un message d'erreur
    echo "<h1>Erreur : " . $e->getMessage() . "</h1>";
    echo "<a href='commande.php'>Retour à la page de commande</a>";
}
?>