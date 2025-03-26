<?php
// filepath: c:\xampp\htdocs\GESTION_COMMANDE_FACTURATION\pages\secretaire\traitement_commandes.php
session_start();

// Vérifier si l'utilisateur est une secrétaire
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'secretaire') {
    header('Location: ../../index.php'); // Rediriger vers la page d'accueil
    exit();
}

require_once __DIR__ . '/../../database.php';

// Récupérer les données envoyées par JavaScript
$data = json_decode(file_get_contents('php://input'), true);

// Vérifier si des données ont été reçues
if (empty($data)) {
    http_response_code(400); // Bad Request
    echo json_encode(['message' => 'Aucune donnée reçue.']);
    exit;
}

// Exemple : Enregistrer les commandes dans la base de données
foreach ($data as $commande) {
    // Logique pour enregistrer chaque commande dans la base de données
    // ...
}

// Réponse JSON pour confirmer la réception des données
echo json_encode(['message' => 'Données reçues avec succès.']);
?>