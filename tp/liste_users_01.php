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

	//-- Requête ----------------------------------------
	$sql = 'SELECT utiNom, utiMail, utiDateInscription, utiJours, utiHeureMin, utiHeureMax 
			FROM utilisateur
			WHERE utiID = 1';
	$r = mysqli_query($GLOBALS['bd'], $sql) or ec_bd_erreur();

	//-- Traitement -------------------------------------
	$enr = mysqli_fetch_assoc($r);
	ec_htmlProteger($enr);

	echo '<h2>Utilisateur 1</h2>',
	'<ul>',
		'<li>Nom : ',$enr['utiNom'],'</li>',
		'<li>Mail : ',$enr['utiMail'],'</li>',
		'<li>Inscription : ',ec_amj_claire($enr['utiDateInscription']),'</li>',
		'<li>Jour &agrave; afficher : ',$enr['utiJours'],'</li>',
		'<li>Heure d&eacute;but : ',$enr['utiHeureMin'],'</li>',
		'<li>Heure fin : ',$enr['utiHeureMax'],'</li>',
	'</ul>';

	//-- Déconnexion ------------------------------------
	mysqli_free_result($r);
	mysqli_close($GLOBALS['bd']);

	ec_htmlFin();
	ob_end_flush();
?>
