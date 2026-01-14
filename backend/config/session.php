<?php
// Gestion des sessions

// Demarre la session si elle n'est pas deja demarré
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verifie si l'utilisateur est connecté
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Recupere l'utilisateur connecté
function getCurrentUser() {
    if (isLoggedIn()) {
        return [
            'id' => $_SESSION['user_id'],
            'nom' => $_SESSION['user_nom'],
            'email' => $_SESSION['user_email'],
            'role' => $_SESSION['user_role']
        ];
    }
    return null;
}

// Connecte un utilisateur
function login($user) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_nom'] = $user['nom'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_role'] = $user['role'];
}

// Deconnecte l'utilisateur
function logout() {
    session_destroy();
    header('Location: /views/login.php');
    header('Location: /backend/views/login.php');
    exit();
}

// Verifie si l'utilisateur a un role specifique
function hasRole($role) {
    if (!isLoggedIn()) {
        return false;
    }
    return $_SESSION['user_role'] === $role;
}

// Redirige si pas connecté
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: /views/login.php');
        header('Location: /backend/views/login.php');
        exit();
    }
}
?>
