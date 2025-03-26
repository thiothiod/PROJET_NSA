<?php
// Vérification de session et des rôles
if (session_status() === PHP_SESSION_NONE) session_start();

$allowedRoles = ['secretaire', 'admin', 'manager'];
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $allowedRoles)) {
    header('Location: ../../index.php');
    exit();
}

// Configuration
$config = [
    'page_title' => 'Gestion des Commandes',
    'allowed_statuses' => ['En attente', 'Validée', 'Annulée', 'En préparation', 'Expédiée'],
    'default_status' => 'En attente',
    'enable_stats' => true,
    'enable_invoice' => true,
    'delivery_persons' => ['Mamadou Ndiaye', 'Aminata Fall', 'Ibrahima Diop']
];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($config['page_title']) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.css">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f8f9fa;
        }
        .container {
            max-width: 1200px;
            margin-top: 20px;
        }
        .card {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        .card-header {
            background-color: #fff;
            border-bottom: 1px solid rgba(0, 0, 0, 0.125);
            font-weight: 500;
        }
        .badge {
            font-size: 0.85em;
            padding: 5px 10px;
        }
        .no-commands {
            text-align: center;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 8px;
            margin-top: 20px;
        }
        .btn-group .btn {
            margin-right: 5px;
        }
        #deliveryModal .modal-content {
            border-radius: 10px;
        }
        #deliveryModal .form-select, 
        #deliveryModal .form-control {
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }
        #confirmDelivery {
            background-color: #0d6efd;
            border: none;
            padding: 8px 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- En-tête -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><i class="fas fa-clipboard-list me-2"></i><?= htmlspecialchars($config['page_title']) ?></h1>
            <div>
                <span class="me-3">Connecté en tant que: <strong><?= htmlspecialchars($_SESSION['role']) ?></strong></span>
                <a href="../../deconnexion.php" class="btn btn-secondary">
                    <i class="fas fa-sign-out-alt"></i> Déconnexion
                </a>
            </div>
        </div>

        <!-- Statistiques -->
        <?php if ($config['enable_stats']): ?>
        <div class="row mb-4" id="statsSection">
            <!-- Contenu des stats généré par JavaScript -->
        </div>
        <?php endif; ?>

        <!-- Liste des commandes -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-list me-2"></i>Liste des Commandes</span>
                <div>
                    <button id="refreshBtn" class="btn btn-sm btn-light">
                        <i class="fas fa-sync-alt me-1"></i> Actualiser
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div id="commandsContainer">
                    <div class="no-commands">
                        <i class="fas fa-box-open fa-3x mb-3 text-muted"></i>
                        <h4>Aucune commande trouvée</h4>
                        <p class="text-muted">Les commandes apparaîtront ici lorsqu'elles seront disponibles</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modals -->
    <!-- Modal Détails Commande -->
    <div class="modal fade" id="orderDetailsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Détails de la Commande #<span id="orderId"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6>Informations Client</h6>
                            <p><strong>Nom:</strong> THIORO DIENG</p>
                            <p><strong>Email:</strong> thiorodieng@gmail.com</p>
                            <p><strong>Adresse:</strong> SCAT-URBAM</p>
                        </div>
                        <div class="col-md-6">
                            <h6>Informations Commande</h6>
                            <p><strong>Date:</strong> <span id="orderDate"></span></p>
                            <p><strong>Statut:</strong> <span id="orderStatus" class="badge"></span></p>
                            <p><strong>Total:</strong> <span id="orderTotal"></span></p>
                            <p id="deliveryInfo"><strong>Livreur:</strong> <span id="deliveryPerson"></span></p>
                        </div>
                    </div>
                    
                    <h6>Produits Commandés</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Produit</th>
                                    <th>Prix unitaire</th>
                                    <th>Quantité</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody id="orderProducts">
                                <!-- Les produits seront ajoutés ici -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Livraison -->
    <div class="modal fade" id="deliveryModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Organiser la livraison</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="deliveryForm">
                        <input type="hidden" id="deliveryOrderId">
                        <div class="mb-3">
                            <label for="deliveryPerson" class="form-label">Livreur</label>
                            <select class="form-select" id="deliveryPersonSelect" required>
                                <option value="">Sélectionner un livreur</option>
                                <?php foreach ($config['delivery_persons'] as $person): ?>
                                <option value="<?= htmlspecialchars($person) ?>"><?= htmlspecialchars($person) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="deliveryDate" class="form-label">Date prévue de livraison</label>
                            <input type="date" class="form-control" id="deliveryDate" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="button" class="btn btn-primary" id="confirmDelivery">Confirmer la livraison</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Facture -->
    <div class="modal fade" id="invoiceModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Facture #<span id="invoiceNumber"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="invoiceContent">
                    <!-- Contenu de la facture généré par JavaScript -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                    <button type="button" class="btn btn-primary" id="printInvoice">
                        <i class="fas fa-print me-1"></i> Imprimer
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>
    
    <script>
        // Configuration
        const config = {
            enableStats: <?= $config['enable_stats'] ? 'true' : 'false' ?>,
            enableInvoice: <?= $config['enable_invoice'] ? 'true' : 'false' ?>,
            allowedStatuses: <?= json_encode($config['allowed_statuses']) ?>,
            currentUserRole: '<?= $_SESSION['role'] ?>',
            deliveryPersons: <?= json_encode($config['delivery_persons']) ?>
        };

        // Charger les commandes
        function loadCommands() {
            const commands = JSON.parse(localStorage.getItem('commandes')) || [];
            const container = $('#commandsContainer');
            
            if (commands.length === 0) {
                container.html(`
                    <div class="no-commands">
                        <i class="fas fa-box-open fa-3x mb-3 text-muted"></i>
                        <h4>Aucune commande trouvée</h4>
                        <p class="text-muted">Les commandes apparaîtront ici lorsqu'elles seront disponibles</p>
                    </div>
                `);
                return;
            }

            let html = `
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="commandsTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Client</th>
                                <th>Produits</th>
                                <th>Total</th>
                                <th>Date</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
            `;

            commands.forEach((cmd, index) => {
                html += `
                    <tr>
                        <td>${index + 1}</td>
                        <td>THIORO DIENG</td>
                        <td>
                            <ul class="list-unstyled">
                                ${cmd.produits.map(p => `<li>${p.libelle} (x${p.quantite})</li>`).join('')}
                            </ul>
                        </td>
                        <td>${calculateTotal(cmd.produits)} CFA</td>
                        <td>${cmd.date}</td>
                        <td><span class="badge ${getStatusClass(cmd.statut)}">${cmd.statut}</span></td>
                        <td>
                            <div class="btn-group">
                                <button class="btn btn-sm btn-info btn-details" data-id="${index}">
                                    <i class="fas fa-eye"></i>
                                </button>
                                ${getActionButtons(cmd.statut, index)}
                            </div>
                        </td>
                    </tr>
                `;
            });

            html += `
                        </tbody>
                    </table>
                </div>
            `;

            container.html(html);
            $('#commandsTable').DataTable({
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/fr-FR.json'
                }
            });
            attachEventHandlers();
            
            if (config.enableStats) {
                updateStats(commands);
            }
        }

        // Calculer le total d'une commande
        function calculateTotal(products) {
            return products.reduce((total, p) => {
                const prix = parseFloat(p.prix) || 0;
                const quantite = parseInt(p.quantite) || 0;
                return total + (prix * quantite);
            }, 0);
        }

        // Obtenir les classes CSS pour le statut
        function getStatusClass(status) {
            const classes = {
                'En attente': 'bg-warning text-dark',
                'Validée': 'bg-success',
                'Annulée': 'bg-danger',
                'En préparation': 'bg-primary',
                'Expédiée': 'bg-info'
            };
            return classes[status] || 'bg-secondary';
        }

        // Obtenir les boutons d'action selon le statut et le rôle
        function getActionButtons(status, id) {
            let buttons = '';
            
            if (status === 'En attente' && ['secretaire', 'admin'].includes(config.currentUserRole)) {
                buttons += `
                    <button class="btn btn-sm btn-success btn-validate" data-id="${id}">
                        <i class="fas fa-check"></i>
                    </button>
                    <button class="btn btn-sm btn-danger btn-cancel" data-id="${id}">
                        <i class="fas fa-times"></i>
                    </button>
                `;
            }
            
            if (status === 'Validée' && ['admin', 'manager'].includes(config.currentUserRole)) {
                buttons += `
                    <button class="btn btn-sm btn-primary btn-prepare" data-id="${id}">
                        <i class="fas fa-box-open"></i>
                    </button>
                `;
            }
            
            if (status === 'En préparation' && ['admin', 'manager'].includes(config.currentUserRole)) {
                buttons += `
                    <button class="btn btn-sm btn-info btn-deliver" data-id="${id}">
                        <i class="fas fa-truck"></i>
                    </button>
                `;
            }
            
            if (config.enableInvoice) {
                buttons += `
                    <button class="btn btn-sm btn-warning btn-invoice" data-id="${id}">
                        <i class="fas fa-file-invoice"></i>
                    </button>
                `;
            }
            
            return buttons;
        }

        // Mettre à jour les statistiques
        function updateStats(commands) {
            const today = new Date().toLocaleDateString('fr-FR');
            
            // Stats par statut
            const statusCounts = {};
            config.allowedStatuses.forEach(s => statusCounts[s] = 0);
            
            commands.forEach(cmd => {
                statusCounts[cmd.statut]++;
            });
            
            // Stats livraisons du jour
            const todayDeliveries = commands.filter(cmd => 
                cmd.statut === 'Expédiée' && cmd.date === today
            ).length;
            
            const todayOrders = commands.filter(cmd => 
                cmd.date === today
            ).length;
            
            // Mettre à jour l'interface
            $('#statsSection').html(`
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <i class="fas fa-chart-pie me-2"></i>Statut des Commandes
                        </div>
                        <div class="card-body">
                            <canvas id="statusChart" height="200"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <i class="fas fa-truck me-2"></i>Livraisons Aujourd'hui
                        </div>
                        <div class="card-body">
                            <canvas id="deliveryChart" height="200"></canvas>
                        </div>
                    </div>
                </div>
            `);
            
            // Graphique statut
            new Chart(document.getElementById('statusChart'), {
                type: 'doughnut',
                data: {
                    labels: config.allowedStatuses,
                    datasets: [{
                        data: config.allowedStatuses.map(s => statusCounts[s]),
                        backgroundColor: [
                            '#ffc107', '#198754', '#dc3545', '#0d6efd', '#0dcaf0'
                        ]
                    }]
                }
            });
            
            // Graphique livraisons
            new Chart(document.getElementById('deliveryChart'), {
                type: 'bar',
                data: {
                    labels: ['Commandes', 'Livraisons'],
                    datasets: [{
                        label: "Aujourd'hui",
                        data: [todayOrders, todayDeliveries],
                        backgroundColor: ['#0d6efd', '#198754']
                    }]
                }
            });
        }

        // Afficher les détails de la commande
        function showOrderDetails(id) {
            const commands = JSON.parse(localStorage.getItem('commandes')) || [];
            if (id >= 0 && id < commands.length) {
                const cmd = commands[id];
                
                $('#orderId').text(id + 1);
                $('#orderDate').text(cmd.date);
                $('#orderTotal').text(calculateTotal(cmd.produits) + ' CFA');
                
                const statusBadge = $('#orderStatus');
                statusBadge.text(cmd.statut);
                statusBadge.removeClass().addClass('badge ' + getStatusClass(cmd.statut));
                
                if (cmd.statut === 'Expédiée') {
                    $('#deliveryInfo').show();
                    $('#deliveryPerson').text(cmd.livreur || 'Non spécifié');
                } else {
                    $('#deliveryInfo').hide();
                }
                
                let productsHtml = '';
                cmd.produits.forEach(p => {
                    const prix = parseFloat(p.prix) || 0;
                    const quantite = parseInt(p.quantite) || 0;
                    productsHtml += `
                        <tr>
                            <td>${p.libelle}</td>
                            <td>${prix} CFA</td>
                            <td>${quantite}</td>
                            <td>${prix * quantite} CFA</td>
                        </tr>
                    `;
                });
                $('#orderProducts').html(productsHtml);
                
                new bootstrap.Modal('#orderDetailsModal').show();
            }
        }

        // Organiser la livraison
        function organizeDelivery(id) {
            $('#deliveryOrderId').val(id);
            $('#deliveryDate').val(new Date().toISOString().split('T')[0]);
            new bootstrap.Modal('#deliveryModal').show();
        }

        // Générer une facture
        function generateInvoice(id) {
            const commands = JSON.parse(localStorage.getItem('commandes')) || [];
            if (id >= 0 && id < commands.length) {
                const cmd = commands[id];
                const today = new Date().toLocaleDateString('fr-FR');
                
                $('#invoiceNumber').text(`${today.replace(/\//g, '')}-${id + 1}`);
                
                let productsHtml = '';
                let total = 0;
                
                cmd.produits.forEach(p => {
                    const prix = parseFloat(p.prix) || 0;
                    const quantite = parseInt(p.quantite) || 0;
                    const subtotal = prix * quantite;
                    total += subtotal;
                    
                    productsHtml += `
                        <tr>
                            <td>${p.libelle}</td>
                            <td>${prix} CFA</td>
                            <td>${quantite}</td>
                            <td>${subtotal} CFA</td>
                        </tr>
                    `;
                });
                
                $('#invoiceContent').html(`
                    <div class="mb-4">
                        <h4>THIORO DIENG</h4>
                        <p>Email: thiorodieng@gmail.com</p>
                        <p>Adresse: SCAT-URBAM</p>
                    </div>
                    
                    <div class="mb-4">
                        <p><strong>Date:</strong> ${today}</p>
                        <p><strong>Statut:</strong> <span class="badge ${getStatusClass(cmd.statut)}">${cmd.statut}</span></p>
                    </div>
                    
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Produit</th>
                                <th>Prix</th>
                                <th>Quantité</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${productsHtml}
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3">Total</th>
                                <th>${total} CFA</th>
                            </tr>
                        </tfoot>
                    </table>
                `);
                
                new bootstrap.Modal('#invoiceModal').show();
            }
        }

        // Valider une commande
        function validateOrder(id) {
            const commands = JSON.parse(localStorage.getItem('commandes')) || [];
            if (id >= 0 && id < commands.length) {
                commands[id].statut = 'Validée';
                localStorage.setItem('commandes', JSON.stringify(commands));
                loadCommands();
                showAlert('success', 'Commande validée avec succès');
            }
        }

        // Annuler une commande
        function cancelOrder(id) {
            const commands = JSON.parse(localStorage.getItem('commandes')) || [];
            if (id >= 0 && id < commands.length) {
                commands[id].statut = 'Annulée';
                localStorage.setItem('commandes', JSON.stringify(commands));
                loadCommands();
                showAlert('success', 'Commande annulée avec succès');
            }
        }

        // Préparer une commande
        function prepareOrder(id) {
            const commands = JSON.parse(localStorage.getItem('commandes')) || [];
            if (id >= 0 && id < commands.length) {
                commands[id].statut = 'En préparation';
                localStorage.setItem('commandes', JSON.stringify(commands));
                loadCommands();
                showAlert('success', 'Commande en préparation');
            }
        }

        // Attacher les gestionnaires d'événements
        function attachEventHandlers() {
            $('.btn-details').click(function() {
                showOrderDetails($(this).data('id'));
            });
            
            $('.btn-validate').click(function() {
                if (confirm("Valider cette commande ?")) {
                    validateOrder($(this).data('id'));
                }
            });
            
            $('.btn-cancel').click(function() {
                if (confirm("Annuler cette commande ?")) {
                    cancelOrder($(this).data('id'));
                }
            });
            
            $('.btn-prepare').click(function() {
                if (confirm("Mettre cette commande en préparation ?")) {
                    prepareOrder($(this).data('id'));
                }
            });
            
            $('.btn-deliver').click(function() {
                organizeDelivery($(this).data('id'));
            });
            
            $('.btn-invoice').click(function() {
                generateInvoice($(this).data('id'));
            });
            
            $('#confirmDelivery').click(function() {
                const id = $('#deliveryOrderId').val();
                const livreur = $('#deliveryPersonSelect').val();
                const dateLivraison = $('#deliveryDate').val();
                
                if (!livreur) {
                    showAlert('danger', 'Veuillez sélectionner un livreur');
                    return;
                }
                
                const commands = JSON.parse(localStorage.getItem('commandes')) || [];
                if (id >= 0 && id < commands.length) {
                    commands[id].statut = 'Expédiée';
                    commands[id].livreur = livreur;
                    commands[id].dateLivraison = dateLivraison;
                    
                    localStorage.setItem('commandes', JSON.stringify(commands));
                    loadCommands();
                    showAlert('success', 'Livraison confirmée');
                    $('#deliveryModal').modal('hide');
                }
            });
            
            $('#printInvoice').click(function() {
                window.print();
            });
        }

        // Afficher une alerte
        function showAlert(type, message) {
            const alert = $(`
                <div class="alert alert-${type} alert-dismissible fade show position-fixed bottom-0 end-0 m-3">
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `);
            
            $('body').append(alert);
            setTimeout(() => alert.alert('close'), 3000);
        }

        // Initialisation
        $(document).ready(function() {
            loadCommands();
            
            $('#refreshBtn').click(function() {
                loadCommands();
                showAlert('success', 'Liste actualisée');
            });
        });
    </script>
</body>
</html>