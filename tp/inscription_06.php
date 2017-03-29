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
			$GLOBALS['utiID'] = mysqli_insert_id($GLOBALS['bd']);
		}

		return $erreurs;
	}

//  DEBUT ------------------------------------------------------------------
	//verification de la presence de btnValider dans le tableau $_POST
	if (array_key_exists('btnValider', $_POST)) 
	{
		// verification des saisie et si pas d'erreur, envoie dans la bd et redirection vert une autre page
		$erreurs = ecl_add_utilisateur();
		if (count($erreurs) == 0) 
		{
			header ('location: liste_users_02.php');
			exit();
		}
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

	echo '<h2>Inscription d\'un utilisateur</h2>

			<form method="POST" action="inscription.php">
				<table border="1" cellpadding="4" cellspacing="0">
					<tr>
						<td>Indiquez votre nom</td>
						<td><input type="text" name="txtNom" value="',$valueNom,'" size="40" maxlength="40"></td>
					</tr>
					<tr>
						<td>Indiquez une adresse mail valide</td>
						<td><input type="text" name="txtMail" value="',$valueMail,'" size="40" maxlength="40"></td>
					</tr>
					<tr>
						<td>Choississez un mot de passe</td>
						<td><input type="password" name="txtPasse" value="',$valuePasse,'" size="20" maxlength="20"></td>
					</tr>
					<tr>
						<td>R&eacute;p&eacute;tez votre mot de passe</td>
						<td><input type="password" name="txtVerif" value="',$valueVerif,'" size="20" maxlength="20"></td>
					</tr>
					<tr>
						<td>Pour v&eacute;rification, indiquez la date du jour</td>
						<td>
							<select name="selDate_j">
								<option value=\'1\' selected>1
								<option value=\'2\'>2
								<option value=\'3\'>3
								<option value=\'4\'>4
								<option value=\'5\'>5
								<option value=\'6\'>6
								<option value=\'7\'>7
								<option value=\'8\'>8
								<option value=\'9\'>9
								<option value=\'10\'>10
								<option value=\'11\'>11
								<option value=\'12\'>12
								<option value=\'13\'>13
								<option value=\'14\'>14
								<option value=\'15\'>15
								<option value=\'16\'>16
								<option value=\'17\'>17
								<option value=\'18\'>18
								<option value=\'19\'>19
								<option value=\'20\'>20
								<option value=\'21\'>21
								<option value=\'22\'>22
								<option value=\'23\'>23
								<option value=\'24\'>24
								<option value=\'25\'>25
								<option value=\'26\'>26
								<option value=\'27\'>27
								<option value=\'28\'>28
								<option value=\'29\'>29
								<option value=\'30\'>30
								<option value=\'31\'>31
							</select>
							<select name="selDate_m">
								<option value=\'1\' selected>Janvier
								<option value=\'2\'>Février
								<option value=\'3\'>Mars
								<option value=\'4\'>Avril
								<option value=\'5\'>Mai
								<option value=\'6\'>Juin
								<option value=\'7\'>Juillet
								<option value=\'8\'>Août
								<option value=\'9\'>Septembre
								<option value=\'10\'>Octobre
								<option value=\'11\'>Novembre
								<option value=\'12\'>Décembre
							</select>
							<select name="selDate_a">
								<option value=\'2017\'>2017
								<option value=\'2016\'>2016
								<option value=\'2015\'>2015
								<option value=\'2014\'>2014
								<option value=\'2013\'>2013
								<option value=\'2012\'>2012
								<option value=\'2011\'>2011
								<option value=\'2010\'>2010
								<option value=\'2009\'>2009
								<option value=\'2008\'>2008
								<option value=\'2007\'>2007
								<option value=\'2006\'>2006
								<option value=\'2005\'>2005
								<option value=\'2004\'>2004
								<option value=\'2003\'>2003
								<option value=\'2002\'>2002
								<option value=\'2001\'>2001
								<option value=\'2000\' selected>2000
								<option value=\'1999\'>1999
								<option value=\'1998\'>1998
								<option value=\'1997\'>1997
								<option value=\'1996\'>1996
								<option value=\'1995\'>1995
								<option value=\'1994\'>1994
								<option value=\'1993\'>1993
								<option value=\'1992\'>1992
								<option value=\'1991\'>1991
								<option value=\'1990\'>1990
								<option value=\'1989\'>1989
								<option value=\'1988\'>1988
								<option value=\'1987\'>1987
								<option value=\'1986\'>1986
								<option value=\'1985\'>1985
							</select>
						</td>
					</tr>
					<tr>
						<td></td>
						<td><input type="submit" name="btnValider" value="Je m\'inscris"></td>
					</tr>
				</table>
			</form>';

	mysqli_free_result($r);
	mysqli_close($GLOBALS['bd']);

	ec_htmlFin();
	ob_end_flush();
?>