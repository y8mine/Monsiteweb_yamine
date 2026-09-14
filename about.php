<?php

require_once 'config.php';


$user = fetchOne(executeQuery("SELECT * FROM utilisateur WHERE id_utilisateur = 1"));


$experiences = fetchAll(executeQuery("SELECT * FROM experience WHERE id_utilisateur = 1 ORDER BY date_debut DESC"));


$formations = fetchAll(executeQuery("SELECT * FROM formation WHERE id_utilisateur = 1 ORDER BY date_debut DESC"));
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>À Propos - <?php echo htmlspecialchars($user['Yamine'] . ' ' . $user['EL GAZI']); ?></title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="public_style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;800&display=swap" rel="stylesheet">
</head>
<body>

    <nav class="public-nav">
        <div class="nav-content">
            <div class="logo"><a href="index.php" style="color:white; text-decoration:none;"><?php echo strtoupper(substr($user['prenom'], 0, 1) . substr($user['nom'], 0, 1)); ?>.</a></div>
            <ul class="nav-links">
                <li><a href="index.php">Accueil</a></li>
                <li><a href="about.php" class="active">Mon Parcours</a></li>
                <li><a href="index.php#projets">Projets</a></li>
                <li>
                <?php if (!empty($user['url_linkedin'])): ?>
                    <a href="<?php echo htmlspecialchars($user['url_linkedin']); ?>" target="_blank" class="btn-cta">
                    <i class="fab fa-linkedin"></i> LinkedIn
                    </a>
                <?php else: ?>
                    <a href="mailto:<?php echo htmlspecialchars($user['email']); ?>" class="btn-cta">
                    <i class="fas fa-envelope"></i> Me Contacter
                    </a>
                <?php endif; ?>
                </li>
            </ul>
        </div>
    </nav>

    <header class="page-header">
        <div class="container">
            <span class="badge-role">Mon Histoire</span>
            <h1>Plus qu'un simple <span class="highlight">CV</span>.</h1>
        </div>
    </header>

    <div class="container section-padding">
        <div class="profile-layout">
            
            <aside class="profile-sidebar">
                <div class="sidebar-card">
                    <h3><?php echo htmlspecialchars($user['prenom'] . ' ' . $user['nom']); ?></h3>
                    <p class="role-subtitle">Data Scientist</p>
                    <hr class="divider">
                    <p class="bio-text">
                        <?php echo nl2br(htmlspecialchars($user['description'])); ?>
                    </p>
                    <div class="contact-info">
                        <?php if($user['email']): ?>
                            <div class="info-item"><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($user['email']); ?></div>
                        <?php endif; ?>
                        <?php if($user['telephone']): ?>
                            <div class="info-item"><i class="fas fa-phone"></i> <?php echo htmlspecialchars($user['telephone']); ?></div>
                        <?php endif; ?>
                    </div>

                    <div style="margin-top: 25px;">
                        <?php if (!empty($user['cv_pdf'])): ?>
                            <a href="<?php echo htmlspecialchars($user['cv_pdf']); ?>" target="_blank" class="btn btn-primary full-width">
                            <i class="fas fa-download"></i> Télécharger mon CV
                            </a>
                        <?php else: ?>
                            <button class="btn btn-primary full-width" style="opacity: 0.5; cursor: not-allowed;" disabled>
                            <i class="fas fa-file-pdf"></i> CV bientôt disponible
                            </button>
                        <?php endif; ?>
                        <a href="#" class="btn btn-primary full-width"><i class="fas fa-download"></i> Télécharger mon CV</a>
                    </div>
            </aside>

            <main class="profile-content">
                
                <h2 class="section-title-small"><i class="fas fa-briefcase"></i> Expériences Professionnelles</h2>
                <div class="timeline">
                    <?php foreach ($experiences as $exp): ?>
                        <div class="timeline-item">
                            <div class="timeline-dot"></div>
                            <div class="timeline-date">
                                <?php 
                                    $debut = date('Y', strtotime($exp['date_debut']));
                                    $fin = $exp['date_fin'] ? date('Y', strtotime($exp['date_fin'])) : 'Auj.';
                                    echo "$debut - $fin";
                                ?>
                            </div>
                            <div class="timeline-content">
                                <h3><?php echo htmlspecialchars($exp['titre']); ?></h3>
                                <div class="company-name"><?php echo htmlspecialchars($exp['entreprise']); ?></div>
                                <p><?php echo nl2br(htmlspecialchars($exp['description'])); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <h2 class="section-title-small mt-5"><i class="fas fa-graduation-cap"></i> Formation Académique</h2>
                <div class="timeline">
                    <?php foreach ($formations as $formation): ?>
                        <div class="timeline-item">
                            <div class="timeline-dot dot-school"></div>
                            <div class="timeline-date">
                                <?php 
                                    $debut = date('Y', strtotime($formation['date_debut']));
                                    $fin = $formation['date_fin'] ? date('Y', strtotime($formation['date_fin'])) : 'Auj.';
                                    echo "$debut - $fin";
                                ?>
                            </div>
                            <div class="timeline-content">
                                <h3><?php echo htmlspecialchars($formation['diplome']); ?></h3>
                                <div class="company-name"><?php echo htmlspecialchars($formation['ecole']); ?> — <?php echo htmlspecialchars($formation['ville']); ?></div>
                                <p><?php echo nl2br(htmlspecialchars($formation['description'])); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

            </main>
        </div>
    </div>

    <footer class="main-footer">
    <p>© <?php echo date('Y'); ?> <?php echo htmlspecialchars($user['prenom']); ?>. Tous droits réservés.</p>
    
    <div class="admin-link-container">
        <a href="admin.php" class="admin-discrete-link">
            <i class="fas fa-lock"></i> Administration
        </a>
    </div>
    </footer>

</body>
</html>