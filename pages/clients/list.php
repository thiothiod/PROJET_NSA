<?php
// filepath: c:\xampp\htdocs\GESTION_COMMANDE_FACTURATION\pages\clients\list.php
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Clients</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        .containerr {
            margin-top: 20px;
            background-color: rgb(199, 209, 215);
            transition: background-color 0.3s ease-in-out;
        }
        body {
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
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
            font-size: 40px;
        }
        table th, table td {
            font-size: 14px;
        }
        .btn {
            font-size: 12px;
            padding: 5px 10px;
        }
    </style>
</head>
<body>
<div class="containerr mt-6">
    <h1 class="text-center mb-4">Liste des clients</h1>

    <div class="btn-nouveau">
        <a href="?action=addClient" class="btn btn-primary">➕ Nouveau Client</a>
    </div>

    <table class="table table-striped">
        <thead class="table-dark">
            <tr>
                <th>Id</th>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Adresse</th>
                <th>Téléphone</th>
                <th>Produit</th>
                <th>Login</th>
                <th>Photo</th> <!-- Ajout de la colonne -->
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Inclure le fichier de connexion à la base de données
            include_once __DIR__ . '/../../database.php';

            // Vérifier la connexion
            if (!$connexion) {
                die("Erreur de connexion à la base de données : " . mysqli_connect_error());
            }

            // Requête SQL avec la photo
            $query = "SELECT 
                        client.id, client.nom, client.prenom, client.adresse, client.telephone, 
                        produit.libelle, client.login, client.photo
                      FROM client 
                      LEFT JOIN produit ON client.produit_id = produit.id";

            $result = mysqli_query($connexion, $query);

            if (!$result) {
                die("Erreur SQL : " . mysqli_error($connexion));
            }

            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['nom']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['prenom']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['adresse']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['telephone']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['libelle']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['login']) . "</td>";
                    echo "<td><img src='/GESTION_COMMANDE_FACTURATION/uploads/" . htmlspecialchars($row['photo']) . "' width='50' height='50' style='border-radius:50%;'></td>";
                    echo "<td>
                            <a href='edit.php?id=" . urlencode($row['id']) . "' class='btn btn-warning btn-sm'>✏️ Modifier</a>
                            <a href='delete.php?id=" . urlencode($row['id']) . "' class='btn btn-danger btn-sm'>🗑️ Supprimer</a>
                          </td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='9' class='text-center text-danger'>Aucun client trouvé.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>