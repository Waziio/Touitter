<?php
include_once("db.php");
session_start();

// on vérifie si l'utilisateur est bien connecté
if (isset($_SESSION['connexion'])) {

    if (isset($_GET['posts'])) {
        try {
            // on récupère tout les posts de l'utilisateur
            $db = new PDO("$server:host=$host;dbname=$base", $user, $pass);
            $sql = "SELECT id_post, msg_post, id_user, DATE_FORMAT(date_post, '%d/%m/%Y') AS 'date', TIME_FORMAT(heure_post, '%H:%i') AS 'heure' FROM Post WHERE id_user = '" . $_SESSION['identifiant'] .  "' ORDER BY date_post DESC, heure_post DESC;";
            $st = $db->query($sql);
            $lignes = $st->fetchAll();
        } catch (PDOException $err) {
            echo "Erreur : " . $err->getMessage() . "<br />";
            die();
        }
    }
} else {
    // Si pas connecté, on redirige vers page de connexion
    header('Location:connexion.php');
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Touitter - Fil Personnel</title>
    <link rel="shortcut icon" href="images/logoTouitter.svg" />
    <link rel="stylesheet" href="css/filPerso.css">
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
        <input type="button" value="Poster" id="poster" onclick="popupPost()">
    </header>

    <div id="popupPost" class="wb-overlay modal-content overlay-def wb-popup-mid overlay-bg">
        <form id="formPost" method="POST" action="createPost.php">
            <div id="headerCreatePost">
                <h3 id="pubPost">Publier un post privé</h3>
                <img id="croix" src="images/croix.png" onclick="closePopup()">
            </div>
            <textarea name="msgPost" id="msgPost" cols="30" rows="10" placeholder="Écrivez le contenu de votre post ici..." required></textarea>
            <div id="divPublic">
                <label id="labelPublic" for="public">Publier également sur le fil principal</label>
                <input type="checkbox" name="public" id="public">
            </div>
            <input type="submit" name="posterPerso" id="sendPost" value="Poster">
        </form>
    </div>

    <img id="fleche" onclick="back()" src="images/fleche.png">

    <div id="listeTouitte">
        <h2 id=titlePerso>Fil Personnel</h2>
        <?php
        // Si il y'a des posts a afficher
        if (!empty($lignes)) {
            $dateDuJour = date("d/m/Y");
            $dateHier = date('d/m/Y', strtotime("yesterday"));
            // on affiche les posts
            foreach ($lignes as $key => $value) {
                // Texte affiché correspondant a la date du jour : Aujourd'hui/hier/date
                $textDate = ($dateDuJour == $value['date'] ? "Aujourd'hui à" : ($dateHier == $value['date'] ? "Hier à" : ($value['date'])));
                // on récupère le nombre de commentaires du post
                $reqNbComm = "SELECT COUNT(id_comm) AS 'NbComm' FROM Commentaire WHERE id_post='" . $value['id_post'] . "';";
                $execNbComm = $db->query($reqNbComm);
                $fetchNbComm = $execNbComm->fetch();
                $nbComm = $fetchNbComm['NbComm'];

                // on recupère la note moyenne du post
                $requeteMoy = "SELECT AVG(note) AS 'Moyenne' FROM Commentaire WHERE id_post='" . $value['id_post'] . "';";
                $execMoy = $db->query($requeteMoy);
                $fetchMoy = $execMoy->fetch();
                $noteMoyenne = $fetchMoy != "" ? round($fetchMoy['Moyenne'], 0) : "";

                // On affiche le nombre d'étoiles en fonction de la note moy du post
                switch ($noteMoyenne) {
                    case 1:
                        echo "<div class='touitte' onclick='clickPost(this)' id='" . $value['id_post'] . "'>
                        <div class='contenu'>
                            <p class='nom'>" . $value['id_user'] . "</p>
                            <p class='date'>" . $textDate . " " . $value['heure'] . "</p>
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
                            <p class='contenuTouitte'>" . $value['msg_post'] . "</p>
                        </div>
                        <hr class='separateurTouitte'>
                    </div>";

                        break;

                    case 2:
                        echo "<div class='touitte' onclick='clickPost(this)' id='" . $value['id_post'] . "'>
                        <div class='contenu'>
                            <p class='nom'>" . $value['id_user'] . "</p>
                            <p class='date'>" . $textDate . " " . $value['heure'] . "</p>
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
                            <p class='contenuTouitte'>" . $value['msg_post'] . "</p>
                        </div>
                        <hr class='separateurTouitte'>
                    </div>";

                        break;

                    case 3:
                        echo "<div class='touitte' onclick='clickPost(this)' id='" . $value['id_post'] . "'>
                        <div class='contenu'>
                            <p class='nom'>" . $value['id_user'] . "</p>
                            <p class='date'>" . $textDate . " " . $value['heure'] . "</p>
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
                            <p class='contenuTouitte'>" . $value['msg_post'] . "</p>
                        </div>
                        <hr class='separateurTouitte'>
                    </div>";

                        break;

                    case 4:
                        echo "<div class='touitte' onclick='clickPost(this)' id='" . $value['id_post'] . "'>
                        <div class='contenu'>
                            <p class='nom'>" . $value['id_user'] . "</p>
                            <p class='date'>" . $textDate . " " . $value['heure'] . "</p>
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
                            <p class='contenuTouitte'>" . $value['msg_post'] . "</p>
                        </div>
                        <hr class='separateurTouitte'>
                    </div>";

                        break;

                    case 5:
                        echo "<div class='touitte' onclick='clickPost(this)' id='" . $value['id_post'] . "'>
                        <div class='contenu'>
                            <p class='nom'>" . $value['id_user'] . "</p>
                            <p class='date'>" . $textDate . " " . $value['heure'] . "</p>
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
                            <p class='contenuTouitte'>" . $value['msg_post'] . "</p>
                        </div>
                        <hr class='separateurTouitte'>
                    </div>";

                        break;

                    default:
                        echo "<div class='touitte' onclick='clickPost(this)' id='" . $value['id_post'] . "'>
                        <div class='contenu'>
                            <p class='nom'>" . $value['id_user'] . "</p>
                            <p class='date'>" . $textDate . " " . $value['heure'] . "</p>
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
                            <p class='contenuTouitte'>" . $value['msg_post'] . "</p>
                        </div>
                        <hr class='separateurTouitte'>
                    </div>";

                        break;
                }
            }
        }
        else {
            // Si il n'y a aucun post
            echo "<p id='error'>Il n'y a aucun post à afficher ...</p>";
        }
        ?>
    </div>
</body>

<script src="js/accueil.js"></script>

</html>