<?php
// Page de connexion
require_once '../config/database.php';
require_once '../config/session.php';
<<<<<<< HEAD
require_once '../config/helpers.php';

$error = '';
$debug = '';

// Si l'utilisateur est deja connecté, redirige vers le dashboard
if (isLoggedIn()) {
    redirect('/views/dashboard.php');
=======

$error = '';

// Si l'utilisateur est deja connecté, redirige vers le dashboard
if (isLoggedIn()) {
    redirect('/backend/views/dashboard.php');
>>>>>>> 1b402c43edf8776eb44ed747824a00ed1ca1350d
}

// Traitement du formulaire de connexion
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = clean($_POST['email']);
    $password = $_POST['password'];
    
<<<<<<< HEAD
    $debug .= "=== DEBUG COMPLET ===<br><br>";
    $debug .= "1. Email saisi : '$email'<br>";
    $debug .= "2. Password saisi : '$password'<br>";
    $debug .= "3. Longueur password : " . strlen($password) . "<br><br>";
    
    // Test 1 : Requete simple sans parametres
    try {
        $test_query = $pdo->query("SELECT * FROM users WHERE email = 'adminok@test.com'");
        $test_user = $test_query->fetch(PDO::FETCH_ASSOC);
        
        if ($test_user) {
            $debug .= "TEST 1 (requête directe) : ✅ User trouvé !<br>";
            $debug .= "   - Nom : " . $test_user['nom'] . "<br>";
            $debug .= "   - Email : " . $test_user['email'] . "<br>";
            $debug .= "   - Password hash : " . substr($test_user['password'], 0, 20) . "...<br><br>";
        } else {
            $debug .= "TEST 1 (requête directe) : ❌ Aucun user<br><br>";
        }
    } catch (Exception $e) {
        $debug .= "TEST 1 ERREUR : " . $e->getMessage() . "<br><br>";
    }
    
    // Test 2 : Avec fetchOne et $1
    try {
        $user = fetchOne("SELECT * FROM users WHERE email = $1", [$email]);
        
        if ($user) {
            $debug .= "TEST 2 (fetchOne avec \$1) : ✅ User trouvé !<br>";
            $debug .= "   - Nom : " . $user['nom'] . "<br>";
            $debug .= "   - Password hash : " . substr($user['password'], 0, 20) . "...<br><br>";
        } else {
            $debug .= "TEST 2 (fetchOne avec \$1) : ❌ Aucun user<br><br>";
        }
    } catch (Exception $e) {
        $debug .= "TEST 2 ERREUR : " . $e->getMessage() . "<br><br>";
    }
    
    // Test 3 : Avec ? au lieu de $1
    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user3 = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user3) {
            $debug .= "TEST 3 (avec ?) : ✅ User trouvé !<br>";
            $debug .= "   - Nom : " . $user3['nom'] . "<br><br>";
        } else {
            $debug .= "TEST 3 (avec ?) : ❌ Aucun user<br><br>";
        }
    } catch (Exception $e) {
        $debug .= "TEST 3 ERREUR : " . $e->getMessage() . "<br><br>";
    }
    
    // Utilise le user du test qui a marché
    $user = $test_user ?? $user3 ?? null;
    
    if ($user && password_verify($password, $user['password'])) {
        login($user);
        redirect('/views/dashboard.php');
=======
    // Recupere l'utilisateur par email
    $user = fetchOne("SELECT * FROM users WHERE email = $1", [$email]);
    
    if ($user && password_verify($password, $user['password'])) {
        // Connexion reussi
        login($user);
        redirect('/backend/views/dashboard.php');
>>>>>>> 1b402c43edf8776eb44ed747824a00ed1ca1350d
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
<<<<<<< HEAD
    <link rel="stylesheet" href="../../public/css/style.css">
=======
    <link rel="stylesheet" href="/public/css/style.css">
>>>>>>> 1b402c43edf8776eb44ed747824a00ed1ca1350d
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <h1>Gestion d'Événements</h1>
            <h2>Connexion</h2>
            
<<<<<<< HEAD
            <?php if ($debug): ?>
                <div style="background: #fff3cd; padding: 15px; margin-bottom: 15px; border-radius: 5px; font-size: 11px; font-family: monospace; text-align: left; max-height: 400px; overflow-y: auto;">
                    <?php echo $debug; ?>
                </div>
            <?php endif; ?>
            
=======
>>>>>>> 1b402c43edf8776eb44ed747824a00ed1ca1350d
            <?php if ($error): ?>
                <div class="error-message"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label>Email</label>
<<<<<<< HEAD
                    <input type="email" name="email" value="adminok@test.com">
=======
                    <input type="email" name="email" required>
>>>>>>> 1b402c43edf8776eb44ed747824a00ed1ca1350d
                </div>
                
                <div class="form-group">
                    <label>Mot de passe</label>
<<<<<<< HEAD
                    <input type="password" name="password" value="Okay94">
=======
                    <input type="password" name="password" required>
>>>>>>> 1b402c43edf8776eb44ed747824a00ed1ca1350d
                </div>
                
                <button type="submit" class="btn-primary">Se connecter</button>
            </form>
            
            <div class="test-accounts">
                <p>Comptes de test :</p>
                <ul>
<<<<<<< HEAD
                    <li>adminok@test.com / Okay94</li>
=======
                    <li>admin@test.com / admin123</li>
                    <li>jean@test.com / jean123</li>
                    <li>marie@test.com / marie123</li>
>>>>>>> 1b402c43edf8776eb44ed747824a00ed1ca1350d
                </ul>
            </div>
        </div>
    </div>
</body>
</html>