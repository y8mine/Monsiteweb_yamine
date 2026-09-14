<?php
require_once 'config.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: index.php');
    exit();
}

$id_projet = intval($_GET['id']);

$query = "SELECT * FROM projet WHERE id_projet = ? AND id_utilisateur = 1";
$stmt = executeQuery($query, array($id_projet));
$projet = fetchOne($stmt);

if (!$projet) {
    header('Location: index.php');
    exit();
}

$query_comp = "SELECT c.nom_competence, c.categorie 
               FROM projet_competence pc 
               JOIN competence c ON pc.id_competence = c.id_competence 
               WHERE pc.id_projet = ?";
$competences = fetchAll(executeQuery($query_comp, array($id_projet)));
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($projet['titre']); ?> - Détails</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="public_style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;800&display=swap" rel="stylesheet">
</head>
<body>

    <nav class="public-nav">
        <div class="nav-content">
            <div class="logo"><a href="index.php" style="color:white; text-decoration:none;">&larr; Retour</a></div>
        </div>
    </nav>

    <header class="project-header-hero" <?php echo !empty($projet['image']) ? 'style="background-image: linear-gradient(rgba(15, 23, 42, 0.9), rgba(15, 23, 42, 0.95)), url(\'' . htmlspecialchars($projet['image']) . '\'); background-size: cover; background-position: center;"' : ''; ?>>
        <div class="container">
            <div class="project-meta-top">
                <span class="project-year"><?php echo date('Y', strtotime($projet['date_debut'])); ?></span>
                <?php if($projet['lien_demo']): ?>
                    <span class="status-badge live">Live Demo</span>
                <?php endif; ?>
            </div>
            <h1><?php echo htmlspecialchars($projet['titre']); ?></h1>
            
            <div class="tech-tags-hero">
                <?php 
                $techs = explode(',', $projet['technologie_utilisee']);
                foreach($techs as $tech): 
                    if(trim($tech)): ?>
                    <span class="tech-tag"><?php echo trim(htmlspecialchars($tech)); ?></span>
                <?php endif; endforeach; ?>
            </div>

            <div class="action-buttons">
                <?php if($projet['lien_code']): ?>
                    <a href="<?php echo htmlspecialchars($projet['lien_code']); ?>" target="_blank" class="btn btn-outline">
                        <i class="fab fa-github"></i> Voir le Code
                    </a>
                <?php endif; ?>
                <?php if($projet['lien_demo']): ?>
                    <a href="<?php echo htmlspecialchars($projet['lien_demo']); ?>" target="_blank" class="btn btn-primary">
                        <i class="fas fa-external-link-alt"></i> Voir la Démo
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <div class="container section-padding">
        <div class="project-grid-layout">
            <div class="project-main-desc">
                <h3>Contexte & Solution</h3>
                <div class="long-description">
                    <?php echo nl2br(htmlspecialchars($projet['description'])); ?>
                </div>
            </div>
            
            <div class="project-sidebar-details">
                <div class="detail-box">
                    <h4>Technologies Clés</h4>
                    <ul class="check-list">
                        <?php foreach($competences as $comp): ?>
                            <li><i class="fas fa-check"></i> <?php echo htmlspecialchars($comp['nom_competence']); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <footer class="main-footer">
        <a href="index.php" class="back-link">Retour à l'accueil</a>
    </footer>

</body>
</html>