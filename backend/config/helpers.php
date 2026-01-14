<?php
// Fonctions utiles pour tout le projet
// Redirige vers une page
function redirect($path) {
    header("Location: $path");
    exit();
}

// Affiche un message d'erreur
function showError($message) {
    return "<div class='error'>$message</div>";
}

// Affiche un message de succes
function showSuccess($message) {
    return "<div class='success'>$message</div>";
}

// Nettoye les données d'entré
function clean($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

// Verifie si email est valide
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Formate une date en francais
function formatDate($date) {
    $timestamp = strtotime($date);
    return date('d/m/Y', $timestamp);
}

// Calcul la difference entre 2 montant (budget)
function calculEcart($prevu, $reel) {
    return $reel - $prevu;
}
?>