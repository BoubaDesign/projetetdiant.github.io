<?php
session_start();
if (!isset($_SESSION["utilisateur"]) || $_SESSION["type"] !== "admin") {
    header("Location: login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "root", "notes_etudiants");
if ($conn->connect_error) {
    die("Erreur connexion : " . $conn->connect_error);
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $mot_de_passe = $_POST["mot_de_passe"];
    $hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);

    // Vérification si l'admin existe déjà
    $verif = $conn->prepare("SELECT * FROM administrateurs WHERE email = ?");
    $verif->bind_param("s", $email);
    $verif->execute();
    $verifResult = $verif->get_result();

    if ($verifResult->num_rows > 0) {
        $message = "<span style='color: red;'>Cet administrateur existe déjà.</span>";
    } else {
        $stmt = $conn->prepare("INSERT INTO administrateurs (email, mot_de_passe) VALUES (?, ?)");
        $stmt->bind_param("ss", $email, $hash);

        if ($stmt->execute()) {
            $message = "<span style='color: green;'>Administrateur ajouté avec succès !</span>";
        } else {
            $message = "<span style='color: red;'>Erreur lors de l'ajout.</span>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un administrateur</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            background-color: #f8f9fa;
        }

        .sidebar {
            width: 200px;
            background-color: #0056b3;
            color: white;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            padding-top: 4rem;
        }

        .sidebar a {
            display: block;
            padding: 1rem;
            color: white;
            text-decoration: none;
        }

        .sidebar a:hover {
            background-color: #004080;
        }

        .main {
            margin-left: 220px;
            padding: 2rem;
        }

        .card {
            background-color: white;
            max-width: 500px;
            margin: auto;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        input {
            width: 100%;
            padding: 0.7rem;
            margin-top: 0.5rem;
        }

        button {
            background-color: #007bff;
            color: white;
            padding: 0.7rem;
            border: none;
            border-radius: 5px;
            margin-top: 1rem;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }

        .message {
            margin-top: 1rem;
            text-align: center;
        }
    </style>
</head>
<body>

<!-- ✅ Menu admin -->
<nav class="sidebar">
  <a href="admin.php">Accueil admin</a>
  <a href="ajouter_etudiant.php">Ajouter un étudiant</a>
  <a href="ajouter_formation.php">Ajouter une formation</a>
  <a href="ajouter_matiere.php">Ajouter une matière</a>
  <a href="ajouter_note.php">Ajouter une note</a>
  <a href="voir_etudiants.php">Voir les étudiants</a>
  <a href="voir_notes.php">Voir les notes</a>
  <a href="ajouter_admin.php">Ajouter un admin</a>
  <a href="logout.php">Se déconnecter</a>
</nav>

<!-- ✅ Formulaire -->
<div class="main">
  <div class="card">
    <h2>Ajouter un nouvel administrateur</h2>
    <?= $message ?>
    <form method="post">
        <label>Email</label>
        <input type="email" name="email" required>

        <label>Mot de passe</label>
        <input type="password" name="mot_de_passe" required>

        <button type="submit">Créer le compte admin</button>
    </form>
  </div>
</div>

</body>
</html>

<?php $conn->close(); ?>
