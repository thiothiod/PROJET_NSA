<?php
require_once __DIR__ . '/../../database.php';

// Récupérer les données envoyées en JSON
$data = json_decode(file_get_contents("php://input"), true);

if (!$data || empty($data['cart'])) {
    echo json_encode(["success" => false, "message" => "Panier vide"]);
    exit;
}

$cart = $data['cart'];
$date_commande = date("Y-m-d H:i:s");

// Insérer la commande principale
$sql = "INSERT INTO commandes (date_commande) VALUES ('$date_commande')";
if (mysqli_query($connexion, $sql)) {
    $commande_id = mysqli_insert_id($connexion); // Récupérer l'ID de la commande insérée

    // Insérer chaque produit du panier dans les détails de la commande
    foreach ($cart as $produit) {
        $produit_id = $produit['id'];
        $quantite = $produit['quantity'];
        $prix = $produit['prix'];

        $sql_detail = "INSERT INTO commande_details (commande_id, produit_id, quantite, prix_unitaire) 
                       VALUES ('$commande_id', '$produit_id', '$quantite', '$prix')";
        mysqli_query($connexion, $sql_detail);
    }

    // Réponse JSON avec succès et ID de la commande
    echo json_encode(["success" => true, "commande_id" => $commande_id]);
} else {
    echo json_encode(["success" => false, "message" => "Erreur d'enregistrement"]);
}

mysqli_close($connexion);
?>
