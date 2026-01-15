<?php
header('Content-Type: application/json');
require_once '../../config/database.php';
require_once '../../config/session.php';

requireLogin();

// Récupère tous les événements avec leur budget et calculs
$stmt = $pdo->query("
    SELECT
        e.id,
        e.nom,
        e.date_debut,
        e.statut,
        b.id as budget_id,
        b.budget_total,
        COALESCE(SUM(bc.montant_reel), 0) as total_depense,
        CASE
            WHEN b.budget_total > 0 THEN ROUND((COALESCE(SUM(bc.montant_reel), 0) / b.budget_total) * 100)
            ELSE 0
        END as pourcentage_utilise
    FROM events e
    LEFT JOIN budgets b ON e.id = b.event_id
    LEFT JOIN budget_categories bc ON b.id = bc.budget_id
    GROUP BY e.id, e.nom, e.date_debut, e.statut, b.id, b.budget_total
    ORDER BY e.date_debut DESC
");

$budgets = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    'budgets' => $budgets
], JSON_PRETTY_PRINT);
?>
