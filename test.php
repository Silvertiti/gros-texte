<?php
session_start();

// Connexion à la base de données
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "users_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Échec de connexion : " . $conn->connect_error);
}

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

// Récupérer les informations de l'utilisateur connecté
$user_id = $_SESSION['user_id'];
$sql = "SELECT prenom, nom, role FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Suppression d'un utilisateur
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_user_id'])) {
    $delete_id = $_POST['delete_user_id'];
    $sql = "DELETE FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $stmt->close();
    header("Location: dashboard.php?page=utilisateurs");
    exit();
}

// Ajout d'un utilisateur
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_user'])) {
    $prenom = trim($_POST['prenom']);
    $nom = trim($_POST['nom']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $password_hashed = password_hash($password, PASSWORD_DEFAULT);

    if (!empty($prenom) && !empty($nom) && !empty($email) && !empty($password)) {
        $sql = "INSERT INTO users (prenom, nom, email, password, role) VALUES (?, ?, ?, ?, 'Étudiant')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $prenom, $nom, $email, $password_hashed);
        if ($stmt->execute()) {
            header("Location: dashboard.php?page=utilisateurs");
            exit();
        } else {
            echo "Erreur SQL : " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "<script>alert('Veuillez remplir tous les champs.');</script>";
    }
}

// Récupération des utilisateurs
$users = [];
$sql = "SELECT id, prenom, nom, email, role FROM users";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}

$conn->close();

// Déterminer la page active
$page = isset($_GET['page']) ? $_GET['page'] : 'accueil';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
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
            display: block;
            margin-bottom: 15px;
            text-decoration: none;
        }
        .sidebar a:hover {
            color: #ffffff;
        }
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.4);
            justify-content: center;
            align-items: center;
        }
        .modal-content {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            width: 350px;
            text-align: center;
        }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <!-- Barre latérale -->
        <div class="col-md-3 sidebar">
            <h4>Bienvenue, <?= htmlspecialchars($user['prenom']) ?> <?= htmlspecialchars($user['nom']) ?></h4>
            <a href="dashboard.php?page=accueil">🏠 Accueil</a>
            <a href="dashboard.php?page=utilisateurs">👥 Gestion des Utilisateurs</a>
            <a href="dashboard.php?page=statistiques">📊 Statistiques</a>
            <a href="logout.php" class="text-danger">🚪 Déconnexion</a>
        </div>

        <!-- Contenu principal -->
        <div class="col-md-9 d-flex justify-content-center align-items-center" style="height: 100vh;">
            <div class="card p-5 w-75">
                <div id="mainContent" class="text-center">
                    <?php
                    if ($page == 'accueil') {
                        echo "<h2 class='text-dark'>🏠 Bienvenue sur le Dashboard</h2>";
                    } elseif ($page == 'utilisateurs') {
                        echo "<h2 class='text-dark'>👥 Gestion des Utilisateurs</h2>";
                        if (empty($users)) {
                            echo "<p class='text-danger'>Aucun utilisateur trouvé.</p>";
                        } else {
                            echo "<table class='table table-striped table-hover'>
                                    <thead class='table-dark'>
                                        <tr>
                                            <th>Prénom</th>
                                            <th>Nom</th>
                                            <th>Email</th>
                                            <th>Rôle</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>";
                            foreach ($users as $userItem) {
                                echo "<tr>
                                        <td>" . htmlspecialchars($userItem['prenom']) . "</td>
                                        <td>" . htmlspecialchars($userItem['nom']) . "</td>
                                        <td>" . htmlspecialchars($userItem['email']) . "</td>
                                        <td>" . htmlspecialchars($userItem['role']) . "</td>
                                        <td>
                                            <form method='POST' style='display:inline;'>
                                                <input type='hidden' name='delete_user_id' value='" . $userItem['id'] . "'>
                                                <button type='submit' class='btn btn-danger btn-sm'>Supprimer</button>
                                            </form>
                                        </td>
                                      </tr>";
                            }
                            echo "</tbody></table>";
                        }
                        echo "<button class='btn btn-success mt-3' onclick='openModal()'>Ajouter un Utilisateur</button>";
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Ajout Utilisateur -->
<div id="userModal" class="modal">
    <div class="modal-content">
        <h3>Ajouter un utilisateur</h3>
        <form method="POST" action="">
            <input type="text" name="prenom" class="form-control" placeholder="Prénom" required>
            <input type="text" name="nom" class="form-control" placeholder="Nom" required>
            <input type="email" name="email" class="form-control" placeholder="Email" required>
            <input type="password" name="password" class="form-control" placeholder="Mot de passe" required>
            <button type="submit" class="btn btn-primary">Ajouter</button>
            <button type="button" class="btn btn-secondary" onclick="closeModal()">Annuler</button>
        </form>
    </div>
</div>

<script>
    function openModal() { document.getElementById('userModal').style.display = 'flex'; }
    function closeModal() { document.getElementById('userModal').style.display = 'none'; }
</script>

</body>
</html>
