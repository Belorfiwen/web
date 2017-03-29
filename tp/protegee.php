<?php
	ob_start();
	error_reporting(E_ALL);
	include ('bibli_24sur7.php');
	session_start();

	ec_html_head('24sur7 | Agenda', '-');

	ec_verifie_session ();

	echo '<p>Nom : ',$_SESSION['utiNom'],'</br>ID : ',$_SESSION['utiID'],'</br><a href="Deconnexion.php">Deconnexion</a></p>';

	ec_htmlFin();
	ob_end_flush();
?>