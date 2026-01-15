<?php
// Formulaire d'ajout de personnel
require_once '../../config/database.php';
require_once '../../config/session.php';
require_once '../../config/helpers.php';
require_once '../../controllers/PersonnelController.php';

requireLogin();

$user = getCurrentUser();
$message = '';
$error = '';

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller = new PersonnelController($pdo);
    
    $data = [
        'nom' => clean($_POST['nom']),
        'prenom' => clean($_POST['prenom']),
        'email' => clean($_POST['email']),
        'telephone' => clean($_POST['telephone']),
        'poste' => clean($_POST['poste'])
    ];
    
    $result = $controller->store($data);
    
    if ($result['success']) {
        $message = $result['message'];
        header("refresh:2;url=liste.php");
    } else {
        $error = $result['message'];
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un membre</title>
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
                <li><a href="../carte.php">Carte</a></li>
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
                <h1>Ajouter un membre du personnel</h1>
                <a href="liste.php" class="btn-secondary">← Retour</a>
            </div>
            
            <?php if ($message): ?>
                <div class="success-message"><?php echo $message; ?></div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="error-message"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <div class="section">
                <form method="POST" action="" class="form-event">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Nom *</label>
                            <input type="text" name="nom" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Prénom *</label>
                            <input type="text" name="prenom" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email">
                        </div>
                        
                        <div class="form-group">
                            <label>Téléphone</label>
                            <input type="text" name="telephone">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Poste</label>
                        <input type="text" name="poste" placeholder="Ex: Chef de projet, Technicien...">
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn-primary">Ajouter le membre</button>
                        <a href="liste.php" class="btn-secondary">Annuler</a>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>
</html>

