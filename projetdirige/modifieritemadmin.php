<?php session_start(); ?>

<head>
    <title>Modification d'un item (Admin)</title>
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
    if(isset($_POST["modifierItemAdmin"]))
    {
        if(empty($_POST["nomObjetAdmin"]) || empty($_POST["descriptionObjetAdmin"]) || empty($_POST["typeObjetAdmin"]) || empty($_POST["poidsObjetAdmin"]) || empty($_POST["quantiteStockAdmin"]) || empty($_POST["prixUnitaireAdmin"]))
        {
            echo "Impossible de modifier un item lorsqu'un des champs est vide!";
        }
        else{
            UpdateObjetAdmin($_POST["nomObjetAdmin"],$_POST["descriptionObjetAdmin"],$_POST["typeObjetAdmin"], $_POST["poidsObjetAdmin"],$_POST["quantiteStockAdmin"],$_POST["prixUnitaireAdmin"],$_GET["IdObjet"]);
            echo "Objet à jour";
        }
    }
    ?>


    <?php FormAdminModifierItem(); ?>




    <?php CreateFooter(); ?>
</body>