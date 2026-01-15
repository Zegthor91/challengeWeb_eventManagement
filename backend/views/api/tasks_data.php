<?php
header('Content-Type: application/json');
require_once '../../config/database.php';
require_once '../../config/session.php';

requireLogin();

// Récupère toutes les tâches avec leurs événements et assignés
$stmt = $pdo->query("
    SELECT
        t.*,
        e.nom as event_nom,
        u.nom as assigne_nom
    FROM tasks t
    LEFT JOIN events e ON t.event_id = e.id
    LEFT JOIN users u ON t.assigne_a = u.id
    ORDER BY t.date_limite DESC
");

$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    'tasks' => $tasks
], JSON_PRETTY_PRINT);
?>
