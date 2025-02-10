<?php
// Connexion à la base de données
$servername = "localhost";
$username = "root";  // Par défaut sur WAMP
$password = "";      // Par défaut sur WAMP (vide)
$dbname = "users_db"; // Assurez-toi que c'est bien ta base de données

$conn = new mysqli($servername, $username, $password, $dbname);

// Vérification de la connexion
if ($conn->connect_error) {
    die("Échec de connexion : " . $conn->connect_error);
}

// Récupération des offres depuis la base de données
$sql = "SELECT entreprise, titre, description FROM offres ORDER BY id DESC";
$result = $conn->query($sql);

$offres = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $offres[] = $row;
    }
}

// Convertir les données en JSON pour l'affichage dans publication.html
header('Content-Type: application/json');
echo json_encode($offres);

$conn->close();
?>
