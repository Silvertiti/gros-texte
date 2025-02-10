<?php
// Connexion à la base de données
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "users_db";

$conn = new mysqli($servername, $username, $password, $dbname);

// Vérification de la connexion
if ($conn->connect_error) {
    die("Echec de connexion : " . $conn->connect_error);
}

// Forcer l'encodage UTF-8 pour corriger les caractères spéciaux
$conn->set_charset("utf8mb4");

// Vérifier si un ID est passé dans l'URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("ID de l'offre non fourni.");
}

$id = intval($_GET['id']);
$sql = "SELECT entreprise, titre, description FROM offres WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

// Vérifier si une offre correspond
if ($result->num_rows == 0) {
    die("Aucune offre trouvée.");
}
$offre = $result->fetch_assoc();
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails de l'offre</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="publication.php">Retour aux Offres</a>
        </div>
    </nav>

    <section class="container my-5">
        <div class="col-md-8 mx-auto">
            <div class="card p-4 shadow-lg">
                <div class="card-body">
                    <h1 class="card-title fw-bold"><?php echo htmlspecialchars($offre['titre']); ?></h1>
                    <h4 class="card-subtitle mb-3 text-muted"><?php echo htmlspecialchars($offre['entreprise']); ?></h4>
                    <p class="card-text fs-5"><?php echo nl2br(htmlspecialchars($offre['description'])); ?></p>
                    <a href="#" class="btn btn-primary">Postuler</a>
                </div>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
