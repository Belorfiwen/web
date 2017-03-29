<?php
	/**
	 * Page d'accueil de l'application 24sur7
	 *
	 */
	ob_start();

	error_reporting(E_ALL);

	include ('bibli_24sur7.php');

	ec_html_head('24sur7 | Inscription', '-');

	ec_db_connexion();

	$erreurs = array();

	echo '<h2>R&eacute;ception du formulaire d\'inscription utilisateur</h2>';

	//-----------------------------------------------------
	// Vérification des zones
	//-----------------------------------------------------

	// Verification du Nom

	$txtNom = trim($_POST['txtNom']);
	$txtNom = mysqli_real_escape_string($GLOBALS['bd'],$txtNom);

	$long = mb_strlen($txtNom, 'UTF-8');
	if ($long < 4 || $long > 30) 
	{
		$erreurs[] = 'Le nom doit avoir de 4 à 30 caract&egrave;res';
	}

	//Verification du mail

	$txtMail = trim($_POST['txtMail']);
	$txtMail = mysqli_real_escape_string($GLOBALS['bd'],$txtMail);

	if ($txtMail == '') {
		$erreurs[] = 'L\'adresse mail est obligatoire';
	}
	elseif ((mb_strpos($txtMail, '@', 0, 'UTF-8') === FALSE) || (mb_strpos($txtMail, '.', 0, 'UTF-8') === FALSE)) {
		$erreurs[] = 'L\'adresse mail n\'est pas valide';
	}
	else 
	{
		// Vérification que le mail n'existe pas dans la BD
		$sql = "SELECT	count(*)
				FROM	utilisateur
				WHERE	utiMail = '$txtMail'";

		$r = mysqli_query($GLOBALS['bd'], $sql) or fd_bd_erreur($GLOBALS['bd'], $sql);

		$D = mysqli_fetch_row($r);

		if ($D[0] > 0) {
			$erreurs[] = 'Le mail doit &ecirc;tre chang&eacute;';
		}

		mysqli_free_result($r);
		mysqli_close($GLOBALS['bd']);
	}

	// Vérification du mot de passe
	$txtPasse = trim($_POST['txtPasse']);
	$txtPasse = mysqli_real_escape_string($GLOBALS['bd'],$txtPasse);

	$long = mb_strlen($txtPseudo, 'UTF-8');
	
	if ($txtPasse == '') {
		$erreurs[] = 'Le mot de passe est obligatoire';	
	}
	elseif ($long < 4 || $long > 20) 
	{
		$erreurs[] = 'Le mot de passe doit avoir de 4 à 30 caract&egrave;res';
	}
	
	$txtVerif = trim($_POST['txtVerif']);
	if ($txtPasse != $txtVerif) {
		$erreurs[] = 'Le mot de passe est diff&eacute;rent dans les 2 zones';
	}

	// Vérification de la date

	// Vérification de la date

	$selJour = (int) $_POST['selDate_j'];
	$selMois = (int) $_POST['selDate_m'];
	$selAnnee = (int) $_POST['selDate_a'];
	$date = $selAnnee.(($selMois<10)?'0':'').$selMois.(($selJour<10)?'0':'').$selJour;
	
	if (! checkdate($selMois, $selJour, $selAnnee)) 
	{
		$erreurs[] = 'La date n\'est pas valide';
	}
	if ($date != date('Ymd')) 
	{
		$erreurs[] = 'La date doit etre celle du jour';
	}

	//affichage des erreurs eventuelles
	if (count($erreurs) == 0) {
		echo 'Aucune erreur de saisie';
	}
	else
	{
		echo '<strong>Les erreurs suivantes ont &eacute;t&eacute; d&eacute;tect&eacute;es</strong>';
		foreach ($erreurs as $value) {
			echo '</br>',$value;
		}
	}

	ec_htmlFin();
	ob_end_flush();
?>