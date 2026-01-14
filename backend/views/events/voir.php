<?php
<<<<<<< HEAD
// Details du budget d'un evenement
require_once '../../config/database.php';
require_once '../../config/session.php';
require_once '../../config/helpers.php';
require_once '../../models/Budget.php';
=======
// Details d'un evenement
require_once '../../config/database.php';
require_once '../../config/session.php';
require_once '../../config/helpers.php';
require_once '../../models/Event.php';
>>>>>>> 1b402c43edf8776eb44ed747824a00ed1ca1350d

requireLogin();

$user = getCurrentUser();

<<<<<<< HEAD
$event_id = $_GET['event_id'] ?? null;
=======
$event_id = $_GET['id'] ?? null;
>>>>>>> 1b402c43edf8776eb44ed747824a00ed1ca1350d

if (!$event_id) {
    redirect('liste.php');
}

<<<<<<< HEAD
// Recupere l'evenement
$event = fetchOne("SELECT * FROM events WHERE id = ?", [$event_id]);
=======
$eventModel = new Event($pdo);
$event = $eventModel->getById($event_id);
>>>>>>> 1b402c43edf8776eb44ed747824a00ed1ca1350d

if (!$event) {
    redirect('liste.php');
}

<<<<<<< HEAD
$budgetModel = new Budget($pdo);
$budget = $budgetModel->getByEventId($event_id);

if (!$budget) {
    redirect("ajouter.php?event_id=$event_id");
}

$categories = $budgetModel->getCategories($budget['id']);
$total_prevu = $budgetModel->getTotalPrevu($budget['id']);
$total_reel = $budgetModel->getTotalReel($budget['id']);

// Calcul l'ecart
$ecart = $total_reel - $total_prevu;
$pourcentage = $total_prevu > 0 ? ($total_reel / $total_prevu) * 100 : 0;
=======
// Recupere les infos liées a l'evenement
$budget = fetchOne("SELECT * FROM budgets WHERE event_id = $1", [$event_id]);
$personnel = fetchAll("SELECT p.*, ep.role_event FROM personnel p 
                       JOIN event_personnel ep ON p.id = ep.personnel_id 
                       WHERE ep.event_id = $1", [$event_id]);
$prestataires = fetchAll("SELECT pr.*, epr.cout, epr.evaluation FROM prestataires pr 
                          JOIN event_prestataires epr ON pr.id = epr.prestataire_id 
                          WHERE epr.event_id = $1", [$event_id]);
$tasks = fetchAll("SELECT t.*, u.nom as assigne_nom FROM tasks t 
                   LEFT JOIN users u ON t.assigne_a = u.id 
                   WHERE t.event_id = $1 AND t.parent_task_id IS NULL 
                   ORDER BY t.priorite DESC, t.date_limite", [$event_id]);
>>>>>>> 1b402c43edf8776eb44ed747824a00ed1ca1350d
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<<<<<<< HEAD
    <title>Budget - <?php echo $event['nom']; ?></title>
    <link rel="stylesheet" href="../../../public/css/style.css">
=======
    <title>Détails - <?php echo $event['nom']; ?></title>
    <link rel="stylesheet" href="/public/css/style.css">
>>>>>>> 1b402c43edf8776eb44ed747824a00ed1ca1350d
</head>
<body>
    <div class="container">
        <!-- Menu -->
        <nav class="sidebar">
            <h2>Gestion Events</h2>
            <ul>
                <li><a href="../dashboard.php">Dashboard</a></li>
<<<<<<< HEAD
                <li><a href="../events/liste.php">Événements</a></li>
                <li><a href="liste.php" class="active">Budget</a></li>
=======
                <li><a href="liste.php" class="active">Événements</a></li>
                <li><a href="../budget/liste.php">Budget</a></li>
>>>>>>> 1b402c43edf8776eb44ed747824a00ed1ca1350d
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
        
        <!-- Contenu -->
        <main class="main-content">
            <div class="page-header">
<<<<<<< HEAD
                <h1>Budget : <?php echo $event['nom']; ?></h1>
                <div>
                    <a href="modifier.php?event_id=<?php echo $event_id; ?>" class="btn-primary">Modifier</a>
=======
                <h1><?php echo $event['nom']; ?></h1>
                <div>
                    <a href="modifier.php?id=<?php echo $event['id']; ?>" class="btn-primary">Modifier</a>
>>>>>>> 1b402c43edf8776eb44ed747824a00ed1ca1350d
                    <a href="liste.php" class="btn-secondary">← Retour</a>
                </div>
            </div>
            
<<<<<<< HEAD
            <!-- Resume du budget -->
            <div class="budget-summary">
                <div class="budget-card">
                    <h3>Budget prévu</h3>
                    <p class="amount"><?php echo number_format($total_prevu, 2); ?> €</p>
                </div>
                
                <div class="budget-card">
                    <h3>Dépensé (réel)</h3>
                    <p class="amount"><?php echo number_format($total_reel, 2); ?> €</p>
                </div>
                
                <div class="budget-card <?php echo $ecart < 0 ? 'positive' : ($ecart > 0 ? 'negative' : ''); ?>">
                    <h3>Écart</h3>
                    <p class="amount"><?php echo ($ecart > 0 ? '+' : '') . number_format($ecart, 2); ?> €</p>
                    <p class="percentage"><?php echo number_format($pourcentage, 1); ?>% du budget</p>
                </div>
                
                <div class="budget-card">
                    <h3>Budget total</h3>
                    <p class="amount"><?php echo number_format($budget['budget_total'], 2); ?> €</p>
                </div>
            </div>
            
            <!-- Progression -->
            <div class="section">
                <h2>Progression</h2>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: <?php echo min($pourcentage, 100); ?>%;"></div>
                </div>
                <p style="text-align: center; margin-top: 10px;">
                    <?php echo number_format($pourcentage, 1); ?>% du budget utilisé
                </p>
            </div>
            
            <!-- Categories -->
            <div class="section">
                <h2>Catégories de budget</h2>
                
                <?php if (empty($categories)): ?>
                    <p>Aucune catégorie définie</p>
                    <a href="modifier.php?event_id=<?php echo $event_id; ?>" class="btn-small">Ajouter des catégories</a>
                <?php else: ?>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Catégorie</th>
                                <th>Montant prévu</th>
                                <th>Montant réel</th>
                                <th>Écart</th>
                                <th>%</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($categories as $cat): ?>
                            <?php 
                                $cat_ecart = $cat['montant_reel'] - $cat['montant_prevu'];
                                $cat_pct = $cat['montant_prevu'] > 0 ? ($cat['montant_reel'] / $cat['montant_prevu']) * 100 : 0;
                            ?>
                            <tr>
                                <td><strong><?php echo $cat['categorie']; ?></strong></td>
                                <td><?php echo number_format($cat['montant_prevu'], 2); ?> €</td>
                                <td><?php echo number_format($cat['montant_reel'], 2); ?> €</td>
                                <td class="<?php echo $cat_ecart < 0 ? 'text-success' : ($cat_ecart > 0 ? 'text-danger' : ''); ?>">
                                    <?php echo ($cat_ecart > 0 ? '+' : '') . number_format($cat_ecart, 2); ?> €
                                </td>
                                <td><?php echo number_format($cat_pct, 1); ?>%</td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
=======
            <!-- Informations principales -->
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
            
            <!-- Budget -->
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
            
            <!-- Personnel affecté -->
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
            
            <!-- Prestataires -->
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
            
            <!-- Taches -->
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
>>>>>>> 1b402c43edf8776eb44ed747824a00ed1ca1350d
                <?php endif; ?>
            </div>
        </main>
    </div>
</body>
</html>