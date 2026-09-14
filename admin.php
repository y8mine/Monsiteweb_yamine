<?php
require_once 'config.php';
checkAdmin(); 


$nb_projets = fetchOne(executeQuery("SELECT COUNT(*) as c FROM projet WHERE id_utilisateur = 1"))['c'];


$nb_exp = fetchOne(executeQuery("SELECT COUNT(*) as c FROM experience WHERE id_utilisateur = 1"))['c'];


$nb_skills = fetchOne(executeQuery("SELECT COUNT(*) as c FROM competence"))['c'];


$first_exp = fetchOne(executeQuery("SELECT MIN(date_debut) as d FROM experience WHERE id_utilisateur = 1"))['d'];
$years_exp = 0;
if ($first_exp) {
    $date1 = new DateTime($first_exp);
    $date2 = new DateTime(); 
    $interval = $date1->diff($date2);
    $years_exp = $interval->y;
}

$db_status_label = '<span style="color:#10b981; font-weight:bold; font-size:0.9rem; background:rgba(16, 185, 129, 0.1); padding:5px 15px; border-radius:20px; border:1px solid #10b981; display:inline-flex; align-items:center; gap:8px;">
                        <i class="fas fa-database"></i> SQL Connecté
                    </span>';

$page = 'admin.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Data Scientist</title>
    <link rel="stylesheet" href="style.css?v=3">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

    <header class="top-navbar">
        <div class="navbar-container">
            <div class="brand">ADMIN <span style="font-size:0.7em; opacity:0.7;">// DASHBOARD</span></div>
            
            <nav class="main-nav">
                <a href="profil.php" class="nav-link"><i class="fas fa-user"></i> Profil</a>
                <a href="formations.php" class="nav-link"><i class="fas fa-graduation-cap"></i> Formations</a>
                <a href="experiences.php" class="nav-link"><i class="fas fa-briefcase"></i> Expériences</a>
                <a href="projets.php" class="nav-link"><i class="fas fa-folder"></i> Projets</a>
                <a href="competences.php" class="nav-link"><i class="fas fa-trophy"></i> Compétences</a>
            </nav>

            <div class="user-actions">
                <a href="index.php" target="portfolio_public" class="btn-site">
                    <i class="fas fa-eye"></i> Voir le site
                </a>
                <a href="logout.php" class="btn-dashboard" style="background-color: #333;">
                    <i class="fas fa-sign-out-alt"></i> Déconnexion
                </a>
            </div>
        </div>
    </header>

    <main class="main-content">
        <div class="welcome-banner" style="text-align:left; margin-bottom:40px;">
            <h2><i class="fas fa-chart-line"></i> Vue d'ensemble des Données</h2>
            <p style="color:#888;">Bienvenue dans votre centre de contrôle.</p>
        </div>

        <div class="dashboard-grid">
            
            <div class="stat-card card-blue">
                <div class="stat-icon">
                    <i class="fas fa-rocket"></i>
                    <i class="fas fa-ellipsis-h" style="font-size: 1rem; opacity: 0.5;"></i>
                </div>
                <div>
                    <div class="stat-number"><?php echo $nb_projets; ?></div>
                    <div class="stat-label">Projets Réalisés</div>
                </div>
            </div>

            <div class="stat-card card-purple">
                <div class="stat-icon">
                    <i class="fas fa-layer-group"></i>
                </div>
                <div>
                    <div class="stat-number"><?php echo $nb_skills; ?></div>
                    <div class="stat-label">Compétences / Stack</div>
                </div>
            </div>

            <div class="stat-card card-orange">
                <div class="stat-icon">
                    <i class="fas fa-briefcase"></i>
                </div>
                <div>
                    <div class="stat-number"><?php echo $nb_exp; ?></div>
                    <div class="stat-label">Postes Occupés</div>
                </div>
            </div>

            <div class="stat-card card-green">
                <div class="stat-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div>
                    <div class="stat-number"><?php echo $years_exp; ?> <span style="font-size:1rem;">ans</span></div>
                    <div class="stat-label">Expérience Cumulée</div>
                </div>
            </div>

        </div>

    </main>
</body>
</html>