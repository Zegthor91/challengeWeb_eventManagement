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
    $error = "⚠️ Aucun événement spécifié (event_id manquant)";
    $event = null;
} else {
    // Recupere l'evenement avec placeholder ?
    try {
        $stmt = $pdo->prepare("SELECT * FROM events WHERE id = ?");
        $stmt->execute([$event_id]);
        $event = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$event) {
            $error = "⚠️ Événement introuvable (ID: $event_id)";
        } else {
            // Verifie si budget existe deja
            $stmt2 = $pdo->prepare("SELECT * FROM budgets WHERE event_id = ?");
            $stmt2->execute([$event_id]);
            $budget_exist = $stmt2->fetch(PDO::FETCH_ASSOC);
            
            if ($budget_exist) {
                $message = "⚠️ ATTENTION : Un budget existe déjà pour cet événement (ID: {$budget_exist['id']}). Vous pouvez le voir ou le modifier au lieu d'en créer un nouveau.";
            }
        }
    } catch (PDOException $e) {
        $error = "Erreur base de données : " . $e->getMessage();
    }
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$error) {
    // Récupère et valide le budget total
    $budget_total = $_POST['budget_total'] ?? null;
    
    if (empty($budget_total) || !is_numeric($budget_total)) {
        $error = "Le budget total est obligatoire et doit être un nombre";
    } else {
        $controller = new BudgetController($pdo);
        
        $result = $controller->createBudget($event_id, $budget_total);
        
        if ($result['success']) {
            // Ajoute les categories si il y en a
            if (isset($_POST['categories']) && is_array($_POST['categories'])) {
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
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer budget<?php echo $event ? ' - ' . htmlspecialchars($event['nom']) : ''; ?></title>
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
                <li><a href="liste.php" class="active">Budget</a></li>
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
        
        <!-- Contenu -->
        <main class="main-content">
            <div class="page-header">
                <h1>Créer le budget<?php echo $event ? ' : ' . htmlspecialchars($event['nom']) : ''; ?></h1>
                <a href="liste.php" class="btn-secondary">← Retour</a>
            </div>
            
            <!-- DEBUG INFO -->
            <?php if (isset($_GET['debug'])): ?>
                <div style="background: #fff3cd; padding: 15px; margin-bottom: 20px; border-radius: 5px;">
                    <strong>🔍 DEBUG:</strong><br>
                    Event ID: <?php echo var_export($event_id, true); ?><br>
                    Event trouvé: <?php echo $event ? 'OUI ✅' : 'NON ❌'; ?><br>
                    <?php if ($event): ?>
                        Nom événement: <?php echo htmlspecialchars($event['nom']); ?><br>
                        Type: <?php echo htmlspecialchars($event['type_event'] ?? 'N/A'); ?><br>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            
            <?php if ($message): ?>
                <div style="background: #fff3cd; color: #856404; padding: 15px; margin-bottom: 20px; border-radius: 5px;">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
                <p><a href="liste.php" class="btn-secondary">← Retour à la liste</a></p>
            <?php endif; ?>
            
            <?php if ($event && !$error): ?>
            <div class="section">
                <form method="POST" action="" id="budgetForm">
                    <div class="form-group">
                        <label>Budget total prévu * <span style="color: red;">(obligatoire)</span></label>
                        <input type="number" 
                               name="budget_total" 
                               id="budget_total"
                               step="0.01" 
                               min="0"
                               placeholder="Ex: 10000.00"
                               required>
                        <small style="color: #666;">Entrez le budget total en euros</small>
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
                                    <input type="number" name="categories[0][montant]" step="0.01" min="0" placeholder="0.00">
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
            <?php endif; ?>
            
            <!-- Lien debug -->
            <div style="margin-top: 20px; padding: 10px; background: #f0f0f0; border-radius: 5px;">
                <a href="?event_id=<?php echo $event_id; ?>&debug=1" style="color: #666; font-size: 12px;">🔍 Activer le mode debug</a>
            </div>
        </main>
    </div>
    
    <script>
    let categorieCount = 1;
    
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
                    <input type="number" name="categories[${categorieCount}][montant]" step="0.01" min="0" placeholder="0.00">
                </div>
            </div>
        `;
        container.appendChild(newCategorie);
        categorieCount++;
    }
    
    var form = document.getElementById('budgetForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            const budgetTotal = document.getElementById('budget_total').value;
            
            if (!budgetTotal || budgetTotal <= 0) {
                e.preventDefault();
                alert('⚠️ Le budget total est obligatoire et doit être supérieur à 0');
                document.getElementById('budget_total').focus();
                return false;
            }
        });
    }
    </script>
</body>
</html>