<?php
session_start();
if (!isset($_SESSION["utilisateur"]) || $_SESSION["type"] !== "admin") {
    header("Location: login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "root", "notes_etudiants");
if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

// Récupérer les formations pour le filtre
$formations = $conn->query("SELECT id, libelle FROM formations");

// Si un filtre est appliqué
$filtre = "";
if (!empty($_GET["formation_id"])) {
    $formation_id = intval($_GET["formation_id"]);
    $filtre = "WHERE e.formation_id = $formation_id";
}

// Récupérer les étudiants
$sql = "SELECT e.matricule, e.nom, e.prenom, e.adresse, e.telephone, f.libelle 
        FROM etudiants e 
        JOIN formations f ON e.formation_id = f.id 
        $filtre 
        ORDER BY e.nom";
$etudiants = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Voir les Étudiants</title>
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

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }

        th, td {
            padding: 0.75rem;
            border: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #f1f1f1;
        }

        select, button {
            padding: 0.5rem;
            margin-top: 1rem;
        }

        .btn-modifier {
            background-color: #ffc107;
            padding: 5px 10px;
            border: none;
            color: white;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn-modifier:hover {
            background-color: #e0a800;
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
  <a href="logout.php">Se déconnecter</a>
</nav>

<!-- ✅ Zone principale -->
<div class="main">
  <div class="card">
    <h2>Liste des étudiants</h2>

    <form method="get">
        <label for="formation_id">Filtrer par formation :</label>
        <select name="formation_id" id="formation_id" onchange="this.form.submit()">
            <option value="">-- Toutes les formations --</option>
            <?php while ($f = $formations->fetch_assoc()): ?>
                <option value="<?= $f['id'] ?>" <?= (isset($_GET['formation_id']) && $_GET['formation_id'] == $f['id']) ? 'selected' : '' ?>>
                    <?= $f['libelle'] ?>
                </option>
            <?php endwhile; ?>
        </select>
    </form>

    <table>
        <thead>
            <tr>
                <th>Matricule</th>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Adresse</th>
                <th>Téléphone</th>
                <th>Formation</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($etudiant = $etudiants->fetch_assoc()): ?>
                <tr>
                    <td><?= $etudiant["matricule"] ?></td>
                    <td><?= $etudiant["nom"] ?></td>
                    <td><?= $etudiant["prenom"] ?></td>
                    <td><?= $etudiant["adresse"] ?></td>
                    <td><?= $etudiant["telephone"] ?></td>
                    <td><?= $etudiant["libelle"] ?></td>
                    <td>
                        <a href="modifier_etudiant.php?matricule=<?= $etudiant["matricule"] ?>" class="btn-modifier">Modifier</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
  </div>
</div>

</body>
</html>

<?php $conn->close(); ?>
