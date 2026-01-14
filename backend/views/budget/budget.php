<?php
// Modele pour gerer les budgets

class Budget {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    // Recupere le budget d'un evenement
    public function getByEventId($event_id) {
        $sql = "SELECT * FROM budgets WHERE event_id = $1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$event_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Recupere toutes les categories d'un budget
    public function getCategories($budget_id) {
        $sql = "SELECT * FROM budget_categories WHERE budget_id = $1 ORDER BY categorie";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$budget_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Cree un nouveau budget
    public function create($event_id, $budget_total) {
        $sql = "INSERT INTO budgets (event_id, budget_total) VALUES ($1, $2) RETURNING id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$event_id, $budget_total]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['id'];
    }
    
    // Met a jour le budget total
    public function updateTotal($budget_id, $budget_total) {
        $sql = "UPDATE budgets SET budget_total = $1 WHERE id = $2";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$budget_total, $budget_id]);
    }
    
    // Ajoute une categorie au budget
    public function addCategorie($budget_id, $categorie, $montant_prevu, $montant_reel = 0) {
        $sql = "INSERT INTO budget_categories (budget_id, categorie, montant_prevu, montant_reel) 
                VALUES ($1, $2, $3, $4) RETURNING id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$budget_id, $categorie, $montant_prevu, $montant_reel]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['id'];
    }
    
    // Met a jour une categorie
    public function updateCategorie($cat_id, $categorie, $montant_prevu, $montant_reel) {
        $sql = "UPDATE budget_categories 
                SET categorie = $1, montant_prevu = $2, montant_reel = $3 
                WHERE id = $4";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$categorie, $montant_prevu, $montant_reel, $cat_id]);
    }
    
    // Supprime une categorie
    public function deleteCategorie($cat_id) {
        $sql = "DELETE FROM budget_categories WHERE id = $1";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$cat_id]);
    }
    
    // Calcul le total prevu
    public function getTotalPrevu($budget_id) {
        $sql = "SELECT SUM(montant_prevu) as total FROM budget_categories WHERE budget_id = $1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$budget_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }
    
    // Calcul le total reel
    public function getTotalReel($budget_id) {
        $sql = "SELECT SUM(montant_reel) as total FROM budget_categories WHERE budget_id = $1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$budget_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }
}
?>
