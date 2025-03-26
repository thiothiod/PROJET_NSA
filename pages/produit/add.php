<?php
require_once __DIR__ . '/../../database.php';

// Vérifier la connexion
if (!$connexion) {
    die("Erreur de connexion : " . mysqli_connect_error());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['libelle']) && isset($_POST['qte_stock']) && isset($_POST['qte_seuil']) && isset($_POST['prix']) && isset($_FILES['photo'])) {
        $libelle = mysqli_real_escape_string($connexion, $_POST['libelle']);
        $qte_stock = intval($_POST['qte_stock']);
        $qte_seuil = intval($_POST['qte_seuil']);
        $prix = floatval($_POST['prix']);

        // Gestion du téléchargement de la photo
        $photo_dir = __DIR__ . '/../../uploads/';
        if (!is_dir($photo_dir)) {
            mkdir($photo_dir, 0777, true);
        }

        $photo_path = $photo_dir . basename($_FILES['photo']['name']);
        if (move_uploaded_file($_FILES['photo']['tmp_name'], $photo_path)) {
            $photo_url = 'uploads/' . basename($_FILES['photo']['name']);

            // Requête d'insertion sécurisée
            $sql = "INSERT INTO produit (libelle, quantite_stock, quantite_seuil, prix, photo) 
                    VALUES ('$libelle', '$qte_stock', '$qte_seuil', '$prix', '$photo_url')";

            if (mysqli_query($connexion, $sql)) {
                header('Location: index.php');
                exit(); // Arrêter le script après redirection
            } else {
                echo "Erreur : " . mysqli_error($connexion);
            }
        } else {
            echo "Erreur lors du téléchargement de la photo.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un Produit</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5 p-4" style="background-color: #f1f1f1; border-radius: 10px;">
        <h1 class="text-center mb-4">Ajouter un Produit</h1>

        <form action="#" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label">Libellé</label>
                <input type="text" name="libelle" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Quantité en Stock</label> 
                <input type="number" name="qte_stock" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Quantité Seuil</label>
                <input type="number" name="qte_seuil" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Prix</label>
                <input type="number" name="prix" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Photo</label>
                <input type="file" name="photo" class="form-control" required>
            </div>

            <div class="text-center">
                <button type="submit" class="btn btn-success">✅ Valider</button>
                <button type="reset" class="btn btn-danger">❌ Annuler</button>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>