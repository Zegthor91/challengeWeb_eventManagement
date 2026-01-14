<?php
<<<<<<< HEAD
// Formulaire de modification de personnel
require_once '../../config/database.php';
require_once '../../config/session.php';
require_once '../../config/helpers.php';
require_once '../../controllers/PersonnelController.php';
=======
// Formulaire de modification d'evenement
require_once '../../config/database.php';
require_once '../../config/session.php';
require_once '../../config/helpers.php';
require_once '../../controllers/EventController.php';
>>>>>>> 1b402c43edf8776eb44ed747824a00ed1ca1350d

requireLogin();

$user = getCurrentUser();
$message = '';
$error = '';

<<<<<<< HEAD
$personnel_id = $_GET['id'] ?? null;

if (!$personnel_id) {
    redirect('liste.php');
}

$controller = new PersonnelController($pdo);
$personnel = $controller->show($personnel_id);

if (!$personnel) {
    redirect('liste.php');
}

=======
// Recupere l'ID de l'evenement
$event_id = $_GET['id'] ?? null;

if (!$event_id) {
    redirect('liste.php');
}

$controller = new EventController($pdo);
$event = $controller->show($event_id);

if (!$event) {
    redirect('liste.php');
}

// Liste des utilisateurs
$users = fetchAll("SELECT id, nom, role FROM users WHERE role IN ('administrateur', 'chef_projet')");

>>>>>>> 1b402c43edf8776eb44ed747824a00ed1ca1350d
// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'nom' => clean($_POST['nom']),
<<<<<<< HEAD
        'prenom' => clean($_POST['prenom']),
        'email' => clean($_POST['email']),
        'telephone' => clean($_POST['telephone']),
        'poste' => clean($_POST['poste'])
    ];
    
    $result = $controller->update($personnel_id, $data);
    
    if ($result['success']) {
        $message = $result['message'];
        $personnel = $controller->show($personnel_id);
=======
        'type_event' => clean($_POST['type_event']),
        'date_debut' => $_POST['date_debut'],
        'date_fin' => $_POST['date_fin'] ?? null,
        'lieu' => clean($_POST['lieu']),
        'description' => clean($_POST['description']),
        'responsable_id' => $_POST['responsable_id'] ?? null,
        'statut' => $_POST['statut']
    ];
    
    $result = $controller->update($event_id, $data);
    
    if ($result['success']) {
        $message = $result['message'];
        // Recharge l'evenement mis a jour
        $event = $controller->show($event_id);
>>>>>>> 1b402c43edf8776eb44ed747824a00ed1ca1350d
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
<<<<<<< HEAD
    <title>Modifier le membre</title>
    <link rel="stylesheet" href="../../../public/css/style.css">
=======
    <title>Modifier l'événement</title>
    <link rel="stylesheet" href="/public/css/style.css">
>>>>>>> 1b402c43edf8776eb44ed747824a00ed1ca1350d
</head>
<body>
    <div class="container">
        <!-- Menu -->
        <nav class="sidebar">
            <h2>Gestion Events</h2>
            <ul>
                <li><a href="../dashboard.php">Dashboard</a></li>
<<<<<<< HEAD
                <li><a href="../events/liste.php">Événements</a></li>
                <li><a href="../budget/liste.php">Budget</a></li>
                <li><a href="liste.php" class="active">Personnel</a></li>
=======
                <li><a href="liste.php" class="active">Événements</a></li>
                <li><a href="../budget/liste.php">Budget</a></li>
                <li><a href="../personnel/liste.php">Personnel</a></li>
>>>>>>> 1b402c43edf8776eb44ed747824a00ed1ca1350d
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
<<<<<<< HEAD
                <h1>Modifier : <?php echo $personnel['prenom'] . ' ' . $personnel['nom']; ?></h1>
=======
                <h1>Modifier : <?php echo $event['nom']; ?></h1>
>>>>>>> 1b402c43edf8776eb44ed747824a00ed1ca1350d
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
<<<<<<< HEAD
                            <label>Nom *</label>
                            <input type="text" name="nom" value="<?php echo $personnel['nom']; ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Prénom *</label>
                            <input type="text" name="prenom" value="<?php echo $personnel['prenom']; ?>" required>
=======
                            <label>Nom de l'événement *</label>
                            <input type="text" name="nom" value="<?php echo $event['nom']; ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Type d'événement</label>
                            <select name="type_event">
                                <option value="">Sélectionner...</option>
                                <option value="Séminaire" <?php echo $event['type_event'] == 'Séminaire' ? 'selected' : ''; ?>>Séminaire</option>
                                <option value="Conférence" <?php echo $event['type_event'] == 'Conférence' ? 'selected' : ''; ?>>Conférence</option>
                                <option value="Team Building" <?php echo $event['type_event'] == 'Team Building' ? 'selected' : ''; ?>>Team Building</option>
                                <option value="Lancement" <?php echo $event['type_event'] == 'Lancement' ? 'selected' : ''; ?>>Lancement de produit</option>
                                <option value="Salon" <?php echo $event['type_event'] == 'Salon' ? 'selected' : ''; ?>>Salon</option>
                                <option value="Formation" <?php echo $event['type_event'] == 'Formation' ? 'selected' : ''; ?>>Formation</option>
                                <option value="Autre" <?php echo $event['type_event'] == 'Autre' ? 'selected' : ''; ?>>Autre</option>
                            </select>
>>>>>>> 1b402c43edf8776eb44ed747824a00ed1ca1350d
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
<<<<<<< HEAD
                            <label>Email</label>
                            <input type="email" name="email" value="<?php echo $personnel['email']; ?>">
                        </div>
                        
                        <div class="form-group">
                            <label>Téléphone</label>
                            <input type="text" name="telephone" value="<?php echo $personnel['telephone']; ?>">
=======
                            <label>Date de début *</label>
                            <input type="date" name="date_debut" value="<?php echo $event['date_debut']; ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Date de fin</label>
                            <input type="date" name="date_fin" value="<?php echo $event['date_fin']; ?>">
>>>>>>> 1b402c43edf8776eb44ed747824a00ed1ca1350d
                        </div>
                    </div>
                    
                    <div class="form-group">
<<<<<<< HEAD
                        <label>Poste</label>
                        <input type="text" name="poste" value="<?php echo $personnel['poste']; ?>">
=======
                        <label>Lieu</label>
                        <input type="text" name="lieu" value="<?php echo $event['lieu']; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" rows="4"><?php echo $event['description']; ?></textarea>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Responsable</label>
                            <select name="responsable_id">
                                <option value="">Non assigné</option>
                                <?php foreach ($users as $u): ?>
                                    <option value="<?php echo $u['id']; ?>" <?php echo $event['responsable_id'] == $u['id'] ? 'selected' : ''; ?>><?php echo $u['nom']; ?> (<?php echo $u['role']; ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label>Statut</label>
                            <select name="statut">
                                <option value="en_preparation" <?php echo $event['statut'] == 'en_preparation' ? 'selected' : ''; ?>>En préparation</option>
                                <option value="en_cours" <?php echo $event['statut'] == 'en_cours' ? 'selected' : ''; ?>>En cours</option>
                                <option value="termine" <?php echo $event['statut'] == 'termine' ? 'selected' : ''; ?>>Terminé</option>
                            </select>
                        </div>
>>>>>>> 1b402c43edf8776eb44ed747824a00ed1ca1350d
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn-primary">Enregistrer les modifications</button>
                        <a href="liste.php" class="btn-secondary">Annuler</a>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>
</html>