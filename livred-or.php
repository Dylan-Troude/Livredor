<?php
session_start();
$host = 'localhost'; // Adresse du serveur MySQL
$dbname = 'livre_dor'; // Nom de ta base de données
$username = 'root'; // Nom d'utilisateur MySQL
$password = ''; // Mot de passe MySQL (laisser vide si pas de mot de passe)

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

$commentsPerPage = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $commentsPerPage;

// Récupération des commentaires avec jointure utilisateur
$sql = "SELECT commentaire.id, commentaire.commentaire, commentaire.date, user.username 
        FROM commentaire 
        JOIN user ON commentaire.id_user = user.id 
        ORDER BY commentaire.date DESC 
        LIMIT :offset, :limit";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->bindParam(':limit', $commentsPerPage, PDO::PARAM_INT);
$stmt->execute();
$comments = $stmt->fetchAll();

// Récupération du nombre total de commentaires
$totalComments = $pdo->query("SELECT COUNT(*) FROM commentaire")->fetchColumn();
$totalPages = ceil($totalComments / $commentsPerPage);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Livre d'or</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Livre d'or</h1>
    
    <?php if (isset($_SESSION['user_id'])): ?>
        <p><a href="comm.php">Ajouter un commentaire</a></p>
    <?php endif; ?>
    
    <?php foreach ($comments as $comment): ?>
        <div class="comment">
            <p><strong>Posté le <?= date('d/m/Y', strtotime($comment['date'])) ?> par <?= htmlspecialchars($comment['username']) ?></strong></p>
            <p><?= nl2br(htmlspecialchars($comment['commentaire'])) ?></p>
        </div>
    <?php endforeach; ?>
    
    <div class="pagination">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="livred-or.php?page=<?= $i ?>" class="<?= ($i == $page) ? 'active' : '' ?>"> <?= $i ?> </a>
        <?php endfor; ?>
    </div>
</body>
</html>