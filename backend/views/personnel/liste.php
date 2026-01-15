<?php
// Liste du personnel
require_once '../../config/database.php';
require_once '../../config/session.php';
require_once '../../config/helpers.php';
require_once '../../models/Personnel.php';

requireLogin();

$user = getCurrentUser();

// Recupere tout le personnel
$personnelModel = new Personnel($pdo);
$personnel = $personnelModel->getAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Personnel - Gestion</title>
    <link rel="stylesheet" href="..//public/css/style.css">
</head>
<body>
    <div class="container">
        <!-- Menu -->
        <nav class="sidebar">
            <h2>Gestion Events</h2>
            <ul>
                <li><a href="../dashboard.php">Dashboard</a></li>
                <li><a href="../events/liste.php">Événements</a></li>
                <li><a href="../budget/liste.php">Budget</a></li>
                <li><a href="liste.php" class="active">Personnel</a></li>
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
                <h1>Gestion du Personnel</h1>
                <a href="ajouter.php" class="btn-primary">+ Ajouter un membre</a>
            </div>
            
            <div class="section">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Prénom</th>
                            <th>Email</th>
                            <th>Téléphone</th>
                            <th>Poste</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($personnel)): ?>
                            <tr>
                                <td colspan="6" style="text-align: center;">Aucun membre de personnel</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($personnel as $p): ?>
                            <tr>
                                <td><strong><?php echo $p['nom']; ?></strong></td>
                                <td><?php echo $p['prenom']; ?></td>
                                <td><?php echo $p['email'] ?? '-'; ?></td>
                                <td><?php echo $p['telephone'] ?? '-'; ?></td>
                                <td><?php echo $p['poste'] ?? '-'; ?></td>
                                <td class="actions">
                                    <a href="modifier.php?id=<?php echo $p['id']; ?>" class="btn-small">Modifier</a>
                                    <a href="supprimer.php?id=<?php echo $p['id']; ?>" class="btn-small btn-danger" onclick="return confirm('Voulez-vous vraiment supprimer ce membre ?')">Supprimer</a>
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

