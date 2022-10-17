<?php session_start(); ?>

<head>
    <title>Profil Knapsack</title>
    <meta charset="utf-8">
    <?php require_once('fonctions.php'); ?>
    <?php require_once('sql.php'); ?>
    <style>
        <?php require 'css/style.css'; ?>
    </style>
</head>
<?php
if (!empty($_SESSION)) {
  CreateHeaderV2(
      "Retour",
      "Panier",
      "Déconnexion",
      "magasin.php",
      "panier.php",
      "index.php",
      $_SESSION["username"],
      $_SESSION["caps"],
      $_SESSION["poids"],
      $_SESSION["dex"],
      "Profile"
  );
}
?>

<?php 
$currentUsername = $_SESSION["username"];
 if(isset($_POST["modifierJoueurInfo"]))
 {
     if(empty($_POST["aliasJoueur"]) || empty($_POST["mdpJoueur"]) || empty($_POST["emailJoueur"]))
     {
         echo "Tous les champs doivent être remplis pour modifier votre profil";
     }
     else if(CheckUsernameExist($_POST["aliasJoueur"]) && $currentUsername != $_POST["aliasJoueur"] )
     {
         echo "Alias déja existant!";
     }
     else if(ConfirmEmail($_POST["emailJoueur"]))
     {
         echo "Email non valide!";
     }
     else{
         UpdateProfilJoueur($_POST["aliasJoueur"],$_POST["mdpJoueur"],$_POST["emailJoueur"],$_SESSION["idJoueur"]);
         $_SESSION["username"] = $_POST["aliasJoueur"];
         echo "Votre profil est à jour!";
     }
 }
?>

<body>
 <?php ModifierProfilJoueur(); ?>
 <?php CreateFooter(); ?>
</body>
</html>
