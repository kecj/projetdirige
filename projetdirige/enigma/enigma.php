<?php 
    session_start();
?>
<!DOCTYPE html>
<head>
    <title>Enigma</title>
    <meta charset="utf-8">
    <?php require_once("enigmasql.php"); ?>
    <style>
        <?php require "enigmaStyle.css"; ?>
    </style>
</head>

<?php 
    if(empty($_SESSION)){ ChangePage("https://www.youtube.com/watch?v=xvFZjo5PgG0"); }
    if(!$_SESSION["incorrecte"]){
        $_SESSION["incorrecte"] = 0;
    }
    if(!$_SESSION["correcte"]){
        $_SESSION["correcte"] = 0;
    }
?>

<?php //echo "<script> window.location.href='https://www.youtube.com/watch?v=xvFZjo5PgG0' </script>";?>
<body>
    <div class="header">
        <a href="http://74.56.250.97/projetdirige/magasin.php" class="bouton_principal">Knapsack</a>
        <div class='header_title'>Enigma</div>
        <div>
            <div class="header_nom_joueur">
                <?php echo $_SESSION["username"]; ?>
            </div>
            <div class="header_nbr_caps">
                <?php echo $_SESSION["caps"]; ?>
            </div>
        </div>
    </div>

    <form class="conteneur_principal" method="post">
        <input type="submit" class="submit_button" value="Énigme Aléatoire" name="aleatoire">
        <div class="conteneur_choix_difficulté">
            <div class="choix_difficulté">
                <div>Choisissez la difficulté</div>
                <div>
                    <input type="radio" name="difficulté" value="1">
                        <label for="1" class="diff_label_label">1</label>
                    <input type="radio" name="difficulté" value="2">
                        <label for="2" class="diff_label_label">2</label>        
                    <input type="radio" name="difficulté" value="3">
                        <label for="3" class="diff_label_label">3</label>
                </div>
            </div>
        </div>
        <input type="submit" class="submit_button" value="Choisir la difficulté" name="choisir">
        <div class="message_erreur" id="erreur_choix_enigme">Veuillez choisir une difficulté</div>
        <div class="message_erreur" id="erreur_choix_réponse">Vous n'avez pas choisi de réponse.</div>
   
        <?php 
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            if(isset($_POST["aleatoire"])){
                $_SESSION["répondu"] = true;
                AfficherQuestionAlléatoire();
            }
            if(isset($_POST["choisir"])){
                if(empty($_POST["difficulté"]))
                {
                    AfficherMessage("erreur_choix_enigme");
                }
                else {
                    $_SESSION["répondu"] = true;
                    $_SESSION['difficulté'] = $_POST["difficulté"];
                    AfficherQuestionSpécifique($_POST["difficulté"]);
                }
            }
            if(isset($_POST["répondre"]))
            {
                if(empty($_POST["réponse"]))
                {
                    AfficherMessage("erreur_choix_réponse");
                }
                else {
                    if(CheckReponse($_POST["réponse"]) == 1){
                        if($_SESSION["répondu"]){
                            $_SESSION["répondu"] = false;
                            $_SESSION["correcte"] += 1;
                            $_SESSION["incorrecte"] = 0;
                            UpdateSoldeJoueur($_SESSION["difficulté"]);
                        }
                        AfficherMessage("réponse_correcte");
                    }
                    else{
                        if($_SESSION["répondu"]){
                            $_SESSION["répondu"] = false;
                            $_SESSION["incorrecte"] += 1;
                            $_SESSION["correcte"] = 0;
                        }
                        AfficherMessage("réponse_incorrecte");
                    }
                }
            }
        }
        ?>
        <div class="message_erreur" id="réponse_incorrecte"> 
            Réponse incorrecte. <?php echo $_SESSION["incorrecte"] ?> réponses incorrectes de suite. 
        </div>
        <div class="message_correcte" id="réponse_correcte"> 
            Réponse correcte. <?php echo $_SESSION["correcte"] ?> bonnes réponses de suite. 
            + <?php echo $_SESSION["montantAjouté"] ?> caps
            vous avez maintenant :<?php echo $_SESSION["caps"] ?> caps
        </div>
    </form>
</body>