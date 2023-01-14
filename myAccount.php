<?php
include_once("db.php");
session_start();
// variable pour affichage de "info modifié avec succès"
$saved = false;
// Si le user est bien connecté
if (isset($_SESSION['connexion'])) {
    if (isset($_POST['save'])) {
        $db = new PDO("$server:host=$host;dbname=$base", $user, $pass);
        $hashMdp = password_hash($_POST['mdp'], PASSWORD_DEFAULT);
        // On met à jour les informations de l'utilisateur
        $sql = "UPDATE Utilisateur SET nom_user='" . $_POST['nom'] . "', prenom_user='" . $_POST['prenom'] . "', mail_user='" . $_POST['mail'] . "', pwd_user='" . $hashMdp . "' WHERE id_user='" . $_SESSION['identifiant'] . "';";
        // on met a jour les variables de session
        $_SESSION['nom'] = $_POST['nom'];
        $_SESSION['prenom'] = $_POST['prenom'];
        $_SESSION['mail'] = $_POST['mail'];

        $st = $db->query($sql);
        // On affiche le message "infos modifiés avec succès"
        $saved = true;
    }
} else {
    // Si le user est pas connecté, on redirige vers la page connexion
    header('Location:connexion.php');
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Touitter - Mon compte</title>
    <link rel="shortcut icon" href="images/logoTouitter.svg" />
    <link rel="stylesheet" href="css/myAccount.css">
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

        <div id="contenaireRechercher" class="containerProfil">
            <input type="text" name="q" onkeyup="showHint(this.value)" id="inputRechercher" class="inputRechercher" placeholder="Rechercher">
            <ul id="listUsers" class="listUsers"></ul>
        </div>
        <img src="images/rechercher.svg" alt="Rechercher" class="imgHeader" id="rechercherImage" onclick="activateRechercherAccueil(this.value)">

        <img src="images/logoTouitter.svg" alt="Logo touitter" id="logoTouitter" onclick="backToAccueil()">
        <input type="button" value="Poster" id="poster" onclick="popupPost()">
    </header>

    <img id="fleche" onclick="back()" src="images/fleche.png">

    <form method="POST">
        <div id="blocMesInfos">
            <h2>Vos informations</h2>
            <div id="formCompte">
                <div id="blocPrenom">
                    <label id="labelPre" for="prenom">Prénom</label>
                    <input id="inputPre" class="inpTxt" type="text" value="<?php echo ($_SESSION['prenom']); ?>" name="prenom" required>
                </div>

                <div id="blocNom">
                    <label id="labelNom" for="nom">Nom</label>
                    <input id="inputNom" class="inpTxt" type="text" value="<?php echo ($_SESSION['nom']); ?>" name="nom" required>
                </div>

                <div id="blocMail">
                    <label id="labelMail" for="mail">Adresse mail</label>
                    <input id="inputMail" class="inpTxt" type="mail" value="<?php echo ($_SESSION['mail']); ?>" name="mail" required>
                </div>

                <div id="blocMdp">
                    <label id="labelMdp" for="mdp">Mot de passe</label>
                    <input id="inpMdp" placeholder="Mot de passe" class="inpTxt" type="password" name="mdp" required>
                </div>
            </div>
        </div>

        <input id="save" type="submit" value="Enregistrer" name="save">


    </form>

    <?php
    // On affiche le message que les modifs ont bien été faites
    if ($saved == true) {
        echo '<p id="msgSaved">Vos informations ont bien été enregistrées.</p>';
    }
    ?>

</body>

<script src="js/accueil.js"></script>

</html>