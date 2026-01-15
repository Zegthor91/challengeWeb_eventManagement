<?php
// Tableau de bord principal
require_once '../config/database.php';
require_once '../config/session.php';
require_once '../config/helpers.php';

requireLogin();

$user = getCurrentUser();

// Statistiques
$total_events = fetchOne("SELECT COUNT(*) as count FROM events")['count'];
$events_en_cours = fetchOne("SELECT COUNT(*) as count FROM events WHERE statut = 'en_cours'")['count'];
$events_termines = fetchOne("SELECT COUNT(*) as count FROM events WHERE statut = 'termine'")['count'];
$total_tasks = fetchOne("SELECT COUNT(*) as count FROM tasks")['count'];
$tasks_a_faire = fetchOne("SELECT COUNT(*) as count FROM tasks WHERE statut = 'a_faire'")['count'];

// Evenements recents
$recent_events = fetchAll("SELECT * FROM events ORDER BY created_at DESC LIMIT 5");
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Gestion Événements</title>
    <link rel="stylesheet" href="/public/css/style.css">
</head>
<body>
    <div class="container">
        <nav class="sidebar">
            <h2>Gestion Events</h2>
            <ul>
                <li><a href="dashboard.php" class="active">Dashboard</a></li>
                <li><a href="events/liste.php">Événements</a></li>
                <li><a href="budget/liste.php">Budget</a></li>
                <li><a href="personnel/liste.php">Personnel</a></li>
                <li><a href="prestataires/liste.php">Prestataires</a></li>
                <li><a href="tasks/liste.php">Tâches</a></li>
            </ul>
            <div class="user-info">
                <p><strong><?php echo $user['nom']; ?></strong></p>
                <p><?php echo $user['role']; ?></p>
                <a href="logout.php">Déconnexion</a>
            </div>
        </nav>
        
        <main class="main-content">
            <h1>Tableau de bord</h1>
            
            <div class="stats-grid">
                <div class="stat-card">
                    <h3>Total Événements</h3>
                    <p class="stat-number"><?php echo $total_events; ?></p>
                </div>
                
                <div class="stat-card">
                    <h3>En cours</h3>
                    <p class="stat-number"><?php echo $events_en_cours; ?></p>
                </div>
                
                <div class="stat-card">
                    <h3>Terminés</h3>
                    <p class="stat-number"><?php echo $events_termines; ?></p>
                </div>
                
                <div class="stat-card">
                    <h3>Tâches à faire</h3>
                    <p class="stat-number"><?php echo $tasks_a_faire; ?> / <?php echo $total_tasks; ?></p>
                </div>
            </div>
            
            <div class="section">
                <h2>Événements récents</h2>
                
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Type</th>
                            <th>Date</th>
                            <th>Lieu</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recent_events as $event): ?>
                        <tr>
                            <td><?php echo $event['nom']; ?></td>
                            <td><?php echo $event['type_event']; ?></td>
                            <td><?php echo formatDate($event['date_debut']); ?></td>
                            <td><?php echo $event['lieu']; ?></td>
                            <td><span class="badge badge-<?php echo $event['statut']; ?>"><?php echo str_replace('_', ' ', $event['statut']); ?></span></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>
