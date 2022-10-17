<head>
    <title>Inscription Knapsack</title>
    <meta charset="utf-8">
    <?php require_once('fonctions.php'); ?>
    <?php require_once('sql.php'); ?>
    <style>
        <?php require 'css/style.css'; ?>
    </style>
</head>

<?php   
        // Permet de garder les informations déjà entrés si il y a une erreur
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        if(!isset($_POST['username'])){ $usrnm = ""; } 
        else { $usrnm = $_POST['username']; } 

        if(!isset($_POST['prenom'])){ $prenom = ""; } 
        else { $prenom = $_POST['prenom']; } 

        if(!isset($_POST['nom'])){ $nom = ""; } 
        else { $nom = $_POST['nom']; } 

        if(!isset($_POST['email'])){ $email= ""; } 
        else { $email = $_POST['email']; } 

        // Vérifie si tous les champs ont été entrés.
        if(!empty($_POST['username'])
        && !empty($_POST['prenom'])
        && !empty($_POST['nom'])
        && !empty($_POST['password'])
        && !empty($_POST['passwordConfirm'])
        && !empty($_POST['email']))
        {
            // Affiche un message d'erreur si le courriel est invalide
            if (ConfirmEmail($_POST['email'])){
                echo '  <script>
                    window.onload = () => { document.getElementById("erreur_email").style.display = "block"; }
                </script>';
            }
            else if (ConfirmPassword($_POST['password'])){
                echo '  <script>
                    window.onload = () => { document.getElementById("erreur_mdp").style.display = "block"; }
                </script>';
            }
            else if ($_POST['password'] != $_POST['passwordConfirm']){
                echo '  <script>
                    window.onload = () => { document.getElementById("erreur_mdp_confirm").style.display = "block"; }
                </script>';
            }
            else {
                Ajouter($_POST['username'], $_POST['prenom'], $_POST['nom'], $_POST['password'], $_POST['email']);
                ChangePage("connexion.php");
            }
        }
        // Affiche un message d'erreur si les champs ne sont pas tous remplis
        else
        {
            echo '  <script>
                    window.onload = () => { document.getElementById("erreur_entries").style.display = "block"; }
                </script>';
        }
    }
?>

<body>


    <?php CreateHeader("Retour", "Connexion", "index.php", "connexion.php", "Inscription"); ?> 

    <form action="" method="post" class="inscription_form">
        <label for="username">Nom d'utilisateur</label>
        <input type="text" id="username" name="username" value="<?= $usrnm ?>" class="text_input">

        <label for="prenom">Prenom</label>
        <input type="text" id="prenom" name="prenom" value="<?= $prenom ?>" class="text_input">

        <label for="nom">Nom</label>
        <input type="text" id="nom" name="nom" value="<?= $nom ?>" class="text_input">

        <label for="password">Mot de passe</label>
        <input type="password" id="password" name="password" class="text_input">

        <label for="passwordConfirm">Confirmer mot de passe</label>
        <input type="password" id="passwordConfirm" name="passwordConfirm" class="text_input">
        <div class="password_show" onclick="AfficherMdp()"> Afficher mot de passe </div>

        <label for="email">Courriel</label>
        <input type="text" id="email" name="email" value="<?= $email ?>" class="text_input">
        <input type="submit" value="Confirmer" class="submit_button">

        <div class="message_erreur" id="erreur_entries">Tous les champs sont obligatoires</div>
        <div class="message_erreur" id="erreur_mdp">Mot de passe invalide, il doit contenir minumum 5 caractères, dont au moins 1 lettre et 1 chiffre</div>
        <div class="message_erreur" id="erreur_mdp_confirm">Les mots de passe ne correspondent pas</div>
        <div class="message_erreur" id="erreur_email">Courriel invalide</div>
    </form>

    <?php CreateFooter(); ?>
</body>
<script>
    // Permet de changer le mot de passe et la confirmation de mot de passe de caché à visible
    function AfficherMdp(){
        var x = document.getElementById('password');
        var y = document.getElementById('passwordConfirm');
        if (x.type === 'password') {
                x.type = 'text';
                y.type = 'text';
        } else {
                x.type = 'password';
                y.type = 'password';
        }
    }
</script>