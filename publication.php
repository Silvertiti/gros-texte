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

// Récupération des offres
$sql = "SELECT id, entreprise, titre, description FROM offres ORDER BY id DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Offres de Stage</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css" />

</head>
<body>
<nav class="navbar navbar-expand-lg">
      <div class="container-fluid">
        <a class="navbar-brand" href="#">GROS TEXTE</a>
        <button
          class="navbar-toggler"
          type="button"
          data-bs-toggle="collapse"
          data-bs-target="#navbarNav"
          aria-controls="navbarNav"
          aria-expanded="false"
          aria-label="Toggle navigation"
        >
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav me-auto">
            <li class="nav-item">
              <a class="nav-link" href="#home">Propostion</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="#services">Services</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="#about">À propos</a>
            </li>
          </ul>
          <ul class="navbar-nav">
            <li class="nav-item">
              <a class="nav-link" href="login.html">Connexion</a>
            </li>
          </ul>
        </div>
      </div>
    </nav>

    <section class="container my-5">
        <h1 class="text-center mb-4" class="">Offres de Stage</h1>
        <div class="col-md-10 mx-auto">
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $titre = !empty($row["titre"]) ? htmlspecialchars($row["titre"]) : "Titre non disponible";
                    $entreprise = !empty($row["entreprise"]) ? htmlspecialchars($row["entreprise"]) : "Entreprise inconnue";
                    $description = !empty($row["description"]) ? nl2br(htmlspecialchars($row["description"])) : "Aucune description disponible.";
                    echo '<div class="card mb-4 shadow-lg p-4">
                            <div class="card-body">
                                <h3 class="card-title fw-bold">' . $titre . '</h3>
                                <h5 class="card-subtitle mb-3 text-muted">' . $entreprise . '</h5>
                                <p class="card-text fs-5">' . $description . '</p>
                                <a href="offre_details.php?id=' . $row["id"] . '" class="btn btn-lg btn-primary">Afficher l\'offre</a>
                            </div>
                        </div>';
                }
            } else {
                echo '<p class="text-center fs-4">Aucune offre de stage disponible.</p>';
            }
            ?>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$conn->close();
?>
