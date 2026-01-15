<?php
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

$stmt = $pdo->prepare("SELECT * FROM events WHERE id = ?");
$stmt->execute([$event_id]);
$event = $stmt->fetch(PDO::FETCH_ASSOC);

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

$ecart = $total_reel - $total_prevu;
$budget_total = floatval($budget['budget_total']);
$pourcentage = $budget_total > 0 ? ($total_reel / $budget_total) * 100 : 0;
$pourcentage_alloue = $budget_total > 0 ? ($total_prevu / $budget_total) * 100 : 0;

$width_depense = min($pourcentage, 100);
$width_alloue = min($pourcentage_alloue, 100);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Budget - <?php echo htmlspecialchars($event['nom']); ?></title>
    <link rel="stylesheet" href="/public/css/style.css">
</head>
<body>
    <div class="container">
        <nav class="sidebar">
            <h2>Gestion Events</h2>
            <ul>
                <li><a href="../dashboard.php">Dashboard</a></li>
                <li><a href="../events/liste.php">Événements</a></li>
                <li><a href="../carte.php">Carte</a></li>
                <li><a href="liste.php" class="active">Budget</a></li>
                <li><a href="../personnel/liste.php">Personnel</a></li>
                <li><a href="../prestataires/liste.php">Prestataires</a></li>
                <li><a href="../tasks/liste.php">Tâches</a></li>
            </ul>
            <div class="user-info">
                <p><strong><?php echo htmlspecialchars($user['nom']); ?></strong></p>
                <p><?php echo htmlspecialchars($user['role']); ?></p>
                <a href="../logout.php">Déconnexion</a>
            </div>
        </nav>
        
        <main class="main-content">
            <div class="page-header">
                <h1>Budget : <?php echo htmlspecialchars($event['nom']); ?></h1>
                <div>
                    <a href="modifier.php?event_id=<?php echo $event_id; ?>" class="btn-primary">Modifier</a>
                    <a href="liste.php" class="btn-secondary">← Retour</a>
                </div>
            </div>
            
            <!-- DEBUG -->
            <?php if (isset($_GET['debug'])): ?>
            <div style="background: #fff3cd; padding: 15px; margin-bottom: 20px; border-radius: 5px; font-family: monospace;">
                <strong>🔍 DEBUG BARRE DE PROGRESSION:</strong><br>
                Budget total: <?php echo $budget_total; ?> €<br>
                Total prévu: <?php echo $total_prevu; ?> €<br>
                Total réel: <?php echo $total_reel; ?> €<br>
                Pourcentage dépensé: <?php echo $pourcentage; ?> %<br>
                Width calculé: <?php echo $width_depense; ?> %<br>
                HTML de la barre: &lt;div style="width: <?php echo $width_depense; ?>%"&gt;
            </div>
            <?php endif; ?>
            
            <div class="budget-summary">
                <div class="budget-card">
                    <h3>Budget total</h3>
                    <p class="amount"><?php echo number_format($budget_total, 2); ?> €</p>
                </div>
                
                <div class="budget-card">
                    <h3>Budget alloué</h3>
                    <p class="amount"><?php echo number_format($total_prevu, 2); ?> €</p>
                    <p class="percentage"><?php echo number_format($pourcentage_alloue, 1); ?> %</p>
                </div>
                
                <div class="budget-card">
                    <h3>Dépensé</h3>
                    <p class="amount"><?php echo number_format($total_reel, 2); ?> €</p>
                </div>
                
                <div class="budget-card">
                    <h3>Écart</h3>
                    <p class="amount"><?php echo ($ecart > 0 ? '+' : '') . number_format($ecart, 2); ?> €</p>
                </div>
            </div>
            
            <div class="section">
                <h2>Progression</h2>
                <div style="width: 100%; height: 30px; background: #ecf0f1; border-radius: 15px; overflow: hidden; border: 2px solid #3498db;">
                    <div style="height: 100%; background: #667eea; width: <?php echo $width_depense; ?>%; transition: all 0.3s;"></div>
                </div>
                <p style="text-align: center; margin-top: 10px;">
                    <strong style="font-size: 18px; color: #667eea;"><?php echo number_format($pourcentage, 2); ?> %</strong> utilisé
                </p>
            </div>
            
            <div class="section">
                <h2>Catégories</h2>
                <?php if (empty($categories)): ?>
                    <p>Aucune catégorie</p>
                <?php else: ?>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Catégorie</th>
                                <th>Prévu</th>
                                <th>Réel</th>
                                <th>Écart</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($categories as $cat): ?>
                            <?php $cat_ecart = floatval($cat['montant_reel']) - floatval($cat['montant_prevu']); ?>
                            <tr>
                                <td><?php echo htmlspecialchars($cat['categorie']); ?></td>
                                <td><?php echo number_format($cat['montant_prevu'], 2); ?> €</td>
                                <td><?php echo number_format($cat['montant_reel'], 2); ?> €</td>
                                <td><?php echo ($cat_ecart > 0 ? '+' : '') . number_format($cat_ecart, 2); ?> €</td>
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