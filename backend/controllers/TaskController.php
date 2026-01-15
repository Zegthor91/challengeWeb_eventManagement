<?php
// Controleur pour les taches

if (!isset($pdo)) {
    require_once __DIR__ . '/../config/database.php';
}

require_once __DIR__ . '/../models/Task.php';

class TaskController {
    private $taskModel;
    
    public function __construct($pdo) {
        $this->taskModel = new Task($pdo);
    }
    
    // Affiche la liste des taches
    public function index() {
        return $this->taskModel->getAll();
    }
    
    // Affiche une tache specifique
    public function show($id) {
        return $this->taskModel->getById($id);
    }
    
    // Cree une nouvelle tache
    public function store($data) {
        // Validation
        if (empty($data['titre'])) {
            return ['success' => false, 'message' => 'Le titre est obligatoire'];
        }
        
        try {
            $id = $this->taskModel->create($data);
            return ['success' => true, 'message' => 'Tâche créée avec succès', 'id' => $id];
        } catch (Exception $e) {
            error_log("Erreur création tâche: " . $e->getMessage());
            return ['success' => false, 'message' => 'Erreur : ' . $e->getMessage()];
        }
    }
    
    // Met a jour une tache
    public function update($id, $data) {
        try {
            $this->taskModel->update($id, $data);
            return ['success' => true, 'message' => 'Tâche modifiée'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Erreur : ' . $e->getMessage()];
        }
    }
    
    // Supprime une tache
    public function destroy($id) {
        try {
            $this->taskModel->delete($id);
            return ['success' => true, 'message' => 'Tâche supprimée'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Erreur : ' . $e->getMessage()];
        }
    }
    
    // Recupere les taches d'un evenement
    public function getByEvent($event_id) {
        return $this->taskModel->getByEvent($event_id);
    }
    
    // Recupere les taches d'un utilisateur
    public function getByUser($user_id) {
        return $this->taskModel->getByUser($user_id);
    }
}
?>