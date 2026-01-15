<?php
// Liste des budgets par evenement
require_once '../../config/database.php';
require_once '../../config/session.php';
require_once '../../config/helpers.php';

requireLogin();

$user = getCurrentUser();

// Recupere tous les evenements avec leur budget
$sql = "SELECT e.id, e.nom, e.date_debut, e.statut, b.budget_total, b.id as budget_id
        FROM events e
        LEFT JOIN budgets b ON e.id = b.event_id
        ORDER BY e.date_debut DESC";

$events = fetchAll($sql);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Budgets - Gestion</title>
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
            <h1>Gestion des budgets</h1>
            
            <div class="section">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Événement</th>
                            <th>Date</th>
                            <th>Statut</th>
                            <th>Budget total</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($events as $event): ?>
                        <tr>
                            <td><strong><?php echo $event['nom']; ?></strong></td>
                            <td><?php echo formatDate($event['date_debut']); ?></td>
                            <td><span class="badge badge-<?php echo $event['statut']; ?>"><?php echo str_replace('_', ' ', $event['statut']); ?></span></td>
                            <td>
                                <?php if ($event['budget_total']): ?>
                                    <?php echo number_format($event['budget_total'], 2); ?> €
                                <?php else: ?>
                                    <em>Non défini</em>
                                <?php endif; ?>
                            </td>
                            <td class="actions">
                                <?php if ($event['budget_id']): ?>
                                    <a href="voir.php?event_id=<?php echo $event['id']; ?>" class="btn-small">Voir détails</a>
                                    <a href="modifier.php?event_id=<?php echo $event['id']; ?>" class="btn-small">Modifier</a>
                                <?php else: ?>
                                    <a href="ajouter.php?event_id=<?php echo $event['id']; ?>" class="btn-small">Créer budget</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>

