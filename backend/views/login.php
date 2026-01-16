<?php
// Page de connexion
require_once '../config/database.php';
require_once '../config/session.php';
require_once '../config/helpers.php';

$error = '';

// Si l'utilisateur est déjà connecté, redirige vers le dashboard
if (isLoggedIn()) {
    redirect('/views/dashboard.php');
}

// Traitement du formulaire de connexion
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = clean($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = "Veuillez remplir tous les champs";
    } else {
        // Recherche de l'utilisateur
        $user = fetchOne("SELECT * FROM users WHERE email = ?", [$email]);

        if ($user && password_verify($password, $user['password'])) {
            login($user);
            redirect('/views/dashboard.php');
        } else {
            $error = "Email ou mot de passe incorrect";
        }
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
    <style>
        .register-link {
            text-align: center;
            margin-top: 28px;
            padding-top: 28px;
            border-top: 1px solid #e5e5e5;
        }

        .register-link a {
            color: #000;
            text-decoration: none;
            font-weight: 600;
        }

        .register-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <h1>Gestion d'Événements</h1>
            <h2>Connexion</h2>

            <?php if ($error): ?>
                <div class="error-message"><?php echo $error; ?></div>
            <?php endif; ?>

            <?php if (isset($_GET['registered'])): ?>
                <div class="success-message">Compte créé avec succès ! Vous pouvez maintenant vous connecter.</div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" placeholder="votre@email.com" required>
                </div>

                <div class="form-group">
                    <label>Mot de passe</label>
                    <input type="password" name="password" placeholder="Votre mot de passe" required>
                </div>

                <button type="submit" class="btn-primary">Se connecter</button>
            </form>

            <div class="register-link">
                <p>Pas encore de compte ? <a href="register.php">Créer un compte</a></p>
            </div>
        </div>
    </div>
</body>
</html>
