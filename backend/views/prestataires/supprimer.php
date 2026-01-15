<?php
// Suppression d'un prestataire
require_once '../../config/database.php';
require_once '../../config/session.php';
require_once '../../config/helpers.php';
require_once '../../controllers/PrestataireController.php';

requireLogin();

$prestataire_id = $_GET['id'] ?? null;

if (!$prestataire_id) {
    redirect('liste.php');
}

$controller = new PrestataireController($pdo);
$result = $controller->destroy($prestataire_id);

if ($result['success']) {
    $_SESSION['message'] = $result['message'];
} else {
    $_SESSION['error'] = $result['message'];
}

redirect('liste.php');
?>