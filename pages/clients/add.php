<?php
// filepath: c:\xampp\htdocs\GESTION_COMMANDE_FACTURATION\pages\clients\add.php
// Inclure le fichier de connexion à la base de données
include_once __DIR__ . '/../../database.php';

// Récupérer les produits pour afficher dans la liste déroulante
$sql_products = "SELECT id, libelle FROM produit";
$products_result = mysqli_query($connexion, $sql_products);

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['nom']) && isset($_POST['prenom']) && isset($_POST['adresse']) && isset($_POST['telephone']) && isset($_POST['login']) && isset($_POST['password']) && isset($_POST['produit_id'])) {
        // Récupérer et sécuriser les entrées
        $nom = mysqli_real_escape_string($connexion, $_POST['nom']);
        $prenom = mysqli_real_escape_string($connexion, $_POST['prenom']);
        $adresse = mysqli_real_escape_string($connexion, $_POST['adresse']);
        $telephone = mysqli_real_escape_string($connexion, $_POST['telephone']);
        $login = mysqli_real_escape_string($connexion, $_POST['login']);
        $password = mysqli_real_escape_string($connexion, $_POST['password']);
        $produit_id = intval($_POST['produit_id']);  // S'assurer que produit_id est un entier

        // Gestion de l'upload de la photo
        $photoName = null;
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $photoTmp = $_FILES['photo']['tmp_name'];
            $photoName = time() . '_' . $_FILES['photo']['name'];
            $destination = __DIR__ . '/../../uploads/' . $photoName;

            if (!move_uploaded_file($photoTmp, $destination)) {
                $photoName = null;
                echo "<div class='alert alert-danger'>Erreur lors de l'upload de la photo.</div>";
            }
        }

        // Insérer dans la base de données
        $sql_insert = "INSERT INTO client(nom, prenom, adresse, telephone, produit_id, login, password, photo) 
                       VALUES('$nom', '$prenom', '$adresse', '$telephone', '$produit_id', '$login', '$password', '$photoName')";
        if (mysqli_query($connexion, $sql_insert)) {
            header('Location: index.php');  // Rediriger vers la liste des clients
            exit;  // Assurer que le script s'arrête après la redirection
        } else {
            echo "<div class='alert alert-danger'>Erreur lors de l'ajout du client : " . mysqli_error($connexion) . "</div>";
        }
    }
}
?>

<div class="container" style="background-color: #f1f1f1;">
    <h1 class="text-center mt-5">Ajouter un client</h1>
    <form action="#" method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="nom" class="form-label">Nom</label>
            <input type="text" name="nom" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="prenom" class="form-label">Prénom</label>
            <input type="text" name="prenom" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="adresse" class="form-label">Adresse</label>
            <input type="text" name="adresse" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="telephone" class="form-label">Téléphone</label>
            <input type="text" name="telephone" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="login" class="form-label">Login</label>
            <input type="text" name="login" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Mot de passe</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="produit_id" class="form-label">Produit</label>
            <select class="form-control" name="produit_id" id="produit_id" required>
                <?php while($product = mysqli_fetch_assoc($products_result)){ ?>
                    <option value="<?= $product['id'] ?>"><?= htmlspecialchars($product['libelle']) ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="photo" class="form-label">Photo</label>
            <input type="file" name="photo" class="form-control" accept="image/*" required>
        </div>
        <div class="mt-3">
            <button type="submit" class="btn btn-success">Valider</button>
            <button type="reset" class="btn btn-danger">Annuler</button>
        </div>
    </form>
</div>