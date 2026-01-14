<?php
// Modification du budget et des categories
require_once '../../config/database.php';
require_once '../../config/session.php';
require_once '../../config/helpers.php';
require_once '../../models/Budget.php';
require_once '../../controllers/BudgetController.php';

requireLogin();

$user = getCurrentUser();
$message = '';
$error = '';

$event_id = $_GET['event_id'] ?? null;

if (!$event_id) {
    redirect('liste.php');
}

$event = fetchOne("SELECT * FROM events WHERE id = $1", [$event_id]);

if (!$event) {
    redirect('liste.php');
}

$budgetModel = new Budget($pdo);
$budget = $budgetModel->getByEventId($event_id);

if (!$budget) {
    redirect("ajouter.php?event_id=$event_id");
}

$categories = $budgetModel->getCategories($budget['id']);

// Traitement formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller = new BudgetController($pdo);
    
    // Met a jour le budget total
    if (isset($_POST['budget_total'])) {
        $budgetModel->updateTotal($budget['id'], $_POST['budget_total']);
    }
    
    // Met a jour les categories existantes
    if (isset($_POST['categories'])) {
        foreach ($_POST['categories'] as $cat_id => $cat_data) {
            if (isset($cat_data['delete'])) {
                // Supprime la categorie
                $controller->deleteCategorie($cat_id);
            } else {
                // Met a jour la categorie
                $controller->updateCategorie($cat_id, [
                    'categorie' => $cat_data['nom'],
                    'montant_prevu' => $cat_data['montant_prevu'],
                    'montant_reel' => $cat_data['montant_reel']
                ]);
            }
        }
    }
    
    // Ajoute les nouvelles categories
    if (isset($_POST['new_categories'])) {
        foreach ($_POST['new_categories'] as $new_cat) {
            if (!empty($new_cat['nom']) && !empty($new_cat['montant_prevu'])) {
                $controller->addCategorie($budget['id'], [
                    'categorie' => $new_cat['nom'],
                    'montant_prevu' => $new_cat['montant_prevu'],
                    'montant_reel' => $new_cat['montant_reel'] ?? 0
                ]);
            }
        }
    }
    
    $message = "Budget mis à jour avec succès";
    // Recharge les donné
    $budget = $budgetModel->getByEventId($event_id);
    $categories = $budgetModel->getCategories($budget['id']);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier budget - <?php echo $event['nom']; ?></title>
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
            <div class="page-header">
                <h1>Modifier budget : <?php echo $event['nom']; ?></h1>
                <a href="voir.php?event_id=<?php echo $event_id; ?>" class="btn-secondary">← Retour</a>
            </div>
            
            <?php if ($message): ?>
                <div class="success-message"><?php echo $message; ?></div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="error-message"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <div class="section">
                <form method="POST" action="">
                    <div class="form-group">
                        <label>Budget total prévu *</label>
                        <input type="number" name="budget_total" value="<?php echo $budget['budget_total']; ?>" step="0.01" required>
                    </div>
                    
                    <h3>Catégories existantes</h3>
                    
                    <?php foreach ($categories as $cat): ?>
                    <div class="categorie-edit">
                        <div class="form-row">
                            <div class="form-group">
                                <label>Catégorie</label>
                                <input type="text" name="categories[<?php echo $cat['id']; ?>][nom]" value="<?php echo $cat['categorie']; ?>">
                            </div>
                            <div class="form-group">
                                <label>Montant prévu</label>
                                <input type="number" name="categories[<?php echo $cat['id']; ?>][montant_prevu]" value="<?php echo $cat['montant_prevu']; ?>" step="0.01">
                            </div>
                            <div class="form-group">
                                <label>Montant réel</label>
                                <input type="number" name="categories[<?php echo $cat['id']; ?>][montant_reel]" value="<?php echo $cat['montant_reel']; ?>" step="0.01">
                            </div>
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <label class="checkbox-label">
                                    <input type="checkbox" name="categories[<?php echo $cat['id']; ?>][delete]" value="1">
                                    Supprimer
                                </label>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    
                    <h3 style="margin-top: 30px;">Ajouter des catégories</h3>
                    
                    <div id="newCategoriesContainer">
                        <div class="categorie-item">
                            <div class="form-row">
                                <div class="form-group">
                                    <label>Catégorie</label>
                                    <input type="text" name="new_categories[0][nom]" placeholder="Ex: Communication">
                                </div>
                                <div class="form-group">
                                    <label>Montant prévu</label>
                                    <input type="number" name="new_categories[0][montant_prevu]" step="0.01" placeholder="0.00">
                                </div>
                                <div class="form-group">
                                    <label>Montant réel</label>
                                    <input type="number" name="new_categories[0][montant_reel]" step="0.01" placeholder="0.00">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <button type="button" onclick="ajouterNouvelleCategorie()" class="btn-secondary" style="margin-bottom: 20px;">+ Ajouter une catégorie</button>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn-primary">Enregistrer les modifications</button>
                        <a href="voir.php?event_id=<?php echo $event_id; ?>" class="btn-secondary">Annuler</a>
                    </div>
                </form>
            </div>
        </main>
    </div>
    
    <script>
    let newCatCount = 1;
    
    function ajouterNouvelleCategorie() {
        const container = document.getElementById('newCategoriesContainer');
        const newCat = document.createElement('div');
        newCat.className = 'categorie-item';
        newCat.innerHTML = `
            <div class="form-row">
                <div class="form-group">
                    <label>Catégorie</label>
                    <input type="text" name="new_categories[${newCatCount}][nom]" placeholder="Ex: Décoration">
                </div>
                <div class="form-group">
                    <label>Montant prévu</label>
                    <input type="number" name="new_categories[${newCatCount}][montant_prevu]" step="0.01" placeholder="0.00">
                </div>
                <div class="form-group">
                    <label>Montant réel</label>
                    <input type="number" name="new_categories[${newCatCount}][montant_reel]" step="0.01" placeholder="0.00">
                </div>
            </div>
        `;
        container.appendChild(newCat);
        newCatCount++;
    }
    </script>
</body>
</html>
