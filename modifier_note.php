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
$code_matiere = $_GET['code'] ?? '';
$erreur = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nouvelle_note = $_POST["note"];
    $commentaire = $_POST["commentaire"];

    $stmt = $conn->prepare("UPDATE notes SET note = ?, commentaire = ? WHERE matricule_etudiant = ? AND code_matiere = ?");
    $stmt->bind_param("ssss", $nouvelle_note, $commentaire, $matricule, $code_matiere);

    if ($stmt->execute()) {
        $success = "Note modifiée avec succès.";
    } else {
        $erreur = "Erreur lors de la mise à jour.";
    }
}

// Récupérer les infos actuelles
$stmt = $conn->prepare("SELECT * FROM notes WHERE matricule_etudiant = ? AND code_matiere = ?");
$stmt->bind_param("ss", $matricule, $code_matiere);
$stmt->execute();
$note = $stmt->get_result()->fetch_assoc();

// Récupérer le nom de la matière
$matiere = $conn->query("SELECT libelle FROM matieres WHERE code = '$code_matiere'")->fetch_assoc()["libelle"];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier la Note</title>
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

        input, textarea {
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
    <h2>Modifier la note de <?= $matiere ?></h2>

    <?php if ($success): ?>
        <div class="success"><?= $success ?></div>
    <?php elseif ($erreur): ?>
        <div class="erreur"><?= $erreur ?></div>
    <?php endif; ?>

    <form method="post">
        <label>Note</label>
        <input type="text" name="note" value="<?= $note['note'] ?>" required>

        <label>Commentaire</label>
        <textarea name="commentaire" rows="3"><?= $note['commentaire'] ?></textarea>

        <button type="submit">Mettre à jour</button>
    </form>
</div>

</body>
</html>

<?php $conn->close(); ?>
