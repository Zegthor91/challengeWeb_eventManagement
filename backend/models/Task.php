<?php
// Modele pour gerer les taches

class Task {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    // Recupere toutes les taches
    public function getAll() {
        $sql = "SELECT t.*, e.nom as event_nom, u.nom as assigne_nom 
                FROM tasks t 
                LEFT JOIN events e ON t.event_id = e.id
                LEFT JOIN users u ON t.assigne_a = u.id 
                ORDER BY t.priorite DESC, t.date_limite";
        
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Recupere une tache par ID
    public function getById($id) {
        $sql = "SELECT t.*, e.nom as event_nom, u.nom as assigne_nom, u.email as assigne_email
                FROM tasks t 
                LEFT JOIN events e ON t.event_id = e.id
                LEFT JOIN users u ON t.assigne_a = u.id 
                WHERE t.id = ?";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Cree une nouvelle tache
    public function create($data) {
        error_log("Task::create() avec: " . print_r($data, true));
        
        $sql = "INSERT INTO tasks (event_id, titre, description, statut, priorite, date_limite, assigne_a, parent_task_id) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?) RETURNING id";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            $data['event_id'],
            $data['titre'],
            $data['description'],
            $data['statut'],
            $data['priorite'],
            $data['date_limite'],
            $data['assigne_a'],
            $data['parent_task_id'] ?? null
        ]);
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        error_log("Task créée ID: " . ($result['id'] ?? 'NULL'));
        return $result['id'];
    }
    
    // Met a jour une tache
    public function update($id, $data) {
        $sql = "UPDATE tasks 
                SET event_id = ?, titre = ?, description = ?, statut = ?, priorite = ?, 
                    date_limite = ?, assigne_a = ? 
                WHERE id = ?";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $data['event_id'],
            $data['titre'],
            $data['description'],
            $data['statut'],
            $data['priorite'],
            $data['date_limite'],
            $data['assigne_a'],
            $id
        ]);
    }
    
    // Supprime une tache
    public function delete($id) {
        $sql = "DELETE FROM tasks WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id]);
    }
    
    // Recupere les taches par statut
    public function getByStatut($statut) {
        $sql = "SELECT t.*, e.nom as event_nom, u.nom as assigne_nom 
                FROM tasks t 
                LEFT JOIN events e ON t.event_id = e.id
                LEFT JOIN users u ON t.assigne_a = u.id 
                WHERE t.statut = ?
                ORDER BY t.priorite DESC, t.date_limite";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$statut]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Recupere les taches d'un evenement
    public function getByEvent($event_id) {
        $sql = "SELECT t.*, u.nom as assigne_nom 
                FROM tasks t 
                LEFT JOIN users u ON t.assigne_a = u.id 
                WHERE t.event_id = ?
                ORDER BY t.priorite DESC, t.date_limite";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$event_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Recupere les taches assignees a un utilisateur
    public function getByUser($user_id) {
        $sql = "SELECT t.*, e.nom as event_nom 
                FROM tasks t 
                LEFT JOIN events e ON t.event_id = e.id
                WHERE t.assigne_a = ?
                ORDER BY t.priorite DESC, t.date_limite";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>