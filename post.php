<?php
include_once("db.php");
session_start();

// Si le user est bien connecté
if (isset($_SESSION['connexion'])) {

    if (isset($_GET['id'])) {
        try {
            $db = new PDO("$server:host=$host;dbname=$base", $user, $pass);
            // on récupère le post dont l'id est passé en requete GET
            $sql = "SELECT id_post, msg_post, id_user, DATE_FORMAT(date_post, '%d/%m/%Y') AS 'date', TIME_FORMAT(heure_post, '%H:%i') AS 'heure' FROM Post WHERE id_post = '" . $_GET['id'] . "' ORDER BY date DESC, heure DESC;";
            $st = $db->query($sql);
            $result = $st->fetch();

            // on récupère les infos du post que l'on va afficher
            $idPost = $result['id_post'];
            $idUser = $result['id_user'];
            $msg = $result['msg_post'];
            $date = $result['date'];
            $heure = $result['heure'];
            $_SESSION['postId'] = $_GET['id'];
        } catch (PDOException $err) {
            echo "Erreur : " . $e->getMessage() . "<br />";
            die();
        }
    }
} else {
    // Si le user est pas connecté, redirection vers page de connexion
    header('Location:connexion.php');
}



?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Touitter - Post de </title>
    <link rel="shortcut icon" href="images/logoTouitter.svg" />
    <link rel="stylesheet" href="css/post.css">
    <link href="https://fonts.googleapis.com/css2?family=Comfortaa:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>

<body>
    <header>
        <div id="containerProfil" class="containerProfil" onclick="showProfil()">
            <div id="profilContainer">
                <div id="photoNomContainer">
                    <img src="images/photoProfil.png" alt="photo de profil" id="photoProfilAccueil">
                    <p id="nomUtilisateur"><?php echo ($_SESSION['identifiant']) ?></p>
                </div>
            </div>
            <div id="headerProfil">
                <div id="menuDeroulant">
                    <ul id="listeLien">
                        <li class="notLast"><a href="accueil.php?posts=true">Accueil</a></li>
                        <li class="notLast"><a href="myAccount.php">Mon Compte</a></li>
                        <li class="notLast"><a href="filPerso.php?posts=true">Mon Fil Personnel</a></li>
                        <li id="lastLi"><a href="logout.php">Déconnexion</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <img src="images/poster.svg" alt="Poster" class="imgHeader" id="ecrireTweetTelephone" onclick="popupPost()">

        <div id="contenaireRechercher" class="containerProfil">
            <input type="text" name="q" onkeyup="showHint(this.value)" id="inputRechercher" class="inputRechercher" placeholder="Rechercher">
            <ul id="listUsers" class="listUsers"></ul>
        </div>
        <img src="images/rechercher.svg" alt="Rechercher" class="imgHeader" id="rechercherImage" onclick="activateRechercher(this.value)">

        <img src="images/logoTouitter.svg" alt="Logo touitter" id="logoTouitter" onclick="backToAccueil()">
        <input type="button" value="Commenter" id="poster" onclick="popupPost()">
    </header>

    <div id="popupPost" class="wb-overlay modal-content overlay-def wb-popup-mid overlay-bg">
        <form id="formPost" method="POST" action="createPost.php">
            <div id="headerCreatePost">
                <h3 id="pubPost">Publier un commentaire</h3>
                <img id="croix" src="images/croix.png" onclick="closePopup()">
            </div>
            <textarea name="msgPost" id="msgPost" cols="30" rows="10" placeholder="Écrivez le contenu de votre commentaire ici..." required></textarea>
            <div id="cases">
                <label for="1">1</label>
                <input class="caseNote" id="1" type="radio" name="caseNote" value="1">
                <label for="2">2</label>
                <input class="caseNote" id="2" type="radio" name="caseNote" value="2">
                <label for="3">3</label>
                <input class="caseNote" id="3" type="radio" name="caseNote" value="3">
                <label for="4">4</label>
                <input class="caseNote" id="4" type="radio" name="caseNote" value="4">
                <label for="5">5</label>
                <input class="caseNote" id="5" type="radio" name="caseNote" value="5">
            </div>
            <input type="hidden" name="postId" value="<?php $_GET['id'] ?>">
            <input type="submit" name="posterComm" id="sendPost" value="Poster">
        </form>
    </div>
    <div id="gridPostComm">
        <img id="fleche" onclick="back()" src="images/fleche.png">
        <?php

        $dateDuJour = date("d/m/Y");
        $dateHier = date('d/m/Y', strtotime("yesterday"));
        // Texte affiché correspondant a la date du jour : Aujourd'hui/hier/date
        $textDate = ($dateDuJour == $date ? "Aujourd'hui à" : ($dateHier == $date ? "Hier à" : ($date)));
        // on récupère le nombre de commentaires du post
        $reqNbComm = "SELECT COUNT(id_comm) AS 'NbComm' FROM Commentaire WHERE id_post='" . $idPost . "';";
        $execNbComm = $db->query($reqNbComm);
        $fetchNbComm = $execNbComm->fetch();
        $nbComm = $fetchNbComm['NbComm'];

        // on recupère la note moyenne du post
        $requeteMoy = "SELECT AVG(note) AS 'Moyenne' FROM Commentaire WHERE id_post='" . $idPost . "';";
        $execMoy = $db->query($requeteMoy);
        $fetchMoy = $execMoy->fetch();
        $noteMoyenne = $fetchMoy != "" ? round($fetchMoy['Moyenne'], 0) : "";

        // On affiche le post et ses etoiles en fonction de sa note moyenne
        switch ($noteMoyenne) {

            case 1:
                echo "<div class='touitteGros' id='" . $idPost . "'>
                        <div class='contenu'>
                            <p class='nom'>" . $idUser . "</p>
                            <p class='date'>" . $textDate . " " . $heure . "</p>
                            <div class='etoiles'>
                                <img src='images/etoileRouge.svg' alt='etoile positive' class='etoile'>
                                <img src='images/etoileVide.svg' alt='etoile positive' class='etoile'>
                                <img src='images/etoileVide.svg' alt='etoile positive' class='etoile'>
                                <img src='images/etoileVide.svg' alt='etoile negative' class='etoile'>
                                <img src='images/etoileVide.svg' alt='etoile negative' class='etoile'>
                            </div>
                            <div class='commentaire'>
                                <img src='images/commentaire.svg' alt='bulle commentaire' class='bulleCommentaire'>
                                <p class='nombreCommentaire'>" . $nbComm . "</p>
                            </div>
                            <img src='images/photoProfil.png' alt='Photo profil' class='photoProfil'>
                            <p class='contenuTouitte'>" . $msg . "</p>
                        </div>
                        <hr class='separateurTouitte'>
                    </div>";
                break;

            case 2:
                echo "<div class='touitteGros' id='" . $idPost . "'>
                                    <div class='contenu'>
                                        <p class='nom'>" . $idUser . "</p>
                                        <p class='date'>" . $textDate . " " . $heure . "</p>
                                        <div class='etoiles'>
                                            <img src='images/etoileRouge.svg' alt='etoile positive' class='etoile'>
                                            <img src='images/etoileRouge.svg' alt='etoile positive' class='etoile'>
                                            <img src='images/etoileVide.svg' alt='etoile positive' class='etoile'>
                                            <img src='images/etoileVide.svg' alt='etoile negative' class='etoile'>
                                            <img src='images/etoileVide.svg' alt='etoile negative' class='etoile'>
                                        </div>
                                        <div class='commentaire'>
                                            <img src='images/commentaire.svg' alt='bulle commentaire' class='bulleCommentaire'>
                                            <p class='nombreCommentaire'>" . $nbComm . "</p>
                                        </div>
                                        <img src='images/photoProfil.png' alt='Photo profil' class='photoProfil'>
                                        <p class='contenuTouitte'>" . $msg . "</p>
                                    </div>
                                    <hr class='separateurTouitte'>
                                </div>";
                break;

            case 3:
                echo "<div class='touitteGros' id='" . $idPost . "'>
                                        <div class='contenu'>
                                            <p class='nom'>" . $idUser . "</p>
                                            <p class='date'>" . $textDate . " " . $heure . "</p>
                                            <div class='etoiles'>
                                                <img src='images/etoileJaune.svg' alt='etoile positive' class='etoile'>
                                                <img src='images/etoileJaune.svg' alt='etoile positive' class='etoile'>
                                                <img src='images/etoileJaune.svg' alt='etoile positive' class='etoile'>
                                                <img src='images/etoileVide.svg' alt='etoile negative' class='etoile'>
                                                <img src='images/etoileVide.svg' alt='etoile negative' class='etoile'>
                                            </div>
                                            <div class='commentaire'>
                                                <img src='images/commentaire.svg' alt='bulle commentaire' class='bulleCommentaire'>
                                                <p class='nombreCommentaire'>" . $nbComm . "</p>
                                            </div>
                                            <img src='images/photoProfil.png' alt='Photo profil' class='photoProfil'>
                                            <p class='contenuTouitte'>" . $msg . "</p>
                                        </div>
                                        <hr class='separateurTouitte'>
                                    </div>";
                break;

            case 4:
                echo "<div class='touitteGros' id='" . $idPost . "'>
                                            <div class='contenu'>
                                                <p class='nom'>" . $idUser . "</p>
                                                <p class='date'>" . $textDate . " " . $heure . "</p>
                                                <div class='etoiles'>
                                                    <img src='images/etoileVerte.svg' alt='etoile positive' class='etoile'>
                                                    <img src='images/etoileVerte.svg' alt='etoile positive' class='etoile'>
                                                    <img src='images/etoileVerte.svg' alt='etoile positive' class='etoile'>
                                                    <img src='images/etoileVerte.svg' alt='etoile negative' class='etoile'>
                                                    <img src='images/etoileVide.svg' alt='etoile negative' class='etoile'>
                                                </div>
                                                <div class='commentaire'>
                                                    <img src='images/commentaire.svg' alt='bulle commentaire' class='bulleCommentaire'>
                                                    <p class='nombreCommentaire'>" . $nbComm . "</p>
                                                </div>
                                                <img src='images/photoProfil.png' alt='Photo profil' class='photoProfil'>
                                                <p class='contenuTouitte'>" . $msg . "</p>
                                            </div>
                                            <hr class='separateurTouitte'>
                                        </div>";
                break;

            case 5:
                echo "<div class='touitteGros' id='" . $idPost . "'>
                                                <div class='contenu'>
                                                    <p class='nom'>" . $idUser . "</p>
                                                    <p class='date'>" . $textDate . " " . $heure . "</p>
                                                    <div class='etoiles'>
                                                        <img src='images/etoileVerte.svg' alt='etoile positive' class='etoile'>
                                                        <img src='images/etoileVerte.svg' alt='etoile positive' class='etoile'>
                                                        <img src='images/etoileVerte.svg' alt='etoile positive' class='etoile'>
                                                        <img src='images/etoileVerte.svg' alt='etoile negative' class='etoile'>
                                                        <img src='images/etoileVerte.svg' alt='etoile negative' class='etoile'>
                                                    </div>
                                                    <div class='commentaire'>
                                                        <img src='images/commentaire.svg' alt='bulle commentaire' class='bulleCommentaire'>
                                                        <p class='nombreCommentaire'>" . $nbComm . "</p>
                                                    </div>
                                                    <img src='images/photoProfil.png' alt='Photo profil' class='photoProfil'>
                                                    <p class='contenuTouitte'>" . $msg . "</p>
                                                </div>
                                                <hr class='separateurTouitte'>
                                            </div>";
                break;

            default:
                echo "<div class='touitteGros' id='" . $idPost . "'>
                                                    <div class='contenu'>
                                                        <p class='nom'>" . $idUser . "</p>
                                                        <p class='date'>" . $textDate . " " . $heure . "</p>
                                                        <div class='etoiles'>
                                                            <img src='images/etoileVide.svg' alt='etoile positive' class='etoile'>
                                                            <img src='images/etoileVide.svg' alt='etoile positive' class='etoile'>
                                                            <img src='images/etoileVide.svg' alt='etoile positive' class='etoile'>
                                                            <img src='images/etoileVide.svg' alt='etoile negative' class='etoile'>
                                                            <img src='images/etoileVide.svg' alt='etoile negative' class='etoile'>
                                                        </div>
                                                        <div class='commentaire'>
                                                            <img src='images/commentaire.svg' alt='bulle commentaire' class='bulleCommentaire'>
                                                            <p class='nombreCommentaire'>" . $nbComm . "</p>
                                                        </div>
                                                        <img src='images/photoProfil.png' alt='Photo profil' class='photoProfil'>
                                                        <p class='contenuTouitte'>" . $msg . "</p>
                                                    </div>
                                                    <hr class='separateurTouitte'>
                                                </div>";
                break;
        }

        ?>

        <div id="listeTouitte">
            <h2>Commentaires</h2>

            <?php
            // on affiche les commentaire du post
            $sql = "SELECT id_user, msg_comm, note, DATE_FORMAT(date_comm, '%d/%m/%Y') AS 'date', TIME_FORMAT(heure_comm, '%H:%i') AS 'heure' FROM Commentaire WHERE id_post = '" . $_GET['id'] . "' ORDER BY date_comm DESC, heure_comm DESC;";
            $st = $db->query($sql);
            $lignes = $st->fetchAll();

            if (!empty($lignes)) {
                $dateDuJour = date("d/m/Y");
                $dateHier = date('d/m/Y', strtotime("yesterday"));
                // on affiche les commentaires
                foreach ($lignes as $key => $value) {
                    // Texte affiché correspondant a la date du jour : Aujourd'hui/hier/date
                    $textDate = ($dateDuJour == $value['date'] ? "Aujourd'hui à" : ($dateHier == $value['date'] ? "Hier à" : ($value['date'])));

                    // On affiche les commentaires et leur etoiles en fonction de sa note
                    switch ($value['note']) {

                        case 1:
                            echo "<div class='touitte'>
                            <div class='petitContenu'>
                                <p class='nom'>" . $value['id_user'] . "</p>
                                <p class='date'>" . $textDate . " " . $value['heure'] . "</p>
                                <div class='petitesEtoiles'>
                                    <img src='images/etoileRouge.svg' alt='etoile positive' class='etoile'>
                                    <img src='images/etoileVide.svg' alt='etoile positive' class='etoile'>
                                    <img src='images/etoileVide.svg' alt='etoile positive' class='etoile'>
                                    <img src='images/etoileVide.svg' alt='etoile negative' class='etoile'>
                                    <img src='images/etoileVide.svg' alt='etoile negative' class='etoile'>
                                </div>
                                <div class='commentaire'>
                                </div>
                                <img src='images/photoProfil.png' alt='Photo profil' class='photoProfil'>
                                <p class='contenuTouitte'>" . $value['msg_comm'] . "</p>
                            </div>
                            <hr class='separateurTouitte'>
                        </div>";
                            break;

                        case 2:
                            echo "<div class='touitte'>
                                <div class='petitContenu'>
                                    <p class='nom'>" . $value['id_user'] . "</p>
                                    <p class='date'>" . $textDate . " " . $value['heure'] . "</p>
                                    <div class='petitesEtoiles'>
                                        <img src='images/etoileRouge.svg' alt='etoile positive' class='etoile'>
                                        <img src='images/etoileRouge.svg' alt='etoile positive' class='etoile'>
                                        <img src='images/etoileVide.svg' alt='etoile positive' class='etoile'>
                                        <img src='images/etoileVide.svg' alt='etoile negative' class='etoile'>
                                        <img src='images/etoileVide.svg' alt='etoile negative' class='etoile'>
                                    </div>
                                    <div class='commentaire'>
                                    </div>
                                    <img src='images/photoProfil.png' alt='Photo profil' class='photoProfil'>
                                    <p class='contenuTouitte'>" . $value['msg_comm'] . "</p>
                                </div>
                                <hr class='separateurTouitte'>
                            </div>";
                            break;

                        case 3:
                            echo "<div class='touitte'>
                                    <div class='petitContenu'>
                                        <p class='nom'>" . $value['id_user'] . "</p>
                                        <p class='date'>" . $textDate . " " . $value['heure'] . "</p>
                                        <div class='petitesEtoiles'>
                                            <img src='images/etoileJaune.svg' alt='etoile positive' class='etoile'>
                                            <img src='images/etoileJaune.svg' alt='etoile positive' class='etoile'>
                                            <img src='images/etoileJaune.svg' alt='etoile positive' class='etoile'>
                                            <img src='images/etoileVide.svg' alt='etoile negative' class='etoile'>
                                            <img src='images/etoileVide.svg' alt='etoile negative' class='etoile'>
                                        </div>
                                        <div class='commentaire'>
                                        </div>
                                        <img src='images/photoProfil.png' alt='Photo profil' class='photoProfil'>
                                        <p class='contenuTouitte'>" . $value['msg_comm'] . "</p>
                                    </div>
                                    <hr class='separateurTouitte'>
                                </div>";
                            break;

                        case 4:
                            echo "<div class='touitte'>
                                        <div class='petitContenu'>
                                            <p class='nom'>" . $value['id_user'] . "</p>
                                            <p class='date'>" . $textDate . " " . $value['heure'] . "</p>
                                            <div class='petitesEtoiles'>
                                                <img src='images/etoileVerte.svg' alt='etoile positive' class='etoile'>
                                                <img src='images/etoileVerte.svg' alt='etoile positive' class='etoile'>
                                                <img src='images/etoileVerte.svg' alt='etoile positive' class='etoile'>
                                                <img src='images/etoileVerte.svg' alt='etoile negative' class='etoile'>
                                                <img src='images/etoileVide.svg' alt='etoile negative' class='etoile'>
                                            </div>
                                            <div class='commentaire'>
                                            </div>
                                            <img src='images/photoProfil.png' alt='Photo profil' class='photoProfil'>
                                            <p class='contenuTouitte'>" . $value['msg_comm'] . "</p>
                                        </div>
                                        <hr class='separateurTouitte'>
                                    </div>";
                            break;

                        case 5:
                            echo "<div class='touitte'>
                                        <div class='petitContenu'>
                                            <p class='nom'>" . $value['id_user'] . "</p>
                                            <p class='date'>" . $textDate . " " . $value['heure'] . "</p>
                                            <div class='petitesEtoiles'>
                                                <img src='images/etoileVerte.svg' alt='etoile positive' class='etoile'>
                                                <img src='images/etoileVerte.svg' alt='etoile positive' class='etoile'>
                                                <img src='images/etoileVerte.svg' alt='etoile positive' class='etoile'>
                                                <img src='images/etoileVerte.svg' alt='etoile negative' class='etoile'>
                                                <img src='images/etoileVerte.svg' alt='etoile negative' class='etoile'>
                                            </div>
                                            <div class='commentaire'>
                                            </div>
                                            <img src='images/photoProfil.png' alt='Photo profil' class='photoProfil'>
                                            <p class='contenuTouitte'>" . $value['msg_comm'] . "</p>
                                        </div>
                                        <hr class='separateurTouitte'>
                                    </div>";
                            break;
                    }
                }
            }
            else {
                // Si il n'ya aucun commentaire à afficher
                echo "<p id='error'>Il n'y a aucun commentaire à afficher ...</p>";
            }
            ?>
        </div>
    </div>


</body>

<script src="js/accueil.js"></script>

</html>