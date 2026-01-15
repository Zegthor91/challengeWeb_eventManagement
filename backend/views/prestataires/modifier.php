<?php
// Formulaire de modification de prestataire
require_once '../../config/database.php';
require_once '../../config/session.php';
require_once '../../config/helpers.php';
require_once '../../controllers/PrestataireController.php';

requireLogin();

$user = getCurrentUser();
$message = '';
$error = '';

$prestataire_id = $_GET['id'] ?? null;

if (!$prestataire_id) {
    redirect('liste.php');
}

$controller = new PrestataireController($pdo);
$prestataire = $controller->show($prestataire_id);

if (!$prestataire) {
    redirect('liste.php');
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // DEBUG - Affiche ce qui est reçu
    error_log("POST reçu: " . print_r($_POST, true));
    
    $data = [
        'nom' => clean($_POST['nom']),
        'type_service' => clean($_POST['type_service'] ?? ''),
        'contact' => clean($_POST['contact'] ?? ''),
        'telephone' => clean($_POST['telephone'] ?? ''),
        'email' => clean($_POST['email'] ?? ''),
        'adresse' => clean($_POST['adresse'] ?? '')
    ];
    
    // DEBUG
    error_log("Data à envoyer: " . print_r($data, true));
    
    $result = $controller->update($prestataire_id, $data);
    
    // DEBUG
    error_log("Résultat: " . print_r($result, true));
    
    if ($result['success']) {
        $message = $result['message'];
        $prestataire = $controller->show($prestataire_id);
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
    <title>Modifier le prestataire</title>
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
                <li><a href="../personnel/liste.php">Personnel</a></li>
                <li><a href="liste.php" class="active">Prestataires</a></li>
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
                <h1>Modifier : <?php echo htmlspecialchars($prestataire['nom']); ?></h1>
                <a href="liste.php" class="btn-secondary">← Retour</a>
            </div>
            
            <!-- DEBUG INFO -->
            <?php if (isset($_POST['debug'])): ?>
                <div style="background: #fff3cd; padding: 15px; margin-bottom: 20px; border-radius: 5px;">
                    <strong>DEBUG INFO:</strong><br>
                    Prestataire ID: <?php echo $prestataire_id; ?><br>
                    POST reçu: <?php echo $_SERVER['REQUEST_METHOD']; ?><br>
                    Données prestataire: <pre><?php print_r($prestataire); ?></pre>
                </div>
            <?php endif; ?>
            
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
                        <input type="text" name="nom" value="<?php echo htmlspecialchars($prestataire['nom']); ?>" required>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Type de service</label>
                            <select name="type_service">
                                <option value="">Sélectionner...</option>
                                <option value="Restauration" <?php echo ($prestataire['type_service'] ?? '') == 'Restauration' ? 'selected' : ''; ?>>Restauration</option>
                                <option value="Sonorisation" <?php echo ($prestataire['type_service'] ?? '') == 'Sonorisation' ? 'selected' : ''; ?>>Sonorisation</option>
                                <option value="Décoration" <?php echo ($prestataire['type_service'] ?? '') == 'Décoration' ? 'selected' : ''; ?>>Décoration</option>
                                <option value="Transport" <?php echo ($prestataire['type_service'] ?? '') == 'Transport' ? 'selected' : ''; ?>>Transport</option>
                                <option value="Sécurité" <?php echo ($prestataire['type_service'] ?? '') == 'Sécurité' ? 'selected' : ''; ?>>Sécurité</option>
                                <option value="Animation" <?php echo ($prestataire['type_service'] ?? '') == 'Animation' ? 'selected' : ''; ?>>Animation</option>
                                <option value="Photographie" <?php echo ($prestataire['type_service'] ?? '') == 'Photographie' ? 'selected' : ''; ?>>Photographie</option>
                                <option value="Location" <?php echo ($prestataire['type_service'] ?? '') == 'Location' ? 'selected' : ''; ?>>Location</option>
                                <option value="Autre" <?php echo ($prestataire['type_service'] ?? '') == 'Autre' ? 'selected' : ''; ?>>Autre</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label>Personne de contact</label>
                            <input type="text" name="contact" value="<?php echo htmlspecialchars($prestataire['contact'] ?? ''); ?>">
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Téléphone</label>
                            <input type="text" name="telephone" value="<?php echo htmlspecialchars($prestataire['telephone'] ?? ''); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" value="<?php echo htmlspecialchars($prestataire['email'] ?? ''); ?>">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Adresse</label>
                        <textarea name="adresse" rows="3"><?php echo htmlspecialchars($prestataire['adresse'] ?? ''); ?></textarea>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn-primary">Enregistrer les modifications</button>
                        <button type="submit" name="debug" value="1" class="btn-secondary">Debug Mode</button>
                        <a href="liste.php" class="btn-secondary">Annuler</a>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>
</html>