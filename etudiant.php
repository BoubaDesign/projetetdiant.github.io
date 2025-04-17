<!-- etudiant.php — Espace étudiant -->
<?php
session_start();
if (!isset($_SESSION["utilisateur"]) || $_SESSION["type"] !== "etudiant") {
    header("Location: login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "root", "notes_etudiants");
if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

$matricule = $_SESSION["utilisateur"];
$etudiant = $conn->query("SELECT * FROM etudiants WHERE matricule = '$matricule'")->fetch_assoc();
$notes = $conn->query("SELECT m.libelle, n.note, n.commentaire FROM notes n JOIN matieres m ON n.code_matiere = m.code WHERE n.matricule_etudiant = '$matricule'");
?>

<h2>Bienvenue, <?= $etudiant['prenom'] . " " . $etudiant['nom'] ?> (<?= $matricule ?>)</h2>
<h3>Informations personnelles</h3>
<p><strong>Adresse :</strong> <?= $etudiant['adresse'] ?></p>
<p><strong>Téléphone :</strong> <?= $etudiant['telephone'] ?></p>

<h3>Notes</h3>
<table border="1">
  <tr>
    <th>Matière</th>
    <th>Note</th>
    <th>Commentaire</th>
  </tr>
  <?php while ($note = $notes->fetch_assoc()): ?>
    <tr>
      <td><?= $note['libelle'] ?></td>
      <td><?= $note['note'] ?></td>
      <td><?= $note['commentaire'] ?></td>
    </tr>
  <?php endwhile; ?>
</table>

<hr>
<a href="modifier_mot_de_passe.php">Modifier mon mot de passe</a>
<a href="logout.php">Se déconnecter</a>

<?php $conn->close(); ?>