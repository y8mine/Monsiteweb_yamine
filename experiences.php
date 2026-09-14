<?php
require_once 'config.php';
checkAdmin();


if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    executeNonQuery("DELETE FROM experience WHERE id_experience = ? AND id_utilisateur = 1", [$id]);
    setFlashMessage('success', 'Expérience supprimée !');
    redirect('experiences.php');
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_experience'])) {
    $titre = clean_input($_POST['titre']);
    $entreprise = clean_input($_POST['entreprise']);
    $description = clean_input($_POST['description']);
    $date_debut = clean_input($_POST['date_debut']);
    $date_fin = !empty($_POST['date_fin']) ? clean_input($_POST['date_fin']) : null;
    $id_projet = !empty($_POST['id_projet']) ? intval($_POST['id_projet']) : null;
    
 
    $logo_path = null;
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] == 0) {
        $uploadDir = 'uploads/logos/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
        $filename = time() . '_' . basename($_FILES['logo']['name']);
        if (move_uploaded_file($_FILES['logo']['tmp_name'], $uploadDir . $filename)) {
            $logo_path = $uploadDir . $filename;
        }
    }

    if (isset($_POST['id_experience']) && !empty($_POST['id_experience'])) {
       
        $id = intval($_POST['id_experience']);
        
        $sql = "UPDATE experience SET titre=?, entreprise=?, description=?, date_debut=?, date_fin=?, id_projet=?";
        $params = [$titre, $entreprise, $description, $date_debut, $date_fin, $id_projet];
        
        if ($logo_path) {
            $sql .= ", logo=?";
            $params[] = $logo_path;
        }
        
        $sql .= " WHERE id_experience=? AND id_utilisateur=1";
        $params[] = $id;
        
        executeNonQuery($sql, $params);
        setFlashMessage('success', 'Expérience mise à jour.');
    } else {
      
        $sql = "INSERT INTO experience (id_utilisateur, titre, entreprise, description, date_debut, date_fin, id_projet, logo) VALUES (1, ?, ?, ?, ?, ?, ?, ?)";
        
        
        executeNonQuery($sql, [$titre, $entreprise, $description, $date_debut, $date_fin, $id_projet, $logo_path]);
        
        setFlashMessage('success', 'Expérience créée.');
    }
    redirect('experiences.php');
}


$experiences = fetchAll(executeQuery("SELECT * FROM experience WHERE id_utilisateur = 1 ORDER BY date_debut DESC"));
$projets = fetchAll(executeQuery("SELECT id_projet, titre FROM projet WHERE id_utilisateur = 1"));

$edit_exp = null;
if (isset($_GET['edit'])) {
    $id_edit = intval($_GET['edit']);
    $edit_exp = fetchOne(executeQuery("SELECT * FROM experience WHERE id_experience = ? AND id_utilisateur = 1", [$id_edit]));
}

$page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gérer les Expériences</title>
    <link rel="stylesheet" href="style.css?v=4">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .modal {
            display: <?php echo $edit_exp ? 'block' : 'none'; ?>; 
            position: fixed; 
            z-index: 2000; 
            left: 0; top: 0; 
            width: 100%; height: 100%;
            background-color: rgba(0,0,0,0.8); 
            overflow-y: auto; 
        }
        .modal-content {
            background-color: var(--nav-bg, #1e293b); 
            color: var(--text-color, #f1f5f9);
            margin: 5% auto; 
            padding: 30px; 
            border-radius: 10px;
            width: 90%; 
            max-width: 600px;
            border: 1px solid var(--border-color, #334155);
            position: relative;
        }
        .close-btn {
            position: absolute;
            right: 20px;
            top: 15px;
            color: #aaa;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            z-index: 10;
        }
        .close-btn:hover { color: white; }
        
        .logo-mini { width: 40px; height: 40px; object-fit: contain; background: #fff; border-radius: 4px; padding: 2px; }
    </style>
</head>
<body>

    <header class="top-navbar">
        <div class="navbar-container">
            <div class="brand">ADMIN</div>
            
            <nav class="main-nav">
                <a href="profil.php" class="nav-link <?php echo ($page == 'profil.php') ? 'active' : ''; ?>">
                    <i class="fas fa-user"></i> Profil
                </a>
                <a href="formations.php" class="nav-link <?php echo ($page == 'formations.php') ? 'active' : ''; ?>">
                    <i class="fas fa-graduation-cap"></i> Formations
                </a>
                <a href="experiences.php" class="nav-link <?php echo ($page == 'experiences.php') ? 'active' : ''; ?>">
                    <i class="fas fa-briefcase"></i> Expériences
                </a>
                <a href="projets.php" class="nav-link <?php echo ($page == 'projets.php') ? 'active' : ''; ?>">
                    <i class="fas fa-folder"></i> Projets
                </a>
                <a href="competences.php" class="nav-link <?php echo ($page == 'competences.php') ? 'active' : ''; ?>">
                    <i class="fas fa-trophy"></i> Compétences
                </a>
            </nav>

            <div class="user-actions">
                <a href="index.php" target="portfolio_public" class="btn-site">
                    <i class="fas fa-eye"></i> Voir le site
                </a>
                <a href="admin.php" class="btn-dashboard">
                    <i class="fas fa-home"></i> Dashboard
                </a>
            </div>
        </div>
    </header>

    <main class="main-content">
        <div class="welcome-banner" style="display:flex; justify-content:space-between; align-items:center;">
            <h2><i class="fas fa-briefcase"></i> Expériences Professionnelles</h2>
            <button class="btn btn-primary" onclick="openModal()">
                <i class="fas fa-plus"></i> Ajouter une expérience
            </button>
        </div>

        <?php if ($flash = getFlashMessage()): ?>
            <div class="flash-message flash-<?php echo $flash['type']; ?>">
                <?php echo $flash['message']; ?>
            </div>
        <?php endif; ?>

        <section class="card">
            <table style="width:100%; border-collapse: collapse;">
                <thead>
                    <tr style="text-align:left; border-bottom:1px solid #444;">
                        <th style="padding:10px;">Logo</th>
                        <th>Dates</th>
                        <th>Poste</th>
                        <th>Entreprise</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($experiences)): ?>
                        <tr><td colspan="5" style="text-align:center; padding:20px;">Aucune expérience trouvée.</td></tr>
                    <?php else: ?>
                        <?php foreach ($experiences as $exp): ?>
                            <tr style="border-bottom:1px solid #333;">
                                <td style="padding:10px;">
                                    <?php if($exp['logo']): ?>
                                        <img src="<?php echo htmlspecialchars($exp['logo']); ?>" class="logo-mini">
                                    <?php else: ?>
                                        <i class="fas fa-building" style="color:#666;"></i>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php 
                                    $d1 = $exp['date_debut'] ? date('Y', strtotime($exp['date_debut'])) : '';
                                    $d2 = $exp['date_fin'] ? date('Y', strtotime($exp['date_fin'])) : 'Auj.';
                                    echo "$d1 - $d2";
                                    ?>
                                </td>
                                <td style="font-weight:bold;"><?php echo htmlspecialchars($exp['titre']); ?></td>
                                <td><?php echo htmlspecialchars($exp['entreprise']); ?></td>
                                <td>
                                    <a href="experiences.php?edit=<?php echo $exp['id_experience']; ?>" class="action-link"><i class="fas fa-edit"></i></a>
                                    <a href="experiences.php?delete=<?php echo $exp['id_experience']; ?>" class="action-link" style="color:#ef4444; margin-left:10px;" onclick="return confirm('Supprimer ?')"><i class="fas fa-trash"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>
    </main>

    <div id="expModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeModal()">&times;</span>
            
            <h3><?php echo $edit_exp ? '<i class="fas fa-edit"></i> Modifier' : '<i class="fas fa-plus"></i> Ajouter'; ?></h3>
            
            <form method="POST" action="experiences.php" enctype="multipart/form-data">
                
                <?php if($edit_exp): ?>
                    <input type="hidden" name="id_experience" value="<?php echo $edit_exp['id_experience']; ?>">
                <?php endif; ?>

                <div class="form-group">
                    <label>Titre du poste * :</label>
                    <input type="text" name="titre" required class="form-control" 
                           value="<?php echo $edit_exp ? htmlspecialchars($edit_exp['titre']) : ''; ?>">
                </div>

                <div class="form-group">
                    <label>Entreprise * :</label>
                    <input type="text" name="entreprise" required class="form-control" 
                           value="<?php echo $edit_exp ? htmlspecialchars($edit_exp['entreprise']) : ''; ?>">
                </div>

                <div class="form-row">
                    <div class="form-group half-width">
                        <label>Date Début * :</label>
                        <input type="date" name="date_debut" required class="form-control"
                               value="<?php echo $edit_exp ? $edit_exp['date_debut'] : ''; ?>">
                    </div>
                    <div class="form-group half-width">
                        <label>Date Fin :</label>
                        <input type="date" name="date_fin" class="form-control"
                               value="<?php echo $edit_exp ? $edit_exp['date_fin'] : ''; ?>">
                        <small style="color:#888;">Laisser vide si poste actuel</small>
                    </div>
                </div>

                <div class="form-group">
                    <label>Description des missions :</label>
                    <textarea name="description" rows="5" class="form-control"><?php echo $edit_exp ? htmlspecialchars($edit_exp['description']) : ''; ?></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group half-width">
                        <label>Lier à un projet (Optionnel) :</label>
                        <select name="id_projet" class="form-control">
                            <option value="">-- Aucun --</option>
                            <?php foreach($projets as $p): ?>
                                <option value="<?php echo $p['id_projet']; ?>" 
                                    <?php echo ($edit_exp && $edit_exp['id_projet'] == $p['id_projet']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($p['titre']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group half-width">
                        <label>Logo Entreprise :</label>
                        <input type="file" name="logo" class="form-control" accept="image/*">
                        <?php if($edit_exp && $edit_exp['logo']): ?>
                            <small style="color:#28a745;">Logo actuel présent</small>
                        <?php endif; ?>
                    </div>
                </div>

                <button type="submit" name="save_experience" class="btn btn-primary" style="width:100%; margin-top:15px;">
                    <i class="fas fa-save"></i> Enregistrer
                </button>
            </form>
        </div>
    </div>

    <script>
        
        function openModal() {
            document.getElementById('expModal').style.display = 'block';
        }
        
        
        function closeModal() {
            if (window.location.href.indexOf('?edit=') > -1) {
                window.location.href = 'experiences.php';
            } else {
                document.getElementById('expModal').style.display = 'none';
            }
        }
        
        window.onclick = function(event) {
            const modal = document.getElementById('expModal');
            if (event.target == modal) {
                closeModal();
            }
        }
    </script>
</body>
</html>