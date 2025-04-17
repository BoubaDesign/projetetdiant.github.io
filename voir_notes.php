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

// Liste des étudiants pour le filtre
$etudiants = $conn->query("SELECT matricule, nom, prenom FROM etudiants");

$filtre = "";
if (!empty($_GET["matricule"])) {
    $matricule = $conn->real_escape_string($_GET["matricule"]);
    $filtre = "WHERE n.matricule_etudiant = '$matricule'";
}

// Requête pour afficher les notes
$sql = "SELECT n.matricule_etudiant, e.nom, e.prenom, n.code_matiere, m.libelle, n.note, n.commentaire
        FROM notes n
        JOIN matieres m ON n.code_matiere = m.code
        JOIN etudiants e ON n.matricule_etudiant = e.matricule
        $filtre
        ORDER BY e.nom, m.libelle";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Voir les Notes</title>
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

        select {
            padding: 0.5rem;
            margin-bottom: 1rem;
            width: 100%;
        }

        .btn-modifier {
            background-color: #ffc107;
            padding: 5px 10px;
            border: none;
            color: white;
            border-radius: 5px;
            text-decoration: none;
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
    <h2>Liste des notes</h2>

    <form method="get">
        <label for="matricule">Filtrer par étudiant :</label>
        <select name="matricule" onchange="this.form.submit()">
            <option value="">-- Tous les étudiants --</option>
            <?php while ($e = $etudiants->fetch_assoc()): ?>
                <option value="<?= $e["matricule"] ?>" <?= (isset($_GET["matricule"]) && $_GET["matricule"] == $e["matricule"]) ? 'selected' : '' ?>>
                    <?= $e["prenom"] . " " . $e["nom"] . " (" . $e["matricule"] . ")" ?>
                </option>
            <?php endwhile; ?>
        </select>
    </form>

    <table>
        <thead>
            <tr>
                <th>Étudiant</th>
                <th>Matière</th>
                <th>Note</th>
                <th>Commentaire</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($note = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $note["prenom"] . " " . $note["nom"] ?> (<?= $note["matricule_etudiant"] ?>)</td>
                    <td><?= $note["libelle"] ?></td>
                    <td><?= $note["note"] ?></td>
                    <td><?= $note["commentaire"] ?></td>
                    <td>
                        <a class="btn-modifier" href="modifier_note.php?matricule=<?= $note["matricule_etudiant"] ?>&code=<?= $note["code_matiere"] ?>">Modifier</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
  </div>
</div>

</body>
</html>
<a href="modifier_note.php?matricule=<?= $etudiant["matricule"] ?>&code=<?= $note["code_matiere"] ?>">Modifier</a>
<?php $conn->close(); ?>
