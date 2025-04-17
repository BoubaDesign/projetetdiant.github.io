<?php
// Connexion à la base
$conn = new mysqli("localhost", "root", "root", "notes_etudiants");
if ($conn->connect_error) {
    die("Erreur : " . $conn->connect_error);
}
// Exemple d'ajout d'un étudiant
$matricule = "ET1234";
$nom = "Dupont";
$prenom = "Alice";
$adresse = "10 rue des étudiants";
$telephone = "0601020304";
$mdp_etudiant = password_hash("etudiant123", PASSWORD_DEFAULT); // hachage du mot de passe
$formation_id = 1; // ID d'une formation existante

$stmt = $conn->prepare("INSERT INTO etudiants (matricule, nom, prenom, adresse, telephone, mot_de_passe, formation_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssssi", $matricule, $nom, $prenom, $adresse, $telephone, $mdp_etudiant, $formation_id);
$stmt->execute();

// Exemple d'ajout d'un administrateur
$email_admin = "admin@site.com";
$mdp_admin = password_hash("admin123", PASSWORD_DEFAULT);

$stmt = $conn->prepare("INSERT INTO administrateurs (email, mot_de_passe) VALUES (?, ?)");
$stmt->bind_param("ss", $email_admin, $mdp_admin);
$stmt->execute();

echo "Ajout effectué avec succès !";
$conn->close();
?>
