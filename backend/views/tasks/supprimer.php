<?php
require_once '../../config/database.php';
require_once '../../config/session.php';
require_once '../../config/helpers.php';
require_once '../../controllers/TaskController.php';

requireLogin();

$task_id = $_GET['id'] ?? null;

if (!$task_id) {
    redirect('liste.php');
}

$controller = new TaskController($pdo);
$result = $controller->destroy($task_id);

if ($result['success']) {
    $_SESSION['message'] = $result['message'];
} else {
    $_SESSION['error'] = $result['message'];
}

redirect('liste.php');
?>