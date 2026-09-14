<?php
require_once 'config.php';

if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    redirect('admin.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = clean_input($_POST['email']);
    $password_input = $_POST['password'];

    $query = "SELECT * FROM utilisateur WHERE email = ? AND id_utilisateur = 1";
    $stmt = executeQuery($query, [$email]);
    $user = fetchOne($stmt);

    if ($user) {
    
        if (empty($user['password'])) {
            $hash = password_hash($password_input, PASSWORD_DEFAULT);
            executeNonQuery("UPDATE utilisateur SET password = ? WHERE id_utilisateur = 1", [$hash]);
            $_SESSION['admin_logged_in'] = true;
            redirect('admin.php');
        } 
        elseif (password_verify($password_input, $user['password'])) {
            $_SESSION['admin_logged_in'] = true;
            redirect('admin.php');
        } else {
            $error = "Mot de passe incorrect.";
        }
    } else {
        $error = "Email inconnu.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion Admin</title>
    <link rel="stylesheet" href="style.css?v=2">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            display: flex; 
            justify-content: center; 
            align-items: center; 
            height: 100vh; 
            background-color: var(--body-bg, #0f172a); 
            color: var(--text-color, #f1f5f9);
            margin: 0;
        }

        .login-card {
            background-color: var(--nav-bg, #1e293b); 
            padding: 40px; 
            border-radius: 10px; 
            box-shadow: 0 10px 25px rgba(0,0,0,0.5); 
            width: 100%; 
            max-width: 400px; 
            text-align: center;
            border: 1px solid var(--border-color, #334155);
        }

        .login-card h2 { 
            margin-bottom: 25px; 
            color: var(--primary-color, #3b82f6); 
            font-weight: 800;
        }

        .form-group { 
            margin-bottom: 20px; 
            text-align: left; 
        }

        .form-group label { 
            display: block; 
            margin-bottom: 8px; 
            color: var(--text-color, #ccc); 
            font-size: 0.9rem;
        }

        .form-control { 
            width: 100%; 
            padding: 12px; 
            background-color: var(--body-bg, #0f172a); 
            border: 1px solid var(--border-color, #334155); 
            color: white;
            border-radius: 5px; 
            box-sizing: border-box; 
            font-size: 1rem;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary-color, #3b82f6);
            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2);
        }

        .btn-login { 
            width: 100%; 
            padding: 12px; 
            background: var(--primary-color, #3b82f6); 
            color: white; 
            border: none; 
            border-radius: 5px; 
            cursor: pointer; 
            font-size: 1rem; 
            font-weight: bold;
            transition: background 0.3s;
            margin-top: 10px;
        }

        .btn-login:hover { 
            filter: brightness(110%); 
        }

        .error-msg { 
            color: #f87171; 
            background: rgba(220, 38, 38, 0.1); 
            border: 1px solid rgba(220, 38, 38, 0.2);
            padding: 10px; 
            border-radius: 5px; 
            margin-bottom: 20px; 
            font-size: 0.9rem; 
        }

        .first-time { 
            color: #fbbf24; 
            background: rgba(245, 158, 11, 0.1); 
            border: 1px solid rgba(245, 158, 11, 0.2);
            padding: 10px; 
            border-radius: 5px; 
            margin-bottom: 20px; 
            font-size: 0.9rem; 
        }

        .back-link {
            display: inline-block;
            margin-top: 25px;
            color: var(--text-color, #94a3b8);
            text-decoration: none;
            font-size: 0.9rem;
            opacity: 0.7;
            transition: 0.3s;
        }
        .back-link:hover {
            opacity: 1;
            color: white;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <h2><i class="fas fa-shield-alt"></i> Espace Admin</h2>
        
        <?php if($error): ?>
            <div class="error-msg"><i class="fas fa-exclamation-circle"></i> <?php echo $error; ?></div>
        <?php endif; ?>

        <?php 
        
        ?>
        <div class="first-time">
            <i class="fas fa-info-circle"></i> Entrez votre email. S'il n'y a pas encore de mot de passe, celui que vous tapez sera enregistré.
        </div>

        <form method="POST" action="">
            <div class="form-group">
                <label><i class="fas fa-envelope"></i> Email</label>
                <input type="email" name="email" class="form-control" required placeholder="admin@exemple.com">
            </div>
            <div class="form-group">
                <label><i class="fas fa-lock"></i> Mot de passe</label>
                <input type="password" name="password" class="form-control" required placeholder="••••••••">
            </div>
            <button type="submit" class="btn-login">Se connecter <i class="fas fa-arrow-right"></i></button>
        </form>
        
        <a href="index.php" class="back-link">&larr; Retour au site public</a>
    </div>
</body>
</html>