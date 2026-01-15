<?php
header('Content-Type: application/json');
require_once '../../config/database.php';
require_once '../../config/session.php';

requireLogin();

$user = getCurrentUser();
$notifications = [];

// 1. Événements à venir (dans les 7 prochains jours)
$stmt = $pdo->query("
    SELECT id, nom, date_debut, statut
    FROM events
    WHERE date_debut >= CURRENT_DATE
    AND date_debut <= CURRENT_DATE + INTERVAL '7 days'
    AND statut != 'termine'
    ORDER BY date_debut ASC
");

$upcomingEvents = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($upcomingEvents as $event) {
    $daysUntil = (new DateTime($event['date_debut']))->diff(new DateTime())->days;

    $urgency = 'low';
    $icon = '📅';
    if ($daysUntil <= 1) {
        $urgency = 'high';
        $icon = '🔥';
        $message = "L'événement \"{$event['nom']}\" commence demain !";
    } elseif ($daysUntil <= 3) {
        $urgency = 'medium';
        $icon = '⚠️';
        $message = "L'événement \"{$event['nom']}\" commence dans {$daysUntil} jours";
    } else {
        $message = "L'événement \"{$event['nom']}\" commence dans {$daysUntil} jours";
    }

    $notifications[] = [
        'id' => 'event_' . $event['id'],
        'type' => 'event_upcoming',
        'urgency' => $urgency,
        'icon' => $icon,
        'title' => 'Événement à venir',
        'message' => $message,
        'link' => '/views/events/voir.php?id=' . $event['id'],
        'date' => $event['date_debut']
    ];
}

// 2. Tâches en retard
$stmt = $pdo->query("
    SELECT t.id, t.titre, t.date_limite, t.priorite, e.nom as event_nom
    FROM tasks t
    LEFT JOIN events e ON t.event_id = e.id
    WHERE t.statut != 'termine'
    AND t.date_limite < CURRENT_DATE
    ORDER BY t.date_limite DESC
    LIMIT 10
");

$overdueTasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($overdueTasks as $task) {
    $daysLate = (new DateTime())->diff(new DateTime($task['date_limite']))->days;

    $notifications[] = [
        'id' => 'task_overdue_' . $task['id'],
        'type' => 'task_overdue',
        'urgency' => 'high',
        'icon' => '⏰',
        'title' => 'Tâche en retard',
        'message' => "La tâche \"{$task['titre']}\" est en retard de {$daysLate} jour(s)",
        'link' => '/views/tasks/liste.php',
        'date' => $task['date_limite']
    ];
}

// 3. Tâches urgentes non assignées
$stmt = $pdo->query("
    SELECT t.id, t.titre, t.date_limite, e.nom as event_nom
    FROM tasks t
    LEFT JOIN events e ON t.event_id = e.id
    WHERE t.assigne_a IS NULL
    AND t.priorite = 'haute'
    AND t.statut != 'termine'
    ORDER BY t.date_limite ASC
    LIMIT 5
");

$unassignedTasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($unassignedTasks as $task) {
    $notifications[] = [
        'id' => 'task_unassigned_' . $task['id'],
        'type' => 'task_unassigned',
        'urgency' => 'medium',
        'icon' => '👤',
        'title' => 'Tâche non assignée',
        'message' => "La tâche urgente \"{$task['titre']}\" n'est assignée à personne",
        'link' => '/views/tasks/liste.php',
        'date' => $task['date_limite']
    ];
}

// 4. Alertes budget (>80% utilisé)
$stmt = $pdo->query("
    SELECT
        e.id,
        e.nom,
        b.budget_total,
        COALESCE(SUM(bc.montant_reel), 0) as total_depense,
        CASE
            WHEN b.budget_total > 0 THEN ROUND((COALESCE(SUM(bc.montant_reel), 0) / b.budget_total) * 100)
            ELSE 0
        END as pourcentage_utilise
    FROM events e
    INNER JOIN budgets b ON e.id = b.event_id
    LEFT JOIN budget_categories bc ON b.id = bc.budget_id
    WHERE b.budget_total > 0
    GROUP BY e.id, e.nom, b.budget_total
    HAVING ROUND((COALESCE(SUM(bc.montant_reel), 0) / b.budget_total) * 100) >= 80
    ORDER BY pourcentage_utilise DESC
    LIMIT 5
");

$budgetAlerts = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($budgetAlerts as $alert) {
    $urgency = $alert['pourcentage_utilise'] >= 95 ? 'high' : 'medium';
    $icon = $alert['pourcentage_utilise'] >= 95 ? '🚨' : '💰';

    $notifications[] = [
        'id' => 'budget_' . $alert['id'],
        'type' => 'budget_alert',
        'urgency' => $urgency,
        'icon' => $icon,
        'title' => 'Alerte budget',
        'message' => "Le budget de \"{$alert['nom']}\" est utilisé à {$alert['pourcentage_utilise']}%",
        'link' => '/views/budget/liste.php',
        'date' => date('Y-m-d H:i:s')
    ];
}

// Trier par urgence puis par date
usort($notifications, function($a, $b) {
    $urgencyOrder = ['high' => 0, 'medium' => 1, 'low' => 2];

    if ($urgencyOrder[$a['urgency']] !== $urgencyOrder[$b['urgency']]) {
        return $urgencyOrder[$a['urgency']] - $urgencyOrder[$b['urgency']];
    }

    return strtotime($b['date']) - strtotime($a['date']);
});

echo json_encode([
    'notifications' => $notifications,
    'unread_count' => count($notifications)
], JSON_PRETTY_PRINT);
?>
