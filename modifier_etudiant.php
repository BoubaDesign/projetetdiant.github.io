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

$matricule = $_GET['matricule'] ?? '';
$erreur = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nom = $_POST["nom"];
    $prenom = $_POST["prenom"];
    $adresse = $_POST["adresse"];
    $telephone = $_POST["telephone"];
    $formation_id = intval($_POST["formation_id"]);

    $stmt = $conn->prepare("UPDATE etudiants SET nom = ?, prenom = ?, adresse = ?, telephone = ?, formation_id = ? WHERE matricule = ?");
    $stmt->bind_param("ssssis", $nom, $prenom, $adresse, $telephone, $formation_id, $matricule);
    
    if ($stmt->execute()) {
        $success = "Étudiant modifié avec succès.";
    } else {
        $erreur = "Erreur lors de la modification.";
    }
}

// Récupérer les infos de l’étudiant
$stmt = $conn->prepare("SELECT * FROM etudiants WHERE matricule = ?");
$stmt->bind_param("s", $matricule);
$stmt->execute();
$etudiant = $stmt->get_result()->fetch_assoc();

// Récupérer les formations pour le menu déroulant
$formations = $conn->query("SELECT * FROM formations");
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier Étudiant</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            padding: 2rem;
        }

        .card {
            background-color: white;
            max-width: 500px;
            margin: auto;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        label {
            display: block;
            margin-top: 1rem;
        }

        input, select {
            width: 100%;
            padding: 0.5rem;
            margin-top: 0.5rem;
        }

        button {
            margin-top: 1.5rem;
            padding: 0.7rem;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .success {
            color: green;
            margin-top: 1rem;
        }

        .erreur {
            color: red;
            margin-top: 1rem;
        }
    </style>
</head>
<body>

<div class="card">
    <h2>Modifier l'étudiant</h2>

    <?php if ($success): ?>
        <div class="success"><?= $success ?></div>
    <?php elseif ($erreur): ?>
        <div class="erreur"><?= $erreur ?></div>
    <?php endif; ?>

    <form method="post">
        <label>Nom</label>
        <input type="text" name="nom" value="<?= $etudiant['nom'] ?>" required>

        <label>Prénom</label>
        <input type="text" name="prenom" value="<?= $etudiant['prenom'] ?>" required>

        <label>Adresse</label>
        <input type="text" name="adresse" value="<?= $etudiant['adresse'] ?>" required>

        <label>Téléphone</label>
        <input type="text" name="telephone" value="<?= $etudiant['telephone'] ?>" required>

        <label>Formation</label>
        <select name="formation_id" required>
            <?php while ($f = $formations->fetch_assoc()): ?>
                <option value="<?= $f['id'] ?>" <?= ($f['id'] == $etudiant['formation_id']) ? 'selected' : '' ?>>
                    <?= $f['libelle'] ?>
                </option>
            <?php endwhile; ?>
        </select>

        <button type="submit">Enregistrer les modifications</button>
    </form>
</div>

</body>
</html>

<?php $conn->close(); ?>
