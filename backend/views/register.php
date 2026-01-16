<?php
// Page d'inscription
require_once '../config/database.php';
require_once '../config/session.php';
require_once '../config/helpers.php';

$error = '';
$success = '';

// Si l'utilisateur est déjà connecté, redirige vers le dashboard
if (isLoggedIn()) {
    redirect('/views/dashboard.php');
}

// Traitement du formulaire d'inscription
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = clean($_POST['nom'] ?? '');
    $email = clean($_POST['email'] ?? '');
    $telephone = clean($_POST['telephone'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    // Validation
    $errors = [];

    if (empty($nom)) {
        $errors[] = "Le nom est requis";
    } elseif (strlen($nom) < 2) {
        $errors[] = "Le nom doit contenir au moins 2 caractères";
    }

    if (empty($email)) {
        $errors[] = "L'email est requis";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "L'email n'est pas valide";
    }

    if (empty($password)) {
        $errors[] = "Le mot de passe est requis";
    } elseif (strlen($password) < 6) {
        $errors[] = "Le mot de passe doit contenir au moins 6 caractères";
    }

    if ($password !== $password_confirm) {
        $errors[] = "Les mots de passe ne correspondent pas";
    }

    // Vérifier si l'email existe déjà
    if (empty($errors)) {
        $existing = fetchOne("SELECT id FROM users WHERE email = ?", [$email]);
        if ($existing) {
            $errors[] = "Cet email est déjà utilisé";
        }
    }

    if (!empty($errors)) {
        $error = implode("<br>", $errors);
    } else {
        // Créer le compte administrateur
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        try {
            $stmt = $pdo->prepare("INSERT INTO users (nom, email, password, role, telephone) VALUES (?, ?, ?, 'administrateur', ?)");
            $stmt->execute([$nom, $email, $password_hash, $telephone]);

            $success = "Compte créé avec succès ! Vous pouvez maintenant vous connecter.";

            // Réinitialiser les champs
            $nom = $email = $telephone = '';
        } catch (PDOException $e) {
            $error = "Erreur lors de la création du compte. Veuillez réessayer.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - Gestion Événements</title>
    <link rel="stylesheet" href="/public/css/style.css">
    <style>
        .login-box {
            max-width: 480px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .password-requirements {
            margin-top: 8px;
            font-size: 12px;
            color: #737373;
        }

        .login-link {
            text-align: center;
            margin-top: 28px;
            padding-top: 28px;
            border-top: 1px solid #e5e5e5;
        }

        .login-link a {
            color: #000;
            text-decoration: none;
            font-weight: 600;
        }

        .login-link a:hover {
            text-decoration: underline;
        }

        @media (max-width: 480px) {
            .form-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <h1>Gestion d'Événements</h1>
            <h2>Créer un compte administrateur</h2>

            <?php if ($error): ?>
                <div class="error-message"><?php echo $error; ?></div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="success-message"><?php echo $success; ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label>Nom complet *</label>
                    <input type="text" name="nom" value="<?php echo htmlspecialchars($nom ?? ''); ?>" placeholder="Votre nom" required>
                </div>

                <div class="form-group">
                    <label>Email *</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($email ?? ''); ?>" placeholder="votre@email.com" required>
                </div>

                <div class="form-group">
                    <label>Téléphone</label>
                    <input type="tel" name="telephone" value="<?php echo htmlspecialchars($telephone ?? ''); ?>" placeholder="06 12 34 56 78">
                </div>

                <div class="form-group">
                    <label>Mot de passe *</label>
                    <input type="password" name="password" placeholder="Minimum 6 caractères" required>
                    <p class="password-requirements">Le mot de passe doit contenir au moins 6 caractères</p>
                </div>

                <div class="form-group">
                    <label>Confirmer le mot de passe *</label>
                    <input type="password" name="password_confirm" placeholder="Confirmez votre mot de passe" required>
                </div>

                <button type="submit" class="btn-primary">Créer mon compte</button>
            </form>

            <div class="login-link">
                <p>Vous avez déjà un compte ? <a href="login.php">Se connecter</a></p>
            </div>
        </div>
    </div>
</body>
</html>
