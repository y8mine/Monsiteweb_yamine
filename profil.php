<?php
require_once 'config.php';
checkAdmin();

$user = fetchOne(executeQuery("SELECT * FROM utilisateur WHERE id_utilisateur = 1"));

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $nom = clean_input($_POST['nom']);
    $prenom = clean_input($_POST['prenom']);
    $email = clean_input($_POST['email']);
    $telephone = clean_input($_POST['telephone']);
    $description = clean_input($_POST['description']);
    $url_linkedin = isset($_POST['url_linkedin']) ? clean_input($_POST['url_linkedin']) : '';

    $query = "UPDATE utilisateur 
              SET nom = ?, prenom = ?, email = ?, telephone = ?, description = ?, url_linkedin = ? 
              WHERE id_utilisateur = 1";
    executeNonQuery($query, array($nom, $prenom, $email, $telephone, $description, $url_linkedin));

    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] == 0) {
        $uploadDir = 'uploads/users/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
        $filename = 'avatar_' . time() . '.' . pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
        if (move_uploaded_file($_FILES['avatar']['tmp_name'], $uploadDir . $filename)) {
            executeNonQuery("UPDATE utilisateur SET avatar = ? WHERE id_utilisateur = 1", [$uploadDir . $filename]);
        }
    }

    if (isset($_FILES['cv_pdf']) && $_FILES['cv_pdf']['error'] == 0) {
        $uploadDir = 'uploads/docs/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
        $filename = 'cv_' . time() . '.' . pathinfo($_FILES['cv_pdf']['name'], PATHINFO_EXTENSION);
        if (move_uploaded_file($_FILES['cv_pdf']['tmp_name'], $uploadDir . $filename)) {
            executeNonQuery("UPDATE utilisateur SET cv_pdf = ? WHERE id_utilisateur = 1", [$uploadDir . $filename]);
        }
    }
    
    setFlashMessage('success', 'Profil mis à jour avec succès !');
    redirect('profil.php');
}

$page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon Profil</title>
    <link rel="stylesheet" href="style.css?v=8">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .current-avatar {
            width: 100px; height: 100px; border-radius: 50%; object-fit: cover;
            margin-top: 10px; border: 3px solid var(--primary-color);
            transition: transform 0.3s; cursor: pointer;
        }
        .current-avatar:hover { transform: scale(1.1); box-shadow: 0 0 15px rgba(59, 130, 246, 0.5); }
    </style>
</head>
<body>

    <header class="top-navbar">
        <div class="navbar-container">
            <div class="brand">ADMIN</div>
            <nav class="main-nav">
                <a href="profil.php" class="nav-link active"><i class="fas fa-user"></i> Profil</a>
                <a href="formations.php" class="nav-link"><i class="fas fa-graduation-cap"></i> Formations</a>
                <a href="experiences.php" class="nav-link"><i class="fas fa-briefcase"></i> Expériences</a>
                <a href="projets.php" class="nav-link"><i class="fas fa-folder"></i> Projets</a>
                <a href="competences.php" class="nav-link"><i class="fas fa-trophy"></i> Compétences</a>
            </nav>
            <div class="user-actions">
                <a href="index.php" target="portfolio_public" class="btn-site"><i class="fas fa-eye"></i> Voir le site</a>
                <a href="logout.php" class="btn-dashboard"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
            </div>
        </div>
    </header>

    <main class="main-content">
        <div class="welcome-banner" style="text-align: center;">
            <h2><i class="fas fa-user-edit"></i> Éditer mes informations</h2>
        </div>

        <?php if ($flash = getFlashMessage()): ?>
            <div class="flash-message flash-<?php echo $flash['type']; ?>">
                <?php echo $flash['message']; ?>
            </div>
        <?php endif; ?>

        <section class="card">
            <h3><i class="fas fa-id-card"></i> Informations Personnelles</h3>
            
            <form method="POST" action="" enctype="multipart/form-data">
                
                <div class="form-row">
                    <div class="form-group half-width">
                        <label for="nom">Nom :</label>
                        <input type="text" id="nom" name="nom" class="form-control" value="<?php echo htmlspecialchars($user['nom'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group half-width">
                        <label for="prenom">Prénom :</label>
                        <input type="text" id="prenom" name="prenom" class="form-control" value="<?php echo htmlspecialchars($user['prenom'] ?? ''); ?>" required>
                    </div>
                </div>

                <div class="form-row upload-zone" style="padding: 20px; border-radius: 8px; margin-bottom: 20px;">
                    <div class="form-group half-width">
                        <label><i class="fas fa-camera"></i> Photo de profil :</label>
                        <input type="file" name="avatar" class="form-control" accept="image/*">
                        
                        <?php if(!empty($user['avatar'])): ?>
                            <div style="margin-top: 10px;">
                                <a href="<?php echo htmlspecialchars($user['avatar']); ?>" target="_blank" title="Cliquez pour agrandir">
                                    <img src="<?php echo htmlspecialchars($user['avatar']); ?>" class="current-avatar" alt="Avatar actuel">
                                </a>
                                <br><small style="color: #10b981;"><i class="fas fa-check"></i> Image actuelle enregistrée</small>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group half-width">
                        <label><i class="fas fa-file-pdf"></i> CV (PDF) :</label>
                        <input type="file" name="cv_pdf" class="form-control" accept=".pdf">
                        <?php if(!empty($user['cv_pdf'])): ?>
                            <div style="margin-top: 25px;">
                                <a href="<?php echo htmlspecialchars($user['cv_pdf']); ?>" target="_blank" class="btn btn-secondary" style="background:#475569; border:none;">
                                    <i class="fas fa-eye"></i> Voir le CV actuel
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group half-width">
                        <label for="email">Email :</label>
                        <input type="email" id="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group half-width">
                        <label for="telephone">Téléphone :</label>
                        <input type="tel" id="telephone" name="telephone" class="form-control" value="<?php echo htmlspecialchars($user['telephone'] ?? ''); ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label><i class="fab fa-linkedin"></i> URL LinkedIn (Optionnel) :</label>
                    <input type="url" name="url_linkedin" class="form-control" placeholder="https://www.linkedin.com/in/votre-profil" value="<?php echo htmlspecialchars($user['url_linkedin'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="description">Description / Bio :</label>
                    <textarea id="description" name="description" rows="5" class="form-control" required><?php echo htmlspecialchars($user['description'] ?? ''); ?></textarea>
                </div>

                <button type="submit" name="update_profile" class="btn btn-primary" style="width:100%;">
                    <i class="fas fa-save"></i> ENREGISTRER LES MODIFICATIONS
                </button>
            </form>
        </section>
    </main>
</body>
</html>