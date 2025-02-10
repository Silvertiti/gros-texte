<?php
session_start();

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

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

// Récupérer les informations de l'utilisateur
$user_id = $_SESSION['user_id'];
$sql = "SELECT prenom, nom, role FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();
$conn->close();

// Déterminer la page à afficher
$page = isset($_GET['page']) ? $_GET['page'] : 'accueil';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css" />
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="dashboard.php">Dashboard</a>
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="logout.php">Déconnexion</a></li>
            </ul>
        </div>
    </nav>

    <section class="container-fluid my-5">
        <div class="row">
            <!-- Menu latéral -->
            <div class="col-md-3 bg-light p-4" style="height: 80vh;">
                <h4>Catégories</h4>
                <ul class="nav flex-column">
                    <li class="nav-item"><a class="nav-link" href="dashboard.php?page=accueil">Accueil</a></li>
                    <li class="nav-item"><a class="nav-link" href="dashboard.php?page=categorie1">Catégorie 1</a></li>
                    <li class="nav-item"><a class="nav-link" href="dashboard.php?page=categorie2">Catégorie 2</a></li>
                    <li class="nav-item"><a class="nav-link" href="dashboard.php?page=categorie3">Catégorie 3</a></li>
                    <li class="nav-item"><a class="nav-link" href="dashboard.php?page=categorie4">Catégorie 4</a></li>
                </ul>
            </div>

            <!-- Contenu principal -->
            <div class="col-md-9 d-flex justify-content-center align-items-center" style="height: 80vh;">
                <div class="card shadow-lg p-5" style="width: 80%; height: 70vh; display: flex; justify-content: center; align-items: center;">
                    <div id="mainContent" class="text-center">
                        <?php
                        if ($page == 'accueil') {
                            echo "<h1>Bienvenue, " . htmlspecialchars($user['prenom'] . ' ' . $user['nom']) . " !</h1>";
                            if ($user['role'] === 'Employeur') {
                                echo "<h2 class='mt-4'>CESI</h2>";
                            }
                            echo "<p class='mt-3'>Sélectionnez une catégorie sur le côté pour afficher plus d'options.</p>";
                        } elseif ($page == 'categorie1') {
                            echo "<h2>Contenu unique de la Catégorie 1</h2><p>Description et détails ici.</p>";
                        } elseif ($page == 'categorie2') {
                            echo "<h2>Contenu unique de la Catégorie 2</h2><p>Description et détails ici.</p>";
                        } elseif ($page == 'categorie3') {
                            echo "<h2>Contenu unique de la Catégorie 3</h2><p>Description et détails ici.</p>";
                        } elseif ($page == 'categorie4') {
                            echo "<h2>Contenu unique de la Catégorie 4</h2><p>Description et détails ici.</p>";
                        } else {
                            echo "<h2>Page introuvable</h2>";
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
