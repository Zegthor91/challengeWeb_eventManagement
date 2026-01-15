<?php
header('Content-Type: application/json');
require_once '../../config/database.php';
require_once '../../config/session.php';

requireLogin();

// Stats générales
$stats = [];

// Total événements
$stmt = $pdo->query("SELECT COUNT(*) as count FROM events");
$stats['total_events'] = $stmt->fetch()['count'];

// Événements en cours
$stmt = $pdo->query("SELECT COUNT(*) as count FROM events WHERE statut = 'en_cours'");
$stats['events_en_cours'] = $stmt->fetch()['count'];

// Total tâches
$stmt = $pdo->query("SELECT COUNT(*) as count FROM tasks");
$stats['total_tasks'] = $stmt->fetch()['count'];

// Tâches à faire
$stmt = $pdo->query("SELECT COUNT(*) as count FROM tasks WHERE statut = 'a_faire'");
$stats['tasks_a_faire'] = $stmt->fetch()['count'];

// Budget total
$stmt = $pdo->query("SELECT SUM(budget_total) as total, COUNT(*) as count FROM budgets");
$budget_data = $stmt->fetch();
$stats['budget_total'] = $budget_data['total'] ?? 0;
$stats['budget_count'] = $budget_data['count'] ?? 0;

// Total personnel
$stmt = $pdo->query("SELECT COUNT(*) as count FROM personnel");
$stats['total_personnel'] = $stmt->fetch()['count'];

// Prochains événements (5 prochains)
$stmt = $pdo->query("
    SELECT id, nom, type_event, date_debut, date_fin, lieu, statut 
    FROM events 
    WHERE date_debut >= CURRENT_DATE 
    ORDER BY date_debut 
    LIMIT 5
");
$upcomingEvents = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Tâches urgentes (haute priorité, pas terminées)
$stmt = $pdo->query("
    SELECT t.id, t.titre, t.priorite, t.date_limite, t.statut, e.nom as event_nom
    FROM tasks t
    LEFT JOIN events e ON t.event_id = e.id
    WHERE t.priorite = 'haute' AND t.statut != 'terminee'
    ORDER BY t.date_limite
    LIMIT 5
");
$urgentTasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Alertes budget (> 80% utilisé)
$stmt = $pdo->query("
    SELECT 
        e.id as event_id,
        e.nom as event_nom,
        b.budget_total,
        COALESCE(SUM(bc.montant_reel), 0) as total_reel,
        CASE 
            WHEN b.budget_total > 0 THEN ROUND((COALESCE(SUM(bc.montant_reel), 0) / b.budget_total) * 100)
            ELSE 0 
        END as percentage
    FROM events e
    JOIN budgets b ON e.id = b.event_id
    LEFT JOIN budget_categories bc ON b.id = bc.budget_id
    GROUP BY e.id, e.nom, b.budget_total
    HAVING CASE 
            WHEN b.budget_total > 0 THEN ROUND((COALESCE(SUM(bc.montant_reel), 0) / b.budget_total) * 100)
            ELSE 0 
        END > 80
    ORDER BY percentage DESC
    LIMIT 5
");
$budgetAlerts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Réponse JSON
echo json_encode([
    'stats' => $stats,
    'upcomingEvents' => $upcomingEvents,
    'urgentTasks' => $urgentTasks,
    'budgetAlerts' => $budgetAlerts
], JSON_PRETTY_PRINT);
?>