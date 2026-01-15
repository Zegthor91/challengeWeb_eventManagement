<?php
header('Content-Type: application/json');
require_once '../../config/database.php';
require_once '../../config/session.php';

requireLogin();

// Récupère tout le personnel
$stmt = $pdo->query("
    SELECT
        p.*
    FROM personnel p
    ORDER BY p.nom
");

$personnel = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    'personnel' => $personnel
], JSON_PRETTY_PRINT);
?>
