<?php
require_once '../../config/database.php';
require_once '../../config/session.php';
require_once '../../config/helpers.php';
require_once '../../controllers/PrestataireController.php';

requireLogin();

$user = getCurrentUser();
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // DEBUG
    error_log("POST Prestataire: " . print_r($_POST, true));
    
    // Validation
    if (empty($_POST['nom'])) {
        $error = "Le nom du prestataire est obligatoire";
    } else {
        $controller = new PrestataireController($pdo);
        
        $data = [
            'nom' => clean($_POST['nom']),
            'type_service' => clean($_POST['type_service'] ?? ''),
            'contact' => clean($_POST['contact'] ?? ''),
            'telephone' => clean($_POST['telephone'] ?? ''),
            'email' => clean($_POST['email'] ?? ''),
            'adresse' => clean($_POST['adresse'] ?? '')
        ];
        
        $result = $controller->store($data);
        
        if ($result['success']) {
            redirect('liste.php');
        } else {
            $error = $result['message'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un prestataire</title>
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
                <li><a href="../budget/liste.php">Budget</a></li>
                <li><a href="../personnel/liste.php">Personnel</a></li>
                <li><a href="liste.php" class="active">Prestataires</a></li>
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
                <h1>Ajouter un prestataire</h1>
                <a href="liste.php" class="btn-secondary">← Retour</a>
            </div>
            
            <?php if ($message): ?>
                <div class="success-message"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <div class="section">
                <form method="POST" action="" class="form-event">
                    <div class="form-group">
                        <label>Nom du prestataire *</label>
                        <input type="text" name="nom" placeholder="Ex: Traiteur Deluxe" required>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Type de service</label>
                            <select name="type_service">
                                <option value="">Sélectionner...</option>
                                <option value="Restauration">Restauration</option>
                                <option value="Sonorisation">Sonorisation</option>
                                <option value="Décoration">Décoration</option>
                                <option value="Transport">Transport</option>
                                <option value="Sécurité">Sécurité</option>
                                <option value="Animation">Animation</option>
                                <option value="Photographie">Photographie</option>
                                <option value="Location">Location</option>
                                <option value="Autre">Autre</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label>Personne de contact</label>
                            <input type="text" name="contact" placeholder="Ex: Jean Dupont">
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Téléphone</label>
                            <input type="text" name="telephone" placeholder="Ex: 0612345678">
                        </div>
                        
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" placeholder="contact@prestataire.fr">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Adresse</label>
                        <textarea name="adresse" rows="3" placeholder="Adresse complète..."></textarea>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn-primary">Ajouter le prestataire</button>
                        <a href="liste.php" class="btn-secondary">Annuler</a>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>
</html>