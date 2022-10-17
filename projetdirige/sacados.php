<?php session_start()?>
<head>
<title>Sac à dos Knapsack</title>
    <meta charset="utf-8">
    <?php require_once('fonctions.php'); ?>
    <?php require_once('sql.php'); ?>
    <style>
        <?php require 'css/style.css'; ?>
    </style>
</head>

<?php 
  if(empty($_SESSION)){
    ChangePage("index.php");
  }
  if(isset($_POST["CapsRequest"]))
  {
    AskForCaps();
    echo "Votre demande de capsule est en traitement";
  }
?>

<?php  ?>
<body>


   <?php
    CreateHeaderV2("Magasin", "Panier", "Déconnexion", "magasin.php", "panier.php", "index.php", 
                    $_SESSION["username"], $_SESSION["caps"], $_SESSION["poids"], $_SESSION["dex"], "Inventaire"); ?>

  <div class="conteneur_principal">
      <div class="conteneur_de_cases"><?php AfficherSacAdos($_SESSION["idJoueur"]); ?></div>
      <form action='sacados.php' method='post'> 
              <input type='submit' name='CapsRequest' value='Demande de capsule' size="20">
            </form>
  </div>
</body>

<?php CreateFooter() ?>