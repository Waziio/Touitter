<?php
include_once("db.php");
session_start();
// Si le user est pas déjà connecté
if (isset($_SESSION['connexion']) == false) {
    if (isset($_POST['btn'])) {
        try {
            $db = new PDO("$server:host=$host;dbname=$base", $user, $pass);
            // On hash le mot de passe
            $pwdHash = password_hash($_POST['pwd'], PASSWORD_DEFAULT);

            // On insere le user dans la bdd
            $sql = "INSERT INTO Utilisateur VALUES (:identifiant, :pwdHash, :nom, :prenom, :date, :mail);";
            $st = $db->prepare($sql);
            $st->execute([
                "identifiant" => $_POST['identifiant'],
                "pwdHash" => $pwdHash,
                "nom" => $_POST['nom'],
                "prenom" => $_POST['prenom'],
                "date" => $_POST['date'],
                "mail" => $_POST['mail']

            ]);
            // Redirection vers accueil
            header('Location:connexion.php');
        } catch (PDOException $err) {
            echo "Erreur : " . $err->getMessage() . "<br />";
            die();
        }
    }
} else {
    // Si le user est déjà connecté, redirection vers accueil
    header('Location:accueil.php?posts=true');
}

/**
 * Récupèrer un id_post aléatoire* 
 * @param {string} Nom du serveur de la base de donnée
 * @param {string} Host
 * @param {string} Nom de la base de données
 * @param {string} Identifiant de connexion
 * @param {string} Mot de passe
 * @return {int} Retourne l'id d'un post séléctionné aléatoirement  
 */
function getRandomIdPost($server, $host,$base, $user, $pass)
	{
        $bdd=new PDO("$server:host=$host;dbname=$base", $user, $pass);
        $sql = "SELECT id_post FROM Post";
        $arguments = array();
        $reponse = $bdd->prepare($sql);
        $reponse->execute($arguments);
        $allid= $reponse->fetchAll();
        if (sizeof($allid) == 0) {
            return null;
        }
        else {
            $randomid = array_rand($allid);
            $randomid = $allid[$randomid]['id_post'];
            return $randomid;    
        }
	}

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/signup.css">
    <title>Touitter - Inscription</title>
    <link rel="shortcut icon" href="images/logoTouitter.svg" />
    <link href="https://fonts.googleapis.com/css2?family=Comfortaa:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Condensed:wght@700&display=swap" rel="stylesheet">
</head>

<body id="pageConnexion">
    <div id="seConnecter">
        <div id="logo">
            <img src="images/logoTouitter.svg" alt="Logo touitter" id="imgLogoTouitter">
            <p id="texteSeConnecter">Créer un compte</p>
            <hr id="separateurConnecterIdentifiant">
        </div>
        <form id="informationConnexion" method="POST" action="">
            <div class="information">
                <p class="texteInput">Identifiant</p>
                <div class="inputValue">
                    <img src="images/identifiant.svg" alt="Mail" class="imgInput">
                    <hr class="separateurInput">
                    <input name="identifiant" type="text" class="input" placeholder="DarkSasukeDu49">
                </div>
            </div>
            <div class="information">
                <p class="texteInput">Mot de passe</p>
                <div class="inputValue">
                    <img src="images/password.svg" alt="Mail" class="imgInput">
                    <hr class="separateurInput">
                    <input name="pwd" type="password" class="input" placeholder="unmotdepasse">
                </div>
            </div>
            <div class="information">
                <p class="texteInput">Prénom</p>
                <div class="inputValue">
                    <img src="images/prenom.svg" alt="Mot de passe" class="imgInput">
                    <hr class="separateurInput">
                    <input name="prenom" type="text" class="input" placeholder="John">
                </div>
            </div>
            <div class="information">
                <p class="texteInput">Nom</p>
                <div class="inputValue">
                    <img src="images/prenom.svg" alt="Mail" class="imgInput">
                    <hr class="separateurInput">
                    <input name="nom" type="text" class="input" placeholder="Doe">
                </div>
            </div>
            <div class="information">
                <p class="texteInput">Mail</p>
                <div class="inputValue">
                    <img src="images/mail.svg" alt="Mail" class="imgInput">
                    <hr class="separateurInput">
                    <input name="mail" type="email" class="input" placeholder="exemple@gmail.com">
                </div>
            </div>
            <div class="information">
                <p class="texteInput">Date de naissance</p>
                <div class="inputValue">
                    <img src="images/dateNaissance.svg" alt="Mail" class="imgInput">
                    <hr class="separateurInput">
                    <input name="date" type="date" class="input" placeholder="12/06/2001">
                </div>
            </div>
            <input id="btn" type="submit" name="btn" value="Créer">
        </form>
        <div id="autreConnexion">
            <div id="separateurOu">
                <!--<div class="separateurHorizontal"></div>-->
                <hr class="separateurHorizontal">
                <p id="ou">Ou</p>
                <hr class="separateurHorizontal">
                <!--<div class="separateurHorizontal"></div>-->
            </div>
            <div id="logoAutreConnexion">
                <img src="images/logoFacebook.svg" alt="facebook" class="logoAutreConnexion">
                <img src="images/logoGoogle.svg" alt="google" class="logoAutreConnexion">
            </div>
            <p id="creerUnCompte">Déjà un compte ? <a class="surligneJaune" href="connexion.php">Se connecter</a></p>
        </div>
    </div>
    <div id="touitter">
        <p id="touitterTexte">TOUITTER</p>
        <?php
            $dateDuJour = date("d/m/Y");
            $dateHier = date('d/m/Y', strtotime("yesterday"));
            //on recupere la date du jour et celle d'hier
            
            $idHasard = getRandomIdPost($server, $host,$base, $user, $pass);

            if ($idHasard != null) {
                $db=new PDO("$server:host=$host;dbname=$base", $user, $pass);
                $requete = "SELECT id_post, msg_post, id_user, DATE_FORMAT(date_post, '%d/%m/%Y') AS 'date_post', TIME_FORMAT(heure_post, '%H:%i') AS 'heure_post' FROM Post WHERE id_post=".$idHasard." ;";
                $exec = $db->query($requete);
                $value = $exec->fetch();
                $textDate = ($dateDuJour == $value['date_post'] ? "Aujourd'hui à" : ($dateHier == $value['date_post'] ? "Hier à" : ($value['date_post'])));
                //on recupere le touitte genere aleatoirement
                
                $reqNbComm = "SELECT COUNT(id_comm) AS 'NbComm' FROM Commentaire WHERE id_post='" . $value['id_post'] . "';";
                $execNbComm = $db->query($reqNbComm);
                $fetchNbComm = $execNbComm->fetch();
                $nbComm = $fetchNbComm['NbComm'];
                //on recupere le nombre de commentaire du touitte
                
                $str =  "<div class='touitte' onclick='clickPost(this)' id='" . $value['id_post'] . "'>
                <div class='contenu'>
                    <p class='nom'>" . $value['id_user'] . "</p>
                    <p class='date'>" . $textDate . " " . $value['heure_post'] . "</p>
                    <div class='etoiles'>";

                $requeteMoy = "SELECT AVG(note) AS 'Moyenne' FROM Commentaire WHERE id_post='" . $value['id_post'] . "';";
                $execMoy = $db->query($requeteMoy);
                $fetchMoy = $execMoy->fetch();
                $noteMoyenne = $fetchMoy != "" ? round($fetchMoy['Moyenne'], 0) : "";
                //on recupere la moyenne des notes du touitte sur 5
                
                $pattern = "etoileVerte.svg";
                if ($noteMoyenne <= 2) {
                    $pattern = "etoileRouge.svg";
                }
                else if ($noteMoyenne <= 4) {
                    $pattern = "etoileJaune.svg";
                }
                //on change la couleur des etoiles en fonction de la note moyenne

                $str .= str_repeat("<img src='images/".$pattern."' alt='etoile positive' class='etoile'>", $noteMoyenne);
                $str .= str_repeat("<img src='images/etoileVide.svg' alt='etoile positive' class='etoile'>", 5-$noteMoyenne);
            
                $str .= "
                    </div>
                    <div class='commentaire'>
                        <img src='images/commentaire.svg' alt='bulle commentaire' class='bulleCommentaire'>
                        <p class='nombreCommentaire'>" . $nbComm . "</p>
                    </div>
                    <img src='images/photoProfil.png' alt='Photo profil' class='photoProfil'>
                    <p class='contenuTouitte'>" . $value['msg_post'] . "</p>
                </div>
                <hr class='separateurTouitte'>
            </div>";
            echo $str;
        }
        ?>
    </div>
</body>

</html>