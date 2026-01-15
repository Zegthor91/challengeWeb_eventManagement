<?php
class Prestataire {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    public function getAll() {
        $sql = "SELECT * FROM prestataires ORDER BY nom";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getById($id) {
        $sql = "SELECT * FROM prestataires WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function create($data) {
        error_log("Prestataire::create() avec: " . print_r($data, true));
        
        $sql = "INSERT INTO prestataires (nom, type_service, contact, telephone, email, adresse) 
                VALUES (?, ?, ?, ?, ?, ?) RETURNING id";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            $data['nom'],
            $data['type_service'],
            $data['contact'],
            $data['telephone'],
            $data['email'],
            $data['adresse']
        ]);
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        error_log("Prestataire créé ID: " . ($result['id'] ?? 'NULL'));
        return $result['id'];
    }
    
    public function update($id, $data) {
        $sql = "UPDATE prestataires 
                SET nom = ?, type_service = ?, contact = ?, telephone = ?, email = ?, adresse = ? 
                WHERE id = ?";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $data['nom'],
            $data['type_service'],
            $data['contact'],
            $data['telephone'],
            $data['email'],
            $data['adresse'],
            $id
        ]);
    }
    
    public function delete($id) {
        $sql = "DELETE FROM prestataires WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id]);
    }
    
    public function affectToEvent($prestataire_id, $event_id, $cout = null, $evaluation = null, $commentaire = null) {
        $sql = "INSERT INTO event_prestataires (event_id, prestataire_id, cout, evaluation, commentaire) 
                VALUES (?, ?, ?, ?, ?)";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$event_id, $prestataire_id, $cout, $evaluation, $commentaire]);
    }
    
    public function getByEvent($event_id) {
        $sql = "SELECT p.*, ep.cout, ep.evaluation, ep.commentaire, ep.id as affectation_id 
                FROM prestataires p 
                JOIN event_prestataires ep ON p.id = ep.prestataire_id 
                WHERE ep.event_id = ?
                ORDER BY p.nom";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$event_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function updateAffectation($affectation_id, $cout, $evaluation, $commentaire) {
        $sql = "UPDATE event_prestataires 
                SET cout = ?, evaluation = ?, commentaire = ? 
                WHERE id = ?";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$cout, $evaluation, $commentaire, $affectation_id]);
    }
    
    public function removeFromEvent($affectation_id) {
        $sql = "DELETE FROM event_prestataires WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$affectation_id]);
    }
}
?>