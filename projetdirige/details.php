<?php session_start(); ?>

<head>
    <title>Détails Knapsack</title>
    <meta charset="utf-8">
    <?php require_once('fonctions.php'); ?>
    <?php require_once('sql.php'); ?>
    <style>
        <?php require 'css/style.css'; ?>
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>

<?php

$nbrAchat = 1;
$obj = $_GET["IdObjet"];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    if (isset($_POST['qte'])) {
        $nbrAchat = $_POST['qte'];
        $idObjet = $_SESSION['itemId'];
        AjoutItemPanier($_SESSION['idJoueur'], $_SESSION['itemId'], $nbrAchat);
    }
    if (isset($_POST["Ajouter"])) {

        AddCommentItem($_POST['comment'], $_SESSION['itemId'], $_POST["rate"]);
    }
    if (isset($_POST["Modifier"])) {

        UpdateCommentaire($_POST["rate"], $_POST['comment'], $_SESSION['itemId']);
    }
    if (isset($_POST["retirer"])) {
        deleteCommentaires($_POST["retirer"]);
    }
}
?>

<body>
    <?php
    if (!empty($_SESSION)) {
        CreateHeaderV2( "Retour", "Panier", "Déconnexion",
                        "magasin.php", "panier.php", "index.php",
                        $_SESSION["username"], $_SESSION["caps"], $_SESSION["poids"], $_SESSION["dex"],
                        "Détails"
                     );
    } 
    else 
    {
        CreateHeader("Retour", "Connexion", "index.php", "connexion.php");
    }
    ?>
    <div class="conteneur_details">
            <div  class="details">
            <?php AfficherDetails($nbrAchat); ?>
                <div>Commentaires</div>
                <form method="post" >
                    <div class="details">
                        <input type="text" name="comment" id="comment" placeholder="Entrez votre commentaire" />
                        <div class="rate">
                            <input type="radio" id="star5" name="rate" value="5" />
                            <label for="star5" title="text">5 stars</label>
                            <input type="radio" id="star4" name="rate" value="4" />
                            <label for="star4" title="text">4 stars</label>
                            <input type="radio" id="star3" name="rate" value="3" />
                            <label for="star3" title="text">3 stars</label>
                            <input type="radio" id="star2" name="rate" value="2" />
                            <label for="star2" title="text">2 stars</label>
                            <input type="radio" id="star1" name="rate" value="1" />
                            <label for="star1" title="text">1 star</label>
                        </div>
                        <?php
                        echo GetSubmit();?>
                    </div>
                    <?php ShowComments($_GET["IdObjet"]); ?>
                </form>
        </div>
    </div>
    <?php
    function GetSubmit()
        {
            $valeurCheck=checkJoueurCommenter($_SESSION['itemId'],GetIdJoueur($_SESSION["username"]));
            if(isset($_SESSION["username"]) && CheckSiItemAcheter($_SESSION['itemId'],GetIdJoueur($_SESSION["username"])))
            {
                if($valeurCheck<=0)
                {
                    $ValeurDeForm="Ajouter";
                }
                else if($valeurCheck>0)
                {
                    $ValeurDeForm="Modifier";
                }
                return  '<input type="submit" name="'.$ValeurDeForm.'" value="'.$ValeurDeForm.'" class="item_panier_retirer">';
            }
            else
            {
                return  '';
            }
        }
    ?>
    <?php CreateFooter() ?>

</body>