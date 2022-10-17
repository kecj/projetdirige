<!-- Page servant uniquement à regrouper les fonctions n'ayant pas besoin de requêtes SQL -->

<style>
        <?php require 'css/style.css'; ?>
    </style>
<?php 
    require_once 'sql.php';

    // Sert à Créer le header des pages. 
    // $txt1 et 2 correspondent au texte que nous voulons mettre dans les boutons 
    // $link1 et 2 correspondent à la page vers laquelle nous voulons acheminer les usagers
    // $title est le titre au centre du header. (par défaut Knapsack)

    function CreateHeader($txt1, $txt2, $link1, $link2,  $title = "Knapsack")
    {
        echo "  <div class='header'> 
                    <a href='" . $link1 . "'>
                        <div class='bouton_principal bouton_gauche'>
                            " . $txt1 . "
                        </div>
                    </a>
                    <div class='header_title'>
                        " . $title . "
                    </div>
                    <a href='". $link2 . "'>
                        <div class='bouton_principal'>
                            " . $txt2 . "
                        </div>
                    </a>
                </div>";
    }

    function CreateHeaderV2($txt1, $txt2, $txt3, $link1, $link2, $link3, $username, $capsCount, $poids, $dex, $title = "Knapsack")
    {
        echo "
            <div class='header'>
                <div class='header_section_gauche'>
                    <a href='" . $link1 . "'>
                        <div class='bouton_principal'>
                            " . $txt1 . "
                        </div>
                    </a> 
                    <div class='header_cap_count'>
                        " . $capsCount . " <img src='./images/caps.png' alt='caps' class='caps'>
                    </div>
                    <a href='" . $link2 . "'>
                        <div class='bouton_principal'>
                            " . $txt2 . "
                        </div>
                    </a>
                    <a class='bouton_principal' href='enigma/enigma.php'>
                        Enigma
                    </a>
                </div>

                <div class='header_title'>
                    " . $title . "
                </div>

                <div class='header_section_droite'>
                    <div class='header_section_droite_informations'>
                        <a href='profil.php' class='header_information_username'>
                            " . $username . "
                        </a>
                        <div>
                            Poids total : " . $poids . " lbs
                        </div>
                        <div>
                            Dextérité :  " . $dex . "
                        </div>
                    </div>

                    <a href='". $link3 . "'>
                        <div class='bouton_principal'>
                            " . $txt3 . "
                        </div>
                    </a>
                </div>
            </div>
        ";

    }
    function CreateHeaderV3($txt1, $txt2, $txt3, $txt4, $link1, $link2, $link3, $link4, $userConnected,$title = "Knapsack")
    {
        echo "  <div class='header'> 
                    <div class='header_section_gauche'>
                        <a href='$link1' class='bouton_principal'>$txt1</a>
                        <a href='$link2'class='bouton_principal'>$txt2</a>
                        <a href='$link4'class='bouton_principal'>$txt4</a>
                    </div>
                    
                    <div class='header_title'>$title</div>

                    <div class='header_section_droite'>
                        <div class='header_username'>$userConnected</div>
                        <a href='$link3' class='bouton_principal'>$txt3</a>
                    </div>
                </div>";
    }

    function AfficherFiltres(){
        $type = 'NomObjet';
        $typeDordre = 'Desc';
        if ($_SERVER['REQUEST_METHOD'] == "POST") {
          $type = $_POST['classement'];
          $typeDordre = $_POST['ordre'];
        }
    
        echo '<form action="index.php" method="post">
                <div class="filtres">
                  <select name="classement" class="filtre_classement">
                    <option value="Poids" '. ChoisirSelectedFiltreClassement("Poids") .'>Poids</option>
                    <option value="quantiteStock" '. ChoisirSelectedFiltreClassement("quantiteStock") .'>Quantite en Stock</option>
                    <option value="NomObjet" '. ChoisirSelectedFiltreClassement("NomObjet") .'>Nom objet</option>
                    <option value="prixUnitaire" '. ChoisirSelectedFiltreClassement("prixUnitaire") .'>Prix</option>
                    <option value="evaluation" '. ChoisirSelectedFiltreClassement("evaluation") .'>Evaluation</option>
                  
                  </select>
                  <select name="ordre" class="filtre_ordre">
                    <option value="ASC" '. ChoisirSelectedFiltreOrdre("ASC") .'>Acsendent</option>
                    <option value="DESC" '. ChoisirSelectedFiltreOrdre("DESC") .'>Descendent</option>
    
                  </select>
                  <input type="submit" value="Filtrer" class="filtre_submit">
                  </div>
              </form>';
      }
    
    function ChoisirSelectedFiltreClassement($optionValue) {
    if ($optionValue == $_POST['classement']){
            return "selected";
        }
    }

    function ChoisirSelectedFiltreOrdre($optionValue){
        if ($optionValue == $_POST['ordre']){
            return "selected";
        }
    }


    function ConfirmEmail($str)
    {
        if(preg_match("/^\S+@\S+\.\S+$/", $str) == 0 )
        {
            return true;
        }
        return false;
    }
    
    function ConfirmPassword($str)
    {
        if(preg_match("/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{5,}$/", $str) == 0 )
        {
            return true;
        }
        return false;
    }

    // // Sert à créer le footer des pages.
    function CreateFooter()
    {
        echo "<div class='footer'>
                <div class='footer_content'>
                    <div>Fait par : Kyle Delisi, Anthony Lamothe, Olivier Provost et Gabriel Lessard</div>
                </div>
              </div>";
    }

    // Permet de changer de page vers la page représentée par le string $goTo
    // ex de $goTo : $goTo = "index.php"
    function ChangePage($goTo){
        echo "<script> window.location.href='". $goTo ."'; </script>";
    }

    function ModifierProfilJoueur()
    {
        $info_joueur = GetInfoJoueurFromUsername($_SESSION["username"]);
        foreach($info_joueur as $info)
        {
            echo '
                <form method="post" class="inscription_form">
                <label for="aliasJoueur">Alias: </label> <input type="text" name="aliasJoueur" id="aliasJoueur" value="'.$info["alias"].'"> <br>
                <label for="mdpJoueur">Mot de passe: </label> <input type="password" name="mdpJoueur" id="mdpJoueur" value="'.$info["Mdp"].'"> <br>
                <label for="emailJoueur">Email: </label> <input type="email" name="emailJoueur" id="emailJoueur" value="'.$info["email"].'">
                <input type="submit" value="Modifier" class="submit_button" name="modifierJoueurInfo">
                </form>';
        }

    }

?> 