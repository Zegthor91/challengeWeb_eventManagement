<?php
// Controleur pour les evenements

if (!isset($pdo)) {
    require_once __DIR__ . '/../config/database.php';
}

require_once __DIR__ . '/../models/Event.php';

class EventController {
    private $eventModel;
    
    public function __construct($pdo) {
        $this->eventModel = new Event($pdo);
    }
    
    // Affiche la liste des evenements
    public function index() {
        return $this->eventModel->getAll();
    }
    
    // Affiche un evenement specifique
    public function show($id) {
        return $this->eventModel->getById($id);
    }
    
    // Cree un nouvel evenement
    public function store($data) {
        // Validation simple
        if (empty($data['nom']) || empty($data['date_debut'])) {
            return ['success' => false, 'message' => 'Le nom et la date sont obligatoire'];
        }
        
        try {
            $id = $this->eventModel->create($data);
            return ['success' => true, 'message' => 'Événement créé avec succès', 'id' => $id];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Erreur : ' . $e->getMessage()];
        }
    }
    
    // Met a jour un evenement
    public function update($id, $data) {
        try {
            $this->eventModel->update($id, $data);
            return ['success' => true, 'message' => 'Événement modifié'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Erreur : ' . $e->getMessage()];
        }
    }
    
    // Supprime un evenement
    public function destroy($id) {
        try {
            $this->eventModel->delete($id);
            return ['success' => true, 'message' => 'Événement supprimé'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Erreur : ' . $e->getMessage()];
        }
    }
}
?>
