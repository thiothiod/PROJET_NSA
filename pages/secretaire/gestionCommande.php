<?php
// filepath: c:\xampp\htdocs\GESTION_COMMANDE_FACTURATION\pages\secretaire\gestionCommande.php

// Vérifier si une session n'est pas déjà active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Vérification du rôle
if (!isset($_SESSION['role'])) {
    header('Location: ../../index.php');
    exit();
}

// Autoriser plusieurs rôles (plus flexible)
$allowedRoles = ['secretaire', 'admin', 'manager'];
if (!in_array($_SESSION['role'], $allowedRoles)) {
    header('Location: ../../index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Commandes</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    <style>
        :root {
            --primary-color: #ff6b6b;
            --secondary-color: #495057;
            --success-color: #28a745;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
            --info-color: #17a2b8;
        }

        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f8f9fa;
            color: #333;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            margin-bottom: 30px;
            color: var(--primary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .card {
            margin-bottom: 20px;
            border: none;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .badge-en-attente { background-color: var(--warning-color); color: #000; }
        .badge-validee { background-color: var(--success-color); }
        .badge-annulee { background-color: var(--danger-color); }
        .badge-preparee { background-color: var(--info-color); }
        .badge-expediee { background-color: #6f42c1; }

        .btn-action {
            margin: 5px;
            transition: all 0.3s;
        }

        .table-responsive {
            margin-bottom: 20px;
            border-radius: 8px;
            overflow: hidden;
        }

        .action-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
        }

        .product-list {
            list-style-type: none;
            padding-left: 0;
            margin-bottom: 0;
        }

        .product-list li {
            margin-bottom: 5px;
            padding: 5px;
            background-color: #f8f9fa;
            border-radius: 4px;
        }

        .modal-content {
            border-radius: 10px;
        }

        .total-display {
            font-weight: bold;
            font-size: 1.1em;
            color: var(--primary-color);
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>
            <i class="fas fa-clipboard-list"></i> Gestion des Commandes
            <small class="text-muted fs-6">Connecté en tant que: <?= htmlspecialchars($_SESSION['role']) ?></small>
        </h1>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-list me-2"></i> Liste des Commandes</span>
                <div>
                    <button id="exportBtn" class="btn btn-sm btn-light">
                        <i class="fas fa-file-export me-1"></i> Exporter
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                            <input type="text" id="searchInput" class="form-control" placeholder="Rechercher...">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex flex-wrap align-items-center">
                            <label for="statusFilter" class="me-2 mb-0">Filtrer par statut:</label>
                            <select id="statusFilter" class="form-select form-select-sm" style="width: auto;">
                                <option value="">Tous</option>
                                <option value="En attente">En attente</option>
                                <option value="Validée">Validée</option>
                                <option value="En préparation">En préparation</option>
                                <option value="Expédiée">Expédiée</option>
                                <option value="Annulée">Annulée</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table id="commandesTable" class="table table-striped table-hover" style="width:100%">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Client</th>
                                <th>Date</th>
                                <th>Produits</th>
                                <th>Total</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="commandesBody">
                            <!-- Les commandes seront ajoutées dynamiquement ici -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="text-center mt-3">
            <a href="../../produit/details.php" class="btn btn-primary me-2">
                <i class="fas fa-arrow-left me-2"></i> Retour aux produits
            </a>
            <a href="../../deconnexion.php" class="btn btn-secondary">
                <i class="fas fa-sign-out-alt me-2"></i> Déconnexion
            </a>
        </div>
    </div>

    <!-- Modal pour les détails de commande -->
    <div class="modal fade" id="detailsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Détails de la commande #<span id="modalCommandeId"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6><i class="fas fa-user me-2"></i>Informations client</h6>
                            <div id="clientInfo"></div>
                        </div>
                        <div class="col-md-6">
                            <h6><i class="fas fa-info-circle me-2"></i>Statut de la commande</h6>
                            <p id="orderStatus" class="mb-0"></p>
                        </div>
                    </div>
                    
                    <h6><i class="fas fa-boxes me-2"></i>Produits commandés</h6>
                    <div class="table-responsive mb-3">
                        <table class="table table-sm" id="productsTable">
                            <thead>
                                <tr>
                                    <th>Produit</th>
                                    <th>Prix unitaire</th>
                                    <th>Quantité</th>
                                    <th>Sous-total</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <h6><i class="fas fa-comment me-2"></i>Commentaires</h6>
                            <p id="orderComments" class="text-muted"></p>
                        </div>
                        <div class="col-md-6 text-end">
                            <h5>Total: <span id="orderTotal" class="total-display"></span></h5>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>
    
    <script>
        // Configuration
        const config = {
            currentUserRole: '<?= $_SESSION['role'] ?>',
            allowedStatuses: ['En attente', 'Validée', 'En préparation', 'Expédiée', 'Annulée'],
            defaultStatus: 'En attente'
        };

        // Données de test (à supprimer en production)
        if (!localStorage.getItem('commandes')) {
            const commandesTest = [
                {
                    client: { nom: "THIORO DIENG", email: "thiorodieng@gmail.com", telephone: "779908899" },
                    produits: [
                        { libelle: "Produit 1", prix: 5000, quantite: 2 },
                        { libelle: "Produit 2", prix: 3000, quantite: 1 }
                    ],
                    total: 13000,
                    date: new Date().toLocaleDateString('fr-FR'),
                    statut: "En attente"
                },
                {
                    client: { nom: "Autre Client", email: "client@example.com", telephone: "771234567" },
                    produits: [
                        { libelle: "Produit 3", prix: 7000, quantite: 3 }
                    ],
                    total: 21000,
                    date: new Date().toLocaleDateString('fr-FR'),
                    statut: "Validée"
                }
            ];
            localStorage.setItem('commandes', JSON.stringify(commandesTest));
        }

        // Initialisation de DataTable
        let commandesTable;
        
        // Charger et afficher les commandes
        function chargerCommandes() {
            let commandes = JSON.parse(localStorage.getItem('commandes')) || [];
            
            // Initialiser le statut si non défini
            commandes = commandes.map(cmd => {
                if (!cmd.statut) {
                    cmd.statut = config.defaultStatus;
                }
                return cmd;
            });
            
            localStorage.setItem('commandes', JSON.stringify(commandes));
            
            // Initialiser DataTable si ce n'est pas déjà fait
            if (!$.fn.DataTable.isDataTable('#commandesTable')) {
                commandesTable = $('#commandesTable').DataTable({
                    language: {
                        url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/fr-FR.json'
                    },
                    responsive: true,
                    columns: [
                        { data: 'id' },
                        { data: 'client' },
                        { data: 'date' },
                        { 
                            data: 'produits',
                            render: function(data) {
                                return '<ul class="product-list">' + 
                                    data.map(p => `<li>${p.libelle} - ${p.prix} CFA (x${p.quantite})</li>`).join('') + 
                                    '</ul>';
                            }
                        },
                        { 
                            data: 'total',
                            render: function(data) {
                                return `<span class="total-display">${data} CFA</span>`;
                            }
                        },
                        { 
                            data: 'statut',
                            render: function(data) {
                                return `<span class="badge ${getStatusBadgeClass(data)}">${data}</span>`;
                            }
                        },
                        {
                            data: null,
                            render: function(data, type, row) {
                                return getActionButtons(row);
                            }
                        }
                    ]
                });
                
                // Filtre de recherche
                $('#searchInput').keyup(function() {
                    commandesTable.search(this.value).draw();
                });
                
                // Filtre par statut
                $('#statusFilter').change(function() {
                    commandesTable.column(5).search(this.value).draw();
                });
            }
            
            // Vider et recharger les données
            commandesTable.clear();
            
            if (commandes.length === 0) {
                commandesTable.draw();
                return;
            }
            
            // Formater les données pour DataTables
            const formattedData = commandes.map((commande, index) => ({
                id: index + 1,
                client: commande.client ? commande.client.nom : 'Non spécifié',
                date: commande.date,
                produits: commande.produits,
                total: commande.total,
                statut: commande.statut,
                rawData: commande // Conserver les données brutes
            }));
            
            commandesTable.rows.add(formattedData).draw();
        }

        // Générer les boutons d'action selon le statut et le rôle
        function getActionButtons(row) {
            const commandeId = row.id - 1; // ID réel dans le localStorage
            const statut = row.statut;
            
            let buttons = `
                <div class="action-buttons">
                    <button class="btn btn-sm btn-secondary btn-details" 
                            data-id="${commandeId}" title="Détails">
                        <i class="fas fa-eye"></i>
                    </button>
            `;
            
            // Boutons pour secrétaire et admin
            if (statut === 'En attente' && ['secretaire', 'admin'].includes(config.currentUserRole)) {
                buttons += `
                    <button class="btn btn-sm btn-success btn-valider" 
                            data-id="${commandeId}" title="Valider">
                        <i class="fas fa-check"></i>
                    </button>
                    <button class="btn btn-sm btn-danger btn-annuler" 
                            data-id="${commandeId}" title="Annuler">
                        <i class="fas fa-times"></i>
                    </button>
                `;
            }
            
            // Boutons pour manager et admin
            if (statut === 'Validée' && ['admin', 'manager'].includes(config.currentUserRole)) {
                buttons += `
                    <button class="btn btn-sm btn-info btn-preparer" 
                            data-id="${commandeId}" title="Préparer">
                        <i class="fas fa-box-open"></i>
                    </button>
                `;
            }
            
            if (statut === 'En préparation' && ['admin', 'manager'].includes(config.currentUserRole)) {
                buttons += `
                    <button class="btn btn-sm btn-primary btn-expedier" 
                            data-id="${commandeId}" title="Expédier">
                        <i class="fas fa-truck"></i>
                    </button>
                `;
            }
            
            buttons += `</div>`;
            return buttons;
        }

        // Fonction pour changer le statut d'une commande
        function changerStatutCommande(id, nouveauStatut) {
            const commandes = JSON.parse(localStorage.getItem('commandes')) || [];
            if (id >= 0 && id < commandes.length) {
                commandes[id].statut = nouveauStatut;
                localStorage.setItem('commandes', JSON.stringify(commandes));
                
                // Mettre à jour le stock si validation
                if (nouveauStatut === 'Validée') {
                    updateStock(id, commandes);
                }
                
                return true;
            }
            return false;
        }

        // Mettre à jour le stock après validation
        function updateStock(id, commandes) {
            const produits = JSON.parse(localStorage.getItem('produits')) || [];
            const commande = commandes[id];
            
            commande.produits.forEach(produitCommande => {
                const produitStock = produits.find(p => p.libelle === produitCommande.libelle);
                if (produitStock) {
                    produitStock.quantite_stock -= produitCommande.quantite;
                }
            });
            
            localStorage.setItem('produits', JSON.stringify(produits));
        }

        // Afficher les détails d'une commande
        function showDetails(id) {
            const commandes = JSON.parse(localStorage.getItem('commandes')) || [];
            if (id >= 0 && id < commandes.length) {
                const commande = commandes[id];
                
                $('#modalCommandeId').text(id + 1);
                $('#clientInfo').html(`
                    <p class="mb-1"><strong>Nom:</strong> ${commande.client?.nom || 'Non spécifié'}</p>
                    <p class="mb-1"><strong>Email:</strong> ${commande.client?.email || 'N/A'}</p>
                    <p class="mb-1"><strong>Téléphone:</strong> ${commande.client?.telephone || 'N/A'}</p>
                `);
                $('#orderStatus').html(`<span class="badge ${getStatusBadgeClass(commande.statut)}">${commande.statut}</span>`);
                $('#orderTotal').text(`${commande.total} CFA`);
                $('#orderComments').text(commande.commentaires || 'Aucun commentaire');
                
                // Remplir le tableau des produits
                const productsTable = $('#productsTable tbody');
                productsTable.empty();
                
                commande.produits.forEach(produit => {
                    productsTable.append(`
                        <tr>
                            <td>${produit.libelle}</td>
                            <td>${produit.prix} CFA</td>
                            <td>${produit.quantite}</td>
                            <td>${produit.prix * produit.quantite} CFA</td>
                        </tr>
                    `);
                });
                
                // Ouvrir le modal
                new bootstrap.Modal(document.getElementById('detailsModal')).show();
            }
        }

        // Fonction pour exporter les données
        function exportData() {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();
            
            doc.text("Liste des Commandes", 14, 15);
            
            doc.autoTable({
                head: [['ID', 'Client', 'Date', 'Total', 'Statut']],
                body: commandesTable.data().toArray().map(row => [
                    row.id,
                    row.client,
                    row.date,
                    row.total,
                    row.statut
                ]),
                startY: 25,
                styles: { fontSize: 8 },
                headStyles: { fillColor: [255, 107, 107] }
            });
            
            doc.save('commandes.pdf');
        }

        // Fonction pour déterminer la classe du badge selon le statut
        function getStatusBadgeClass(status) {
            switch(status) {
                case 'Validée': return 'badge-validee';
                case 'En attente': return 'badge-en-attente';
                case 'Annulée': return 'badge-annulee';
                case 'En préparation': return 'badge-preparee';
                case 'Expédiée': return 'badge-expediee';
                default: return 'badge-secondary';
            }
        }

        // Initialisation
        $(document).ready(function() {
            chargerCommandes();
            
            // Gestion des événements
            $(document).on('click', '.btn-details', function() {
                showDetails($(this).data('id'));
            });
            
            $(document).on('click', '.btn-valider', function() {
                if (confirm("Valider cette commande ?")) {
                    if (changerStatutCommande($(this).data('id'), 'Validée')) {
                        chargerCommandes();
                        alert('Commande validée avec succès');
                    }
                }
            });
            
            $(document).on('click', '.btn-annuler', function() {
                if (confirm("Annuler cette commande ?")) {
                    if (changerStatutCommande($(this).data('id'), 'Annulée')) {
                        chargerCommandes();
                        alert('Commande annulée');
                    }
                }
            });
            
            $(document).on('click', '.btn-preparer', function() {
                if (confirm("Marquer comme 'En préparation' ?")) {
                    if (changerStatutCommande($(this).data('id'), 'En préparation')) {
                        chargerCommandes();
                        alert('Commande en préparation');
                    }
                }
            });
            
            $(document).on('click', '.btn-expedier', function() {
                if (confirm("Marquer comme 'Expédiée' ?")) {
                    if (changerStatutCommande($(this).data('id'), 'Expédiée')) {
                        chargerCommandes();
                        alert('Commande expédiée');
                    }
                }
            });
            
            // Export PDF
            $('#exportBtn').click(exportData);
        });
    </script>
</body>
</html>