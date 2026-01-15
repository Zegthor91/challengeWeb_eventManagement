<?php
header('Content-Type: application/json');
require_once '../../config/database.php';
require_once '../../config/session.php';

requireLogin();

// Récupère tous les prestataires
$stmt = $pdo->query("
    SELECT
        id,
        nom,
        type_service,
        telephone,
        email,
        adresse
    FROM prestataires
    ORDER BY nom
");

$prestataires = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    'prestataires' => $prestataires
], JSON_PRETTY_PRINT);
?>
