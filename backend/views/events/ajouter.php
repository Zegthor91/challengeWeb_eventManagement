<?php
require_once '../../config/database.php';
require_once '../../config/session.php';
require_once '../../config/helpers.php';
require_once '../../controllers/EventController.php';

requireLogin();

$user = getCurrentUser();
$message = '';
$error = '';

$users = fetchAll("SELECT id, nom, role FROM users WHERE role IN ('administrateur', 'chef_projet')");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // DEBUG
    error_log("POST Events: " . print_r($_POST, true));
    
    // Validation
    if (empty($_POST['nom'])) {
        $error = "Le nom de l'événement est obligatoire";
    } elseif (empty($_POST['date_debut'])) {
        $error = "La date de début est obligatoire";
    } else {
        $controller = new EventController($pdo);
        
        $data = [
            'nom' => clean($_POST['nom']),
            'type_event' => clean($_POST['type_event'] ?? ''),
            'date_debut' => $_POST['date_debut'],
            'date_fin' => $_POST['date_fin'] ?? null,
            'lieu' => clean($_POST['lieu'] ?? ''),
            'description' => clean($_POST['description'] ?? ''),
            'responsable_id' => $_POST['responsable_id'] ?? null,
            'statut' => $_POST['statut'] ?? 'en_preparation'
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
    <title>Ajouter un événement</title>
    <link rel="stylesheet" href="/public/css/style.css">
</head>
<body>
    <div class="container">
        <nav class="sidebar">
            <h2>Gestion Events</h2>
            <ul>
                <li><a href="../dashboard.php">Dashboard</a></li>
                <li><a href="liste.php" class="active">Événements</a></li>
                <li><a href="../carte.php">Carte</a></li>
                <li><a href="../budget/liste.php">Budget</a></li>
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
                <h1>Ajouter un événement</h1>
                <a href="liste.php" class="btn-secondary">Retour</a>
            </div>
            
            <?php if ($message): ?>
                <div class="success-message"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <div class="section">
                <form method="POST" action="" class="form-event">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Nom de l'événement *</label>
                            <input type="text" name="nom" placeholder="Ex: Séminaire 2026" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Type d'événement</label>
                            <select name="type_event">
                                <option value="">Sélectionner...</option>
                                <option value="Séminaire">Séminaire</option>
                                <option value="Conférence">Conférence</option>
                                <option value="Team Building">Team Building</option>
                                <option value="Lancement">Lancement de produit</option>
                                <option value="Salon">Salon</option>
                                <option value="Formation">Formation</option>
                                <option value="Autre">Autre</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Date de début *</label>
                            <input type="date" name="date_debut" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Date de fin</label>
                            <input type="date" name="date_fin">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Lieu</label>
                        <input type="text" name="lieu" placeholder="Ville, adresse...">
                    </div>
                    
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" rows="4"></textarea>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Responsable</label>
                            <select name="responsable_id">
                                <option value="">Non assigné</option>
                                <?php foreach ($users as $u): ?>
                                    <option value="<?php echo $u['id']; ?>"><?php echo htmlspecialchars($u['nom']); ?> (<?php echo htmlspecialchars($u['role']); ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label>Statut</label>
                            <select name="statut">
                                <option value="en_preparation">En préparation</option>
                                <option value="en_cours">En cours</option>
                                <option value="termine">Terminé</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn-primary">Créer l'événement</button>
                        <a href="liste.php" class="btn-secondary">Annuler</a>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>
</html>