<?php
// Suppression d'un membre
require_once '../../config/database.php';
require_once '../../config/session.php';
require_once '../../config/helpers.php';
require_once '../../controllers/PersonnelController.php';

requireLogin();

$personnel_id = $_GET['id'] ?? null;

if (!$personnel_id) {
    redirect('liste.php');
}

$controller = new PersonnelController($pdo);
$result = $controller->destroy($personnel_id);

if ($result['success']) {
    $_SESSION['message'] = $result['message'];
} else {
    $_SESSION['error'] = $result['message'];
}

redirect('liste.php');
?>
