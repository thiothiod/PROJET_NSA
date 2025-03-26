<?php
// Démarrer la session si elle n'est pas déjà active
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../database.php';

// Fonction pour passer la commande
if (isset($_POST['confirm'])) {
    // Récupérer les produits du panier depuis le formulaire
    $produits = json_decode($_POST['produits'], true);

    // Calculer le total de la commande
    $total = array_reduce($produits, function($carry, $item) {
        return $carry + ($item['prix'] * $item['quantity']);
    }, 0);

    // Enregistrer la commande dans la session (ou la base de données)
    $_SESSION['commande'] = [
        'produits' => $produits,
        'total' => $total
    ];

    // Rediriger avec un paramètre de succès
    header('Location: recapulatif.php?success=1');
    exit();
}

// Vérifier si la commande a été passée avec succès
$success = isset($_GET['success']) && $_GET['success'] == 1;
$commande = isset($_SESSION['commande']) ? $_SESSION['commande'] : null;
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Confirmation de la Commande</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
        }
        .container {
            margin-top: 50px;
        }
        .btn-custom {
            background-color: #007bff;
            color: white;
            border-radius: 20px;
            padding: 10px 20px;
            font-weight: bold;
            transition: background 0.3s;
        }
        .btn-custom:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center mb-4">Récapitulatif de la Commande</h1>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Libelle</th>
                    <th>Prix</th>
                    <th>Quantité</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody id="produits-body">
                <!-- Les produits seront ajoutés ici dynamiquement -->
            </tbody>
        </table>
        <div class="text-center">
            <!-- Bouton pour ouvrir la modale de confirmation -->
            <button class="btn btn-custom" data-bs-toggle="modal" data-bs-target="#confirmationModal">Confirmer la Commande</button>
        </div>
    </div>

    <!-- Modale de confirmation -->
    <div class="modal fade" id="confirmationModal" tabindex="-1" aria-labelledby="confirmationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmationModalLabel">Confirmation de la Commande</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <?php if ($success && $commande): ?>
                        <div class="alert alert-success text-center">
                            Commande passée avec succès !
                        </div>
                        <h5 class="card-title">Détails de la Commande</h5>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Libelle</th>
                                    <th>Prix</th>
                                    <th>Quantité</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($commande['produits'] as $produit): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($produit['libelle']); ?></td>
                                        <td><?php echo htmlspecialchars($produit['prix']); ?> CFA</td>
                                        <td><?php echo htmlspecialchars($produit['quantity']); ?></td>
                                        <td><?php echo htmlspecialchars($produit['prix'] * $produit['quantity']); ?> CFA</td>
                                    </tr>
                                <?php endforeach; ?>
                                <tr>
                                    <td colspan="3" class="text-end"><strong>Total :</strong></td>
                                    <td><strong><?php echo $commande['total']; ?> CFA</strong></td>
                                </tr>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p>Êtes-vous sûr de vouloir confirmer cette commande ?</p>
                    <?php endif; ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <!-- Formulaire pour soumettre la commande -->
                    <form id="confirmForm" method="POST" action="recapulatif.php">
                        <input type="hidden" id="produits-input" name="produits">
                        <button type="submit" name="confirm" class="btn btn-custom">Confirmer</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Récupérer les produits du localStorage
            let cart = JSON.parse(localStorage.getItem('cart')) || [];
            let produitsBody = document.getElementById('produits-body');
            let produitsInput = document.getElementById('produits-input');
            let total = 0;

            if (cart.length === 0) {
                produitsBody.innerHTML = '<tr><td colspan="4" class="text-center">Votre panier est vide</td></tr>';
            } else {
                // Afficher les produits dans le tableau
                cart.forEach(product => {
                    let totalProduit = product.prix * product.quantity;
                    total += totalProduit;

                    let row = `
                        <tr>
                            <td>${product.libelle}</td>
                            <td>${product.prix} CFA</td>
                            <td>${product.quantity}</td>
                            <td>${totalProduit} CFA</td>
                        </tr>
                    `;
                    produitsBody.innerHTML += row;
                });

                // Ajouter le total au tableau
                let totalRow = `
                    <tr>
                        <td colspan="3" class="text-end"><strong>Total :</strong></td>
                        <td><strong>${total} CFA</strong></td>
                    </tr>
                `;
                produitsBody.innerHTML += totalRow;

                // Ajouter les produits au champ caché du formulaire
                produitsInput.value = JSON.stringify(cart);
            }

            // Soumettre le formulaire lors de la confirmation
            document.getElementById('confirmForm').addEventListener('submit', function() {
                // Enregistrer la commande dans localStorage
                let commandes = JSON.parse(localStorage.getItem('commandes')) || [];
                let nouvelleCommande = {
                    date: new Date().toLocaleString(),
                    produits: cart,
                    total: total
                };
                commandes.push(nouvelleCommande);
                localStorage.setItem('commandes', JSON.stringify(commandes));

                // Vider le panier après confirmation
                localStorage.removeItem('cart');
            });
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>