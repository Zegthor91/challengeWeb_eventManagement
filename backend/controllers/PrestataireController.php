<?php
// Controleur pour les prestataires

if (!isset($pdo)) {
    require_once __DIR__ . '/../config/database.php';
}

require_once __DIR__ . '/../models/Prestataire.php';

class PrestataireController {
    private $prestataireModel;
    
    public function __construct($pdo) {
        $this->prestataireModel = new Prestataire($pdo);
    }
    
    // Affiche la liste
    public function index() {
        return $this->prestataireModel->getAll();
    }
    
    // Affiche un prestataire
    public function show($id) {
        return $this->prestataireModel->getById($id);
    }
    
    // Cree un nouveau prestataire
    public function store($data) {
        if (empty($data['nom'])) {
            return ['success' => false, 'message' => 'Le nom est obligatoire'];
        }
        
        try {
            $id = $this->prestataireModel->create($data);
            return ['success' => true, 'message' => 'Prestataire ajouté', 'id' => $id];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Erreur : ' . $e->getMessage()];
        }
    }
    
    // Met a jour un prestataire
    public function update($id, $data) {
        try {
            $this->prestataireModel->update($id, $data);
            return ['success' => true, 'message' => 'Prestataire modifié'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Erreur : ' . $e->getMessage()];
        }
    }
    
    // Supprime un prestataire
    public function destroy($id) {
        try {
            $this->prestataireModel->delete($id);
            return ['success' => true, 'message' => 'Prestataire supprimé'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Erreur : ' . $e->getMessage()];
        }
    }
}
?>