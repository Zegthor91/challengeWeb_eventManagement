<?php
// Suppression d'un evenement
require_once '../../config/database.php';
require_once '../../config/session.php';
require_once '../../config/helpers.php';
require_once '../../controllers/EventController.php';

requireLogin();

$event_id = $_GET['id'] ?? null;

if (!$event_id) {
    redirect('liste.php');
}

$controller = new EventController($pdo);
$result = $controller->destroy($event_id);

if ($result['success']) {
    $_SESSION['message'] = $result['message'];
} else {
    $_SESSION['error'] = $result['message'];
}

redirect('liste.php');
?>
