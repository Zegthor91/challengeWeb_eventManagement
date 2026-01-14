<?php
// Liste des evenements
require_once '../../config/database.php';
require_once '../../config/session.php';
require_once '../../config/helpers.php';
require_once '../../models/Event.php';

// Verifie que l'utilisateur est connecté
requireLogin();

$user = getCurrentUser();

// Recupere tous les evenements
$eventModel = new Event($pdo);
$events = $eventModel->getAll();

// Filtre par statut si demandé
$statut_filtre = isset($_GET['statut']) ? $_GET['statut'] : null;
if ($statut_filtre) {
    $events = $eventModel->getByStatut($statut_filtre);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Événements - Gestion</title>
    <link rel="stylesheet" href="/public/css/style.css">
</head>
<body>
    <div class="container">
        <!-- Menu -->
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
        
        <!-- Contenu principal -->
        <main class="main-content">
            <div class="page-header">
                <h1>Gestion des Événements</h1>
                <a href="ajouter.php" class="btn-primary">+ Nouvel événement</a>
            </div>
            
            <!-- Filtres -->
            <div class="filters">
                <a href="liste.php" class="filter-btn <?php echo !$statut_filtre ? 'active' : ''; ?>">Tous</a>
                <a href="liste.php?statut=en_preparation" class="filter-btn <?php echo $statut_filtre == 'en_preparation' ? 'active' : ''; ?>">En préparation</a>
                <a href="liste.php?statut=en_cours" class="filter-btn <?php echo $statut_filtre == 'en_cours' ? 'active' : ''; ?>">En cours</a>
                <a href="liste.php?statut=termine" class="filter-btn <?php echo $statut_filtre == 'termine' ? 'active' : ''; ?>">Terminés</a>
            </div>
            
            <!-- Tableau des evenements -->
            <div class="section">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Type</th>
                            <th>Date début</th>
                            <th>Date fin</th>
                            <th>Lieu</th>
                            <th>Responsable</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($events)): ?>
                            <tr>
                                <td colspan="8" style="text-align: center;">Aucun événement trouvé</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($events as $event): ?>
                            <tr>
                                <td><strong><?php echo $event['nom']; ?></strong></td>
                                <td><?php echo $event['type_event']; ?></td>
                                <td><?php echo formatDate($event['date_debut']); ?></td>
                                <td><?php echo $event['date_fin'] ? formatDate($event['date_fin']) : '-'; ?></td>
                                <td><?php echo $event['lieu']; ?></td>
                                <td><?php echo $event['responsable_nom'] ?? 'Non assigné'; ?></td>
                                <td><span class="badge badge-<?php echo $event['statut']; ?>"><?php echo str_replace('_', ' ', $event['statut']); ?></span></td>
                                <td class="actions">
                                    <a href="voir.php?id=<?php echo $event['id']; ?>" class="btn-small">Voir</a>
                                    <a href="modifier.php?id=<?php echo $event['id']; ?>" class="btn-small">Modifier</a>
                                    <a href="supprimer.php?id=<?php echo $event['id']; ?>" class="btn-small btn-danger" onclick="return confirm('Voulez-vous vraiment supprimer cet événement ?')">Supprimer</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>