<?php

$host = "localhost";
$dbname = "portfolio";
$username = "root"; 
$password = "";     

try {
    
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    
    
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
} catch(PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

function executeQuery($sql, $params = []) {
    global $conn;
    try {
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    } catch (PDOException $e) {
        die("Erreur SQL : " . $e->getMessage());
    }
}


function executeNonQuery($sql, $params = []) {
    return executeQuery($sql, $params);
}

function fetchAll($stmt) {
    return $stmt->fetchAll();
}

function fetchOne($stmt) {
    return $stmt->fetch();
}

function clean_input($data) {
    if ($data === null) return null;
    return htmlspecialchars(stripslashes(trim($data)));
}

function redirect($url) {
    header("Location: $url");
    exit();
}

if (session_status() === PHP_SESSION_NONE) session_start();

function setFlashMessage($type, $message) {
    $_SESSION['flash_message'] = ['type' => $type, 'message' => $message];
}

function getFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $msg = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        return $msg;
    }
    return null;
}

/**
 * 
 * @param array 
 * @param string 
 * @return string|null 
 */
function uploadFile($file, $folder = 'uploads/') {
 
    if (!isset($file) || $file['error'] != 0) {
        return null;
    }

    
    if (!is_dir($folder)) {
        mkdir($folder, 0777, true);
    }

    
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'webp'];
    $filename = $file['name'];
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

    if (!in_array($ext, $allowed)) {
        return null; 
    }

    
    $new_filename = uniqid() . '.' . $ext;
    $destination = $folder . $new_filename;

   
    if (move_uploaded_file($file['tmp_name'], $destination)) {
        return $destination;
    }

    return null;
}

function checkAdmin() {
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        redirect('login.php');
    }
}
?>