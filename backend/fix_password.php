<?php
require_once 'config/database.php';

// Met a jour le mot de passe directement
$sql = "UPDATE users SET password = 'Okay94' WHERE email = 'adminok@test.com'";
$pdo->exec($sql);

// Verifie
$result = $pdo->query("SELECT nom, email, password FROM users WHERE email = 'adminok@test.com'");
$user = $result->fetch(PDO::FETCH_ASSOC);

echo "Nom: " . $user['nom'] . "<br>";
echo "Email: " . $user['email'] . "<br>";
echo "Password: " . $user['password'] . "<br>";
echo "<br>";
echo "Le password est-il hashé ? " . (strpos($user['password'], '$2y$') === 0 ? "OUI 😭" : "NON 🎉");
?>
