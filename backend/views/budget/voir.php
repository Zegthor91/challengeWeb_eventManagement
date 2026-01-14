<?php
// Details du budget d'un evenement
require_once '../../config/database.php';
require_once '../../config/session.php';
require_once '../../config/helpers.php';
require_once '../../models/Budget.php';

requireLogin();

$user = getCurrentUser();

$event_id = $_GET['event_id'] ?? null;

if (!$event_id) {
    redirect('liste.php');
}

// Recupere l'evenement
$event = fetchOne("SELECT * FROM events WHERE id = $1", [$event_id]);

if (!$event) {
    redirect('liste.php');
}

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
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Budget - <?php echo $event['nom']; ?></title>
    <link rel="stylesheet" href="/public/css/style.css">
</head>
<body>
    <div class="container">
        <!-- Menu -->
        <nav class="sidebar">
            <h2>Gestion Events</h2>
            <ul>
                <li><a href="../dashboard.php">Dashboard</a></li>
                <li><a href="../events/liste.php">Événements</a></li>
                <li><a href="liste.php" class="active">Budget</a></li>
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
                <h1>Budget : <?php echo $event['nom']; ?></h1>
                <div>
                    <a href="modifier.php?event_id=<?php echo $event_id; ?>" class="btn-primary">Modifier</a>
                    <a href="liste.php" class="btn-secondary">← Retour</a>
                </div>
            </div>
            
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
                <?php endif; ?>
            </div>
        </main>
    </div>
</body>
</html>
