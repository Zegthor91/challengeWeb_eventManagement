<?php
// Controleur pour les budgets

if (!isset($pdo)) {
    require_once __DIR__ . '/../config/database.php';
}

require_once __DIR__ . '/../models/Budget.php';

class BudgetController {
    private $budgetModel;
    
    public function __construct($pdo) {
        $this->budgetModel = new Budget($pdo);
    }
    
    // Cree un budget pour un evenement
    public function createBudget($event_id, $budget_total) {
        try {
            $id = $this->budgetModel->create($event_id, $budget_total);
            return ['success' => true, 'message' => 'Budget créé', 'id' => $id];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Erreur : ' . $e->getMessage()];
        }
    }
    
    // Ajoute une categorie
    public function addCategorie($budget_id, $data) {
        if (empty($data['categorie']) || empty($data['montant_prevu'])) {
            return ['success' => false, 'message' => 'Catégorie et montant prévus obligatoire'];
        }
        
        try {
            $this->budgetModel->addCategorie(
                $budget_id, 
                $data['categorie'], 
                $data['montant_prevu'],
                $data['montant_reel'] ?? 0
            );
            return ['success' => true, 'message' => 'Catégorie ajoutée'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Erreur : ' . $e->getMessage()];
        }
    }
    
    // Met a jour une categorie
    public function updateCategorie($cat_id, $data) {
        try {
            $this->budgetModel->updateCategorie(
                $cat_id,
                $data['categorie'],
                $data['montant_prevu'],
                $data['montant_reel']
            );
            return ['success' => true, 'message' => 'Catégorie mise à jour'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Erreur : ' . $e->getMessage()];
        }
    }
    
    // Supprime une categorie
    public function deleteCategorie($cat_id) {
        try {
            $this->budgetModel->deleteCategorie($cat_id);
            return ['success' => true, 'message' => 'Catégorie supprimée'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Erreur : ' . $e->getMessage()];
        }
    }
}
?>
