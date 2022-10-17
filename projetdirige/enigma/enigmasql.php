<?php
    #region Connexion BD

    // La section suivante ne doit pas être modifiée. Elle établit une connexion entre
    // le site web PHP et la base de donnée. 
    function GetPdo() {
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
    #endregion

    function AfficherQuestionAlléatoire(){

        try {
            $pdo = GetPdo();
            $sql = "SELECT * FROM QuestionEnigma ORDER BY RAND() LIMIT 1;";
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            while($row = $stmt->fetch())
            {
                echo '
                <div class="conteneur_énigme">
                    <form>
                        <div class="énigme_titre">
                            Question #'. $row['idQuestion'] .'
                        </div>
                        <div class="énigme_énoncé_question">
                            ' . $row['enoncé'] . '
                        </div>
                        <div class="zone_réponse">
                            <div>
                                ' . AfficherChoixRéponses($row['idQuestion']) . '
                            </div>
                        </div>
                        <input type="submit" name="répondre" class="submit_button">
                    </form>
                </div>';
            }
            } catch (Exception $e) {
            echo "Houston, we have a problem!";
            exit;
            }
    }

    function AfficherQuestionSpécifique($difficulté){
        try {
            $pdo = GetPdo();
            $sql = "SELECT * FROM QuestionEnigma WHERE difficulté = $difficulté ORDER BY RAND() LIMIT 1;";
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            while($row = $stmt->fetch())
            {
                echo '
                    <div class="conteneur_énigme">
                        <form>
                            <div class="énigme_titre">
                                Question #'. $row['idQuestion'] .'
                            </div>
                            <div class="énigme_énoncé_question">
                                ' . $row['enoncé'] . '
                            </div>
                            <div class="zone_réponse">
                                <div>
                                    ' . AfficherChoixRéponses($row['idQuestion']) . '
                                </div>
                            </div>
                            <input type="submit" name="répondre" class="submit_button">
                        </form>
                    </div>';
            }
        } catch (Exception $e) {
            echo "Houston, we have a problem!";
            exit;
        }
    }

    function AfficherChoixRéponses($idQuestion) {
        try {
            $pdo = GetPdo();
            $sql = "SELECT * FROM RéponseEnigma WHERE idQuestion = $idQuestion;";
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $réponses = "";
            while($row = $stmt->fetch())
            {
                $réponses .= ' 
                <div>
                    <input type="radio" name="réponse" value="'.$row['idRéponse'].'">
                    <label for="'.$row['idRéponse'].'" class="diff_label_label">'. $row['enoncéRéponse'] .'</label>
                </div>';
            }
            return $réponses;

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
        while($row = $stmt->fetch()){
            return $row['bonneRéponse'];
        }
        } catch (Exception $e) {
        echo "Houston, we have a problem!";
        exit;
        }
    }

    function AfficherMessage($id){
        echo '  
            <script>
                window.onload = () => { document.getElementById("'.$id.'").style.display = "block"; }
            </script>';
    }

    function UpdateSoldeJoueur($difficulté)
    {
        $_SESSION["montantAjouté"] = 600 + ($difficulté - 1) * 200;
        try
        {
            $pdo = GetPdo();
            $sql = "UPDATE Joueurs set capital = capital + ? where IdJoueur = ?";
            $stmtm_update_solde = $pdo->prepare($sql);  
            $stmtm_update_solde->execute([$_SESSION["montantAjouté"], $_SESSION["idJoueur"]]);
            $_SESSION["caps"] = GetCapsAmout($_SESSION["username"]);

            $sql2 = "UPDATE Joueurs set scoreEnigma = scoreEnigma + ? where IdJoueur = ?";
            $stmtm_update_scoreEnigma = $pdo->prepare($sql2);  
            $stmtm_update_scoreEnigma->execute([$difficulté, $_SESSION["idJoueur"]]);
        }
        catch(Exception $ex)
        {
            echo $ex;
        }
    }

    function GetCapsAmout($username)
    {
      $pdo = GetPdo();
      $stmt_info_joueur = $pdo->query("SELECT capital from Joueurs where alias = '" . $username . "'");
      while($row = $stmt_info_joueur->fetch())
      {
        $capsAmout = $row['capital'];
      }
      return $capsAmout;
    }

?>