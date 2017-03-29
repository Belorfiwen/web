<?php
	/**
	 * Page d'accueil de l'application 24sur7
	 *
	 */
	ob_start();

	error_reporting(E_ALL);

	include ('bibli_24sur7.php');

	ec_html_head('24sur7 | Inscription', '-');

	$erreurs = array();

	echo '<h2>R&eacute;ception du formulaire d\'inscription utilisateur</h2>';

	//-----------------------------------------------------
	// Vérification des zones
	//-----------------------------------------------------

	// Verification du Nom

	$txtNom = trim($_POST['txtNom']);
	$long = mb_strlen($txtNom, 'UTF-8');
	if ($long < 4 || $long > 30) 
	{
		$erreurs[] = 'Le Nom doit avoir de 4 à 30 caract&egrave;res';
	}

	//Verification du mail

	$txtMail = trim($_POST['txtMail']);
	if ($txtMail == '') {
		$erreurs[] = 'L\'adresse mail est obligatoire';
	}
	elseif (!(mb_strpos($txtMail, 'UTF-8') && mb_strpos($txtMail, 'UTF_8'))) {
		$erreurs[] = 'L\'adresse mail n\'est pas valide';
	}

	// Vérification du mot de passe

	$long = mb_strlen($txtPseudo, 'UTF-8');
	$txtPasse = trim($_POST['txtPasse']);
	if ($txtPasse == '') {
		$erreurs[] = 'Le mot de passe est obligatoire';	
	}
	elseif ($long < 4 || $long > 20) 
	{
		$erreur[] = 'Le mot de passe doit avoir de 4 à 30 caract&egrave;res';
	}
	
	$txtVerif = trim($_POST['txtVerif']);
	if ($txtPasse != $txtVerif) {
		$erreurs[] = 'Le mot de passe est diff&eacute;rent dans les 2 zones';
	}

	// Vérification de la date

	$selJour = (int) $_POST['selDate_j'];
	$selMois = (int) $_POST['selDate_m'];
	$selAnnee = (int) $_POST['selDate_a'];
	if (! checkdate($selMois, $selJour, $selAnnee)) {
		$erreurs[] = 'La date n\'est pas valide';
	}
	if (mktime(0, 0, 0, $selMois, $selJour, $selAnnee) != time()) {
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