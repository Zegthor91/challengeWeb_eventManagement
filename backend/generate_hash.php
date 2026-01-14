<?php
$password = 'Okay94';
$hash = password_hash($password, PASSWORD_DEFAULT);

echo "Hash pour 'Okay94' :\n";
echo $hash;
echo "\n\n";

// Test de verification
if (password_verify('Okay94', $hash)) {
    echo "✅ Vérification OK !";
} else {
    echo "❌ Vérification échouée !";
}
?>
