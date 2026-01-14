<?php
// Controleur pour le personnel

// Si database.php n'est pas deja chargé, on le charge
if (!isset($pdo)) {
    require_once __DIR__ . '/../config/database.php';
}

require_once __DIR__ . '/../models/Personnel.php';

class PersonnelController {
    private $personnelModel;
    
    public function __construct($pdo) {
        $this->personnelModel = new Personnel($pdo);
    }
    
    // Affiche la liste
    public function index() {
        return $this->personnelModel->getAll();
    }
    
    // Affiche un membre
    public function show($id) {
        return $this->personnelModel->getById($id);
    }
    
    // Cree un nouveau membre
    public function store($data) {
        if (empty($data['nom']) || empty($data['prenom'])) {
            return ['success' => false, 'message' => 'Le nom et prenom sont obligatoires'];
        }
        
        try {
            $id = $this->personnelModel->create($data);
            return ['success' => true, 'message' => 'Membre ajouté', 'id' => $id];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Erreur : ' . $e->getMessage()];
        }
    }
    
    // Met a jour un membre
    public function update($id, $data) {
        try {
            $this->personnelModel->update($id, $data);
            return ['success' => true, 'message' => 'Membre modifié'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Erreur : ' . $e->getMessage()];
        }
    }
    
    // Supprime un membre
    public function destroy($id) {
        try {
            $this->personnelModel->delete($id);
            return ['success' => true, 'message' => 'Membre supprimé'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Erreur : ' . $e->getMessage()];
        }
    }
}
?>
