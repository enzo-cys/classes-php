<?php
// Pour afficher toutes les erreurs PHP (pratique en dev)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Inclure la classe que tu veux tester
require_once 'User.php'; // ou 'user-pdo.php' pour tester l'autre version

echo "---- Test Classe User (mysqli) ----<br><br>";

$user = new User();

// Test register (création utilisateur)
echo "Création de l'utilisateur...<br>";
$res = $user->register('johndoe', 'motdepasse123', 'john@doe.com', 'John', 'Doe');
if ($res) {
    echo "Utilisateur créé : <pre>";
    print_r($res);
    echo "</pre>";
} else {
    echo "Erreur lors de la création (login ou email existe déjà ?)<br>";
}

// Test connexion
echo "<br>Connexion...<br>";
if ($user->connect('johndoe', 'motdepasse123')) {
    echo "Connecté !<br>";
    print_r($user->getAllInfos());
} else {
    echo "Erreur de connexion<br>";
}

// Test isConnected
echo "<br>isConnected: ";
var_export($user->isConnected());
echo "<br>";

// Test MAJ infos
echo "<br>Mise à jour de l'utilisateur...<br>";
$user->update('janedoe', 'newpass', 'jane@doe.com', 'Jane', 'Doe');
print_r($user->getAllInfos());

// Test getLogin, getEmail, getFirstname, getLastname
echo "<br>Login: " . $user->getLogin();
echo "<br>Email: " . $user->getEmail();
echo "<br>Firstname: " . $user->getFirstname();
echo "<br>Lastname: " . $user->getLastname() . "<br>";

// Test déconnexion
echo "<br>Déconnexion...<br>";
$user->disconnect();
echo "isConnected: ";
var_export($user->isConnected());
echo "<br>";

// Test suppression
echo "<br>Suppression de l'utilisateur (et déconnexion)...<br>";
$user->connect('janedoe', 'newpass'); // Reconnecte pour supprimer (sinon delete ne fait rien)
$user->delete();
echo "Utilisateur supprimé.<br>";

// Test final isConnected
echo "isConnected: ";
var_export($user->isConnected());
echo "<br>";
?>