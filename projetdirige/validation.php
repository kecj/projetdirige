<!DOCTYPE html>
<html lang="fr">
<?php session_start(); 
    require ("bd.php");
    if ($_SERVER["REQUEST_METHOD"] == "GET") {
        echo getIdfromPseudo($_GET["id"]);
        if(verifierSiExiste($_GET["id"])){
            setValide($_GET["id"]);
            header('Location: login.php');
        }
    }
?>
</html>