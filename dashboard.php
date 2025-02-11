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

// Suppression d'un utilisateur si une demande est faite
if (isset($_POST['delete_user_id'])) {
    $delete_id = $_POST['delete_user_id'];
    $sql = "DELETE FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $stmt->close();
    header("Location: dashboard.php?page=categorie1"); // Rafraîchir la page
    exit();
}

// Récupérer tous les utilisateurs de la table users
$users = [];
$sql = "SELECT id, prenom, nom, role FROM users";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}

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
    <style>
        body {
            background-color: #f8f9fa;
        }
        .sidebar {
            background-color: #343a40;
            color: white;
            height: 100vh;
            padding: 20px;
        }
        .sidebar a {
            color: #ffc107;
            font-weight: bold;
        }
        .card {
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold text-warning" href="dashboard.php">Dashboard</a>
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link text-warning" href="logout.php">Déconnexion</a></li>
            </ul>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-3 sidebar">
                <h4>Catégories</h4>
                <ul class="nav flex-column">
                    <li class="nav-item"><a class="nav-link" href="dashboard.php?page=accueil">Accueil</a></li>
                    <?php if ($user['role'] === 'Employeur') : ?>
                        <li class="nav-item"><a class="nav-link" href="dashboard.php?page=categorie1">Gérer les étudiants</a></li>
                    <?php endif; ?>
                    <li class="nav-item"><a class="nav-link" href="dashboard.php?page=categorie2">Catégorie 2</a></li>
                    <li class="nav-item"><a class="nav-link" href="dashboard.php?page=categorie3">Catégorie 3</a></li>
                    <li class="nav-item"><a class="nav-link" href="dashboard.php?page=categorie4">Catégorie 4</a></li>
                </ul>
            </div>

            <div class="col-md-9 d-flex justify-content-center align-items-center" style="height: 100vh;">
                <div class="card p-5 w-75">
                    <div id="mainContent" class="text-center">
                        <?php
                        if ($page == 'categorie1') {
                            echo "<h2 class='text-dark'>Liste des Utilisateurs</h2>";
                            if (empty($users)) {
                                echo "<p class='text-danger'>Aucun utilisateur trouvé.</p>";
                            } else {
                                echo "<div class='table-responsive'><table class='table table-striped table-hover'><thead class='table-dark'><tr><th>Prénom</th><th>Nom</th><th>Rôle</th><th>Action</th></tr></thead><tbody>";
                                foreach ($users as $userItem) {
                                    echo "<tr><td>" . htmlspecialchars($userItem['prenom']) . "</td><td>" . htmlspecialchars($userItem['nom']) . "</td><td>" . htmlspecialchars($userItem['role']) . "</td>";
                                    echo "<td>
                                            <form method='POST' style='display:inline;'>
                                                <input type='hidden' name='delete_user_id' value='" . $userItem['id'] . "'>
                                                <button type='submit' class='btn btn-danger btn-sm'>Supprimer</button>
                                            </form>
                                          </td></tr>";
                                }
                                echo "</tbody></table></div>";
                            }
                        }
                        ?>
                    <button type='submit' class='btn btn-danger btn-sm'>Ajouter des Utilisateurs</button>

                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
