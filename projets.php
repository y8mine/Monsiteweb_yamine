<?php
require_once 'config.php';
checkAdmin();

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    executeNonQuery("DELETE FROM projet_competence WHERE id_projet = ?", [$id]);
    executeNonQuery("DELETE FROM projet WHERE id_projet = ? AND id_utilisateur = 1", [$id]);
    setFlashMessage('success', 'Projet supprimé !');
    redirect('projets.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && (isset($_POST['add_projet']) || isset($_POST['update_projet']))) {
    $titre = clean_input($_POST['titre']);
    $description = clean_input($_POST['description']);
    $date_debut = clean_input($_POST['date_debut']);
    $date_fin = !empty($_POST['date_fin']) ? clean_input($_POST['date_fin']) : null;
    $technologie_utilisee = clean_input($_POST['technologie_utilisee']);
    $lien_code = clean_input($_POST['lien_code']);
    $lien_demo = clean_input($_POST['lien_demo']);
    
    $image_path = null;
    $doc_path = null;
    $uploadDir = 'uploads/projets/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $filename = time() . '_img_' . basename($_FILES['image']['name']);
        if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $filename)) {
            $image_path = $uploadDir . $filename;
        }
    }

    if (isset($_FILES['document']) && $_FILES['document']['error'] == 0) {
        $filename = time() . '_doc_' . basename($_FILES['document']['name']);
        if (move_uploaded_file($_FILES['document']['tmp_name'], $uploadDir . $filename)) {
            $doc_path = $uploadDir . $filename;
        }
    }

    if (isset($_POST['id_projet']) && !empty($_POST['id_projet'])) {
        $id = intval($_POST['id_projet']);
        $sql = "UPDATE projet SET titre=?, description=?, date_debut=?, date_fin=?, technologie_utilisee=?, lien_code=?, lien_demo=?";
        $params = [$titre, $description, $date_debut, $date_fin, $technologie_utilisee, $lien_code, $lien_demo];

        if ($image_path) { $sql .= ", image=?"; $params[] = $image_path; }
        if ($doc_path) { $sql .= ", document=?"; $params[] = $doc_path; }

        $sql .= " WHERE id_projet=? AND id_utilisateur=1";
        $params[] = $id;
        executeNonQuery($sql, $params);
        $id_projet = $id;
        setFlashMessage('success', 'Projet mis à jour !');
    } else {
        $sql = "INSERT INTO projet (id_utilisateur, titre, description, date_debut, date_fin, technologie_utilisee, lien_code, lien_demo, image, document) VALUES (1, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        

        executeNonQuery($sql, [$titre, $description, $date_debut, $date_fin, $technologie_utilisee, $lien_code, $lien_demo, $image_path, $doc_path]);
        
        global $conn;
        $id_projet = $conn->lastInsertId();
        setFlashMessage('success', 'Projet créé !');
    }

    executeNonQuery("DELETE FROM projet_competence WHERE id_projet = ?", [$id_projet]);
    if (isset($_POST['competences']) && is_array($_POST['competences'])) {
        foreach ($_POST['competences'] as $id_comp) {
            executeNonQuery("INSERT INTO projet_competence (id_projet, id_competence) VALUES (?, ?)", [$id_projet, intval($id_comp)]);
        }
    }

    redirect('projets.php');
}

$query = "SELECT p.*, 
          (SELECT GROUP_CONCAT(c.nom_competence SEPARATOR ', ') 
           FROM projet_competence pc 
           JOIN competence c ON pc.id_competence = c.id_competence 
           WHERE pc.id_projet = p.id_projet) as skills_list
          FROM projet p 
          WHERE p.id_utilisateur = 1 
          ORDER BY p.date_debut DESC";
$projets = fetchAll(executeQuery($query));
$all_skills = fetchAll(executeQuery("SELECT * FROM competence ORDER BY categorie, nom_competence"));

$edit_proj = null;
$proj_skills_ids = [];
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $edit_proj = fetchOne(executeQuery("SELECT * FROM projet WHERE id_projet = ? AND id_utilisateur = 1", [$id]));
    $res = fetchAll(executeQuery("SELECT id_competence FROM projet_competence WHERE id_projet = ?", [$id]));
    foreach($res as $r) $proj_skills_ids[] = $r['id_competence'];
}

$page = 'projets.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gérer les Projets</title>
    <link rel="stylesheet" href="style.css?v=6">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { 
            background: #020617; 
            color: #f8fafc;
            font-family: 'Segoe UI', sans-serif; 
            padding: 20px; 
            min-height: 100vh;
        }

        .admin-container { 
            max-width: 1000px; 
            margin: 0 auto; 
            background: rgba(30, 41, 59, 0.6);
            padding: 40px; 
            border-radius: 12px; 
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
        }

        .nav-admin { 
            margin-bottom: 30px; 
            border-bottom: 1px solid rgba(255,255,255,0.1); 
            padding-bottom: 15px; 
        }
        .nav-admin a { 
            margin-right: 20px; 
            text-decoration: none; 
            color: #94a3b8; 
            font-weight: 600; 
            font-size: 0.95rem; 
            transition: 0.3s;
        }
        .nav-admin a.active, .nav-admin a:hover { 
            color: #3b82f6; 
            text-shadow: 0 0 10px rgba(59, 130, 246, 0.5);
        }

        h1, h2 { color: #f8fafc; margin-bottom: 20px; }

        .form-box { 
            background: rgba(255,255,255,0.03); 
            padding: 25px; 
            border-radius: 10px; 
            border: 1px solid rgba(255,255,255,0.05); 
            margin-bottom: 40px; 
        }
        .form-group { margin-bottom: 15px; }
        label { display: block; font-weight: 600; margin-bottom: 8px; color: #cbd5e1; }
        
        input[type="text"], input[type="date"], input[type="url"], textarea, select { 
            width: 100%; 
            padding: 10px; 
            background: rgba(0,0,0,0.3); 
            border: 1px solid rgba(255,255,255,0.1); 
            border-radius: 6px; 
            color: white; 
            box-sizing: border-box;
        }
        input:focus, textarea:focus, select:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 10px rgba(59, 130, 246, 0.3);
        }

        .btn { padding: 12px 20px; border: none; cursor: pointer; border-radius: 6px; font-weight: bold; color: white; transition: 0.3s; }
        .btn-save { background: #3b82f6; width: 100%; }
        .btn-save:hover { background: #2563eb; box-shadow: 0 0 15px rgba(59, 130, 246, 0.5); }
        .btn-edit { background: #eab308; padding: 5px 10px; text-decoration: none; border-radius: 4px; color: #000; font-size: 0.8rem; font-weight:bold; }
        .btn-delete { background: #ef4444; padding: 5px 10px; text-decoration: none; border-radius: 4px; color: white; font-size: 0.8rem; font-weight:bold; }

        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { text-align: left; padding: 15px; background: rgba(255,255,255,0.05); color: #94a3b8; font-size: 0.85rem; text-transform: uppercase; }
        td { padding: 15px; border-bottom: 1px solid rgba(255,255,255,0.05); color: #e2e8f0; vertical-align: middle; }
        tr:hover { background: rgba(255,255,255,0.02); }
        
        .logo-preview, .mini-img { width: 50px; height: 50px; object-fit: contain; background: white; border-radius: 4px; padding: 2px; }
        .no-logo { width: 50px; height: 50px; background: rgba(255,255,255,0.1); border-radius: 4px; display: flex; align-items: center; justify-content: center; color: #ccc; }
        

        .admin-layout { display: flex; gap: 30px; }
        .sidebar { width: 250px; background: rgba(30, 41, 59, 0.6); padding: 20px; border-radius: 12px; height: fit-content; border: 1px solid rgba(255,255,255,0.1); }
        .main-content { flex: 1; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; }
        .stat-card { background: rgba(255,255,255,0.03); padding: 20px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.1); display: flex; align-items: center; justify-content: space-between; text-decoration: none; transition: 0.3s; }
        .stat-card:hover { transform: translateY(-5px); border-color: #3b82f6; background: rgba(59, 130, 246, 0.1); }
        .stat-info h3 { margin: 0; font-size: 0.9rem; color: #94a3b8; }
        .stat-info .count { font-size: 2rem; font-weight: bold; color: white; }
        .stat-icon { font-size: 2.5rem; opacity: 0.5; color: #3b82f6; }
    </style>
</head>
<body>
    <header class="top-navbar">
        <div class="navbar-container">
            <div class="brand">ADMIN</div>
            <nav class="main-nav">
                <a href="profil.php" class="nav-link"><i class="fas fa-user"></i> Profil</a>
                <a href="formations.php" class="nav-link"><i class="fas fa-graduation-cap"></i> Formations</a>
                <a href="experiences.php" class="nav-link"><i class="fas fa-briefcase"></i> Expériences</a>
                <a href="projets.php" class="nav-link active"><i class="fas fa-folder"></i> Projets</a>
                <a href="competences.php" class="nav-link"><i class="fas fa-trophy"></i> Compétences</a>
            </nav>
            <div class="user-actions">
                <a href="index.php" target="portfolio_public" class="btn-site"><i class="fas fa-eye"></i> Voir le site</a>
                <a href="logout.php" class="btn-dashboard"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
            </div>
        </div>
    </header>

    <main class="main-content">
        <div class="welcome-banner" style="display:flex; justify-content:space-between; align-items:center;">
            <h2><i class="fas fa-folder-open"></i> Mes Projets</h2>
            <button class="btn btn-primary" onclick="openModal()"><i class="fas fa-plus"></i> Nouveau Projet</button>
        </div>

        <?php if ($flash = getFlashMessage()): ?>
            <div class="flash-message flash-<?php echo $flash['type']; ?>">
                <?php echo $flash['message']; ?>
            </div>
        <?php endif; ?>

        <div class="projects-grid">
            <?php foreach ($projets as $p): ?>
                <div class="project-card">
                    <?php if(!empty($p['image'])): ?>
                        <img src="<?php echo htmlspecialchars($p['image']); ?>" class="project-img">
                    <?php else: ?>
                        <div class="project-img" style="display:flex; align-items:center; justify-content:center; color:#555;">
                            <i class="fas fa-image fa-3x"></i>
                        </div>
                    <?php endif; ?>

                    <div class="project-body">
                        <h3 style="margin:0 0 5px 0; color:var(--text-color);"><?php echo htmlspecialchars($p['titre']); ?></h3>
                        <small style="color:var(--primary-color);">
                            <?php echo date('Y', strtotime($p['date_debut'])); ?>
                        </small>

                        <div class="project-tags">
                            <?php 
                                if($p['technologie_utilisee']) {
                                    foreach(explode(',', $p['technologie_utilisee']) as $t) echo "<span class='tag'>".trim($t)."</span>";
                                }
                            ?>
                        </div>

                        <div style="margin-top:auto; padding-top:15px; display:flex; gap:10px;">
                            <?php if(!empty($p['document'])): ?>
                                <a href="<?php echo htmlspecialchars($p['document']); ?>" target="_blank" style="color:#ef4444; text-decoration:none;">
                                    <i class="fas fa-file-pdf"></i> PDF
                                </a>
                            <?php endif; ?>
                            <?php if(!empty($p['lien_code'])): ?>
                                <a href="<?php echo htmlspecialchars($p['lien_code']); ?>" target="_blank" style="color:var(--text-color);"><i class="fab fa-github"></i></a>
                            <?php endif; ?>
                            
                            <div style="margin-left:auto;">
                                <a href="projets.php?edit=<?php echo $p['id_projet']; ?>" style="color:var(--primary-color); margin-right:10px;"><i class="fas fa-edit"></i></a>
                                <a href="projets.php?delete=<?php echo $p['id_projet']; ?>" style="color:#ef4444;" onclick="return confirm('Supprimer ?')"><i class="fas fa-trash"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </main>

    <div id="projModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeModal()">&times;</span>
            <h3><?php echo $edit_proj ? 'Modifier' : 'Nouveau Projet'; ?></h3>
            <form method="POST" action="projets.php" enctype="multipart/form-data">
                <?php if($edit_proj): ?>
                    <input type="hidden" name="id_projet" value="<?php echo $edit_proj['id_projet']; ?>">
                    <input type="hidden" name="update_projet" value="1">
                <?php else: ?>
                    <input type="hidden" name="add_projet" value="1">
                <?php endif; ?>

                <div class="form-group">
                    <label>Titre * :</label>
                    <input type="text" name="titre" required class="form-control" value="<?php echo $edit_proj ? htmlspecialchars($edit_proj['titre']) : ''; ?>">
                </div>
                <div class="form-group">
                    <label>Description :</label>
                    <textarea name="description" rows="4" class="form-control"><?php echo $edit_proj ? htmlspecialchars($edit_proj['description']) : ''; ?></textarea>
                </div>
                <div class="form-row">
                    <div class="form-group half-width">
                        <label>Image de couverture :</label>
                        <input type="file" name="image" class="form-control" accept="image/*">
                    </div>
                    <div class="form-group half-width">
                        <label>Document (PDF/Rapport) :</label>
                        <input type="file" name="document" class="form-control" accept=".pdf,.doc,.docx,.zip">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group half-width">
                        <label>Date Début * :</label>
                        <input type="date" name="date_debut" required class="form-control" value="<?php echo $edit_proj ? $edit_proj['date_debut'] : ''; ?>">
                    </div>
                    <div class="form-group half-width">
                        <label>Date Fin :</label>
                        <input type="date" name="date_fin" class="form-control" value="<?php echo $edit_proj ? $edit_proj['date_fin'] : ''; ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label>Technologies :</label>
                    <input type="text" name="technologie_utilisee" class="form-control" value="<?php echo $edit_proj ? htmlspecialchars($edit_proj['technologie_utilisee']) : ''; ?>">
                </div>
                <div class="form-row">
                    <div class="form-group half-width">
                        <label>Lien GitHub/Code :</label>
                        <input type="url" name="lien_code" class="form-control" value="<?php echo $edit_proj ? htmlspecialchars($edit_proj['lien_code']) : ''; ?>">
                    </div>
                    <div class="form-group half-width">
                        <label>Lien Démo/Site :</label>
                        <input type="url" name="lien_demo" class="form-control" value="<?php echo $edit_proj ? htmlspecialchars($edit_proj['lien_demo']) : ''; ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label>Compétences liées :</label>
                    <div style="max-height:150px; overflow-y:auto; background:rgba(0,0,0,0.2); padding:10px; border-radius:5px;">
                        <?php foreach($all_skills as $s): ?>
                            <div style="margin-bottom:5px;">
                                <label style="font-weight:normal; cursor:pointer;">
                                    <input type="checkbox" name="competences[]" value="<?php echo $s['id_competence']; ?>" 
                                    <?php echo in_array($s['id_competence'], $proj_skills_ids) ? 'checked' : ''; ?>>
                                    <?php echo htmlspecialchars($s['nom_competence']); ?>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary" style="width:100%; margin-top:15px;">Enregistrer</button>
            </form>
        </div>
    </div>
    <script>
        function openModal() { document.getElementById('projModal').style.display = 'block'; }
        function closeModal() { 
            if (window.location.href.indexOf('?edit=') > -1) window.location.href = 'projets.php';
            else document.getElementById('projModal').style.display = 'none';
        }
        window.onclick = function(e) { if(e.target == document.getElementById('projModal')) closeModal(); }
    </script>
</body>
</html>