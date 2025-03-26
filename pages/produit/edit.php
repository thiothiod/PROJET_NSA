<?php
    require_once './database.php';

    // Vérifier la connexion
    if (!$connexion) {
        die("Erreur de connexion : " . mysqli_connect_error());
    }

    // Vérifier si l'ID du produit est passé en paramètre
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);

        // Récupérer les données du produit
        $sql = "SELECT * FROM produit WHERE id = $id";
        $result = mysqli_query($connexion, $sql);

        if ($result && mysqli_num_rows($result) > 0) {
            $produit = mysqli_fetch_assoc($result);
        } else {
            die("Produit non trouvé.");
        }
    } else {
        die("ID du produit non spécifié.");
    }
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier un Produit</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5 p-4" style="background-color: #f1f1f1; border-radius: 10px;">
        <h1 class="text-center mb-4">MODIFIER UN PRODUIT</h1>

        <form action="?action=updateProduit" method="POST">
            <input type="hidden" name="id" value="<?= $produit['id'] ?>">
            <div class="mb-3">
                <label class="form-label">Libellé</label>
                <input type="text" name="libelle" class="form-control" value="<?= htmlspecialchars($produit['libelle']) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Quantité en Stock</label>
                <input type="number" name="qte_stock" class="form-control" value="<?= $produit['quantite_stock'] ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Quantité Seuil</label>
                <input type="number" name="qte_seuil" class="form-control" value="<?= $produit['quantite_seuil'] ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Prix</label>
                <input type="number" name="prix" class="form-control" value="<?= $produit['prix'] ?>" required>
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