<?php
require_once '../../config/database.php';
require_once '../../config/session.php';
require_once '../../config/helpers.php';
require_once '../../controllers/TaskController.php';

requireLogin();

$user = getCurrentUser();
$message = '';
$error = '';

$task_id = $_GET['id'] ?? null;

if (!$task_id) {
    redirect('liste.php');
}

$controller = new TaskController($pdo);
$task = $controller->show($task_id);

if (!$task) {
    redirect('liste.php');
}

$events = fetchAll("SELECT id, nom FROM events ORDER BY date_debut DESC");
$users = fetchAll("SELECT id, nom FROM users ORDER BY nom");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST['titre'])) {
        $error = "Le titre est obligatoire";
    } else {
        $data = [
            'event_id' => $_POST['event_id'] ?? null,
            'titre' => clean($_POST['titre']),
            'description' => clean($_POST['description'] ?? ''),
            'statut' => $_POST['statut'],
            'priorite' => $_POST['priorite'],
            'date_limite' => $_POST['date_limite'] ?? null,
            'assigne_a' => $_POST['assigne_a'] ?? null
        ];
        
        $result = $controller->update($task_id, $data);
        
        if ($result['success']) {
            $message = $result['message'];
            $task = $controller->show($task_id);
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
    <title>Modifier la tâche</title>
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
                <li><a href="../prestataires/liste.php">Prestataires</a></li>
                <li><a href="liste.php" class="active">Tâches</a></li>
            </ul>
            <div class="user-info">
                <p><strong><?php echo htmlspecialchars($user['nom']); ?></strong></p>
                <p><?php echo htmlspecialchars($user['role']); ?></p>
                <a href="../logout.php">Déconnexion</a>
            </div>
        </nav>
        
        <main class="main-content">
            <div class="page-header">
                <h1>Modifier : <?php echo htmlspecialchars($task['titre']); ?></h1>
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
                        <label>Titre *</label>
                        <input type="text" name="titre" value="<?php echo htmlspecialchars($task['titre']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" rows="4"><?php echo htmlspecialchars($task['description'] ?? ''); ?></textarea>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Événement</label>
                            <select name="event_id">
                                <option value="">Aucun</option>
                                <?php foreach ($events as $e): ?>
                                    <option value="<?php echo $e['id']; ?>" <?php echo $task['event_id'] == $e['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($e['nom']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label>Assigné à</label>
                            <select name="assigne_a">
                                <option value="">Non assigné</option>
                                <?php foreach ($users as $u): ?>
                                    <option value="<?php echo $u['id']; ?>" <?php echo $task['assigne_a'] == $u['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($u['nom']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Priorité</label>
                            <select name="priorite">
                                <option value="basse" <?php echo $task['priorite'] == 'basse' ? 'selected' : ''; ?>>Basse</option>
                                <option value="moyenne" <?php echo $task['priorite'] == 'moyenne' ? 'selected' : ''; ?>>Moyenne</option>
                                <option value="haute" <?php echo $task['priorite'] == 'haute' ? 'selected' : ''; ?>>Haute</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label>Date limite</label>
                            <input type="date" name="date_limite" value="<?php echo $task['date_limite'] ?? ''; ?>">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Statut</label>
                        <select name="statut">
                            <option value="a_faire" <?php echo $task['statut'] == 'a_faire' ? 'selected' : ''; ?>>À faire</option>
                            <option value="en_cours" <?php echo $task['statut'] == 'en_cours' ? 'selected' : ''; ?>>En cours</option>
                            <option value="terminee" <?php echo $task['statut'] == 'terminee' ? 'selected' : ''; ?>>Terminée</option>
                        </select>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn-primary">Enregistrer</button>
                        <a href="liste.php" class="btn-secondary">Annuler</a>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>
</html>