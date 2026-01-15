<?php
// Modele pour gerer les evenements

class Event {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    // Recupere tous les evenements
    public function getAll() {
        $sql = "SELECT e.*, u.nom as responsable_nom 
                FROM events e 
                LEFT JOIN users u ON e.responsable_id = u.id 
                ORDER BY e.date_debut DESC";
        
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Recupere un evenement par son ID
    public function getById($id) {
        $sql = "SELECT e.*, u.nom as responsable_nom, u.email as responsable_email
                FROM events e 
                LEFT JOIN users u ON e.responsable_id = u.id 
                WHERE e.id = ?";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Cree un nouvel evenement
    public function create($data) {
        error_log("Event::create() appelé avec: " . print_r($data, true));
        
        $sql = "INSERT INTO events (nom, type_event, date_debut, date_fin, lieu, description, responsable_id, statut) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?) RETURNING id";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            $data['nom'],
            $data['type_event'],
            $data['date_debut'],
            $data['date_fin'],
            $data['lieu'],
            $data['description'],
            $data['responsable_id'],
            $data['statut']
        ]);
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        error_log("Event créé avec ID: " . ($result['id'] ?? 'NULL'));
        return $result['id'];
    }
    
    // Met a jour un evenement
    public function update($id, $data) {
        $sql = "UPDATE events 
                SET nom = ?, type_event = ?, date_debut = ?, date_fin = ?, 
                    lieu = ?, description = ?, responsable_id = ?, statut = ? 
                WHERE id = ?";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $data['nom'],
            $data['type_event'],
            $data['date_debut'],
            $data['date_fin'],
            $data['lieu'],
            $data['description'],
            $data['responsable_id'],
            $data['statut'],
            $id
        ]);
    }
    
    // Supprime un evenement
    public function delete($id) {
        $sql = "DELETE FROM events WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id]);
    }
    
    // Recupere les evenements par statut
    public function getByStatut($statut) {
        $sql = "SELECT e.*, u.nom as responsable_nom 
                FROM events e 
                LEFT JOIN users u ON e.responsable_id = u.id 
                WHERE e.statut = ?
                ORDER BY e.date_debut DESC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$statut]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>