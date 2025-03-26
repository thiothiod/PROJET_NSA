<?php
// filepath: c:\xampp\htdocs\GESTION_COMMANDE_FACTURATION\pages\secretaire\statut_commande.php
session_start();

// Vérifier si l'utilisateur est une secrétaire
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'secretaire') {
    header('Location: ../../index.php'); // Rediriger vers la page d'accueil
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statut des Commandes</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background-color: #f4f4f9;
            font-family: Arial, sans-serif;
            padding: 20px;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .table th, .table td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }
        .table th {
            background-color: #4a90e2;
            color: white;
        }
        .table tr:hover {
            background-color: #f1f1f1;
        }
        .btn {
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }
        .btn-success {
            background-color: #28a745;
            color: white;
        }
        .btn-warning {
            background-color: #ffc107;
            color: black;
        }
        .btn-danger {
            background-color: #dc3545;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center"><i class="fas fa-list"></i> Gestion des Commandes - Secrétaire</h1>

        <!-- Section des commandes -->
        <h2>Commandes Clients</h2>
        <table class="table" id="tableCommandes">
            <thead>
                <tr>
                    <th>ID Commande</th>
                    <th>Client</th>
                    <th>Produits</th>
                    <th>Statut</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="listeCommandes">
                <!-- Les commandes seront ajoutées dynamiquement ici -->
            </tbody>
        </table>
    </div>

    <script>
        // Fonction pour envoyer les données du localStorage à PHP
        function envoyerDonnees() {
            // Récupérer les commandes depuis localStorage
            const commandes = JSON.parse(localStorage.getItem('commandes')) || [];

            // Envoyer les données à PHP via une requête AJAX
            fetch('traitement_commandes.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(commandes)
            })
            .then(response => response.json())
            .then(data => {
                // Afficher les commandes dans le tableau
                const listeCommandes = document.getElementById('listeCommandes');
                listeCommandes.innerHTML = '';

                if (data.length === 0) {
                    listeCommandes.innerHTML = '<tr><td colspan="5" style="text-align: center;">Aucune commande trouvée.</td></tr>';
                    return;
                }

                data.forEach((commande, index) => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${index + 1}</td>
                        <td>${commande.client.nom} ${commande.client.prenom}</td>
                        <td>
                            <ul>
                                ${commande.produits.map(produit => `
                                    <li>${produit.libelle} - ${produit.prix} CFA (x${produit.quantite})</li>
                                `).join('')}
                            </ul>
                        </td>
                        <td>
                            <select class="statut" data-index="${index}" onchange="mettreAJourStatut(${index}, this.value)">
                                <option value="En attente" ${commande.statut === 'En attente' ? 'selected' : ''}>En attente</option>
                                <option value="Validée" ${commande.statut === 'Validée' ? 'selected' : ''}>Validée</option>
                                <option value="Expédiée" ${commande.statut === 'Expédiée' ? 'selected' : ''}>Expédiée</option>
                            </select>
                        </td>
                        <td>
                            <button class="btn btn-success" onclick="validerCommande(${index})">Valider</button>
                            <button class="btn btn-danger" onclick="supprimerCommande(${index})">Supprimer</button>
                        </td>
                    `;
                    listeCommandes.appendChild(row);
                });
            })
            .catch(error => {
                console.error('Erreur:', error);
            });
        }

        // Fonction pour mettre à jour le statut d'une commande
        function mettreAJourStatut(index, statut) {
            const commandes = JSON.parse(localStorage.getItem('commandes')) || [];
            if (commandes[index]) {
                commandes[index].statut = statut;
                localStorage.setItem('commandes', JSON.stringify(commandes));
                alert('Statut mis à jour avec succès !');
            }
        }

        // Charger les commandes au démarrage de la page
        document.addEventListener('DOMContentLoaded', envoyerDonnees);
    </script>
</body>
</html>