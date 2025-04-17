<?php
session_start();
if (!isset($_SESSION["utilisateur"]) || $_SESSION["type"] !== "admin") {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Espace Administrateur</title>
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
      padding: 2rem;
      border-radius: 10px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }
  </style>
</head>
<body>

<!-- ✅ Menu de navigation admin -->
<nav class="sidebar">
  <a href="admin.php">Accueil admin</a>
  <a href="ajouter_etudiant.php">Ajouter un étudiant</a>
  <a href="ajouter_formation.php">Ajouter une formation</a>
  <a href="ajouter_matiere.php">Ajouter une matière</a>
  <a href="ajouter_note.php">Ajouter une note</a>
  <a href="voir_etudiants.php">Voir les étudiants</a>
  <a href="voir_notes.php">Voir les notes</a>
  <a href="logout.php">Se déconnecter</a>
</nav>

<!-- ✅ Zone principale -->
<div class="main">
  <div class="card">
    <h2>Bienvenue dans l’espace administrateur 👋</h2>
    <p>Utilisez le menu à gauche pour gérer les étudiants, formations, matières et notes.</p>
  </div>
</div>

</body>
</html>
