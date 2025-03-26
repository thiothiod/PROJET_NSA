<?php
// filepath: c:\xampp\htdocs\GESTION_COMMANDE_FACTURATION\pages\produit\panier.php
session_start();
require_once './database.php'; // Inclure la connexion à la base de données

// Vérifier si le panier existe dans la session
if (!isset($_SESSION['panier'])) {
    $_SESSION['panier'] = [];
}

// Ajouter un produit au panier
if (isset($_GET['action']) && $_GET['action'] == 'addToCart' && isset($_GET['id'])) {
    $productId = $_GET['id'];
    $sql = "SELECT id, libelle, prix, photo FROM produit WHERE id = $productId";
    $result = mysqli_query($connexion, $sql);
    if ($row = mysqli_fetch_assoc($result)) {
        // Vérifier si le produit est déjà dans le panier
        $found = false;
        foreach ($_SESSION['panier'] as &$item) {
            if ($item['id'] == $row['id']) {
                $item['quantite'] += 1; // Augmenter la quantité de 1 si le produit existe déjà dans le panier
                $found = true;
                break;
            }
        }
        if (!$found) {
            // Ajouter le produit avec une quantité de 1 s'il n'existe pas dans le panier
            $row['quantite'] = 1;
            $_SESSION['panier'][] = $row;
        }
    }
}

// Supprimer un produit du panier
if (isset($_GET['action']) && $_GET['action'] == 'removeFromCart' && isset($_GET['index'])) {
    $index = $_GET['index'];
    if (isset($_SESSION['panier'][$index])) {
        unset($_SESSION['panier'][$index]);
        $_SESSION['panier'] = array_values($_SESSION['panier']); // Réindexer le tableau
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panier</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .container {
            margin-top: 20px;
        }
        .product-photo {
            width: 100px;
            height: auto;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Panier</h1>

        <!-- Tableau des produits dans le panier -->
        <table class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>Libelle</th>
                    <th>Prix</th>
                    <th>Quantité</th>
                    <th>Photo</th>
                    <th>Total</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $totalPanier = 0;
                if (!empty($_SESSION['panier'])) {
                    foreach ($_SESSION['panier'] as $index => $product) {
                        $totalProduit = $product['prix'] * $product['quantite'];
                        $totalPanier += $totalProduit;
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($product['libelle']) . "</td>";
                        echo "<td>" . $product['prix'] . " CFA</td>";
                        echo "<td>" . $product['quantite'] . "</td>";
                        echo "<td><img src='" . htmlspecialchars($product['photo']) . "' alt='Photo du produit' class='product-photo'></td>";
                        echo "<td>" . number_format($totalProduit, 2) . " CFA</td>";
                        echo "<td>
                            <a href='panier.php?action=removeFromCart&index=$index' class='btn btn-danger btn-sm'>Supprimer</a>
                        </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='6' class='text-center'>Votre panier est vide</td></tr>";
                }
                ?>
            </tbody>
        </table>

        <!-- Total du panier -->
        <?php if (!empty($_SESSION['panier'])): ?>
            <div class="text-end mb-4">
                <h4>Total du panier : <?php echo number_format($totalPanier, 2); ?> CFA</h4>
            </div>
        <?php endif; ?>

        <!-- Bouton pour passer la commande -->
        <div class="text-center">
            <a href="confirmation.php" class="btn btn-success" onclick="confirmOrder()">Passer la commande</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Fonction pour ajouter un produit au panier
        function addToCart(id, libelle, prix, photo) {
            // Récupérer le panier actuel depuis le localStorage
            let cart = JSON.parse(localStorage.getItem('cart')) || [];

            // Vérifier si le produit est déjà dans le panier
            let productIndex = cart.findIndex(product => product.id === id);

            if (productIndex !== -1) {
                // Si le produit existe déjà, mettre à jour la quantité
                cart[productIndex].quantite += 1;
            } else {
                // Sinon, ajouter le produit au panier
                cart.push({ id, libelle, prix, photo, quantite: 1 });
            }

            // Enregistrer le panier mis à jour dans le localStorage
            localStorage.setItem('cart', JSON.stringify(cart));

            // Optionnel : Afficher un message ou mettre à jour l'interface utilisateur
            alert('Produit ajouté au panier !');
        }

        // Fonction pour synchroniser le panier de la session avec le localStorage
        function syncCartWithLocalStorage() {
            // Récupérer le panier depuis le localStorage
            let cart = JSON.parse(localStorage.getItem('cart')) || [];

            // Mettre à jour le panier de la session
            cart.forEach(product => {
                let url = `panier.php?action=addToCart&id=${product.id}`;
                fetch(url).then(response => {
                    if (response.ok) {
                        console.log(`Produit "${product.libelle}" ajouté au panier`);
                    }
                });
            });
        }

        // Fonction pour confirmer la commande
        function confirmOrder() {
            // Vider le panier
            localStorage.removeItem('cart');

            // Rediriger vers la page de confirmation
            window.location.href = '../pages/recapulatif/confirm.php?success=1';
        }

        // Appel initial pour synchroniser le panier avec le localStorage
        syncCartWithLocalStorage();
    </script>
</body>
</html>