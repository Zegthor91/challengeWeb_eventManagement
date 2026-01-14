<?php
// Fichier de connexion a la base de donné PostgreSQL

// Parametres de connexion



$db_host = 'localhost';
$db_port = '5432';
$db_name = 'gestion_evenements';
$db_user = 'postgres';
$db_password = 'root'; // Change ca avec ton vrai mot de passe

// Connexion a PostgreSQL
try {
    $dsn = "pgsql:host=$db_host;port=$db_port;dbname=$db_name";
    $pdo = new PDO($dsn, $db_user, $db_password);
    
    // Configure PDO pour afficher les erreurs
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
$db_password = 'root'; 


try {
    $dsn = "pgsql:host=$db_host;port=$db_port;dbname=$db_name";
    $pdo = new PDO($dsn, $db_user, $db_password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Fonction helper pour executer des requetes
function query($sql, $params = []) {
    global $pdo;
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    
    return $stmt;
}

// Fonction pour recuperer toutes les lignes
function fetchAll($sql, $params = []) {
    global $pdo;
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    
    $stmt = query($sql, $params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Fonction pour recuperer une seule ligne
function fetchOne($sql, $params = []) {
    global $pdo;
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    
    $stmt = query($sql, $params);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
?>
