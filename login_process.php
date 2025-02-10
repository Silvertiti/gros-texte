<?php
session_start(); // Démarrer la session pour gérer l'authentification

// 1. Connexion à la base de données
$servername = "localhost";
$username = "root"; // Par défaut sur WAMP
$password = ""; // Par défaut sur WAMP (vide)
$dbname = "users_db"; // Nom de la base de données

$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifier la connexion
if ($conn->connect_error) {
    die("Échec de connexion à la base de données : " . $conn->connect_error);
}

// 2. Vérifier si les champs sont bien remplis
if (isset($_POST['email'], $_POST['password'])) {
    
    // Récupérer et nettoyer les données
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Vérifier si l'utilisateur existe dans la base
    $sql = "SELECT id, prenom, nom, email, password FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // L'utilisateur existe, vérifier le mot de passe
        $stmt->bind_result($id, $prenom, $nom, $email, $hashed_password);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            // Mot de passe correct, démarrer la session
            $_SESSION['user_id'] = $id;
            $_SESSION['user_name'] = $prenom . " " . $nom;
            $_SESSION['user_email'] = $email;

            // Rediriger vers la page d'accueil ou tableau de bord
            header("Location: dashboard.php");
            exit();
        } else {
            // Mauvais mot de passe
            echo "Erreur : Mot de passe incorrect.";
        }
    } else {
        // Aucun utilisateur trouvé avec cet email
        echo "Erreur : Aucun compte trouvé avec cet email.";
    }

    // Fermer la connexion
    $stmt->close();
    $conn->close();
} else {
    echo "Erreur : Tous les champs sont obligatoires.";
}
?>
