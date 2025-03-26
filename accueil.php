<?php
ob_start();
require_once './database.php';
?>

<!doctype html>
<html lang="fr">
<head>
    <title>ElectroShop - Votre spécialiste en électroménager</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0; /* Supprime les marges par défaut */
            padding: 0; /* Supprime les paddings par défaut */
        }
        .hero-section {
            position: relative;
            background: url('image.png') no-repeat center center/cover;
            color: white;
            padding: 100px 0;
            text-align: center;
            margin-top: 0;
        }

        .hero-section::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5); /* Réduit la luminosité (0.5 = 50% d'opacité) */
        }

        .hero-section h1,h2,
        .hero-section p,
        .hero-section .btn {
            position: relative;
            z-index: 1; /* Pour que le texte reste visible au-dessus de l'overlay */
        }

        .carousel img {
            height: 500px;
            object-fit: cover;
        }
        .category-section {
            padding: 50px 0;
        }
        .category-card {
            transition: transform 0.3s;
        }
        .category-card:hover {
            transform: scale(1.05);
        }
        .footer {
            background-color: #343a40;
            color: white;
            padding: 20px 0;
            text-align: center;
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }
        .hero-section .btn {
            margin-top: 20px;
        }

        .category-card img {
            height: 350px; /* Ajuste la hauteur selon ton besoin */
            object-fit: cover; /* Ajuste l’image sans la déformer */
            width: 100%; /* Assure que l'image prend toute la largeur de la carte */
        }

        .testimonials-section, .contact-section, .order-form-section {
            padding: 50px 0;
        }

    </style>
</head>
<body>
    <?php require_once './shared/navebar.php'; ?>

    <div class="hero-section">
        <h1>Bienvenue sur NSA_DISTRIBUTION</h1>
        <p>Découvrez les meilleurs appareils électroménagers aux meilleurs prix !</p>
        <P>LOYE KHARRRRR </P>
        <h2>DEFFALLL SA COMMANDE GAWWWW......</h2>
        <a href="?action=listProduit" class="btn btn-primary btn-lg">Voir nos produits</a>
    </div>

    <div class="container mt-5">
        <h2 class="text-center mb-4">Nos Meilleurs Produits</h2>
        <div id="carouselExampleIndicators" class="carousel slide" data-bs-ride="carousel" data-bs-interval="2000">
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <img src="image1.png" class="d-block w-100" alt="Produit 1">
                </div>
                <div class="carousel-item">
                    <img src="vert.jpg" class="d-block w-100" alt="Produit 2">
                </div>
                <div class="carousel-item">
                    <img src="ensemble.png" class="d-block w-100" alt="Produit 3">
                </div>
            </div>
            
        </div>
    </div>

    <div class="container category-section">
        <h2 class="text-center mb-4">Nos Catégories</h2>
        <div class="row">
            <div class="col-md-4">
                <div class="card category-card">
                    <img src="ensemble-appareils.png" class="card-img-top" alt="Cuisine">
                    <div class="card-body text-center">
                        <h5 class="card-title">Cuisine</h5>
                        <p class="card-text">Découvrez nos réfrigérateurs, fours et autres équipements pour votre cuisine.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card category-card">
                    <img src="machine.png" class="card-img-top" alt="Entretien">
                    <div class="card-body text-center">
                        <h5 class="card-title">Entretien</h5>
                        <p class="card-text">Aspirateurs, lave-linge et tout ce qu'il vous faut pour un intérieur impeccable.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card category-card">
                    <img src="portrait-femme.png" class="card-img-top" alt="Beauté & Santé">
                    <div class="card-body text-center">
                        <h5 class="card-title">Beauté & Santé</h5>
                        <p class="card-text">Sèche-cheveux, rasoirs électriques et bien plus pour prendre soin de vous.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container testimonials-section">
        <h2 class="text-center mb-4">Témoignages</h2>
        <div class="row">
            <div class="col-md-4">
                <p>⭐⭐⭐⭐⭐ "Super qualité et service rapide !" - Fatou</p>
            </div>
            <div class="col-md-4">
                <p>⭐⭐⭐⭐ "J’ai trouvé ce que je cherchais à un prix raisonnable." - Moussa</p>
            </div>
            <div class="col-md-4">
                <p>⭐⭐⭐⭐⭐ "Livraison rapide et produits fiables !" - Awa</p>
            </div>
        </div>
    </div>

    <div class="container contact-section">
        <h2 class="text-center mb-4">Contact</h2>
        <p class="text-center">📍 Dakar, Sénégal | 📞 +221 77 123 45 67 | ✉ contact@nsadistribution.com</p>
    </div>

    <div class="container order-form-section">
        <h2 class="text-center mb-4">Passer une commande</h2>
        <form>
            <div class="mb-3">
                <label class="form-label">Nom et Prénom</label>
                <input type="text" class="form-control" placeholder="Votre nom">
            </div>
            <div class="mb-3">
                <label class="form-label">Téléphone</label>
                <input type="tel" class="form-control" placeholder="Votre numéro">
            </div>
            <div class="mb-3">
                <label class="form-label">Produit souhaité</label>
                <input type="text" class="form-control" placeholder="Nom du produit">
            </div>
            <button type="submit" class="btn btn-success">Commander</button>
        </form>
    </div>

    <div class="footer">
        <p>&copy; 2025 ElectroShop. Tous droits réservés.</p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 