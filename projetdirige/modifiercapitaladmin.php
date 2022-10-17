<?php session_start(); ?>

<head>
    <title>Modifier le capital d'un joueur (Admin)</title>
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
            <div class="inscription_form" ><?php AfficherJoueurs() ?></div>
            <?php 
      FormUpdateCapitalAdmin(); 
    if(isset($_POST["modifierCapitalAdmin"]))
    {
        if(empty($_POST["aliasJoueurAdmin"]) || empty($_POST["capitalJoueurAdmin"]))
        {
            echo "Tous les champs doivent être rempli!";
        }
        else if(CheckIfCreditReceivedMaxedOut($_POST["aliasJoueurAdmin"]))
        {
            echo "Impossible de rajouter du crédit à ce joueur. Limite reçu atteinte. ";
        }
        else if(CheckUsernameExist($_POST["aliasJoueurAdmin"]) && $_POST["aliasJoueurAdmin"] != "admin")
        {
            UpdateCapitalJoueurV2($_POST["aliasJoueurAdmin"],$_POST["capitalJoueurAdmin"]);
            UpdateReceivedCreditAdmin($_POST["capitalJoueurAdmin"],$_POST["aliasJoueurAdmin"]);
            echo "Un admin a modifié le capital du joueur et lui a rajouté " . " ". $_POST["aliasJoueurAdmin"]. " ". "à". " " . $_POST["capitalJoueurAdmin"];
            echo "<br>";
        }
        else{
            echo "Joueur non existant!";
        }
    }
    ?>

    <?php CreateFooter() ?>
</body>