<?php session_start(); ?>

<head>
    <title>Admin Knapsack</title>
    <meta charset="utf-8">
    <?php require_once('fonctions.php'); ?>
    <?php require_once('sql.php'); ?>
    <style>
        <?php require 'css/style.css'; ?>
    </style>
</head>

<?php   
    if(empty($_SESSION) || CheckIfAdmin($_SESSION["idJoueur"]) != 1){
        ChangePage("index.php");
    }
?>

<body>
    <?php CreateHeaderV3("Capital", "Ajouter", "Déconnexion", "Inventaires",
                            "modifiercapitaladmin.php", "ajout.php", "index.php", "adminInventory.php", $_SESSION["username"]); ?>
    <div class="conteneur_principal">
        <div class="conteneur_de_filtres"><?php AfficherFiltres("magasin.php") ?></div>
        <div class="conteneur_de_cases"><?php AfficherItemsAdmin() ?></div>
    </div>
    <?php CreateFooter(); ?>
</body>