<?php session_start() ?>

<head>
    <title>Panier Knapsack</title>
    <meta charset="utf-8">
    <?php require_once('fonctions.php'); ?>
    <?php require_once('sql.php'); ?>
    <style>
        <?php require 'css/style.css';?>
    </style>
</head>

<?php 
global $coutTotalPanier;
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    if (isset($_POST["Modifier"])) 
    {
        ModifierQuantiteItemPanier($_POST["idItem"], $_POST['qte'], $_SESSION["idJoueur"]);
    }
    else if (isset($_POST["Retirer"])) 
    {
        RetirerItemPanier($_POST["idItem"],$_SESSION["idJoueur"]);
    }
    else if (isset( $_POST["Payer"])) 
    {
        if( $_SESSION["caps"]<$_POST["totalPanier"])
        {
            echo '  <script>
                        window.onload = () => { document.getElementById("erreur_fonds_insufisants").style.display = "block"; }
                    </script>';
        }
        else
        {
            PayerPanier($_SESSION["idJoueur"],$_POST["totalPanier"]);
        }
    }
}
?>

<body>
    <?php CreateHeaderV2( "Magasin", "Inventaire", "Déconnexion", "magasin.php", "sacados.php", "index.php",
        $_SESSION["username"], $_SESSION["caps"], $_SESSION["poids"], $_SESSION["dex"], "Panier"); ?>
    <div class="conteneur_panier">
        <?php AfficherPanier($_SESSION["idJoueur"]); ?>
        <div class="message_erreur" id="erreur_fonds_insufisants">Fonds insufisants</div>
    </div>
    <?php CreateFooter() ?>
</body>