<?php session_start(); ?>

<head>
    <title>Magasin Knapsack</title>
    <meta charset="utf-8">
    <?php require_once('fonctions.php'); ?>
    <?php require_once('sql.php'); ?>
    <style>
        <?php require 'css/style.css'; ?>
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>

<?php   
    if(empty($_SESSION)){
        ChangePage("https://www.youtube.com/watch?v=xvFZjo5PgG0");
    }
?>

<body>
    <?php CreateHeaderV2("Inventaire", "Panier", "Déconnexion", "sacados.php", "panier.php", "index.php", 
                        $_SESSION["username"], $_SESSION["caps"], $_SESSION["poids"], $_SESSION["dex"], "Magasin") ?>

    <div class="conteneur_principal">
        <div class="conteneur_de_filtres"><?php AfficherFiltres("magasin.php") ?></div>
        <div class="conteneur_de_cases"><?php AfficherTousItems() ?></div>
    </div>

    <?php CreateFooter() ?>
</body>

