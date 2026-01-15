<?php
// Details d'un evenement
require_once '../../config/database.php';
require_once '../../config/session.php';
require_once '../../config/helpers.php';
require_once '../../models/Event.php';

requireLogin();

$user = getCurrentUser();

$event_id = $_GET['id'] ?? null;

if (!$event_id) {
    redirect('liste.php');
}

$eventModel = new Event($pdo);
$event = $eventModel->getById($event_id);

if (!$event) {
    redirect('liste.php');
}

// Recupere les infos liées a l'evenement
$budget = fetchOne("SELECT * FROM budgets WHERE event_id = ?", [$event_id]);
$personnel = fetchAll("SELECT p.*, ep.role_event FROM personnel p 
                       JOIN event_personnel ep ON p.id = ep.personnel_id 
                       WHERE ep.event_id = ?", [$event_id]);
$prestataires = fetchAll("SELECT pr.*, epr.cout, epr.evaluation FROM prestataires pr 
                          JOIN event_prestataires epr ON pr.id = epr.prestataire_id 
                          WHERE epr.event_id = ?", [$event_id]);
$tasks = fetchAll("SELECT t.*, u.nom as assigne_nom FROM tasks t 
                   LEFT JOIN users u ON t.assigne_a = u.id 
                   WHERE t.event_id = ? AND t.parent_task_id IS NULL 
                   ORDER BY t.priorite DESC, t.date_limite", [$event_id]);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails - <?php echo $event['nom']; ?></title>
    <link rel="stylesheet" href="..//public/css/style.css">
</head>
<body>
    <div class="container">
        <nav class="sidebar">
            <h2>Gestion Events</h2>
            <ul>
                <li><a href="../dashboard.php">Dashboard</a></li>
                <li><a href="liste.php" class="active">Événements</a></li>
                <li><a href="../budget/liste.php">Budget</a></li>
                <li><a href="../personnel/liste.php">Personnel</a></li>
                <li><a href="../prestataires/liste.php">Prestataires</a></li>
                <li><a href="../tasks/liste.php">Tâches</a></li>
            </ul>
            <div class="user-info">
                <p><strong><?php echo $user['nom']; ?></strong></p>
                <p><?php echo $user['role']; ?></p>
                <a href="../logout.php">Déconnexion</a>
            </div>
        </nav>
        
        <main class="main-content">
            <div class="page-header">
                <h1><?php echo $event['nom']; ?></h1>
                <div>
                    <a href="modifier.php?id=<?php echo $event['id']; ?>" class="btn-primary">Modifier</a>
                    <a href="liste.php" class="btn-secondary">← Retour</a>
                </div>
            </div>
            
            <div class="section">
                <h2>Informations générales</h2>
                <div class="info-grid">
                    <div class="info-item">
                        <strong>Type :</strong>
                        <span><?php echo $event['type_event'] ?? 'Non défini'; ?></span>
                    </div>
                    <div class="info-item">
                        <strong>Statut :</strong>
                        <span class="badge badge-<?php echo $event['statut']; ?>"><?php echo str_replace('_', ' ', $event['statut']); ?></span>
                    </div>
                    <div class="info-item">
                        <strong>Date de début :</strong>
                        <span><?php echo formatDate($event['date_debut']); ?></span>
                    </div>
                    <div class="info-item">
                        <strong>Date de fin :</strong>
                        <span><?php echo $event['date_fin'] ? formatDate($event['date_fin']) : 'Non définie'; ?></span>
                    </div>
                    <div class="info-item">
                        <strong>Lieu :</strong>
                        <span><?php echo $event['lieu'] ?? 'Non défini'; ?></span>
                    </div>
                    <div class="info-item">
                        <strong>Responsable :</strong>
                        <span><?php echo $event['responsable_nom'] ?? 'Non assigné'; ?></span>
                    </div>
                </div>
                
                <?php if ($event['description']): ?>
                    <div class="description">
                        <strong>Description :</strong>
                        <p><?php echo nl2br($event['description']); ?></p>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="section">
                <h2>Budget</h2>
                <?php if ($budget): ?>
                    <p>Budget total : <strong><?php echo number_format($budget['budget_total'], 2); ?> €</strong></p>
                    <a href="../budget/voir.php?event_id=<?php echo $event['id']; ?>" class="btn-small">Voir détails</a>
                <?php else: ?>
                    <p>Aucun budget défini</p>
                    <a href="../budget/ajouter.php?event_id=<?php echo $event['id']; ?>" class="btn-small">Créer budget</a>
                <?php endif; ?>
            </div>
            
            <div class="section">
                <h2>Personnel affecté (<?php echo count($personnel); ?>)</h2>
                <?php if (!empty($personnel)): ?>
                    <ul class="simple-list">
                        <?php foreach ($personnel as $p): ?>
                            <li><?php echo $p['prenom'] . ' ' . $p['nom']; ?> - <em><?php echo $p['role_event']; ?></em></li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p>Aucun personnel affecté</p>
                <?php endif; ?>
            </div>
            
            <div class="section">
                <h2>Prestataires (<?php echo count($prestataires); ?>)</h2>
                <?php if (!empty($prestataires)): ?>
                    <ul class="simple-list">
                        <?php foreach ($prestataires as $pr): ?>
                            <li>
                                <?php echo $pr['nom']; ?> (<?php echo $pr['type_service']; ?>)
                                <?php if ($pr['cout']): ?>
                                    - <?php echo number_format($pr['cout'], 2); ?> €
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p>Aucun prestataire</p>
                <?php endif; ?>
            </div>
            
            <div class="section">
                <h2>Tâches (<?php echo count($tasks); ?>)</h2>
                <?php if (!empty($tasks)): ?>
                    <ul class="simple-list">
                        <?php foreach ($tasks as $task): ?>
                            <li>
                                <strong><?php echo $task['titre']; ?></strong>
                                <span class="badge badge-<?php echo $task['statut']; ?>"><?php echo str_replace('_', ' ', $task['statut']); ?></span>
                                <?php if ($task['assigne_nom']): ?>
                                    - Assigné à : <?php echo $task['assigne_nom']; ?>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p>Aucune tâche</p>
                <?php endif; ?>
            </div>
        </main>
    </div>
</body>
</html>
