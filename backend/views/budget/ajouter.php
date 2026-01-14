<?php
// Formulaire de creation de budget
require_once '../../config/database.php';
require_once '../../config/session.php';
require_once '../../config/helpers.php';
require_once '../../controllers/BudgetController.php';

requireLogin();

$user = getCurrentUser();
$message = '';
$error = '';

$event_id = $_GET['event_id'] ?? null;

if (!$event_id) {
    redirect('liste.php');
}

// Recupere l'evenement
$event = fetchOne("SELECT * FROM events WHERE id = $1", [$event_id]);

if (!$event) {
    redirect('liste.php');
}

// Verifie si budget existe deja
$budget_exist = fetchOne("SELECT * FROM budgets WHERE event_id = $1", [$event_id]);
if ($budget_exist) {
    redirect("voir.php?event_id=$event_id");
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller = new BudgetController($pdo);
    
    $budget_total = $_POST['budget_total'];
    $result = $controller->createBudget($event_id, $budget_total);
    
    if ($result['success']) {
        // Ajoute les categories si il y en a
        if (isset($_POST['categories'])) {
            foreach ($_POST['categories'] as $cat) {
                if (!empty($cat['nom']) && !empty($cat['montant'])) {
                    $controller->addCategorie($result['id'], [
                        'categorie' => $cat['nom'],
                        'montant_prevu' => $cat['montant'],
                        'montant_reel' => 0
                    ]);
                }
            }
        }
        
        redirect("voir.php?event_id=$event_id");
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
    <title>Créer budget - <?php echo $event['nom']; ?></title>
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
                <h1>Créer le budget : <?php echo $event['nom']; ?></h1>
                <a href="liste.php" class="btn-secondary">← Retour</a>
            </div>
            
            <?php if ($error): ?>
                <div class="error-message"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <div class="section">
                <form method="POST" action="" id="budgetForm">
                    <div class="form-group">
                        <label>Budget total prévu *</label>
                        <input type="number" name="budget_total" step="0.01" required>
                    </div>
                    
                    <h3>Catégories de budget</h3>
                    <p style="color: #666; font-size: 14px;">Vous pouvez ajouter des catégories maintenant ou plus tard</p>
                    
                    <div id="categoriesContainer">
                        <div class="categorie-item">
                            <div class="form-row">
                                <div class="form-group">
                                    <label>Nom de la catégorie</label>
                                    <input type="text" name="categories[0][nom]" placeholder="Ex: Restauration">
                                </div>
                                <div class="form-group">
                                    <label>Montant prévu</label>
                                    <input type="number" name="categories[0][montant]" step="0.01" placeholder="0.00">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <button type="button" onclick="ajouterCategorie()" class="btn-secondary" style="margin-bottom: 20px;">+ Ajouter une catégorie</button>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn-primary">Créer le budget</button>
                        <a href="liste.php" class="btn-secondary">Annuler</a>
                    </div>
                </form>
            </div>
        </main>
    </div>
    
    <script>
    let categorieCount = 1;
    
    // Fonction pour ajouter une categorie
    function ajouterCategorie() {
        const container = document.getElementById('categoriesContainer');
        const newCategorie = document.createElement('div');
        newCategorie.className = 'categorie-item';
        newCategorie.innerHTML = `
            <div class="form-row">
                <div class="form-group">
                    <label>Nom de la catégorie</label>
                    <input type="text" name="categories[${categorieCount}][nom]" placeholder="Ex: Location salle">
                </div>
                <div class="form-group">
                    <label>Montant prévu</label>
                    <input type="number" name="categories[${categorieCount}][montant]" step="0.01" placeholder="0.00">
                </div>
            </div>
        `;
        container.appendChild(newCategorie);
        categorieCount++;
    }
    </script>
</body>
</html>
