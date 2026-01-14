<?php
// Modele pour gerer le personnel

class Personnel {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    // Recupere tout le personnel
    public function getAll() {
        $sql = "SELECT * FROM personnel ORDER BY nom, prenom";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Recupere un membre par ID
    public function getById($id) {
        $sql = "SELECT * FROM personnel WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Cree un nouveau membre
    public function create($data) {
        $sql = "INSERT INTO personnel (nom, prenom, email, telephone, poste) 
                VALUES (?, ?, ?, ?, ?) RETURNING id";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            $data['nom'],
            $data['prenom'],
            $data['email'],
            $data['telephone'],
            $data['poste']
        ]);
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['id'];
    }
    
    // Met a jour un membre
    public function update($id, $data) {
        $sql = "UPDATE personnel 
                SET nom = ?, prenom = ?, email = ?, telephone = ?, poste = ? 
                WHERE id = ?";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $data['nom'],
            $data['prenom'],
            $data['email'],
            $data['telephone'],
            $data['poste'],
            $id
        ]);
    }
    
    // Supprime un membre
    public function delete($id) {
        $sql = "DELETE FROM personnel WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id]);
    }
    
    // Affecte un membre a un evenement
    public function affectToEvent($personnel_id, $event_id, $role_event) {
        $sql = "INSERT INTO event_personnel (event_id, personnel_id, role_event) 
                VALUES (?, ?, ?)";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$event_id, $personnel_id, $role_event]);
    }
    
    // Recupere le personnel d'un evenement
    public function getByEvent($event_id) {
        $sql = "SELECT p.*, ep.role_event, ep.id as affectation_id 
                FROM personnel p 
                JOIN event_personnel ep ON p.id = ep.personnel_id 
                WHERE ep.event_id = ?
                ORDER BY p.nom, p.prenom";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$event_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Retire un membre d'un evenement
    public function removeFromEvent($affectation_id) {
        $sql = "DELETE FROM event_personnel WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$affectation_id]);
    }
}
?>
