
<?php
echo "login.php appelé<br>"; // juste pour voir si la page s’exécute
session_start();

echo "<pre>";
print_r($_POST); // pour afficher les données envoyées par le formulaire
echo "</pre>";


$conn = new mysqli("localhost", "root", "root", "notes_etudiants");
if ($conn->connect_error) {
    die("Erreur connexion : " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $type = $_POST["type_compte"]; // admin ou etudiant
    $identifiant = $_POST["identifiant"];
    $mot_de_passe = $_POST["mot_de_passe"];

    if ($type === "admin") {
        $stmt = $conn->prepare("SELECT * FROM administrateurs WHERE email = ?");
    } else {
        $stmt = $conn->prepare("SELECT * FROM etudiants WHERE matricule = ?");
    }

    $stmt->bind_param("s", $identifiant);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if (password_verify($mot_de_passe, $user["mot_de_passe"])) {
            $_SESSION["utilisateur"] = $identifiant;
            $_SESSION["type"] = $type;

            header("Location: " . ($type === "admin" ? "admin.php" : "etudiant.php"));
            exit();
        }
    }

    echo "<p style='color: red;'>Identifiant ou mot de passe incorrect.</p>";
    echo "<p style='color:green;'>Connexion réussie !</p>";
}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion</title>
    <style>
        input {
    width: 100%;
    margin-bottom: 1rem;
    padding: 0.5rem;
}

        select {
    width: 100%;
    margin-bottom: 1rem;
    padding: 0.5rem;
    font-size: 1rem;
}
        body {
            font-family: Arial, sans-serif;
            background: #f1f1f1;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .login-box {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 0 10px #aaa;
            width: 300px;
        }
        input {
            width: 100%;
            margin-bottom: 1rem;
            padding: 0.5rem;
        }
        button {
            width: 100%;
            background: #007bff;
            color: white;
            padding: 0.7rem;
            border: none;
            border-radius: 5px;
        }
        .erreur {
            color: red;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>Connexion</h2>
        <?php if (!empty($erreur)) echo "<div class='erreur'>$erreur</div>"; ?>
        <form method="post" action="login.php">
    <select name="type_compte" required>
        <option value="admin">Administrateur</option>
        <option value="etudiant">Étudiant</option>
    </select>
    <input type="text" name="identifiant" placeholder="Email ou Matricule" required>
    <input type="password" name="mot_de_passe" placeholder="Mot de passe" required>
    <button type="submit">Se connecter</button>
</form>

    </div>
</body>
</html>
