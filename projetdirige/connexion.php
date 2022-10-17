<?php 
    session_start();
?>
<!DOCTYPE html>
<head>
    <title>Connexion Knapsack</title>
    <meta charset="utf-8">
    <?php require_once('fonctions.php'); ?>
    <?php require_once('sql.php'); ?>
    <style>
        <?php require 'css/style.css'; ?>
    </style>
</head>

<?php
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        // Permet de garder les informations déjà entrés si il y a une erreur
        if(!isset($_POST['username'])){ $usrnm = ""; } 
        else { $usrnm = $_POST['username']; } 
        
        // Vérifie si tous les champs ont été entrés.
        if(!empty($_POST['username'])
        && !empty($_POST['password'])){
            // Affiche un message d'erreur si le mot de passe est erronné
            if(ConfirmerMotDePasse($_POST['username'], $_POST['password']))
            {
                $_SESSION["idJoueur"] = GetIdJoueur($_POST['username']);
                if(CheckIfAdmin($_SESSION["idJoueur"]) == 1)
                {
                    $_SESSION["username"] = $_POST['username'];
                    ChangePage("adminconnect.php");
                }
                else
                {
                    $_SESSION["username"] = $_POST['username'];
                    $_SESSION["caps"] = GetCapsAmout($_POST['username']); 
                    $_SESSION["dex"] = GetDex($_POST['username']);
                    $_SESSION["poids"] = GetPoidsTotalJoueur(GetIdJoueur($_POST['username']));
                    ChangePage("magasin.php");
                }
            }
            else{
                echo '  <script>
                            window.onload = () => { document.getElementById("erreur_connexion").style.display = "block"; }
                        </script>';
            }
        }
        // Affiche un message d'erreur si les champs ne sont pas tous remplis
        else {
            echo '  <script>
                window.onload = () => { document.getElementById("erreur_entries").style.display = "block"; }
            </script>';
        }
    }
?>

<html>
<body>

    <?php CreateHeader("Retour", "Inscription", "index.php", "inscription.php", "Connexion") ?> 

    <form method="post" class="inscription_form">
        <label for="username">Nom d'utilisateur</label>
        <input type="text" id="username" name="username" value="<?= $usrnm ?>" class="text_input">

        <label for="password">Mot de passe</label>
        <input type="password" id="password" name="password" class="text_input">

        <div class="password_show" onclick="AfficherMdp()"> Afficher mot de passe </div>

        <input type="submit" value="Confirmer" class="submit_button" class="text_input">
        
        <div class="message_erreur" id="erreur_entries">Tous les champs sont obligatoires</div>
        <div class="message_erreur" id="erreur_connexion">Nom d'utilisateur ou mot de passe invalide</div>
    </form>

    <?php CreateFooter() ?>
    
</body>
</html>
<script>
    // Permet de changer le mot de passe de caché à visible
    function AfficherMdp(){
        var x = document.getElementById('password');
        if (x.type === 'password') {
                x.type = 'text';
        } else {
                x.type = 'password';
        }
    }
</script>