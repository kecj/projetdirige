<?php session_start(); ?>

<head>
    <title>Détails Knapsack</title>
    <meta charset="utf-8">
    <?php require_once('fonctions.php'); ?>
    <?php require_once('sql.php'); ?>
    <style>
        <?php require 'css/style.css'; ?>
    </style>
</head>


<body> 
<?php CreateHeader("Retour", "Déconnexion", "adminconnect.php", "index.php"); ?>
<?php
    if(isset($_POST["goToModifierItem"]))
    {
        ChangePage('modifieritemadmin.php?IdObjet='.$_GET["IdObjet"].'');
    }
?>


    <div class="conteneur_details">
        <?php AfficherDetailsAdmin(); ?> 
        <div class="conteneur_details">
            <h1>Commentaires</h1>

            <?php
        if(isset($_POST["retirer"]))
        {
            deleteCommentaires($_POST["retirer"]);
            echo "Commentaire supprimé!";
        }
    ?>
    <?php ShowComments($_GET["IdObjet"]); ?> 
        </div>
        </div>
    </div>



    <?php CreateFooter() ?>

</body>