<?php
// Activer les erreurs MySQL pour faciliter le débogage
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// 1. Connexion à la base de données WAMP Server
$servername = "localhost";
$username = "root";  // Par défaut sur WAMP
$password = "";       // Par défaut sur WAMP (vide)
$dbname = "users_db"; // Assurez-vous que cette base de données existe

$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifier la connexion
if ($conn->connect_error) {
    die("Échec de connexion à la base de données : " . $conn->connect_error);
}

// 2. Vérification que toutes les valeurs sont bien reçues
if (isset($_POST['prenom'], $_POST['nom'], $_POST['email'], $_POST['password'], $_POST['gender'], $_POST['role'])) {

    // Récupération et sécurisation des données du formulaire
    $prenom = htmlspecialchars(trim($_POST['prenom']));
    $nom = htmlspecialchars(trim($_POST['nom']));
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = password_hash(trim($_POST['password']), PASSWORD_BCRYPT); // Hash du mot de passe
    $gender = htmlspecialchars($_POST['gender']);
    $role = htmlspecialchars($_POST['role']);

    // Vérifier si l'email est valide
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Erreur : L'adresse email n'est pas valide.");
    }

    // Vérifier si l'email est déjà utilisé
    $checkEmail = $conn->prepare("SELECT email FROM users WHERE email = ?");
    $checkEmail->bind_param("s", $email);
    $checkEmail->execute();
    $checkEmail->store_result();

    if ($checkEmail->num_rows > 0) {
        die("Erreur : Cet email est déjà utilisé.");
    }

    // 3. Insérer les données dans la base
    $sql = "INSERT INTO users (prenom, nom, email, password, gender, role) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssss", $prenom, $nom, $email, $password, $gender, $role);

    if ($stmt->execute()) {
        echo "Inscription réussie ! Vous allez être redirigé...";
        header("refresh:3;url=login.html"); // Redirige après 3 secondes
        exit(); // Empêche l'exécution du code après la redirection
    } else {
        die("Erreur lors de l'inscription : " . $conn->error);
    }

    // 4. Fermer la connexion
    $stmt->close();
    $conn->close();

} else {
    die("Erreur : Tous les champs sont obligatoires.");
}
?>
