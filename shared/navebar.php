<?php
// filepath: c:\xampp\htdocs\GESTION_COMMANDE_FACTURATION\shared\navebar.php
?>
<!doctype html>
<html lang="fr">
<head>
    <title>Gestion de Commandes</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    
    <style>
        /* NAVBAR AGRANDI */
        .navbar {
            background-color: rgb(140, 161, 175);
            padding: 10px 15px; /* Augmente la hauteur */
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        }

        .navbar-brand {
            font-size: 35px;
            font-weight: bold;
            color: #007bff;
            transition: color 0.3s ease-in-out;
        }

        .navbar-brand:hover {
            color: #0056b3;
        }

        /* STYLE DU DROPDOWN */
        .dropdown-menu {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            min-width: 260px;
            animation: fadeIn 0.3s ease-in-out;
        }

        .dropdown-item {
            font-size: 18px;
            padding: 12px;
            transition: background-color 0.3s ease-in-out, color 0.3s ease-in-out;
        }

        .dropdown-item i {
            margin-right: 10px;
            color: #007bff;
        }

        .dropdown-item:hover {
            background-color: #007bff;
            color: white;
        }

        .dropdown-item:hover i {
            color: white;
        }

        /* BOUTON DÉCONNEXION PLUS GRAND ET DÉCALÉ */
        .logout-btn {
            font-size: 18px;
            padding: 12px 20px;
            border-radius: 10px;
            margin-left: auto; /* Décalage vers la droite */
        }

        /* MESSAGE DE BIENVENUE PLUS GRAND */
        .welcome-text {
            font-size: 15px;
            font-weight: bold;
            padding: 10px 25px;
            border-radius: 8px;
            background-color: #6c757d;
            color: white;
            margin-left: 20px;
            display: flex;
            align-items: center;
        }

        /* ANIMATION FADEIN */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .navbar-nav .nav-link:hover {
            color: rgb(218, 40, 9) !important;
            transform: scale(1.05);
        }

        .logout-btn {
            transition: 0.3s ease-in-out;
        }

        .logout-btn:hover {
            background-color: #c82333;
            transform: scale(1.05);
        }

        .user-info {
            display: flex;
            align-items: center;
            color: white;
        }

        .user-info img {
            border-radius: 50%;
            width: 40px;
            height: 40px;
            margin-right: 10px;
        }

        .navbar-nav {
            display: flex;
            align-items: center;
        }

        .navbar-nav .nav-item {
            margin-left: 15px;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
                <a class="navbar-brand dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-book"></i> Gestion de Commandes
                </a>
                <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <li><a class="dropdown-item" href="?action=addProduit"><i class="bi bi-journal-plus"></i> Ajouter un produit</a></li>
                    <li><a class="dropdown-item" href="?action=addClient"><i class="bi bi-person-plus"></i> Ajouter un client</a></li>
                    <li><a class="dropdown-item" href="?action=profilCommande"><i  class="bi bi-journal-plus"></i> Profil Commande</a></li>
                </ul>
            <?php endif; ?>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <?php if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="index.php?action=accueil">
                                <i class="bi bi-house"></i> Accueil
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="index.php?action=detailsProduit">
                                <i class="bi bi-info-circle"></i> Détails des produits
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="index.php?action=commande">
                                <i class="bi bi-cart"></i> Commande
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="index.php?action=recapulatif">
                                <i class="bi bi-journal-text"></i> Récapitulatif
                            </a>
                        </li>
                        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'secretaire'): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="index.php?action=gestionCommande">
                                    <i class="bi bi-journal-plus"></i> Gestion Commande
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="index.php?action=validationCommande">
                                    <i class="bi bi-check-circle"></i> Validation des Commandes
                                </a>
                            </li>
                        <?php endif; ?>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="index.php?action=listProduit">
                                <i class="bi bi-list-ul"></i> Liste des produits
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="index.php?action=listClient">
                                <i class="bi bi-people"></i> Liste des clients
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="index.php?action=profilProduit">
                                <i class="bi bi-people"></i> Profil 
                            </a>
                        </li>
                    <?php endif; ?>
                    <?php if (isset($_SESSION['user'])): ?>
                        <li class="nav-item ms-3 user-info">
                            <span class="welcome-text">
                                <i class="bi bi-person-circle"></i> Bienvenue, <?php echo htmlspecialchars($_SESSION['user']['prenom'] . ' ' . $_SESSION['user']['nom']); ?>
                            </span>
                        </li>
                        <li class="nav-item ms-3">
                            <a class="btn btn-danger logout-btn" href="index.php?action=deconnecter">
                                <i class="bi bi-box-arrow-right"></i> Déconnexion
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Bootstrap JavaScript Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>