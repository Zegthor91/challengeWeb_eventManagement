<?php
// Page de connexion
require_once '../config/database.php';
require_once '../config/session.php';

$error = '';

// Si l'utilisateur est deja connecté, redirige vers le dashboard
if (isLoggedIn()) {
    redirect('/backend/views/dashboard.php');
}

// Traitement du formulaire de connexion
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = clean($_POST['email']);
    $password = $_POST['password'];
    
    // Recupere l'utilisateur par email
    $user = fetchOne("SELECT * FROM users WHERE email = $1", [$email]);
    
    if ($user && password_verify($password, $user['password'])) {
        // Connexion reussi
        login($user);
        redirect('/backend/views/dashboard.php');
    } else {
        $error = "Email ou mot de passe incorrect";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Gestion Événements</title>
    <link rel="stylesheet" href="/public/css/style.css">
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <h1>Gestion d'Événements</h1>
            <h2>Connexion</h2>
            
            <?php if ($error): ?>
                <div class="error-message"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" required>
                </div>
                
                <div class="form-group">
                    <label>Mot de passe</label>
                    <input type="password" name="password" required>
                </div>
                
                <button type="submit" class="btn-primary">Se connecter</button>
            </form>
            
            <div class="test-accounts">
                <p>Comptes de test :</p>
                <ul>
                    <li>admin@test.com / admin123</li>
                    <li>jean@test.com / jean123</li>
                    <li>marie@test.com / marie123</li>
                </ul>
            </div>
        </div>
    </div>
</body>
</html>