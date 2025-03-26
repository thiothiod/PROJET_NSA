<?php
require_once './database.php'; // Inclure la connexion à la base de données

// Requête pour récupérer les produits
$sql = "SELECT id, libelle, quantite_stock, quantite_seuil, prix, photo FROM produit";
$result = mysqli_query($connexion, $sql);

// Vérifier si la requête a renvoyé des résultats
if (!$result) {
    die("Erreur de requête : " . mysqli_error($connexion));
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Produits</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .container {
            margin-top: 20px;
            background-color: rgb(107, 109, 110); /* Fond blanc */
        }
        body {
            background-color: #f8f9fa; /* Fond gris clair */
            margin: 0; /* Supprime toute marge */
            padding: 0; /* Supprime le padding */
        }
        .btn-nouveau {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }
        h1 {
            color: white;
            border-radius: 10px;
            background-color: rgb(0, 0, 1);
            padding: 10px;
        }
        .product-photo {
            width: 100px;
            height: auto;
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Liste des Produits</h1>

        <!-- Bouton Ajouter un produit -->
        <div class="btn-nouveau">
            <a href="?action=addProduit" class="btn btn-primary">➕ Nouveau Produit</a>
        </div>

        <!-- Tableau des produits -->
        <table class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>Id</th>
                    <th>Libelle</th>
                    <th>Quantité en Stock</th>
                    <th>Quantité Seuil</th>
                    <th>Prix</th>
                    <th>Photo</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Vérifier si des produits sont disponibles
                if (mysqli_num_rows($result) > 0) {
                    // Afficher chaque produit
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td>" . $row['id'] . "</td>";
                        echo "<td>" . htmlspecialchars($row['libelle']) . "</td>";
                        echo "<td>" . $row['quantite_stock'] . "</td>";
                        echo "<td>" . $row['quantite_seuil'] . "</td>";
                        echo "<td>" . $row['prix'] . "</td>";
                        echo "<td><img src='" . htmlspecialchars($row['photo']) . "' alt='Photo du produit' class='product-photo'></td>";
                        echo "<td>
                            <a href='?action=editProduit&id=" . $row['id'] . "' class='btn btn-warning btn-sm'>Modifier</a>
                            <a href='?action=deleteProduit&id=" . $row['id'] . "' class='btn btn-danger btn-sm'>Supprimer</a>
                        </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='7' class='text-center'>Aucun produit trouvé</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>