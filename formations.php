<?php
session_start();

$host = 'localhost';
$dbname = 'portfolio'; 
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("<h3>Erreur de connexion :</h3> " . $e->getMessage());
}


$message = "";


if (isset($_POST['save'])) {
    
    $diplome = $_POST['diplome'];
    $ecole = $_POST['ecole'];
    $ville = $_POST['ville'];
    $date_debut = $_POST['date_debut'];
    $date_fin = !empty($_POST['date_fin']) ? $_POST['date_fin'] : NULL;
    $description = $_POST['description'];
    $id_formation = $_POST['id_formation'] ?? null;
    
    $logo_path = $_POST['current_logo'] ?? ''; 

    if (!empty($_FILES['logo']['name'])) {
        $filename = time() . '_' . basename($_FILES['logo']['name']);
        $target = "uploads/" . $filename; 
        
        if (!is_dir('uploads')) { mkdir('uploads'); }

        if (move_uploaded_file($_FILES['logo']['tmp_name'], $target)) {
            $logo_path = "uploads/" . $filename;
        } else {
            $message = "Erreur lors de l'upload du logo.";
        }
    }

    if ($id_formation) {
        $sql = "UPDATE formation SET diplome=?, ecole=?, ville=?, date_debut=?, date_fin=?, description=?, logo=? WHERE id_formation=?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$diplome, $ecole, $ville, $date_debut, $date_fin, $description, $logo_path, $id_formation]);
    } else {
        $id_user = 1; 
        $sql = "INSERT INTO formation (id_utilisateur, diplome, ecole, ville, date_debut, date_fin, description, logo) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_user, $diplome, $ecole, $ville, $date_debut, $date_fin, $description, $logo_path]);
    }
    
    header("Location: formations.php"); 
    exit;
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $pdo->prepare("DELETE FROM formation WHERE id_formation = ?")->execute([$id]);
    header("Location: formations.php");
    exit;
}

$edit_mode = false;
$data = [];
if (isset($_GET['edit'])) {
    $edit_mode = true;
    $stmt = $pdo->prepare("SELECT * FROM formation WHERE id_formation = ?");
    $stmt->execute([$_GET['edit']]);
    $data = $stmt->fetch();
}

$formations = $pdo->query("SELECT * FROM formation ORDER BY date_debut DESC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gérer les Formations</title>
    <link rel="stylesheet" href="public_style.css"> 
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

<div class="admin-container">
    <div class="nav-admin">
        <a href="index.php"><i class="fas fa-arrow-left"></i> Site Public</a>
        <a href="admin.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
        <a href="projets.php">Projets</a>
        <a href="formations.php" class="active">Formations</a>
    </div>

    <h1 style="color:#0f172a; margin-bottom:20px;"><?php echo $edit_mode ? 'Modifier' : 'Ajouter'; ?> une Formation</h1>

    <div class="form-box">
        <form method="post" enctype="multipart/form-data">
            <input type="hidden" name="id_formation" value="<?php echo $data['id_formation'] ?? ''; ?>">
            <input type="hidden" name="current_logo" value="<?php echo $data['logo'] ?? ''; ?>">

            <div class="form-group">
                <label>Diplôme (ex: BUT Science des Données)</label>
                <input type="text" name="diplome" value="<?php echo htmlspecialchars($data['diplome'] ?? ''); ?>" required>
            </div>

            <div class="form-group">
                <label>École / Université</label>
                <input type="text" name="ecole" value="<?php echo htmlspecialchars($data['ecole'] ?? ''); ?>" required>
            </div>

            <div class="form-group">
                <label>Ville</label>
                <input type="text" name="ville" value="<?php echo htmlspecialchars($data['ville'] ?? ''); ?>">
            </div>

            <div style="display: flex; gap: 20px;">
                <div class="form-group" style="flex: 1;">
                    <label>Date Début</label>
                    <input type="date" name="date_debut" value="<?php echo $data['date_debut'] ?? ''; ?>" required>
                </div>
                <div class="form-group" style="flex: 1;">
                    <label>Date Fin (Laisser vide si en cours)</label>
                    <input type="date" name="date_fin" value="<?php echo $data['date_fin'] ?? ''; ?>">
                </div>
            </div>

            <div class="form-group">
                <label>Logo de l'établissement</label>
                <?php if ($edit_mode && !empty($data['logo'])): ?>
                    <div style="display:flex; align-items:center; gap:10px; margin-bottom:10px;">
                        <img src="<?php echo htmlspecialchars($data['logo']); ?>" class="logo-preview">
                        <span style="font-size:0.8rem; color:#64748b;">Actuel</span>
                    </div>
                <?php endif; ?>
                <input type="file" name="logo" accept="image/*">
            </div>

            <div class="form-group">
                <label>Description (Utilisez des points-virgules ';' pour créer des listes)</label>
                <textarea name="description" rows="5"><?php echo htmlspecialchars($data['description'] ?? ''); ?></textarea>
            </div>

            <button type="submit" name="save" class="btn btn-save">
                <?php echo $edit_mode ? '<i class="fas fa-save"></i> Mettre à jour' : '<i class="fas fa-plus-circle"></i> Ajouter la formation'; ?>
            </button>
        </form>
    </div>

    <h2 style="font-size:1.2rem; color:#0f172a; margin-bottom:15px;">Liste des Formations</h2>
    <table>
        <thead>
            <tr>
                <th width="80">Logo</th>
                <th>Formation</th>
                <th>Dates</th>
                <th width="120">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($formations as $f): ?>
                <tr>
                    <td>
                        <?php if (!empty($f['logo'])): ?>
                            <img src="<?php echo htmlspecialchars($f['logo']); ?>" class="logo-preview">
                        <?php else: ?>
                            <div class="no-logo"><i class="fas fa-university"></i></div>
                        <?php endif; ?>
                    </td>
                    <td>
                        <strong style="display:block; color:#0f172a;"><?php echo htmlspecialchars($f['diplome']); ?></strong>
                        <span style="color:#64748b; font-size:0.9rem;"><?php echo htmlspecialchars($f['ecole']); ?></span>
                    </td>
                    <td style="font-size:0.9rem;">
                        <?php 
                            echo date('Y', strtotime($f['date_debut']));
                            echo $f['date_fin'] ? ' - ' . date('Y', strtotime($f['date_fin'])) : ' - Auj.';
                        ?>
                    </td>
                    <td>
                        <a href="formations.php?edit=<?php echo $f['id_formation']; ?>" class="btn-edit" title="Modifier"><i class="fas fa-pen"></i></a>
                        <a href="formations.php?delete=<?php echo $f['id_formation']; ?>" class="btn-delete" onclick="return confirm('Supprimer ?');" title="Supprimer"><i class="fas fa-trash"></i></a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</div>

</body>
</html>