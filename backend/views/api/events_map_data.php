<?php
header('Content-Type: application/json');
require_once '../../config/database.php';
require_once '../../config/session.php';

requireLogin();

// Récupère tous les événements avec leurs lieux
$stmt = $pdo->query("
    SELECT
        e.id,
        e.nom,
        e.type_event,
        e.date_debut,
        e.date_fin,
        e.lieu,
        e.statut,
        e.description,
        u.nom as responsable_nom
    FROM events e
    LEFT JOIN users u ON e.responsable_id = u.id
    WHERE e.lieu IS NOT NULL AND e.lieu != ''
    ORDER BY e.date_debut DESC
");

$events = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    'events' => $events
], JSON_PRETTY_PRINT);
?>
