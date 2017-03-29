<?php
	/**
	 * Page d'accueil de l'application 24sur7
	 *
	 */
	ob_start();

	error_reporting(E_ALL);

	include ('bibli_24sur7.php');

	if (!((isset($_GET['IDUser'])) && (estEntier($_GET['IDUser'])) && ($_GET['IDUser'] > 0) && ($_GET['IDUser'] < 999999))){
		header('Location: liste_rdv_02.php');
	}

	ec_html_head('24sur7 | Agenda', '-');

	ec_db_connexion();

	//-- Requête ----------------------------------------
	$sql = 'SELECT utiNom,rdvDate, rdvHeureDebut, rdvHeureFin, rdvLibelle, catCouleurFond, catCouleurBordure, catPublic
			FROM utilisateur 
			LEFT JOIN rendezvous ON utilisateur.utiID = rendezvous.rdvIDUtilisateur
            LEFT JOIN categorie ON rendezvous.rdvIDCategorie = categorie.catID 
			WHERE utiID = '.$_GET['IDUser'].'
			ORDER BY rdvDate, rdvHeureDebut';

	$r = mysqli_query($GLOBALS['bd'], $sql) or ec_bd_erreur();

	//-- Traitement -------------------------------------
	$i = 0;
	if (mysqli_num_rows($r) == 0) {
		echo 'Aucun utilisateur ne correspond &agrave; cette identifiant';
	}
	else {
		while ($enr = mysqli_fetch_assoc($r)) {
			ec_htmlProteger($enr);
			if ($i == 0) {
				echo'<h2>Utilisateur '.$_GET['IDUser'].' : '.$enr['utiNom'].'</h2>',
					'<ul>';
				$i++;
			}
			if ($enr['rdvHeureDebut'] != NULL) {
			echo'<li style="margin: 1px;background-color: #'.$enr['catCouleurFond'].';border: #'.$enr['catCouleurBordure'].' 1px solid;'.(($enr['catPublic'] == 0)?'font-style:italic;':'').'">'.ec_amj_claire($enr['rdvDate']).' - '.(($enr['rdvHeureDebut'] != -1)?ec_heure_claire($enr['rdvHeureDebut']).' &agrave; '.ec_heure_claire($enr['rdvHeureFin']).' - ':'journ&eacute;e enti&egrave;re  - ').$enr['rdvLibelle'].'</li>';
			}
			else {
				echo '<li>Aucun rendez-vous &agrave; afficher.</li>';
			}
		}
		echo'</ul>';
	}

	//-- Déconnexion ------------------------------------
	mysqli_free_result($r);
	mysqli_close($GLOBALS['bd']);

	ec_htmlFin();
	ob_end_flush();
?>
