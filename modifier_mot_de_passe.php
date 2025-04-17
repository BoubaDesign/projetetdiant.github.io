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
$success = "";
$erreur = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ancien = $_POST["ancien"];
    $nouveau = $_POST["nouveau"];
    $confirm = $_POST["confirm"];

    if ($nouveau !== $confirm) {
        $erreur = "Les mots de passe ne correspondent pas.";
    } else {
        $stmt = $conn->prepare("SELECT mot_de_passe FROM etudiants WHERE matricule = ?");
        $stmt->bind_param("s", $matricule);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        if (password_verify($ancien, $result["mot_de_passe"])) {
            $hash = password_hash($nouveau, PASSWORD_DEFAULT);
            $update = $conn->prepare("UPDATE etudiants SET mot_de_passe = ? WHERE matricule = ?");
            $update->bind_param("ss", $hash, $matricule);
            if ($update->execute()) {
                $success = "Mot de passe mis à jour avec succès.";
            } else {
                $erreur = "Erreur lors de la mise à jour.";
            }
        } else {
            $erreur = "Ancien mot de passe incorrect.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier le mot de passe</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .card {
            background-color: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 0 10px #aaa;
            width: 400px;
        }

        label {
            margin-top: 1rem;
            display: block;
        }

        input {
            width: 100%;
            padding: 0.5rem;
            margin-top: 0.3rem;
        }

        button {
            margin-top: 1.5rem;
            width: 100%;
            padding: 0.7rem;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
        }

        .success {
            color: green;
            margin-top: 1rem;
        }

        .erreur {
            color: red;
            margin-top: 1rem;
        }

        a {
            display: block;
            text-align: center;
            margin-top: 1rem;
            color: #007bff;
            text-decoration: none;
        }
    </style>
</head>
<body>

<div class="card">
    <h2>Modifier le mot de passe</h2>

    <?php if ($success): ?>
        <div class="success"><?= $success ?></div>
    <?php elseif ($erreur): ?>
        <div class="erreur"><?= $erreur ?></div>
    <?php endif; ?>

    <form method="post">
        <label>Ancien mot de passe</label>
        <input type="password" name="ancien" required>

        <label>Nouveau mot de passe</label>
        <input type="password" name="nouveau" required>

        <label>Confirmer le nouveau mot de passe</label>
        <input type="password" name="confirm" required>

        <button type="submit">Changer le mot de passe</button>
    </form>

    <a href="etudiant.php">Retour</a>
</div>

</body>
</html>

<?php $conn->close(); ?>
