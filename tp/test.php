<?php
	/**
	 * Page d'accueil de l'application 24sur7
	 *
	 */
	ob_start();

	error_reporting(E_ALL);

	include ('bibli_24sur7.php');

	ec_html_head('24sur7 | Agenda', '-');

	ec_db_connexion();

	mysql_real_escape_string(unescaped_string)

	strip_tags(str)

	trim(str)


	charset UTF-8: utilisation de l'extension nb string
	nb_strlen() nb_strpos() nb_substr()
	=>inqiquez charset de l'appel



	//-- Déconnexion ------------------------------------
	mysqli_free_result($r);
	mysqli_close($GLOBALS['bd']);

	ec_htmlFin();
	ob_end_flush();
?>
