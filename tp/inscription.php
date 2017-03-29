<?php
	/**
	 * Page d'inscription de l'application 24sur7
	 *
	 */
	ob_start();
	error_reporting(E_ALL);

	include ('bibli_24sur7.php');

	
	//___________________________________________________________________
	/**
	 * Verifications des saisie du formulaire d'inscription, &eacutecriture des erreurs dans un tableau &agrave retourner et si aucune erreur, envoi du formulaire &agrave la BD
	 * @return array() $erreur tableau des erreurs
	 */
	function ecl_add_utilisateur() {
		ec_db_connexion();
		$erreurs = array();
	
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

		if ($txtMail == '') 
		{
			$erreurs[] = 'L\'adresse mail est obligatoire';
		}
		elseif ((mb_strpos($txtMail, '@', 0, 'UTF-8') === FALSE) || (mb_strpos($txtMail, '.', 0, 'UTF-8') === FALSE)) 
		{
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

			if ($D[0] > 0) 
			{
				$erreurs[] = 'Le mail doit &ecirc;tre chang&eacute;';
			}
		}

		// Vérification du mot de passe
		$txtPasse = trim($_POST['txtPasse']);
		$txtPasse = mysqli_real_escape_string($GLOBALS['bd'],$txtPasse);

		$long = mb_strlen($txtPasse, 'UTF-8');

		if ($txtPasse == '') 
		{
			$erreurs[] = 'Le mot de passe est obligatoire';	
		}
		elseif ($long < 4 || $long > 20) 
		{
			$erreurs[] = 'Le mot de passe doit avoir de 4 à 20 caract&egrave;res';
		}
		
		$txtVerif = trim($_POST['txtVerif']);
		if ($txtPasse != $txtVerif) 
		{
			$erreurs[] = 'Le mot de passe est diff&eacute;rent dans les 2 zones';
		}

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

		if (count($erreurs) == 0) 
		{
			$sql = 'INSERT INTO utilisateur (utiNom, utiMail, utiPasse, utiDateInscription, utiJours, utiHeureMin, utiHeureMax)
					VALUES ("'.$txtNom.'","'.$txtMail.'","'.md5($txtPasse).'","'.$date.'","127","6","22")';

			$r = mysqli_query($GLOBALS['bd'], $sql) or ec_bd_erreur ($GLOBALS['bd'], $sql);

			session_start();
			$_SESSION['utiID'] = mysqli_insert_id($GLOBALS['bd']);
			$_SESSION['utiNom'] = htmlentities($txtNom, ENT_COMPAT, 'UTF-8');
			header ('location: protegee.php');
			exit();
		}

		return $erreurs;
	}

//  DEBUT ------------------------------------------------------------------
	//verification de la presence de btnValider dans le tableau $_POST
	if (array_key_exists('btnValider', $_POST)) 
	{
		// verification des saisie et si pas d'erreur, envoie dans la bd et redirection vert une autre page
		$erreurs = ecl_add_utilisateur();
	}
	else
	{
		//initialisation de $_POST
		$_POST['txtNom'] = "";
		$_POST['txtMail'] = "";
		$_POST['txtPasse'] = "";
		$_POST['txtVerif'] = "";
		$_POST['selDate_j'] = 1;
		$_POST['selDate_m'] = 1;
		$_POST['selDate_a'] = 2000;
	}
	
	// AFFICHAGE DU FORMULAIRE

	$valueNom = $_POST['txtNom'];
	$valueMail = $_POST['txtMail'];
	$valuePasse = $_POST['txtPasse'];
	$valueVerif = $_POST['txtVerif'];

	ec_html_head('24sur7 | Inscription', '-');
	echo '<h2>R&eacute;ception du formulaire d\'inscription utilisateur</h2>';

	if (count($erreurs)) {
		echo '<strong>Les erreurs suivantes ont &eacute;t&eacute; d&eacute;tect&eacute;es</strong>';

		foreach ($erreurs as $value) 
		{
			echo '</br>',$value;
		}
		$valueNom = $_POST['txtNom'];
		$valueMail = $_POST['txtMail'];
		$valuePasse = $_POST['txtPasse'];
		$valueVerif = $_POST['txtVerif'];
	}

	echo '<h2>Inscription d\'un utilisateur</h2>',

			'<form method="POST" action="inscription.php">',
				'<table border="1" cellpadding="4" cellspacing="0">',
				ec_form_ligne('Indiquez votre nom',ec_form_input('text','txtNom',$valueNom,40),'right'),
				ec_form_ligne('Indiquez une adresse mail',ec_form_input('text','txtMail',$valueMail,40),'right'),
				ec_form_ligne('Choississez un mot de passe',ec_form_input('password','txtPasse',$valuePasse,20),'right'),
				ec_form_ligne('R&eacutep&eacuteter votre mot de passe',ec_form_input('password','txtVerif',$valueVerif,20),'right'),
				ec_form_ligne('Pour v&eacute;rification, indiquez la date du jour',ec_form_date('selDate',1,1,2000)),
				ec_form_ligne('',ec_form_input('submit','btnValider','Je m\'inscris',0)),
			'</table>',
		'</form>';

	mysqli_free_result($r);
	mysqli_close($GLOBALS['bd']);

	ec_htmlFin();
	ob_end_flush();
?>