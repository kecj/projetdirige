<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

#region Connexion BD

// La section suivante ne doit pas être modifiée. Elle établit une connexion entre
// le site web PHP et la base de donnée. 
function GetPdo()
{
  $host = '127.0.0.1';
  $db = 'Knapsack';
  $user = 'root';
  $pass = 'projetdirige';
  $charset = 'utf8';

  $dsn = "mysql:host=$host;dbname=$db;charset=$charset";

  $options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
  ];

  try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    //echo "Connexion établie";
  } catch (\PDOException $e) {
    //echo "Connexion Échue";
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
  }
  return $pdo;
}

//Permet d'ajouter un Objet à la BD en étant Admin
function ajouterItem($nom, $desc, $type, $poids, $qte, $prix, $url)
{

  $pdo = GetPdo();
  try {
    $sql = "CALL AjoutItem(?,?,?,?,?,?,?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$nom, $desc, $type, $poids, $qte, $prix, $url]);
  } catch (Exception $e) {
    echo "Houston, we have a problem!";
    exit;
  }
}
//Sert à ajouter une colonne à la table Joueur de la BD
function Ajouter($username, $prenom, $nom, $password, $email)
{
  $pdo = GetPdo();
  try {
    $sql = "CALL AjouterUser(?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$username, $password, $nom, $prenom, $email]);
    //EnvoyerCourriel();
    //Va marcher éventuellement.
  } catch (Exception $e) {
    echo $username, $password, $nom, $prenom, $email;
    echo "Houston, we have a problem!";
    exit;
  }
}

function UpdateProfilJoueur($aliasJoueur, $mdpJoueur, $emailJoueur, $idPlayer)
  {
    try
    {
      $pdo = GetPdo();

      $sql = "UPDATE Joueurs set alias=?, Mdp=?, email=? WHERE IdJoueur=?";
      $stmt_update_profil = $pdo->prepare($sql);
      $stmt_update_profil->execute([$aliasJoueur,$mdpJoueur,$emailJoueur,$idPlayer]);
    }
    catch(Exception $ex)
    {
      echo $ex;
    }
  }

//Activation de compte lors de l'acceptation de courriel
function activerCompte($pseudo, $un)
{
  $pdo = GetPdo();
  try {
    $sql = "UPDATE inscrip SET actif=? WHERE pseudonyme=?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$un, $pseudo]);
  } catch (Exception $e) {
    echo "$pseudo";
    echo " Houston, we have a problem!";
    exit;
  }
}
#endregion

#region Connexion

// vérifie si le username ou le courriel existe déjà dans la basse de données.
// Vérifie si le mot de passe est associé à ce username.
function ConfirmerMotDePasse($username, $password)
{
  $pdo = GetPdo();
  $confirmedAuthentification = false;
  $stmt_info_joueur = $pdo->query("CALL ConfirmerMotDePasse('" . $username . "')");

  while ($row = $stmt_info_joueur->fetch()) {
    if ($row['mdp'] == $password) {
      $confirmedAuthentification = true;
    }
    echo $row['mdp'];
  }
  return $confirmedAuthentification;
}
//Va chercher le montant de capsule du Joueur connecté.
function GetCapsAmout($username)
{
  $pdo = GetPdo();
  $stmt_info_joueur = $pdo->query("CALL AfficherCapital('" . $username . "')");
  while ($row = $stmt_info_joueur->fetch()) {
    $capsAmout = $row['capital'];
  }
  return $capsAmout;
}
//Va chercher la dextérité du Joueur connecté.
function GetDex($username)
{
  $pdo = GetPdo();
  $stmt_info_joueur = $pdo->query("CALL GetDex('" . $username . "')");
  while ($row = $stmt_info_joueur->fetch()) {
    $dex = $row['dexterite'];
  }
  return $dex;
}
//Va chercher l'identifiant du Joueur connecté.
function GetIdJoueur($username)
{
  $pdo = GetPdo();
  $stmt_info_joueur = $pdo->query("CALL GetIdJoueur('" . $username . "')");
  while ($row = $stmt_info_joueur->fetch()) {
    $id = $row['IdJoueur'];
  }
  return $id;
}
#endregion

#region Sac à dos
//Affiche le sac à dos du Joueur connecté.
function AfficherSacAdos($IdJoueur)
{
  $pdo = getPdo();

  try {
    $sql = "CALL AfficherSacAdos(?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$IdJoueur]);
    while ($row = $stmt->fetch()) {
      echo '<a href="details.php?IdObjet=' . $row["IdObjet"] . '" class="case">
            <div class="case_header">
                <div class="produit_poids">
                    Poids : ' . $row["lePoids"] . '
                </div>
                <div class="produit_stockage">
                    Quantité : ' . $row["quantite"] . '
                </div>
            </div>
            <div class="produit_image">
                <img src="./images/' . $row["urlImage"] . '" alt="' . $row["idObjet"] . '">
            </div>
            <div class="produit_nom">
                ' . $row["NomObjet"] . '
            </div>
        </a>';
    }
  } catch (Exception $e) {
    echo "Houston, we have a problem!";
    exit;
  }
}

#endregion

#region Panier
//Ajoute un Objet selectionné par le Joueur à la table de Panier
function AjoutItemPanier($IdJoueur, $IdObjet, $QteObjet)
{
  $pdo = GetPdo();
  try {
    $sql = "CALL AjoutItemPanierCheck($IdObjet,$IdJoueur)";
    //$sql = "SELECT COUNT(*) FROM LePanier WHERE IdObjet = ? AND IdJoueur = ? ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$IdObjet, $IdJoueur]);
    if ($stmt->fetchColumn() <= 0) {
      $sql = "CALL AjoutItemPanier(?,?,?)";
      $stmtInsert = $pdo->prepare($sql);
      $stmtInsert->execute([$IdJoueur, $IdObjet, $QteObjet]);
    } else {
      $sql = "CALL AjoutItemPanierUpdate(?,?,?)";
      $stmtUpdate = $pdo->prepare($sql);
      $stmtUpdate->execute([$QteObjet, $IdObjet, $IdJoueur]);
    }
  } catch (Exception $e) {
    exit;
  }
}
//Affiche le panier du Joueur connecté.
function AfficherPanier($IdJoueur)
  {
   global $coutTotalPanier;
   $coutTotalPanier=0;
    $pdo= getPdo();
    try {
      $sql="CALL AfficherPanier(?)";
      $stmt= $pdo->prepare($sql);
      $stmt->execute([$IdJoueur]);
      while($row = $stmt->fetch())
      {
           $coutTotalPanier+= $row["quantite"] * $row["prixUnitaire"];
        echo ' 
        <form action="panier.php" method="post">
          <div class="item_panier">
            <div class="item_panier_image">  
              <img max-width: 150px; src="./images/'.$row["urlImage"].'" alt="'.$row["IdObjet"].'">
            </div>
            <div class="item_panier_nom"> 
              <div> '.$row["NomObjet"].' </div>
            </div>
            <div class="item_panier_prix">
            <div>
            <div>'.$row["quantite"] * $row["prixUnitaire"].'</div>
              <img src="./images/caps.png" alt="caps" class="caps">
            </div>
            </div>
            <div class="item_panier_quantite"> 
              <div> 
                <input type="number" name="qte" id="qte" min="1" max="10"  value="'.$row["quantite"].'">  
                <input type="hidden" name="idItem" id="idItem" value="'.$row["IdObjet"].'">  
                <input type="submit" name="Modifier" value="Modifier" class="item_panier_retirer">
              </div> 
            <form action="panier.php" method="post"> 
              </div>
              <div class="item_panier_poids"> 
                <div>
                  <div>'.$row["Poids"] * $row["quantite"].' </div>
                  <div class="item_panier_poids_suffixe"> lbs</div>
                </div>
              </div> 
              <input type="hidden" name="idItem" id="idItem"  value="'.$row["IdObjet"].'">  
              <input type="submit" name="Retirer" value="Retirer" class="item_panier_retirer">
            </form>
          </div>
        </form>';
      }
      
    echo "
    <div class='panier_prix_total'>Total : $coutTotalPanier 
      <img src='./images/caps.png' alt='caps' class='caps'>
    </div>";

    echo "  <form action='panier.php' method='post'> 
              <input type='submit' name='Payer' value='Payer' class='panier_submit'>
              <input type='hidden' name='totalPanier' id='totalPanier' value='$coutTotalPanier'> 
            </form>";
    } 
    catch (Exception $e) {
      echo "
      <script>
        console.log('Erreur lors de l'affichage du panier')
      </script>";
      exit;
    }
  }

//Retire un objet selectionné du panier du Joueur connecté.
function RetirerItemPanier($IdObjet, $IdJoueur)
{
  $pdo = getPdo();

  try {
    $sql = "CALL RetirerItemPanier(?,?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$IdJoueur, $IdObjet]);
  } catch (Exception $e) {
    echo "Houston, we have a problem!";
    exit;
  }
}
//Modifie la quantité voulue d'un objet selectionné du panier du Joueur connecté.
function ModifierQuantiteItemPanier($idObjet, $quantiteVoulu, $idJoueur)
{
  $pdo = GetPdo();
  try {
    $sql = "CALL ModifierQuantiteItemPanier(?,?,?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$idObjet, $quantiteVoulu, $idJoueur]);
  } catch (Exception $ex) {
    echo $ex;
  }
}
//Efface le panier en mettant à jour le magasin, le sac à dos, le poids du Joueur, sa dextérité et son solde.
function PayerPanier($IdJoueur, $TotalPanier)
{
  $pdo = getPdo();
  try {
    $sql = "CALL PayerPanierGetJoueur(?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$IdJoueur]);

    while ($row = $stmt->fetch()) {
      UpdateQteAchatPanier($row["quantite"], $row["IdObjet"]);
      UpdateSacAdosJoueur($row["IdObjet"], $row["quantite"], $row["Poids"]);
    }
    $poidsMax = GetPoidsTotalJoueur($IdJoueur);

    UpdateDexteriteJoueur($poidsMax);

    UpdateSoldeJoueur($IdJoueur, $TotalPanier);

    SupprimerPanier($IdJoueur);
  } catch (Exception $e) {
    echo "Houston, we have a problem!";
    exit;
  }
}
//Met à jour la dextérité du Joueur.
function UpdateDexteriteJoueur($poidsTotalJoueur)
{
  $idJoueur = $_SESSION["idJoueur"];
  try {
    $pdo = GetPdo();

    $sql = "CALL 	UpdateDexteriteJoueur(?,?)";
    $stmt_update_dexterite = $pdo->prepare($sql);
    $stmt_update_dexterite->execute([$poidsTotalJoueur, $idJoueur]);
  } catch (Exception $ex) {
    echo $ex;
  }
}
//Va chercher le poids total des objets du Joueur.
function GetPoidsTotalJoueur($idJoueurConnecter)
{
  try {
    $pdo = GetPdo();
    $sql = "CALL GetPoidsTotalJoueur(?)";
    $stmt_get_poids = $pdo->prepare($sql);
    $stmt_get_poids->execute([$idJoueurConnecter]);

    return $stmt_get_poids->fetchColumn();
  } catch (Exception $ex) {
    echo $ex;
  }
}
//Met à jour les quantités dans le magasins du panier qui a été payé.
function UpdateQteAchatPanier($QteAcheter, $IdObjet)
{
  $pdo = GetPdo();
  try {
    $sql = "CALL UpdateQteAchatPanier(?,?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$QteAcheter, $IdObjet]);
  } catch (Exception $e) {
    echo " Houston, we have a problem!";
    exit;
  }
}
//Efface le contenue du panier pour le Joueur connecté.
function SupprimerPanier($IdJoueur)
{
  $pdo = getPdo();

  try {
    $sql = "CALL SupprimerPanier(?)";
    $stmt = $pdo->prepare($sql);

    $stmt->execute([$IdJoueur]);
  } catch (Exception $e) {
    echo "Houston, we have a problem!";
    exit;
  }
}
//Met à jour le solde du Joueur connecté après le paiement du panier.
function UpdateSoldeJoueur($joueurConnecter, $TotalPanier)
{

  try {
    $pdo = GetPdo();
    $sql = "CALL UpdateSoldeJoueur(?,?)";
    $stmtm_update_solde = $pdo->prepare($sql);
    $stmtm_update_solde->execute([$joueurConnecter, $TotalPanier]);
    $_SESSION["caps"] = GetCapsAmout($_SESSION["username"]);
  } catch (Exception $ex) {
    echo $ex;
  }
}
//Met à jour le sac à dos du Joueur connecté après le paiement du panier.
function UpdateSacAdosJoueur($IdObjet, $quantite, $poidsObjet)
{
  $idJoueur = $_SESSION["idJoueur"];
  $pdo = GetPdo();


  try {
    $sql = "SELECT COUNT(*) FROM LeSacaDos WHERE IdObjet = ? AND IdJoueur = ? ";
    //CALL UpdateSacAdosJoueurCheck(?,?)
    //Cette fonction donne le même résultat que la query SQL mais pour une raison inconnue elle ne marche pas.
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$IdObjet, $idJoueur]);


    if ($stmt->fetchColumn() <= 0) {
      $sql = "CALL UpdateSacAdosJoueurInsert($IdObjet, $idJoueur, $quantite, $poidsObjet)";
      $stmtInsertSac = $pdo->prepare($sql);
      $stmtInsertSac->execute();
    } else {

      $sql = "CALL UpdateSacAdosJoueurUpdate(?,?,?)";
      $stmtUpdateSac = $pdo->prepare($sql);
      $stmtUpdateSac->execute([$quantite, $IdObjet, $_SESSION["idJoueur"]]);
    }
  } catch (Exception $ex) {
    echo $ex;
  }
}

#endregion

#region Autres

//Cherche tout les joueurs afin de les affichers
function AfficherJoueurs()
{
  try {
    $pdo = GetPdo();

    $sql = "SELECT * FROM Joueurs WHERE idType = 0";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    while ($row = $stmt->fetch()) {
      echo '
            <div>
                Nom: '. $row["alias"] . ' Capitale reçus: ' . $row["capitalRecu"] . ' Caps '.(($row["demandeCaps"]?"Demande de Capsules envoyé":"")).'
            </div>
        </a>';
    }
  } catch (Exception $ex) {
    echo $ex;
  }
}
function AfficherJoueursInventaire()
{
  try {
    $pdo = GetPdo();

    $sql = "SELECT * FROM Joueurs WHERE idType = 0";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    while ($row = $stmt->fetch()) {
      echo '
            <div>
                Nom: '. $row["alias"] . '
            </div>
        </a>';
    }
  } catch (Exception $ex) {
    echo $ex;
  }
}
// Permet d'afficher toutes les cases d'items dynamiquement
function AfficherTousItems()
{
  $type = 'NomObjet';
  $typeDordre = 'Desc';
  if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $type = $_POST['classement'];
    $typeDordre = $_POST['ordre'];
  }
  try {
    $pdo = GetPdo();
    if($type == 'evaluation')
    {
      $stmt_info_item = $pdo -> query(' SELECT o.IdObjet,o.NomObjet,o.quantiteStock,o.urlImage,o.prixUnitaire,o.Poids,AVG(c.evaluation)
       from Object o LEFT OUTER JOIN Commentaires c on o.IdObjet = c.idObjet
       GROUP BY o.NomObjet ORDER BY AVG(c.evaluation) '.$typeDordre.'');
    }

    else
    {
      $stmt_info_item =  $pdo->query('SELECT * from Object ORDER BY ' . $type . ' ' . $typeDordre . ' ');
    }
    //CALL afficherTousItems(?,?);
    //Même chose que l'autre CALL disfonctionnel celle-ci ne prend semble pas se valider si appeler en procédure stocké.
    while ($row = $stmt_info_item->fetch()) {
      $nombremoyenne = GetAVGRatingItem($row["IdObjet"]);
      $nombremoyenne = round($nombremoyenne);
      echo '<a href="details.php?IdObjet=' . $row["IdObjet"] . '" class="case">
            <div class="case_header">
                <div class="produit_poids">
                    ' . $row["Poids"] . ' lbs
                </div>
                <div class="produit_stockage">
                    ' . $row["quantiteStock"] . ' EN STOCK
                </div>
            </div>
            <div class="produit_image">
                <img src="./images/' . $row["urlImage"] . '" alt="' . $row["idObjet"] . '">
            </div>
            <div class="produit_nom">
                ' . $row["NomObjet"] . '
            </div>
            <div class="produit_etoiles">
                '.AfficherEtoileMoyenne($nombremoyenne).'
            </div>
            <div class="produit_prix">
               ' . $row["prixUnitaire"] . ' <img src="./images/caps.png" alt="caps" class="caps">
            </div>
        </a>';
    }
  } catch (Exception $ex) {
    echo "$ex";
  }
}
//Cherche les informations d'un objet donné.
function getInfoFromItemId($itemId)
{
  $pdo = getPdo();

  try {
    $pdo = getPdo();
    $sql = "SELECT * FROM Object WHERE IdObjet=?";
    // CALL getInfoFromItemId(?)
    // le code ne reconnait pas la procédure, Code d'erreur 500.
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$itemId]);
    return $stmt;
  } catch (Exception $e) {
    echo "Houston, we have a problem!";
    exit;
  }
}

//Get Item dans Détails
function GetItem($idItem)
{
  $pdo = GetPdo();
  $stmt_info_Item = $pdo->query("CALL GetItem(?)");
  $stmt = $pdo->prepare($stmt_info_Item);
  $stmt->execute([$idItem]);
  return $stmt->fetchColumn();
}
//Chercher le nombre d'item attribué à un objet donné.
function GetNumberByRating($idItem)
{
  $arr = [];
  try {
    for ($i = 1; $i <= 5; $i++) {
      $pdo = GetPdo();
      $sql = "CALL GetNumbersByRating(?,$i)";
      $stmt = $pdo->prepare($sql);
      $stmt->execute([$idItem]);
      $arr[] = $stmt->fetchColumn();
    }
    return $arr;
  } catch (Exception $ex) {
    echo $ex;
  }
}
//prend la moyenne des évaluations attribué à un objet donné.
function GetAVGRatingItem($idItem)
{
  try {
    $pdo = GetPdo();
    $sql = " CALL GetAVGRatingItem(?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$idItem]);
    $row = $stmt->fetch(PDO::FETCH_NUM);
    return $row[0];
  } catch (Exception $ex) {
    echo $ex;
  }
}
//Retourne la moyenne des commentaires d'un objet donné.
function GetAVGRatingItemIdComment($idItem, $idComment)
{
  try {
    $pdo = GetPdo();
    $sql = "CALL GetAVGRatingItemIdComment(?,?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$idItem, $idComment]);
    $row = $stmt->fetch(PDO::FETCH_NUM);
    return $row[0];
  } catch (Exception $ex) {
    echo $ex;
  }
}
//Retourne le nombre d'évaluation qu'un objet donné à reçus.
function GetNumberOfRatingItem($idItem)
{
  try {
    $pdo = GetPdo();
    $sql = " CALL GetNumberOfRatingItem(?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$idItem]);
    $row = $stmt->fetch(PDO::FETCH_NUM);
    return $row[0];
  } catch (Exception $ex) {
    echo $ex;
  }
}
//Affiche la moyenne d'évaluation d'un objet donné.
function AfficherEtoileMoyenne($moyenneEtoile)
{
  $show = '';
  if ($moyenneEtoile == 0) {
    $show = "Pas evalue </span>";
  } else if ($moyenneEtoile == 1) {
    $show = "<span class='fa fa-star'></span>";
  } else if ($moyenneEtoile == 2) {
    $show = "<span class='fa fa-star'></span><span class='fa fa-star'></span> ";
  } else if ($moyenneEtoile == 3) {
    $show = "<span class='fa fa-star'></span><span class='fa fa-star'></span><span class='fa fa-star'></span>";
  } else if ($moyenneEtoile == 4) {
    $show = "<span class='fa fa-star'></span><span class='fa fa-star'></span><span class='fa fa-star'></span><span class='fa fa-star'></span>";
  } else if ($moyenneEtoile == 5) {
    $show = "<span class='fa fa-star'></span><span class='fa fa-star'></span><span class='fa fa-star'></span><span class='fa fa-star'></span><span class='fa fa-star'></span>";
  }
  return $show;
}


//Affiche les évaluations de la page détails complete.
function AfficherDetails($nbrAchat)
{
  $info_item = getInfoFromItemId($_GET["IdObjet"]);
  $nombremoyenne = GetAVGRatingItem($_GET["IdObjet"]);
  $nombremoyenne = round($nombremoyenne);
  $totalEvaluation = GetNumberOfRatingItem($_GET["IdObjet"]);
  $a = GetNumberByRating($_GET["IdObjet"]);
  foreach ($info_item as $info) {
    if (!empty($_SESSION)) {
      $_SESSION["itemId"] = $info['IdObjet'];
    }


    echo '
  
      <form method="post" class="details">
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
      
      <div>Rating : ' . AfficherEtoileMoyenne($nombremoyenne) . ' / ' . $totalEvaluation . ' evaluation</div><div>1 <span class="fa fa-star"></span> :' . $a[0] . '</div><div>2 <span class="fa fa-star"></span> :' . $a[1] . '</div><div>3 <span class="fa fa-star"></span>' . $a[2] . '</div><div>|   4 <span class="fa fa-star"></span>' . $a[3] . '</div><div>|  5 <span class="fa fa-star"></span>' . $a[4] . '</div>
        <img class="image_details" src="./images/' . $info['urlImage'] . '" alt="Image">
        <div class="details_informations">
          <h2 class="details_nom">' . $info['NomObjet'] . '</h2>
          <div class="details_description">' . $info['description'] . '</div>
          <div>Poids : ' . $info['Poids'] . '</div>
          <div>En Stock : ' . $info['quantiteStock'] . '</div>
          <div class="details_prix">Prix : ' . $info['prixUnitaire'] . ' <img src="./images/caps.png" alt="caps" class="caps"></div>
          <div>
            <input type="number" name="qte" min="1" max="' . $info["quantiteStock"] . '" class="details_nombre_input" value="' . $nbrAchat . '">
            <input type="submit" class="submit_button" value="Ajouter">
          </div>
        </div>
      </form>';
  }
}
//Vérifie qu'un Joueur commente un objet.
function checkJoueurCommenter($IdObjet, $idJoueur)
{
  try {
    $pdo = GetPdo();
    $sql = " CALL checkJoueurCommenter(?,?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$IdObjet, $idJoueur]);
    $row = $stmt->fetch(PDO::FETCH_NUM);

    return $row[0];
  } catch (Exception $ex) {
    echo $ex;
  }
}
//Supprime un commentaire donné.
function deleteCommentaires($id)
{
  try {
    $pdo = GetPdo();
    $sql = "CALL deleteCommentaires(?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
  } catch (Exception $e) {
    echo " Houston, we have a problem!";
    exit;
  }
}
//Affiche les commentaires.
function ShowComments($idItem)
{

  try {
    $numeroCommentaire = 0;
    $pdo = GetPdo();
    $sql = "CALL ShowComments(?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$idItem]);
    while ($row = $stmt->fetch()) {

      $numeroCommentaire += 1;
      echo '
        <P>#' . $numeroCommentaire . '<p>
        <div>Rating : ' . AfficherEtoileMoyenne($row["evaluation"]) . ' 
        <div>Nom: ' . GetAliasJoueur($row["idJoueur"]) . '
        <div>Commentaire: ' . $row["leCommentaire"] . '</div>
        ';
      if ($row["idJoueur"] == $_SESSION["idJoueur"]|| CheckIfAdmin($_SESSION["idJoueur"])) {
        echo '<form method="post" class="details">
          <input type="hidden" id="' . $row["idCommentaire"] . '" name="retirer" value="' . $row["idCommentaire"] . '"/>
                <input type="submit" class="submit_button" value="Retirer">
            
          </form>';
      }
    }
  } catch (Exception $ex) {
    echo $ex;
  }
}
//Ajoute un commentaire donné à un objet donné également.
function AddCommentItem($comment, $idItem, $nombreEtoile)
{
  $idJoueur = $_SESSION["idJoueur"];

  try {
    $pdo = GetPdo();
    $sql = "CALL AddCommentItem(?,?,?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$idItem, $idJoueur, $comment]);
    if (isset($nombreEtoile))
      AddRatingItem($nombreEtoile, $idItem);
  } catch (Exception $ex) {
    echo $ex;
  }
}
//Ajoute une évalutation à un objet donné.
function AddRatingItem($nombreEtoile, $idItem)
{
  $idJoueur = $_SESSION["idJoueur"];
  try {
    $pdo = GetPdo();
    $sql = "CALL AddRatingItem(?,?,?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$nombreEtoile, $idItem, $idJoueur]);
  } catch (Exception $ex) {
    echo $ex;
  }
}
//Vérifier si le Joueur à acheté l'objet donné afin de pouvoir y laisser un commentaire.
function CheckSiItemAcheter($idItem)
{
  $idJoueur = $_SESSION["idJoueur"];
  try {
    $pdo = GetPdo();
    $sql = "CALL CheckSiItemAcheter(?,?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$idItem, $idJoueur]);
    $row = $stmt->fetch(PDO::FETCH_NUM);

    return $row[0];
  } catch (Exception $ex) {
    echo $ex;
  }
}
//Met à jour le commentaire du Joueur connecté à l'objet donné.
function UpdateCommentaire($nombreEtoile, $comment, $idItem)
{
  $idJoueur = $_SESSION["idJoueur"];
  try {
    $pdo = GetPdo();
    $sql = "CALL UpdateCommentaire(?,?,?,?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$nombreEtoile, $comment, $idItem, $idJoueur]);
  } catch (Exception $ex) {
    echo $ex;
  }
}
//Active la demande de capsule du joueur
function AskForCaps()
{
  $idJoueur = $_SESSION["idJoueur"];
  try {
    $pdo = GetPdo();
    $sql = "UPDATE Joueurs SET demandeCaps = TRUE WHERE IdJoueur = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$idJoueur]);
  } catch (Exception $ex) {
    echo $ex;
  }
}
//Vérifie si l'identifié est Administrateur.
function CheckIfAdmin($IdJoueur)
{
  $pdo = GetPdo();
  try {
    $sql = "CALL CheckIfAdmin(?) ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$IdJoueur]);
    return $stmt->fetchColumn();
  } catch (Exception $ex) {
    echo $ex;
  }
}
//Le formulaire de modification d'objet pour l'administration.
function FormAdminModifierItem()
{
  $info_item_admin = getInfoFromItemId($_GET["IdObjet"]);
  foreach ($info_item_admin as $info) {
    echo '<form method="post" class="inscription_form" action="modifieritemadmin.php?IdObjet=' . $_GET["IdObjet"] . '">
              <label for="nomObjetAdmin">Nom de item: </label>  <input type="text" name="nomObjetAdmin" id="nomObjetAdmin" value="' . $info['NomObjet'] . '"> <br>

              <label for="descriptionObjetAdmin">Description: </label> <input type="text" name="descriptionObjetAdmin" id="descriptionObjetAdmin" value="' . $info['description'] . '"> <br>

              <div class="ajout_type_container">
              <label for="desc_img">Type</label>
              <select name="typeObjetAdmin" id="color">
                <option value = 1>Armure</option>
                <option value = 2>Arme</option>
                <option value = 3>Nourriture</option>
                <option value = 4>Drogue</option>
                <option value = 5>Munition</option>
                <option value = 6>Autre</option>
              </select>
            </div>

              <label for="poidsObjetAdmin">Poids: </label> <input type="number" min="0" max="20" name="poidsObjetAdmin" id="poidsObjetAdmin" value="' . $info['Poids'] . '"> <br>

              <label for="quantiteStockAdmin">Quantité en stock: </label> <input type="number" min="0" max="100" name="quantiteStockAdmin" id="quantiteStockAdmin" value="' . $info['quantiteStock'] . '"> <br>

              <label for="prixUnitaireAdmin">Prix unitaire: </label> <input type="number" min="0" max="100" name="prixUnitaireAdmin" id="prixUnitaireAdmin" value="' . $info['prixUnitaire'] . '"> <br>

              <label for="urlImageAdmin">Url: </label> <input type="texte" name="urlImageAdmin" id="urlImageAdmin" value="' . $info['urlImage'] . '"> <br>

              <input type="submit" name="modifierItemAdmin" value="Modifier" class="submit_button">
             </form> ';
  }
}
//Cherche l'alias du Joueur connecté.
function GetAliasJoueur($id)
{

  try {

    $pdo = GetPdo();
    $sql = "CALL GetAliasJoueur(?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
    return $stmt->fetchColumn();
  } catch (Exception $ex) {
    echo $ex;
  }
}
//Retire le commentaire sélectionné du Joueur connecté.
function RemoveCommentPlayer($idItem, $idCommentaire)
{
  $idJoueur = $_SESSION("idJoueur");
  try {
    $pdo = GetPdo();
    $sql = "CALL RemoveCommentPlayer(?,?,?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$idItem, $idCommentaire, $idJoueur]);
    return $stmt->fetchColumn();
  } catch (Exception $ex) {
    echo $ex;
  }
}
//Retire le commentaire selectionné en tant qu'administrateur.
function RemoveCommentAdmin($idItem, $idCommentaire)
{
  try {
    $pdo = GetPdo();
    $sql = "CALL RemoveCommentAdmin(?,?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$idItem, $idCommentaire]);
    return $stmt->fetchColumn();
  } catch (Exception $ex) {
    echo $ex;
  }
}
//Vérifie si l'alias du Joueur est déjà dans la BD.
function CheckUsernameExist($aliasJoueur)
{
  try {
    $pdo = GetPdo();

    $sql = "CALL CheckUsernameExist(?)";
    $stmt_username = $pdo->prepare($sql);
    $stmt_username->execute([$aliasJoueur]);

    foreach ($stmt_username as $row) {
      if ($row["alias"] == $aliasJoueur) {
        return true;
      } else {
        return false;
      }
    }
  } catch (Exception $ex) {
    echo $ex;
  }
}
//Met à jour les Caps d'un Joueur donné par l'administrateur(version modifié non finale).
function UpdateCapitalJoueurV2($username, $addedCapital)
{
  try {
    $pdo = GetPdo();
    $sql = "CALL UpdateCapitalJoueurV2($addedCapital,?)";
    $stmtm_update_capital = $pdo->prepare($sql);
    $stmtm_update_capital->execute([$username]);
  } catch (Exception $ex) {
    echo $ex;
  }
}
//Affiche les items spécifiquement pour l'administrateur.
function AfficherItemsAdmin()
{
  $type = 'NomObjet';
  $typeDordre = 'Desc';
  if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $type = $_POST['classement'];
    $typeDordre = $_POST['ordre'];
  }
  try {
    $pdo = GetPdo();

    $stmt_info_item =  $pdo->query('SELECT * from Object ORDER BY ' . $type . ' ' . $typeDordre . ' ');
    //CALL AfficherItemsAdmin(?,?);
    //Procédure stockée fonctionnelle mais à causer des problèmes aléatoires sans savoir pourquoi(en procéssus de correction).

    while ($row = $stmt_info_item->fetch()) {
      echo '<a href="detailsadmin.php?IdObjet=' . $row["IdObjet"] . '" class="case">
        <div class="case_header">
        <div class="produit_poids">
            ' . $row["Poids"] . ' lbs
        </div>
        <div class="produit_stockage">
            ' . $row["quantiteStock"] . ' EN STOCK
        </div>
    </div>
    <div class="produit_image">
        <img src="./images/' . $row["urlImage"] . '" alt="' . $row["idObjet"] . '">
    </div>
    <div class="produit_nom">
        ' . $row["NomObjet"] . '
    </div>
    <div class="produit_etoiles">
        * * * * *
    </div>
    <div class="produit_prix">
       ' . $row["prixUnitaire"] . ' <img src="./images/caps.png" alt="caps" class="caps">
    </div>
        </a>';
    }
  } catch (Exception $ex) {
    echo "$ex";
  }
}
//Le formulaire d'augmentation de Caps de Joueur par l'administrateur.
function FormUpdateCapitalAdmin()
{
  echo '<form method="post" action="modifiercapitaladmin.php" class="inscription_form">
                <label for="aliasJoueurAdmin">Alias du joueur: </label> <input type="text" name="aliasJoueurAdmin" id="aliasJoueurAdmin"> <br>
                <label for="captialJoueurAdmin">Nouveau capital: </label> <input type="number" min="1" max="200" name="capitalJoueurAdmin" id="capitalJoueurAdmin"> <br>
                <input type="submit" name="modifierCapitalAdmin" value="Modifier" class="submit_button">
            </form>';
}
//Le formulaire d'accessibilité d'inventaire pour l'administrateur.
function FormGetInventoryAdmin()
{
  echo '<form method="post" action="adminInventory.php" class="inscription_form">
                <label for="aliasJoueurAdmin">Alias du joueur: </label> <input type="text" name="aliasJoueurAdmin" id="aliasJoueurAdmin"> <br>
                <input type="submit" name="adminInventory" value="Modifier" class="submit_button">
            </form>';
}
//Modification d'un objet par l'admin(non finale, version à dévelloper).
function UpdateObjetAdmin($nomObj, $descriptionObj, $typeObj, $poidsObj, $quantiteEnStock, $prixUni, $idObjet)
{

  try {
    $pdo = GetPdo();
    $sql = "UPDATE Object SET NomObjet=?, description=?, typeObjet=?, Poids=?, quantiteStock=?, prixUnitaire=? WHERE IdObjet=?";
    //CALL UpdateObjetAdmin(?,?,?,?,?,?,?,?);
    $stmt_update_obj_admin = $pdo->prepare($sql);
    $stmt_update_obj_admin->execute([$nomObj, $descriptionObj, $typeObj, $poidsObj, $quantiteEnStock, $prixUni, $idObjet]);
  } catch (Exception $ex) {
    echo $ex;
  }
}
//Vérification d'occtroiement maximale de Caps au Joueur donné.
function CheckIfCreditReceivedMaxedOut($alias)
{
  try {
    $pdo = GetPdo();

    $sql = "CALL CheckIfCreditReceivedMaxedOut(?)";
    $stmt_credit_max = $pdo->prepare($sql);

    $stmt_credit_max->execute([$alias]);

    foreach ($stmt_credit_max as $row) {
      if ($row["capitalRecu"] == 600) {
        return true;
      } else {
        return false;
      }
    }
  } catch (Exception $ex) {
    echo $ex;
  }
}
//Mis à jour de capital(Caps) du Joueur donné ainsi que du capital(Caps) reçus de la part de l'administration.
function UpdateReceivedCreditAdmin($receivedCapital, $aliasUser)
{
  try {
    $pdo = GetPdo();

    $sql = "CALL UpdateReceivedCreditAdmin(?,?)";
    $stmt_update_receive_capital = $pdo->prepare($sql);
    $stmt_update_receive_capital->execute([$receivedCapital, $aliasUser]);
  } catch (Exception $ex) {
    echo $ex;
  }
}
//Affiche la page de détails spécifique à l'administrateur.
function AfficherDetailsAdmin()
{

  $info_obj_admin = getInfoFromItemId($_GET["IdObjet"]);

  foreach ($info_obj_admin as $row) {
    if (!empty($_SESSION)) {
      $_SESSION["itemId"] = $row['IdObjet'];
    }
    echo '
        <form method="post" class="details">
          <img class="image_details" src="./images/' . $row['urlImage'] . '" alt="Image">
          <div class="details_informations">
            <h2 class="details_nom">' . $row['NomObjet'] . '</h2>
            <div class="details_description">' . $row['description'] . '</div>
            <div>Poids : ' . $row['Poids'] . '</div>
            '.(($row['typeObjet']==1)?'<div>Type : Armure</div>':"").'
            '.(($row['typeObjet']==2)?'<div>Type : Arme</div>':"").'
            '.(($row['typeObjet']==3)?'<div>Type : Nourriture</div>':"").'
            '.(($row['typeObjet']==4)?'<div>Type : Drogue</div>':"").'
            '.(($row['typeObjet']==5)?'<div>Type : Munition</div>':"").'
            <div>En Stock : ' . $row['quantiteStock'] . '</div>
            <div class="details_prix">Prix : ' . $row['prixUnitaire'] . ' <img src="./images/caps.png" alt="caps" class="caps"></div>
            <div>
              <button name="goToModifierItem" class="submit_button">Modifier</button>
            </div>
          </div>
        </form>';
  }
}


//Mise à jour du profil du Joueur.(non finale, à améliorer).
function GetInfoJoueurFromUsername($joueurName)
  {
    $pdo= GetPdo();

    try {
      $pdo=GetPdo();
      $sql="SELECT * FROM Joueurs WHERE alias=?";
      $stmt= $pdo->prepare($sql);
      $stmt->execute([$joueurName]);
      return $stmt;
      } catch (Exception $e) {
      echo "Houston, we have a problem!";
      exit;
      }
  }

    function getNbrQuestion()
    {
      try {
        $pdo=GetPdo();
        $sql="SELECT COUNT(*) FROM QuestionEnigma";
        $stmt= $pdo->prepare($sql);
        $stmt->execute();
        $i = $stmt->fetch();
        return array_shift(array_values($i));
        } catch (Exception $e) {
        echo "Houston, we have a problem!";
        exit;
        }
    }
    function getNbrQuestionByLevel($level)
    {
      try {
        $pdo = GetPdo();
        $sql = "SELECT COUNT(*) FROM QuestionEnigma WHERE difficulté = $level";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $i = $stmt->fetch();
        return array_shift(array_values($i));
        } catch (Exception $e) {
        echo "Houston, we have a problem!";
        exit;
        }
    }
    function pickQuestionByLevel($levelQuestion)
    {
      try {
        $pdo = GetPdo();
        $sql = "SELECT * FROM QuestionEnigma WHERE difficulté = $levelQuestion ORDER BY RAND() LIMIT 1;";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        foreach($stmt as $question)
        {

        }
        } catch (Exception $e) {
        echo "Houston, we have a problem!";
        exit;
        }
    }
    function pickQuestion()
    {
      try {
        $pdo = GetPdo();
        $sql = "SELECT * FROM QuestionEnigma ORDER BY RAND() LIMIT 1;";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        foreach($stmt as $question)
        {

        }
        } catch (Exception $e) {
        echo "Houston, we have a problem!";
        exit;
        }
    }
    function getReponses($idQuestion)
    {
      try {
        $pdo = GetPdo();
        $sql = "SELECT * FROM RéponseEnigma WHERE idQuestion = $idQuestion;";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        } catch (Exception $e) {
        echo "Houston, we have a problem!";
        exit;
        }
    }
    function CheckReponse($idReponse)
    {
      try {
        $pdo = GetPdo();
        $sql = "SELECT bonneRéponse FROM RéponseEnigma WHERE idRéponse = $idReponse;";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetch();
        } catch (Exception $e) {
        echo "Houston, we have a problem!";
        exit;
        }
    }
