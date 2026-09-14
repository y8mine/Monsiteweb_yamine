<?php
require_once 'config.php';
checkAdmin();


$query = "SELECT * FROM competence ORDER BY categorie, nom_competence";
$stmt = executeQuery($query);
$all_competences = fetchAll($stmt);


$competences_by_category = array();
foreach ($all_competences as $comp) {
    $categorie = $comp['categorie'] ?? 'Autres';
    if (!isset($competences_by_category[$categorie])) {
        $competences_by_category[$categorie] = array();
    }
    $competences_by_category[$categorie][] = $comp;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_competence'])) {
    $nom_competence = clean_input($_POST['nom_competence']);
    $niveau = clean_input($_POST['niveau']);
    $categorie = clean_input($_POST['categorie']);
    
    
    if (isset($_POST['nouvelle_categorie']) && !empty($_POST['nouvelle_categorie'])) {
        $categorie = clean_input($_POST['nouvelle_categorie']);
    }
    
    $query = "INSERT INTO competence (nom_competence, niveau, categorie) VALUES (?, ?, ?)";
    executeNonQuery($query, array($nom_competence, $niveau, $categorie));
    
    setFlashMessage('success', 'Compétence ajoutée avec succès !');
    redirect('competences.php');
}


if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    

    $query = "DELETE FROM projet_competence WHERE id_competence = ?";
    executeNonQuery($query, array($id));
    
    
    $query = "DELETE FROM competence WHERE id_competence = ?";
    executeNonQuery($query, array($id));
    
    setFlashMessage('success', 'Compétence supprimée avec succès !');
    redirect('competences.php');
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_competence'])) {
    $id = intval($_POST['id_competence']);
    $nom_competence = clean_input($_POST['nom_competence']);
    $niveau = clean_input($_POST['niveau']);
    $categorie = clean_input($_POST['categorie']);
    
    
    if (isset($_POST['nouvelle_categorie']) && !empty($_POST['nouvelle_categorie'])) {
        $categorie = clean_input($_POST['nouvelle_categorie']);
    }
    
    $query = "UPDATE competence SET nom_competence = ?, niveau = ?, categorie = ? WHERE id_competence = ?";
    executeNonQuery($query, array($nom_competence, $niveau, $categorie, $id));
    
    setFlashMessage('success', 'Compétence modifiée avec succès !');
    redirect('competences.php');
}


$competence_edit = null;
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $query = "SELECT * FROM competence WHERE id_competence = ?";
    $stmt = executeQuery($query, array($id));
    $competence_edit = fetchOne($stmt);
}


$categories = array_keys($competences_by_category);

$flash = getFlashMessage();


function getNiveauColor($niveau) {
    $niveau = strtolower($niveau ?? '');
    if (strpos($niveau, 'débutant') !== false || strpos($niveau, 'base') !== false) {
        return '#ffc107'; 
    } elseif (strpos($niveau, 'intermédiaire') !== false || strpos($niveau, 'moyen') !== false) {
        return '#17a2b8'; 
    } elseif (strpos($niveau, 'avancé') !== false || strpos($niveau, 'expert') !== false) {
        return '#28a745'; 
    }
    return '#6c757d'; 
}


function getNiveauPercentage($niveau) {
    $niveau = strtolower($niveau ?? '');
    if (strpos($niveau, 'débutant') !== false || strpos($niveau, 'base') !== false) {
        return 40;
    } elseif (strpos($niveau, 'intermédiaire') !== false || strpos($niveau, 'moyen') !== false) {
        return 70;
    } elseif (strpos($niveau, 'avancé') !== false || strpos($niveau, 'expert') !== false) {
        return 95;
    }
    return 50;
}

$page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Compétences - Portfolio</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .flash-message {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            font-weight: bold;
        }
        .flash-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }
        .modal-content {
            background-color: white;
            margin: 5% auto;
            padding: 30px;
            border-radius: 10px;
            width: 80%;
            max-width: 600px;
            max-height: 80vh;
            overflow-y: auto;
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        .close:hover {
            color: #000;
        }
        .category-section {
            margin-bottom: 30px;
        }
        .category-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .category-header h4 {
            margin: 0;
            font-size: 1.2em;
        }
        .skills-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }
        .skill-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            transition: all 0.3s;
            border-left: 4px solid #007bff;
        }
        .skill-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.15);
        }
        .skill-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        .skill-name {
            font-weight: bold;
            font-size: 1.1em;
            color: #2b3d5b;
        }
        .skill-badge {
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 0.8em;
            font-weight: bold;
            color: white;
        }
        .progress-bar-container {
            background: #e9ecef;
            height: 12px;
            border-radius: 6px;
            overflow: hidden;
            margin: 10px 0;
        }
        .progress-bar {
            height: 100%;
            background: #007bff;
            transition: width 0.5s ease;
            border-radius: 6px;
        }
        .skill-actions {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #eee;
        }
        .tag-cloud {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin: 20px 0;
        }
        .tag {
            background: #e7f1ff;
            color: #007bff;
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 0.9em;
            cursor: pointer;
            transition: all 0.2s;
        }
        .tag:hover {
            background: #007bff;
            color: white;
            transform: scale(1.05);
        }
        .nouvelle-categorie-group {
            display: none;
            margin-top: 10px;
        }
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
            <a href="index.php" target="portfolio_public" class="btn-site" title="Voir le site en direct">
                <i class="fas fa-eye"></i> Voir le site
            </a>
            
            <a href="admin.php" class="btn-dashboard" title="Retour à l'accueil admin">
                <i class="fas fa-home"></i> Dashboard
            </a>
        </div>
    </div>
    </header>

    <main class="main-content">
        <div class="welcome-banner">
            <h2><i class="fas fa-trophy"></i> Mes Compétences Techniques</h2>
        </div>

        <?php if ($flash): ?>
            <div class="flash-message flash-<?php echo $flash['type']; ?>">
                <?php echo $flash['message']; ?>
            </div>
        <?php endif; ?>

        <section class="card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
                <h3><i class="fas fa-star"></i> Portfolio de Compétences</h3>
                <button class="btn btn-primary" onclick="openModal()">
                    <i class="fas fa-plus"></i> Ajouter une compétence
                </button>
            </div>

            
            <div style="margin-bottom: 30px;">
                <h4 style="margin-bottom: 15px;"><i class="fas fa-tags"></i> Mots-clés</h4>
                <div class="tag-cloud">
                    <?php foreach ($all_competences as $comp): ?>
                        <span class="tag" title="<?php echo htmlspecialchars($comp['niveau'] ?? 'Non défini'); ?>">
                            <?php echo htmlspecialchars($comp['nom_competence']); ?>
                        </span>
                    <?php endforeach; ?>
                </div>
            </div>

            
            <?php if (empty($competences_by_category)): ?>
                <p style="color: #666; text-align: center; padding: 40px;">
                    Aucune compétence enregistrée. Cliquez sur "Ajouter une compétence" pour commencer.
                </p>
            <?php else: ?>
                <?php foreach ($competences_by_category as $categorie => $competences): ?>
                    <div class="category-section">
                        <div class="category-header">
                            <i class="fas fa-layer-group"></i>
                            <h4><?php echo htmlspecialchars($categorie); ?></h4>
                            <span style="margin-left: auto; opacity: 0.8;">
                                <?php echo count($competences); ?> compétence<?php echo count($competences) > 1 ? 's' : ''; ?>
                            </span>
                        </div>
                        
                        <div class="skills-grid">
                            <?php foreach ($competences as $comp): ?>
                                <div class="skill-card" style="border-left-color: <?php echo getNiveauColor($comp['niveau']); ?>">
                                    <div class="skill-header">
                                        <span class="skill-name">
                                            <i class="fas fa-code"></i> <?php echo htmlspecialchars($comp['nom_competence']); ?>
                                        </span>
                                        <span class="skill-badge" style="background: <?php echo getNiveauColor($comp['niveau']); ?>">
                                            <?php echo htmlspecialchars($comp['niveau'] ?? 'Non défini'); ?>
                                        </span>
                                    </div>
                                    
                                    <div class="progress-bar-container">
                                        <div class="progress-bar" 
                                             style="width: <?php echo getNiveauPercentage($comp['niveau']); ?>%; 
                                                    background: <?php echo getNiveauColor($comp['niveau']); ?>">
                                        </div>
                                    </div>
                                    
                                    <div style="text-align: right; font-size: 0.85em; color: #666; margin-top: 5px;">
                                        <?php echo getNiveauPercentage($comp['niveau']); ?>%
                                    </div>
                                    
                                    <div class="skill-actions">
                                        <a href="competences.php?edit=<?php echo $comp['id_competence']; ?>" 
                                           class="action-link" style="margin-right: 15px;">
                                            <i class="fas fa-edit"></i> Modifier
                                        </a>
                                        <a href="competences.php?delete=<?php echo $comp['id_competence']; ?>" 
                                           class="action-link" style="color: #dc3545;"
                                           onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette compétence ?');">
                                            <i class="fas fa-trash"></i> Supprimer
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </section>
    </main>

    
    <div id="competenceModal" class="modal" <?php echo $competence_edit ? 'style="display:block;"' : ''; ?>>
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h3>
                <?php echo $competence_edit ? '<i class="fas fa-edit"></i> Modifier la compétence' : '<i class="fas fa-plus"></i> Ajouter une compétence'; ?>
            </h3>
            <form method="POST" action="">
                <?php if ($competence_edit): ?>
                    <input type="hidden" name="id_competence" value="<?php echo $competence_edit['id_competence']; ?>">
                <?php endif; ?>
                
                <div class="form-group">
                    <label for="nom_competence">Nom de la compétence :</label>
                    <input type="text" id="nom_competence" name="nom_competence" 
                           value="<?php echo $competence_edit ? htmlspecialchars($competence_edit['nom_competence']) : ''; ?>" 
                           placeholder="Ex: Python, SQL Server, Power BI..." required>
                </div>
                
                <div class="form-group">
                    <label for="categorie">Catégorie :</label>
                    <select id="categorie" name="categorie" onchange="toggleNouvelleCategorie(this)">
                        <option value="">-- Sélectionner une catégorie --</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo htmlspecialchars($cat); ?>"
                                    <?php echo ($competence_edit && $competence_edit['categorie'] == $cat) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat); ?>
                            </option>
                        <?php endforeach; ?>
                        <option value="nouvelle">+ Nouvelle catégorie</option>
                    </select>
                    
                    <div id="nouvelle-categorie-group" class="nouvelle-categorie-group">
                        <input type="text" name="nouvelle_categorie" placeholder="Nom de la nouvelle catégorie">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="niveau">Niveau de maîtrise :</label>
                    <select id="niveau" name="niveau" required>
                        <option value="">-- Sélectionner un niveau --</option>
                        <option value="Débutant" <?php echo ($competence_edit && $competence_edit['niveau'] == 'Débutant') ? 'selected' : ''; ?>>
                            Débutant (Notions de base)
                        </option>
                        <option value="Intermédiaire" <?php echo ($competence_edit && $competence_edit['niveau'] == 'Intermédiaire') ? 'selected' : ''; ?>>
                            Intermédiaire (Pratique régulière)
                        </option>
                        <option value="Avancé" <?php echo ($competence_edit && $competence_edit['niveau'] == 'Avancé') ? 'selected' : ''; ?>>
                            Avancé (Maîtrise confirmée)
                        </option>
                        <option value="Expert" <?php echo ($competence_edit && $competence_edit['niveau'] == 'Expert') ? 'selected' : ''; ?>>
                            Expert (Expertise approfondie)
                        </option>
                    </select>
                </div>
                
                <button type="submit" name="<?php echo $competence_edit ? 'update_competence' : 'add_competence'; ?>" class="btn btn-primary">
                    <i class="fas fa-save"></i> Enregistrer
                </button>
                <?php if ($competence_edit): ?>
                    <a href="competences.php" class="btn btn-secondary" style="margin-left: 10px;">
                        <i class="fas fa-times"></i> Annuler
                    </a>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <script>
        function openModal() {
            document.getElementById('competenceModal').style.display = 'block';
        }
        
        function closeModal() {
            <?php if ($competence_edit): ?>
                window.location.href = 'competences.php';
            <?php else: ?>
                document.getElementById('competenceModal').style.display = 'none';
            <?php endif; ?>
        }
        
        function toggleNouvelleCategorie(select) {
            const nouvelleGroup = document.getElementById('nouvelle-categorie-group');
            if (select.value === 'nouvelle') {
                nouvelleGroup.style.display = 'block';
            } else {
                nouvelleGroup.style.display = 'none';
            }
        }
        
        window.onclick = function(event) {
            const modal = document.getElementById('competenceModal');
            if (event.target == modal) {
                closeModal();
            }
        }
    </script>
</body>
</html>
