<?php
/** @file
 * Page d'accueil de l'application 24sur7
 *
 * @author : Frederic Dadeau - frederic.dadeau@univ-fcomte.fr
 */
ob_start();
session_start();
include('bibli_24sur7.php');	// Inclusion de la bibliothéque

fd_html_head('24sur7 | Agenda');

fd_html_bandeau(APP_PAGE_PARAMETRES);

echo '<section id="bcContenu">';
	
	
echo	'<section>';
		
$nbErr2=0;		
			
if (! isset($_POST['btnValider1'])) {
	// => On intialise les zones de saisie.
	
	fd_bd_connexion();
	
	$S = "SELECT	utiNom, utiID
					FROM	utilisateur
					WHERE	utiID = {$_SESSION['utiID']}";

	$R = mysqli_query($GLOBALS['bd'], $S) or fd_bd_erreur($S);
	
	$D = mysqli_fetch_assoc($R);
	
	$nbErr = 0;
	$_POST['txtNom']= $D['utiNom'];
	$_POST['txtMail'] = $_SESSION['utiMail'];
	$_POST['txtPasse'] = '';
	$_POST['txtVerif']= '';

} else {
	// On est dans la phase de soumission du formulaire :
	// => vérification des valeurs reçues
	// Si aucune erreur n'est détectée, fdl_modification_utilisateur()
	$erreurs = fdl_modification_utilisateur();
	$nbErr = count($erreurs);	
}


if (! isset($_POST['btnValider2'])) {
	// => On intialise les zones de saisie.
	
	fd_bd_connexion();
		
	$ret = mysqli_set_charset($GLOBALS['bd'], "utf8");
    if ($ret == FALSE){
		fd_bd_erreurExit('Erreur lors du chargement du jeu de caract&egrave;res utf8');
    }
	
	$S = "SELECT	utiID,utiHeureMin, utiHeureMax
					FROM	utilisateur
					WHERE	utiID = {$_SESSION['utiID']}";

	$R = mysqli_query($GLOBALS['bd'], $S) or fd_bd_erreur($S);
	
	$D = mysqli_fetch_assoc($R);
	
	$nbErr = 0;
	$_POST['hMin'] = $D['utiHeureMin'];
	$_POST['hMax'] = $D['utiHeureMax'];

} else {
	// On est dans la phase de soumission du formulaire :
	// => vérification des valeurs reçues et création utilisateur.
	// Si aucune erreur n'est détectée, fdl_modification_affichage_calendrier
	$erreurs2 = fdl_modification_affichage_calendrier();
	$nbErr2 = count($erreurs2);	
}

if (! isset($_POST['ajouter'])) {
	// => On intialise les zones de saisie.
		
	$nbErr3 = 0;
	
	$_POST['catNom']='';
	$_POST['catFond']='';
	$_POST['catBordure']='';
	$_POST['catPublic']=1;
					

} else {
	// On est dans la phase de soumission du formulaire :
	// => vérification des valeurs reçues
	// Si aucune erreur n'est détectée, fdl_modification_utilisateur()
	$erreurs3 = fdl_ajout_categorie();
	$nbErr3 = count($erreurs3);	
}

fd_bd_connexion();
	
	$S = "SELECT 	count(*)
			FROM	categorie
			WHERE	catIDUtilisateur = {$_SESSION['utiID']}";

	$R = mysqli_query($GLOBALS['bd'], $S) or fd_bd_erreur($S);
	$i=1;
while($D = mysqli_fetch_assoc($R)){
		
	
	if (! isset($_POST['sauver'.$i])) {
		// => On intialise les zones de saisie.
		
		$S = "SELECT	catNom, catCouleurFond, catCouleurBordure, catIDUtilisateur, catPublic
						FROM	categorie
						WHERE	catIDUtilisateur = {$_SESSION['utiID']}";

		$R = mysqli_query($GLOBALS['bd'], $S) or fd_bd_erreur($S);
		
		$nbErr3 = 0;
		$i=1;
		while($D = mysqli_fetch_assoc($R)){
			affecter($i,$D['catNom']);
			affecter2($i,$D['catCouleurFond']);
			affecter3($i,$D['catCouleurBordure']);
			affecter4($i,$D['catPublic']);
			
			$i++;
		}
			

	} else {
		// On est dans la phase de soumission du formulaire :
		// => vérification des valeurs reçues
		// Si aucune erreur n'est détectée, fdl_modification_utilisateur()
		$erreurs3 = fdl_modification_categorie();
		$nbErr3 = count($erreurs3);	
	}
	
	if (! isset($_POST['supprimer'.$i])) {
		// => On intialise les zones de saisie.
		
		$S = "SELECT	catNom, catCouleurFond, catCouleurBordure, catIDUtilisateur, catPublic
						FROM	categorie
						WHERE	catIDUtilisateur = {$_SESSION['utiID']}";

		$R = mysqli_query($GLOBALS['bd'], $S) or fd_bd_erreur($S);
		
		$nbErr3 = 0;
		$i=1;
		while($D = mysqli_fetch_assoc($R)){
			affecter($i,$D['catNom']);
			affecter2($i,$D['catCouleurFond']);
			affecter3($i,$D['catCouleurBordure']);
			affecter4($i,$D['catPublic']);
			
			$i++;
		}
			

	} else {
		// On est dans la phase de soumission du formulaire :
		// => vérification des valeurs reçues
		// Si aucune erreur n'est détectée, fdl_modification_utilisateur()
		$erreurs3 = fdl_suppression_categorie();
		$nbErr3 = count($erreurs3);	
		if ($nbErr3 > 0) {
			echo '<strong>Les erreurs suivantes ont &eacute;t&eacute; d&eacute;tect&eacute;es</strong>';
			for ($i = 0; $i < $nbErr3; $i++) {
				echo '<br>', $erreurs3[$i];
			}
		}
	}
}
mysqli_free_result($R);

// Si il y a des erreurs on les affiche

if ($nbErr > 0) {
	echo '<strong>Les erreurs suivantes ont &eacute;t&eacute; d&eacute;tect&eacute;es</strong>';
	for ($i = 0; $i < $nbErr; $i++) {
		echo '<br>', $erreurs[$i];
	}
}

if ($nbErr2 > 0) {
	echo '<strong>Les erreurs suivantes ont &eacute;t&eacute; d&eacute;tect&eacute;es</strong>';
	for ($i = 0; $i < $nbErr2; $i++) {
		echo '<br>', $erreurs2[$i];
	}
}

if ($nbErr3 > 0) {
	echo '<strong>Les erreurs suivantes ont &eacute;t&eacute; d&eacute;tect&eacute;es</strong>';
	for ($i = 0; $i < $nbErr3; $i++) {
		echo '<br>', $erreurs3[$i];
	}
}

echo '<div class="titreparam1"> Informations sur votre compte </div>';
	// Affichage du formulaire
	echo '<form class="newparamUtilisateur" method="POST" action="parametres.php">',
			'<table border="1" cellpadding="4" cellspacing="0">',
			fd_form_ligne('Nom ', 
				fd_form_input(APP_Z_TEXT,'txtNom', $_POST['txtNom'], 30),'','class="colonneGauche"','class="boutonIIAnnuler"'),
			
			fd_form_ligne('Mail ', fd_form_input(APP_Z_TEXT,'txtMail', $_POST['txtMail'], 30),'','class="colonneGauche"','class="boutonIIAnnuler"'),
			fd_form_ligne('Mot de passe ', fd_form_input(APP_Z_PASS,'txtPasse', $_POST['txtPasse'], 30),'','class="colonneGauche"','class="boutonIIAnnuler"'),
			fd_form_ligne('Retapez le mot de passe ', fd_form_input(APP_Z_PASS,'txtVerif', $_POST['txtVerif'], 30),'','class="colonneGauche"','class="boutonIIAnnuler"'),
						
			fd_form_ligne("<input type='submit' name='btnValider1' value=\"Mettre &agrave; jour\" size=15 class='boutonII'>", 
				"<input type='reset' name='btnEffacer1' value=\"Annuler\" size=15 class='boutonII' class='boutonIIAnnuler'>",'','class="colonneGauche"','class="boutonIIAnnuler"'),
			'</table></form>';
			
			
echo '<div class="titreparam2"> Options d\'affichage du calendrier </div>';
	// Affichage du formulaire
	echo '<form class="newparamCalendrier" method="POST" action="parametres.php">',
			'<table border="1" cellpadding="4" cellspacing="0">',
			fd_form_ligne('Jours affich&eacute;s ', '<input type=\'checkbox\' name=\'checkLundi\' value=\'1\' checked> Lundi
												<input type=\'checkbox\' name=\'checkMardi\' value=\'1\' checked> Mardi
												<input type=\'checkbox\' name=\'checkMercredi\' value=\'1\' checked> Mercredi
												<input type=\'checkbox\' name=\'checkJeudi\' value=\'1\' checked> Jeudi
												<input type=\'checkbox\' name=\'checkVendredi\' value=\'1\' checked> Vendredi
												<input type=\'checkbox\' name=\'checkSamedi\' value=\'1\' checked> Samedi
												<input type=\'checkbox\' name=\'checkDimanche\' value=\'1\' checked> Dimanche',
												'','class="colonneGauche"','class="boutonIIAnnuler"'),
			
			fd_form_ligne('Heure minimale ', heure_min_max('hMin',6),'','class="colonneGauche"','class="boutonIIAnnuler"'),
			fd_form_ligne('Heure maximale ', heure_min_max('hMax',22),'','class="colonneGauche"','class="boutonIIAnnuler"'),
			
			fd_form_ligne("<input type='submit' name='btnValider2' value=\"Mettre &agrave; jour\" size=15 class='boutonII'>", 
				"<input type='reset' name='btnEffacer2' value=\"Annuler\" size=15 class='boutonII' class='boutonIIAnnuler'>",'','class="colonneGauche"','class="boutonIIAnnuler"'),
			'</table></form>';
			
			
echo '<div class="titreparam2"> Vos catégories </div>';
	// Affichage du formulaire
	echo '<form class="newparamCategorie" method="POST" action="parametres.php">',
			'<table border="1" cellpadding="4" cellspacing="0">';
	fd_bd_connexion();
	
	$S = "SELECT	catNom, catCouleurFond, catCouleurBordure, catIDUtilisateur, catPublic
					FROM	categorie
					WHERE	catIDUtilisateur = {$_SESSION['utiID']}";

	$R = mysqli_query($GLOBALS['bd'], $S) or fd_bd_erreur($S);
	
	$nbErr3 = 0;
	$i=1;
	while($D = mysqli_fetch_assoc($R)){
		echo fd_form_ligne('Nom : '.fd_form_input(APP_Z_TEXT,"catNom$i", $_POST['catNom'.$i], 10). 
							' Fond : '.fd_form_input(APP_Z_TEXT,"catFond$i", $_POST['catFond'.$i], 10).
							' Bordure : '.fd_form_input(APP_Z_TEXT,"catBordure$i", $_POST['catBordure'.$i], 10),
							fd_form_input('checkbox',"catPublic$i", $_POST['catPublic'.$i]).'Public  
							<input type=\'image\' src=\'../images/sauver.png\' width=\'20px\' height=\'20px\' name=\'sauver'.$i.'\'>
							<input type=\'image\' src=\'../images/supprimer.png\' width=\'20px\' height=\'20px\' name=\'supprimer'.$i.'\'>',
												'','class="colonneGauche"','class="boutonIIAnnuler"');		
		$i++;
	}
	
	echo fd_form_ligne('&nbsp;','&nbsp;','class=\'titreparam3\'','',''),
				fd_form_ligne('Nouvelle catégorie :','','class=\'titreparam3\'','',''), 
				fd_form_ligne('Nom : '.fd_form_input(APP_Z_TEXT,"catNom", $_POST['catNom'], 10). 
							' Fond : '.fd_form_input(APP_Z_TEXT,"catFond", $_POST['catFond'], 10).
							' Bordure : '.fd_form_input(APP_Z_TEXT,"catBordure", $_POST['catBordure'], 10),
							fd_form_input('checkbox',"catPublic", $_POST['catPublic']).'Public  
							<input type=\'submit\' name=\'ajouter\' value="Ajouter" size=15 class=\'boutonII\'>',
												'','class="colonneGauche"','class="boutonIIAnnuler"');	
	
	echo '</table></form>';		
			
			
			
			
	/** 
	* Validation de la saisie et modification d'un utilisateur.
	*
	* Les zones reçues du formulaires de saisie sont vérifiées. Si
	* des erreurs sont détectées elles sont renvoyées sous la forme
	* d'un tableau. Si il n'y a pas d'erreurs,
	* une modification est faite si le rendez vous existe.
	*
	* @global array		$_POST		zones de saisie du formulaire
	* @global array		$_GLOBALS	base de bonnées 
	*
	* @return array 	Tableau des erreurs détectées
	*/		
	function fdl_modification_utilisateur() {
		
		fd_bd_connexion();
		
		//-----------------------------------------------------
		// Vérification des zones
		//-----------------------------------------------------
		$erreurs = array();
		
		// Vérification du nom
		$txtNom = trim($_POST['txtNom']);
		$long = mb_strlen($txtNom, 'UTF-8');
		if ($long < 4
		|| $long > 30)
		{
			$erreurs[] = 'Le nom doit avoir de 4 &agrave; 30 caract&egrave;res';
		}

		// Vérification du mail
		$txtMail = trim($_POST['txtMail']);
		if ($txtMail == '') {
			$erreurs[] = 'L\'adresse mail est obligatoire';
		} elseif (mb_strpos($txtMail, '@', 0, 'UTF-8') === FALSE
				|| mb_strpos($txtMail, '.', 0, 'UTF-8') === FALSE)
		{
			$erreurs[] = 'L\'adresse mail n\'est pas valide';
		}
		if($_SESSION['utiMail'] != $_POST['txtMail']){
			$mail = mysqli_real_escape_string($GLOBALS['bd'], $txtMail);

			$S = "SELECT	count(*)
					FROM	utilisateur
					WHERE	utiMail = '$mail'";

			$R = mysqli_query($GLOBALS['bd'], $S) or fd_bd_erreur($S);

			$D = mysqli_fetch_row($R);

			if ($D[0] > 0) {
				$erreurs[] = 'Cette adresse mail est d&eacute;j&agrave; inscrite.';
			}
			mysqli_free_result($R);
		}
		
		
		// Verification mot de passe
		$mdp=0;
		if(($_POST['txtPasse'] !== '')||($_POST['txtVerif'] !== '')){
			$txtPasse = trim($_POST['txtPasse']);
			$long = mb_strlen($txtPasse, 'UTF-8');
			if ($long < 4 || $long > 20){
				$erreurs[] = 'Le mot de passe doit avoir de 4 &agrave; 20 caract&egrave;res';
			}

			$txtVerif = trim($_POST['txtVerif']);
			if ($txtPasse != $txtVerif) {
				$erreurs[] = 'Le mot de passe est diff&eacute;rent dans les 2 zones';
			}
			$mdp=1;
		}

		// Si il y a des erreurs, la fonction renvoie le tableau d'erreurs
		if (count($erreurs) > 0) {
			return $erreurs;		// RETURN : des erreurs ont été détectées
		}

		//-----------------------------------------------------
		// Modification des paramètres du compte  
		//-----------------------------------------------------
		$txtNom = mysqli_real_escape_string($GLOBALS['bd'], $_POST['txtNom']);

		$txtMail = mysqli_real_escape_string($GLOBALS['bd'], $_POST['txtMail']);
		$txtPasse='';
		if($mdp===1){
			$txtPasse = mysqli_real_escape_string($GLOBALS['bd'], md5($txtPasse));
			$S = "UPDATE utilisateur SET
				utiNom = '$txtNom',
				utiMail = '$txtMail',
				utiPasse = '$txtPasse'
				WHERE utiID = {$_SESSION['utiID']}";
				
			$R = mysqli_query($GLOBALS['bd'], $S) or fd_bd_erreur($S);
		}
		else{
			$S = "UPDATE utilisateur SET
				utiNom = '$txtNom',
				utiMail = '$txtMail'
				WHERE utiID = {$_SESSION['utiID']}";
					
			$R = mysqli_query($GLOBALS['bd'], $S) or fd_bd_erreur($S);
		}	
	
		// Déconnexion de la base de données
		mysqli_close($GLOBALS['bd']);
		
		header ('location: parametres.php');
		exit();
}			



/**
	* Validation de la saisie et modification de l'affichage du calendrier.
	*
	* Les zones reçues du formulaires de saisie sont vérifiées. Si
	* des erreurs sont détectées elles sont renvoyées sous la forme
	* d'un tableau. Si il n'y a pas d'erreurs,une modification est faite dans la table utilisateur.
	*
	* @global array		$_POST		zones de saisie du formulaire
	* @global array		$_GLOBALS	base de bonnées 
	*
	* @return array 	Tableau des erreurs détectées
	*/
function fdl_modification_affichage_calendrier() {
		
		fd_bd_connexion();
		
		//-----------------------------------------------------
		// Vérification des zones
		//-----------------------------------------------------
		$erreurs = array();
		
		//-----------------------------------------------------
		// Vérification des heures min et max
		//-----------------------------------------------------
		$hMin=$_POST['hMin'];
		$hMax=$_POST['hMax'];
		
			if($hMin>=$hMax){
				$erreurs[] = 'Plage horaire trop courte';
			}
			
		$jours='';

		if(isset($_POST['checkLundi'])){
			$jours.='1';
		} else {
			$jours.='0';
		}
		
		if(isset($_POST['checkMardi'])){
			$jours.='1';
		} else {
			$jours.='0';
		}
		
		if(isset($_POST['checkMercredi'])){
			$jours.='1';
		} else {
			$jours.='0';
		}
		
		if(isset($_POST['checkJeudi'])){
			$jours.='1';
		} else {
			$jours.='0';
		}
		
		if(isset($_POST['checkVendredi'])){
			$jours.='1';
		} else {
			$jours.='0';
		}
		
		if(isset($_POST['checkSamedi'])){
			$jours.='1';
		} else {
			$jours.='0';
		}
		
		if(isset($_POST['checkDimanche'])){
			$jours.='1';
		} else {
			$jours.='0';
		}
		
		$jours=bindec($jours);
		
		// Si il y a des erreurs, la fonction renvoie le tableau d'erreurs
		if (count($erreurs) > 0) {
			return $erreurs;		// RETURN : des erreurs ont été détectées
		}
		

		//-----------------------------------------------------
		// mise a jour du calendrier    
		//-----------------------------------------------------
		$S = "SELECT	utiID, utiHeureMin, utiHeureMax
				FROM	utilisateur
				WHERE	utiID = {$_SESSION['utiID']}";

		$R = mysqli_query($GLOBALS['bd'], $S) or fd_bd_erreur($S);
		
		$D = mysqli_fetch_assoc($R);	
		
		if(($hMin != $D['utiHeureMin'])&&($hMax != $D['utiHeureMax'])){
				
			$S = "UPDATE utilisateur SET
				utiJours = '$jours',
				utiHeureMin = '$hMin',
				utiHeureMax = '$hMax'
				WHERE utiID = {$_SESSION['utiID']}";
					
			$R = mysqli_query($GLOBALS['bd'], $S) or fd_bd_erreur($S);
			
		} elseif (($hMin == $D['utiHeureMin'])&&($hMax != $D['utiHeureMax'])) {
			
			$S = "UPDATE utilisateur SET
				utiJours = '$jours',
				utiHeureMax = '$hMax'
				WHERE utiID = {$_SESSION['utiID']}";
					
			$R = mysqli_query($GLOBALS['bd'], $S) or fd_bd_erreur($S);
			
		} elseif (($hMin != $D['utiHeureMin'])&&($hMax == $D['utiHeureMax'])) {
			
			$S = "UPDATE utilisateur SET
				utiJours = '$jours',
				utiHeureMin = '$hMin'
				WHERE utiID = {$_SESSION['utiID']}";
					
			$R = mysqli_query($GLOBALS['bd'], $S) or fd_bd_erreur($S);
			
		} else {
			
			$S = "UPDATE utilisateur SET
				utiJours = '$jours'
				WHERE utiID = {$_SESSION['utiID']}";
					
			$R = mysqli_query($GLOBALS['bd'], $S) or fd_bd_erreur($S);
			
		}

		// Déconnexion de la base de données
		mysqli_close($GLOBALS['bd']);
		
		header ('location: parametres.php');
		exit();
}	






/**
	* Validation de la saisie et ajout de la categorie.
	*
	* Les zones reçues du formulaires de saisie sont vérifiées. Si
	* des erreurs sont détectées elles sont renvoyées sous la forme
	* d'un tableau. Si il n'y a pas d'erreurs,une ajout est fait dans la table categorie.
	*
	* @global array		$_POST		zones de saisie du formulaire
	* @global array		$_GLOBALS	base de bonnées 
	*
	* @return array 	Tableau des erreurs détectées
	*/
function fdl_ajout_categorie() {
		
		fd_bd_connexion();
		
		//-----------------------------------------------------
		// Vérification des zones
		//-----------------------------------------------------
		$erreurs = array();
		
		//-----------------------------------------------------
		// Vérification du nom de la categorie
		//-----------------------------------------------------
		
		$S = "SELECT	catNom
				FROM	categorie
				WHERE	catIDUtilisateur = {$_SESSION['utiID']}";

		$R = mysqli_query($GLOBALS['bd'], $S) or fd_bd_erreur($S);
		$nom = trim($_POST['catNom']);
		$nom = mysqli_real_escape_string($GLOBALS['bd'],$nom);
		$long = mb_strlen($nom, 'UTF-8');
		if($long==0){
			$erreurs[] = 'la catégorie n\'a pas de nom';
		}
		
		while($D = mysqli_fetch_assoc($R)){
			if($D['catNom']===$_POST['catNom']){
				$erreurs[] = 'La catégorie existe déjà';
			}
		}	
		mysqli_free_result($R);
		
		// Vérification de la couleur de fond
		$fond = trim($_POST['catFond']);
		$fond = mysqli_real_escape_string($GLOBALS['bd'],$fond);
		$long = mb_strlen($fond, 'UTF-8');
		if ($long > 6){
			$erreurs[] = 'la couleur de fond est invalide';
		}
		if($long==0){
			$erreurs[] = 'la couleur de fond est vide';
		}	
		
		// Vérification de la couleur de bordure
		$bordure = trim($_POST['catBordure']);
		$bordure = mysqli_real_escape_string($GLOBALS['bd'],$bordure);
		$long = mb_strlen($bordure, 'UTF-8');
		if ($long > 6){
			$erreurs[] = 'la couleur de bordure est invalide';
		}
		if($long==0){
			$erreurs[] = 'la couleur de bordure est vide';
		}

		// Si il y a des erreurs, la fonction renvoie le tableau d'erreurs
		if (count($erreurs) > 0) {
			return $erreurs;		// RETURN : des erreurs ont été détectées
		}
		
		//-----------------------------------------------------
		// ajout de la categorie   
		//-----------------------------------------------------
		
		if(!isset($_POST['catPublic'])){
			$S = "INSERT INTO categorie SET
				catNom = '$nom',
				catCouleurFond = '$fond',
				catCouleurBordure = '$bordure',
				catIDUtilisateur = {$_SESSION['utiID']},
				catPublic = 1";

			$R = mysqli_query($GLOBALS['bd'], $S) or fd_bd_erreur($S);
		}
		else{
			$S = "INSERT INTO categorie SET
				catNom = '$nom',
				catCouleurFond = '$fond',
				catCouleurBordure = '$bordure',
				catIDUtilisateur = {$_SESSION['utiID']},
				catPublic = 0";

			$R = mysqli_query($GLOBALS['bd'], $S) or fd_bd_erreur($S);
		}

		// Déconnexion de la base de données
		mysqli_close($GLOBALS['bd']);
		
		header ('location: parametres.php');
		exit();
}	




/**
	* Validation de la saisie et ajout de la categorie.
	*
	* Les zones reçues du formulaires de saisie sont vérifiées. Si
	* des erreurs sont détectées elles sont renvoyées sous la forme
	* d'un tableau. Si il n'y a pas d'erreurs,une ajout est fait dans la table categorie.
	*
	* @global array		$_POST		zones de saisie du formulaire
	* @global array		$_GLOBALS	base de bonnées 
	*
	* @return array 	Tableau des erreurs détectées
	*/
/*function fdl_modifier_categorie() {
		
		fd_bd_connexion();
		
		//-----------------------------------------------------
		// Vérification des zones
		//-----------------------------------------------------
		$erreurs = array();
		
		//-----------------------------------------------------
		// Vérification du nom de la categorie
		//-----------------------------------------------------
		
		$S = "SELECT	catNom
				FROM	categorie
				WHERE	catIDUtilisateur = {$_SESSION['utiID']}";

		$R = mysqli_query($GLOBALS['bd'], $S) or fd_bd_erreur($S);
		$nom = trim($_POST['catNom']);
		$nom = mysqli_real_escape_string($GLOBALS['bd'],$nom);
		$long = mb_strlen($nom, 'UTF-8');
		if($long==0){
			$erreurs[] = 'la catégorie n\'a pas de nom';
		}
		
		while($D = mysqli_fetch_assoc($R)){
			if($D['catNom']===$_POST['catNom']){
				$erreurs[] = 'La catégorie existe déjà';
			}
		}	
		mysqli_free_result($R);
		
		// Vérification de la couleur de fond
		$fond = trim($_POST['catFond']);
		$fond = mysqli_real_escape_string($GLOBALS['bd'],$fond);
		$long = mb_strlen($fond, 'UTF-8');
		if ($long > 6){
			$erreurs[] = 'la couleur de fond est invalide';
		}
		if($long==0){
			$erreurs[] = 'la couleur de fond est vide';
		}	
		
		// Vérification de la couleur de bordure
		$bordure = trim($_POST['catBordure']);
		$bordure = mysqli_real_escape_string($GLOBALS['bd'],$bordure);
		$long = mb_strlen($bordure, 'UTF-8');
		if ($long > 6){
			$erreurs[] = 'la couleur de bordure est invalide';
		}
		if($long==0){
			$erreurs[] = 'la couleur de bordure est vide';
		}

		// Si il y a des erreurs, la fonction renvoie le tableau d'erreurs
		if (count($erreurs) > 0) {
			return $erreurs;		// RETURN : des erreurs ont été détectées
		}
		
		//-----------------------------------------------------
		// ajout de la categorie   
		//-----------------------------------------------------
		
		if(!isset($_POST['catPublic'])){
			$S = "INSERT INTO categorie SET
				catNom = '$nom',
				catCouleurFond = '$fond',
				catCouleurBordure = '$bordure',
				catPublic = 1";

			$R = mysqli_query($GLOBALS['bd'], $S) or fd_bd_erreur($S);
		}
		else{
			$S = "INSERT INTO categorie SET
				catNom = '$nom',
				catCouleurFond = '$fond',
				catCouleurBordure = '$bordure',
				catPublic = 0";

			$R = mysqli_query($GLOBALS['bd'], $S) or fd_bd_erreur($S);
		}

		// Déconnexion de la base de données
		mysqli_close($GLOBALS['bd']);
		
		header ('location: parametres.php');
		exit();
}*/

				
			
function affecter($i=0,$d=''){
		$ch='catNom'.$i;
		$_POST[$ch]=$d;
}

function affecter2($i=0,$d=''){
		$ch='catFond'.$i;
		$_POST[$ch]=$d;
}

function affecter3($i=0,$d=''){
		$ch='catBordure'.$i;
		$_POST[$ch]=$d;
}

function affecter4($i=0,$d=''){
		$ch='catPublic'.$i;
		$_POST[$ch]=$d;
}			
	echo '</section>';
	




	
	echo '</section>';

		
	
	fd_html_pied();
?>