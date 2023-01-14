<?php
include_once("db.php");
session_start();

if (isset($_SESSION['connexion'])) {
	try {
		// On récupère les user dont le pseudo commence par la valeur q passé en requete GET
		$db = new PDO("$server:host=$host;dbname=$base", $user, $pass);
		$sql = "SELECT id_user FROM Utilisateur WHERE id_user LIKE '" . $_GET['q'] . "%';";

		$st = $db->query($sql);
		$lignes = $st->fetchAll();

		// on renvoie la liste des utilisateurs trouvés (un par un)
		foreach ($lignes as $key => $value) {
			echo $value['id_user'] . "+";
		}
	} catch (PDOException $err) {
		echo "Erreur : " . $e->getMessage() . "<br />";
		die();
	}
}
