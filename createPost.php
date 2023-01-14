<?php
include_once("db.php");
session_start();

// Si l'utilisateur est bien connecté
if (isset($_SESSION['connexion'])) {

    // Post depuis fil principal, donc un post publique
    if (isset($_POST['poster'])) {
        try {
            $db = new PDO("$server:host=$host;dbname=$base", $user, $pass);
            // On récupère la date et l'heure actuelle
            $date = date("Y-m-d");
            $time = date("H:i:s");

            // On insère dans la bdd le post passé en requete POST
            $sql = "INSERT INTO `Post` (`img_post`, `msg_post`, `public_post`, `id_user`, `date_post`, `heure_post`) VALUES (null, :msgPost, 1,  :identifiant, :date , :time);";
            $st = $db->prepare($sql);
            $st->execute([
                "msgPost" => $_POST['msgPost'],
                "identifiant" => $_SESSION['identifiant'],
                "date" => $date,
                "time" => $time
            ]);
            // on reactualise la page
            header('Location:accueil.php?posts=true');
        } catch (PDOException $err) {
            echo "Erreur : " . $err->getMessage() . "<br />";
            die();
        }
    }


    // Post depuis filPerso
    if (isset($_POST['posterPerso'])) {
        try {
            $db = new PDO("$server:host=$host;dbname=$base", $user, $pass);
            // On récupère la date et l'heure actuelle
            $date = date("Y-m-d");
            $time = date("H:i:s");

            if (!(isset($_POST['public']))) {
                //Post privé
                $sql = "INSERT INTO `Post` (`img_post`, `msg_post`, `public_post`, `id_user`, `date_post`, `heure_post`) VALUES (null, :msgPost, 0,  :identifiant, :date, :time);";
                $st = $db->prepare($sql);
                $st->execute([
                    "msgPost" => $_POST['msgPost'],
                    "identifiant" => $_SESSION['identifiant'],
                    "date" => $date,
                    "time" => $time
                ]);
            } else {
                //Post publique
                $sql = "INSERT INTO `Post` (`img_post`, `msg_post`, `public_post`, `id_user`, `date_post`, `heure_post`) VALUES (null, :msgPost, 1,  :identifiant, :date, :time);";
                $st = $db->prepare($sql);
                $st->execute([
                    "msgPost" => $_POST['msgPost'],
                    "identifiant" => $_SESSION['identifiant'],
                    "date" => $date,
                    "time" => $time
                ]);
            }

            // On actualise la page
            header('Location:filPerso.php?posts=true');
        } catch (PDOException $err) {
            echo "Erreur : " . $err->getMessage() . "<br />";
            die();
        }
    }



    // post commentaire
    if (isset($_POST['posterComm'])) {
        try {
            $db = new PDO("$server:host=$host;dbname=$base", $user, $pass);

            // On récupère la date et l'heure actuelle
            $date = date("Y-m-d");
            $time = date("H:i:s");

            // on insere dans la bdd le comentaire passé en requete POST
            $sql = "INSERT INTO `Commentaire` (`msg_comm`, `note`, `date_comm`, `heure_comm`, `id_user`, `id_post`) VALUES (:msgPost, :caseNote, :date, :time, :identifiant, :postId);";
            $st = $db->prepare($sql);
            $st->execute([
                "msgPost" => $_POST['msgPost'],
                "caseNote" => $_POST['caseNote'],
                "identifiant" => $_SESSION['identifiant'],
                "postId" => $_SESSION['postId'],
                "date" => $date,
                "time" => $time
            ]);

            // on actualise la page
            header('Location:post.php?id=' . $_SESSION['postId']);
        } catch (PDOException $err) {
            echo "Erreur : " . $err->getMessage() . "<br />";
            die();
        }
    }
} else {
    // Si le user n'est pas connecté, redirection vers page de connexion
    header('Location:connexion.php');
}
