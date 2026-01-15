<?php
require_once '../../config/database.php';
require_once '../../config/session.php';
require_once '../../config/helpers.php';
require_once '../../controllers/EventController.php';

requireLogin();

$event_id = $_GET['id'] ?? null;

if (!$event_id) {
    redirect('liste.php');
}

// Récupère l'événement original
$stmt = $pdo->prepare("SELECT * FROM events WHERE id = ?");
$stmt->execute([$event_id]);
$event = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$event) {
    redirect('liste.php');
}

// Crée une copie
$controller = new EventController($pdo);

$data = [
    'nom' => $event['nom'] . ' (Copie)',
    'type_event' => $event['type_event'],
    'date_debut' => $event['date_debut'],
    'date_fin' => $event['date_fin'],
    'lieu' => $event['lieu'],
    'description' => $event['description'],
    'responsable_id' => $event['responsable_id'],
    'statut' => 'en_preparation' // Toujours en préparation
];

$result = $controller->store($data);

if ($result['success']) {
    $_SESSION['message'] = "Événement dupliqué avec succès ! ID: " . $result['id'];
    
    // Duplique aussi le budget si il existe
    $stmt_budget = $pdo->prepare("SELECT * FROM budgets WHERE event_id = ?");
    $stmt_budget->execute([$event_id]);
    $budget = $stmt_budget->fetch(PDO::FETCH_ASSOC);
    
    if ($budget) {
        $stmt_new_budget = $pdo->prepare("INSERT INTO budgets (event_id, budget_total) VALUES (?, ?)");
        $stmt_new_budget->execute([$result['id'], $budget['budget_total']]);
        $new_budget_id = $pdo->lastInsertId();
        
        // Duplique les catégories de budget
        $stmt_cat = $pdo->prepare("SELECT * FROM budget_categories WHERE budget_id = ?");
        $stmt_cat->execute([$budget['id']]);
        $categories = $stmt_cat->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($categories as $cat) {
            $stmt_new_cat = $pdo->prepare("INSERT INTO budget_categories (budget_id, categorie, montant_prevu, montant_reel) VALUES (?, ?, ?, 0)");
            $stmt_new_cat->execute([$new_budget_id, $cat['categorie'], $cat['montant_prevu']]);
        }
    }
    
    // Duplique les tâches
    $stmt_tasks = $pdo->prepare("SELECT * FROM tasks WHERE event_id = ? AND parent_task_id IS NULL");
    $stmt_tasks->execute([$event_id]);
    $tasks = $stmt_tasks->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($tasks as $task) {
        $stmt_new_task = $pdo->prepare("INSERT INTO tasks (event_id, titre, description, statut, priorite, date_limite, assigne_a) VALUES (?, ?, ?, 'a_faire', ?, ?, ?)");
        $stmt_new_task->execute([
            $result['id'],
            $task['titre'],
            $task['description'],
            $task['priorite'],
            $task['date_limite'],
            $task['assigne_a']
        ]);
    }
    
    redirect('voir.php?id=' . $result['id']);
} else {
    $_SESSION['error'] = $result['message'];
    redirect('liste.php');
}
?>