<?php
// filepath: c:\xampp\htdocs\GESTION_COMMANDE_FACTURATION\pages\produit\details.php
require_once __DIR__ . '/../../database.php';

$sql = "SELECT * FROM produit";
$result = mysqli_query($connexion, $sql);

if ($result) {
    $produits = mysqli_fetch_all($result, MYSQLI_ASSOC);
} else {
    $produits = [];
    echo "<div class='alert alert-danger'>Erreur lors de la récupération des produits : " . mysqli_error($connexion) . "</div>";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Détails des Produits</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            background-color: #f7f7f7;
            font-family: 'Roboto', sans-serif;
            color: #333;
        }
        .container {
            margin-top: 50px;
        }
        h1 {
            text-align: center;
            margin-bottom: 30px;
            color: #ff6b6b;
            font-weight: 700;
        }
        .product-card {
            transition: transform 0.3s ease-in-out, box-shadow 0.3s;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            background-color: white;
        }
        .product-card:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
        }
        .card-img-top {
            height: 250px;
            object-fit: cover;
            border-bottom: 1px solid #eee;
        }
        .card-body {
            text-align: center;
            padding: 20px;
        }
        .card-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 10px;
        }
        .product-price {
            font-size: 1.25rem;
            font-weight: bold;
            color: #28a745;
            margin-bottom: 15px;
        }
        .card-text {
            font-size: 1rem;
            color: #666;
            margin-bottom: 15px;
        }
        .btn-custom {
            background-color: #ff6b6b;
            color: white;
            border-radius: 20px;
            padding: 10px 20px;
            font-weight: bold;
            transition: background-color 0.3s;
        }
        .btn-custom:hover {
            background-color: #ff4757;
        }
        .cart-icon {
            position: relative;
            font-size: 1.5rem;
            cursor: pointer;
            color: #ff6b6b;
        }
        .badge {
            position: absolute;
            top: -10px;
            right: -10px;
            font-size: 0.75rem;
            background-color: #ff4757;
        }
        .navbar {
            background-color: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .navbar-brand {
            font-weight: 700;
            color: #ff6b6b !important;
        }
        .navbar-nav .nav-link {
            color: #333;
        }
        .navbar-nav .nav-link:hover {
            color: #ff6b6b;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container">
            <a class="navbar-brand" href="#">VOTRE PANIER</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link cart-icon" data-bs-toggle="modal" data-bs-target="#cartModal">
                            <i class="bi bi-cart-fill"></i>
                            <span class="badge bg-danger" id="cart-count">0</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Contenu des produits -->
    <div class="container">
        <h1 class="text-center mb-4">Nos Produits</h1>
        <div class="row">
            <?php if (!empty($produits)): ?>
                <?php foreach ($produits as $produit): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card product-card">
                            <img src="<?php echo htmlspecialchars($produit['photo']); ?>" class="card-img-top" alt="Image du produit">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($produit['libelle']); ?></h5>
                                <p class="product-price"><?php echo htmlspecialchars($produit['prix']); ?> CFA</p>
                                <p class="card-text">Stock: <?php echo htmlspecialchars($produit['quantite_stock']); ?> unités</p>
                                <button class="btn btn-custom" onclick="addToCart(<?php echo $produit['id']; ?>, '<?php echo addslashes($produit['libelle']); ?>', <?php echo $produit['prix']; ?>)">
                                    Ajouter au panier
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="alert alert-warning text-center">Aucun produit trouvé.</div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal du panier -->
    <div class="modal fade" id="cartModal" tabindex="-1" aria-labelledby="cartModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cartModalLabel">Votre Panier</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Libelle</th>
                                <th>Prix</th>
                                <th>Quantité</th>
                                <th>Total</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="cart-items">
                            <!-- Les éléments du panier seront ajoutés ici dynamiquement -->
                        </tbody>
                    </table>
                    <div class="d-flex justify-content-between mt-4">
                        <strong>Total :</strong>
                        <span id="total-price">0 CFA</span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                    <button type="button" class="btn btn-primary" onclick="passerCommande()">Passer la commande</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Fonction pour ajouter un produit au panier
        function addToCart(id, libelle, prix) {
        let cart = JSON.parse(localStorage.getItem('cart')) || [];
        let product = { id: id, libelle: libelle, prix: prix, quantity: 1 };

        // Vérifier si le produit est déjà dans le panier
        let existingProduct = cart.find(item => item.id === id);
        if (existingProduct) {
            existingProduct.quantity += 1;
        } else {
            cart.push(product);
        }

        localStorage.setItem('cart', JSON.stringify(cart));
        updateCartCount();
        displayCart();
    }

        // Fonction pour retirer un produit du panier
         function removeFromCart(id) {
        let cart = JSON.parse(localStorage.getItem('cart')) || [];
        let existingProduct = cart.find(item => item.id === id);
        if (existingProduct) {
            existingProduct.quantity -= 1;
            if (existingProduct.quantity <= 0) {
                cart = cart.filter(item => item.id !== id);
            }
        }

        localStorage.setItem('cart', JSON.stringify(cart));
        updateCartCount();
        displayCart();
    }

        // Fonction pour mettre à jour le nombre d'articles dans le panier
        function updateCartCount() {
            let cart = JSON.parse(localStorage.getItem('cart')) || [];
            let totalItems = cart.reduce((acc, product) => acc + product.quantity, 0);
            document.getElementById('cart-count').textContent = totalItems; // Met à jour le nombre d'articles dans le badge
        }

        // Fonction pour afficher les éléments du panier
        function displayCart() {
            let cart = JSON.parse(localStorage.getItem('cart')) || [];
            let cartItemsContainer = document.getElementById('cart-items');
            cartItemsContainer.innerHTML = '';

            let total = 0;
            cart.forEach(product => {
                let totalProduit = product.prix * product.quantity;
                total += totalProduit;

                let row = `
                    <tr>
                        <td>${product.libelle}</td>
                        <td>${product.prix} CFA</td>
                        <td>${product.quantity}</td>
                        <td>${totalProduit} CFA</td>
                        <td>
                            <button class="btn btn-success btn-sm" onclick="addToCart(${product.id}, '${product.libelle}', ${product.prix})">+</button>
                            <button class="btn btn-danger btn-sm" onclick="removeFromCart(${product.id})">-</button>
                        </td>
                    </tr>
                `;
                cartItemsContainer.innerHTML += row;
            });

            document.getElementById('total-price').textContent = `${total} CFA`;
        }

        // Fonction pour passer la commande
    function passerCommande() {
        let cart = JSON.parse(localStorage.getItem('cart')) || [];
        if (cart.length === 0) {
            alert('Votre panier est vide.');
            return;
        }

        // Récupérer la date actuelle
        const dateCommande = new Date().toLocaleString();

        // Calculer le total de la commande
        const totalCommande = cart.reduce((acc, product) => acc + (product.prix * product.quantity), 0);

        // Préparer les détails de la commande
        const commande = {
            date: dateCommande,
            produits: cart.map(product => ({
                nom: product.libelle,
                prix: product.prix,
                quantite: product.quantity
            })),
            total: totalCommande
        };

        // Envoyer les données de la commande au serveur pour générer la facture
        
        // Vider le panier après avoir passé la commande
        localStorage.removeItem('cart');
        updateCartCount();
        displayCart();
        alert('Commande passée avec succès !');
    }
        // Appel initial pour mettre à jour le badge et l'affichage du panier à chaque chargement de la page
        updateCartCount();
        displayCart();
    </script>
</body>
</html>