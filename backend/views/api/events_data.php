<?php
header('Content-Type: application/json');
require_once '../../config/database.php';
require_once '../../config/session.php';

requireLogin();

// Récupère tous les événements avec responsable
$stmt = $pdo->query("
    SELECT e.*, u.nom as responsable_nom 
    FROM events e 
    LEFT JOIN users u ON e.responsable_id = u.id 
    ORDER BY e.date_debut DESC
");

$events = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    'events' => $events
], JSON_PRETTY_PRINT);
?>