<?php session_start(); ?>

<head>
    <title>Acceuil Knapsack</title>
    <meta charset="utf-8">
    <?php require_once('fonctions.php'); ?>
    <?php require_once('sql.php'); ?>
    <style>
        <?php require 'css/style.css'; ?>
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>

<?php 
    if(!empty($_SESSION))
    {
        session_unset();
        session_destroy();
    } 
?>

<body> 

    <?php CreateHeader("Connexion", "Inscription", "connexion.php", "inscription.php") ?> 

    <div class="conteneur_principal">
        <div class="conteneur_de_filtres"><?php AfficherFiltres("index.php") ?></div>
        <div class="conteneur_de_cases"><?php AfficherTousItems() ?></div>
    </div>

    <?php CreateFooter() ?>
    
</body>





