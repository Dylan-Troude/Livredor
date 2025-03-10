<?php
session_start();
$host = 'localhost';
$dbname = 'livre_dor';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!empty($_POST['commentaire'])) {
        $commentaire = htmlspecialchars(trim($_POST['commentaire']), ENT_QUOTES, 'UTF-8');
        $user_id = $_SESSION['user_id'];
        
        // Insérer le commentaire dans la base de données
        $sql = "INSERT INTO commentaire (commentaire, id_user, date) VALUES (:commentaire, :id_user, NOW())";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':commentaire', $commentaire, PDO::PARAM_STR);
        $stmt->bindParam(':id_user', $user_id, PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            header("Location: livred-or.php");
            exit();
        } else {
            $error = "Une erreur est survenue lors de l'ajout du commentaire.";
        }
    } else {
        $error = "Veuillez entrer un commentaire.";
    }
}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un commentaire</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Ajouter un commentaire</h1>
    
    <?php if (isset($error)): ?>
        <p style="color: red;"> <?= $error ?> </p>
    <?php endif; ?>
    
    <form action="comm.php" method="POST">
        <textarea name="commentaire" rows="4" cols="50" required></textarea><br>
        <button type="submit">Envoyer</button>
    </form>
    
    <p><a href="livred-or.php">Retour au livre d'or</a></p>
</body>
</html>
