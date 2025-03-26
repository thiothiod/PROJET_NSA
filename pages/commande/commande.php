<?php
// filepath: c:\xampp\htdocs\GESTION_COMMANDE_FACTURATION\pages\commande\commande.php
require_once __DIR__ . '/../../database.php';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Commandes</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <style>
        /* Reset some basic styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f7f7f7;
            color: #333;
            margin: 0;
            padding: 20px;
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
            color: #ff6b6b;
        }

        /* Tableau des commandes */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            background-color: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #ff6b6b;
            color: white;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        .commandes-container {
            max-width: 1000px;
            margin: 0 auto;
        }

        .btn-retour {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #ff6b6b;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }

        .btn-retour:hover {
            background-color: #ff4757;
        }

        .btn-retour i {
            margin-right: 10px;
        }

        /* Style pour la liste des produits */
        ul {
            list-style-type: none;
            padding: 0;
            margin: 0;
        }

        ul li {
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <div class="commandes-container">
        <h1><i class="fas fa-clipboard-list"></i> Historique des Commandes</h1>

        <!-- Tableau des commandes -->
        <table id="commandesTable">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Produits</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody id="commandesBody">
                <!-- Les commandes seront ajoutées dynamiquement ici -->
            </tbody>
        </table>

        <!-- Bouton pour retourner à la page des produits -->
        <a href="../../produit/details.php" class="btn-retour">
            <i class="fas fa-arrow-left"></i> Retour aux produits
        </a>
    </div>

    <script>
        // Fonction pour charger les commandes
        function chargerCommandes() {
            // Récupérer les commandes depuis localStorage
            const commandes = JSON.parse(localStorage.getItem('commandes')) || [];
            const commandesBody = document.getElementById('commandesBody');
            commandesBody.innerHTML = '';

            // Si aucune commande n'est trouvée
            if (commandes.length === 0) {
                commandesBody.innerHTML = '<tr><td colspan="3" style="text-align: center;">Aucune commande trouvée.</td></tr>';
                return;
            }

            // Afficher chaque commande dans le tableau
            commandes.forEach((commande) => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${commande.date}</td>
                    <td>
                        <ul>
                            ${commande.produits.map(produit => `
                                <li>${produit.libelle} - ${produit.prix} CFA (x${produit.quantite})</li>
                            `).join('')}
                        </ul>
                    </td>
                    <td>${commande.total} CFA</td>
                `;
                commandesBody.appendChild(row);
            });
        }

        // Charger les commandes au démarrage de la page
        document.addEventListener('DOMContentLoaded', chargerCommandes);
    </script>
</body>
</html>