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
	$sql = 'SELECT utiNom,rdvDate, rdvHeureDebut, rdvHeureFin, rdvLibelle 
			FROM rendezvous, utilisateur
			WHERE rdvIDUtilisateur = 2
			AND utilisateur.utiID = rendezvous.rdvIDUtilisateur
			ORDER BY rdvDate, rdvHeureDebut';

	$r = mysqli_query($GLOBALS['bd'], $sql) or ec_bd_erreur();

	//-- Traitement -------------------------------------
	$i = 0;

	while ($enr = mysqli_fetch_assoc($r)) {
		ec_htmlProteger($enr);
		if ($i == 0) {
			echo'<h2>Utilisateur 2 : '.$enr['utiNom'].'</h2>',
				'<ul>';
			$i++;
		}
		echo'<li>'.ec_amj_claire($enr['rdvDate']).' - '.(($enr['rdvHeureDebut'] != -1)?ec_heure_claire($enr['rdvHeureDebut']).' &agrave; '.ec_heure_claire($enr['rdvHeureFin']).' - ':'journ&eacute;e enti&egrave;re  - ').$enr['rdvLibelle'].'</li>';
	}
	echo'</ul>';

	//-- Déconnexion ------------------------------------
	mysqli_free_result($r);
	mysqli_close($GLOBALS['bd']);

	ec_htmlFin();
	ob_end_flush();
?>
