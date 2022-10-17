<?php session_start(); ?>

<head>
    <title>Inventaire de Joueur</title>
    <meta charset="utf-8">
    <?php require_once('fonctions.php'); ?>
    <?php require_once('sql.php'); ?>
    <style>
        <?php require 'css/style.css'; ?>
    </style>
</head>

<body>
    <?php CreateHeader("Retour","Déconnexion","adminconnect.php","index.php");?>
    <?php 
                ?>
            <div class="inscription_form" ><?php AfficherJoueursInventaire() ?></div>
            <?php 
      FormGetInventoryAdmin();
    if(isset($_POST["adminInventory"]))
    {
        if(empty($_POST["aliasJoueurAdmin"]))
        {
            echo "Tous les champs doivent être rempli!";
        }
        else
        {
            $IdJoueur = GetIdJoueur($_POST["aliasJoueurAdmin"]);
            echo '<div class="conteneur_principal">
            <div class="conteneur_de_cases">'.AfficherSacAdos($IdJoueur).'</div>
            </div>';
        }
    }
    ?>

    <?php CreateFooter() ?>
</body>