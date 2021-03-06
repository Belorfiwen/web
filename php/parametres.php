<?php
/** @file
 * Page des paramètres de l'application 24sur7
 */
ob_start();
include('bibli_24sur7.php');	// Inclusion de la bibliothèque
session_start();
ec_verifie_session();

fd_html_head('24sur7 | Param&egrave;tres');

fd_html_bandeau(APP_PAGE_PARAMETRES);

echo '<div id="bcContenu">';
	
	
echo	'<div style="padding-bottom: 27px;">';
		
		
			
if (! isset($_POST['btnValider1'])) {
	// => On intialise les zones de saisie.
	
	fd_bd_connexion();
	
	$S = "SELECT	utiNom, utiID
					FROM	utilisateur
					WHERE	utiID = {$_SESSION['utiID']}";

	$R = mysqli_query($GLOBALS['bd'], $S) or fd_bd_erreur($S);
	
	$D = mysqli_fetch_assoc($R);
	ec_htmlProteger ($D);
	
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
$alert=0;
if ($nbErr > 0) {
	$alert=1;
	echo '<strong>Les erreurs suivantes ont &eacute;t&eacute; d&eacute;tect&eacute;es</strong>';
	for ($i = 0; $i < $nbErr; $i++) {
		echo '<br>', $erreurs[$i];
	}
}



if (! isset($_POST['btnValider2'])) {
	// => On intialise les zones de saisie.
	
	fd_bd_connexion();
	$nbErr2=0;
	

	$S = "SELECT	utiID,utiHeureMin, utiHeureMax, utiJours
					FROM	utilisateur
					WHERE	utiID = {$_SESSION['utiID']}";

	$R = mysqli_query($GLOBALS['bd'], $S) or fd_bd_erreur($S);
	
	$D = mysqli_fetch_assoc($R);

	ec_htmlProteger($D);
	
	$nbErr = 0;
	$utiJours= $D['utiJours'];
	$_POST['hMin'] = $D['utiHeureMin'];
	$_POST['hMax'] = $D['utiHeureMax'];

	$utiJours = decbin($D['utiJours']);

	$n = mb_strlen($utiJours);
	if ($n < 7) {
		for ($i=0; $i < 7-$n ; $i++) { 
			$utiJours = '0'.$utiJours;
		}
	}

	$_POST['checkLundi'] = mb_substr($utiJours, 0, 1);
	$_POST['checkMardi'] = mb_substr($utiJours, 1, 1);
	$_POST['checkMercredi'] = mb_substr($utiJours, 2, 1);
	$_POST['checkJeudi'] = mb_substr($utiJours, 3, 1);
	$_POST['checkVendredi'] = mb_substr($utiJours, 4, 1);
	$_POST['checkSamedi'] = mb_substr($utiJours, 5, 1);
	$_POST['checkDimanche'] = mb_substr($utiJours, 6, 1);
	

} else {
	// On est dans la phase de soumission du formulaire :
	// => vérification des valeurs reçues et modification de l'affichage
	// Si aucune erreur n'est détectée, fdl_modification_affichage_calendrier()
	$erreurs2 = fdl_modification_affichage_calendrier();
	$nbErr2 = count($erreurs2);	
}

if (! isset($_POST['ajouter'])) {
	// => On intialise les zones de saisie.
	$nbErr3 = 0;
	
	$_POST['catNom1']='';
	$_POST['catFond1']='';
	$_POST['catBordure1']='';
	$_POST['catPublic1']=1;
					

} else {
	// On est dans la phase de soumission du formulaire :
	// => vérification des valeurs reçues
	// Si aucune erreur n'est détectée, fdl_ajout_categorie()
	$erreurs3 = fdl_ajout_categorie();
	$nbErr3 = count($erreurs3);	
}



echo '<div class="titreparam1 titreParametre"> Informations sur votre compte </div>';
	// Affichage du formulaire de modification utilisateur
	echo '<form class="newparamUtilisateur" method="POST" action="parametres.php">',
			'<table style="border: 1; cellpadding: 4; cellspacing: 0;">',
			fd_form_ligne('Nom ', 
				fd_form_input(APP_Z_TEXT,'txtNom', $_POST['txtNom'], 30),'','class="colonneGauche"','class="boutonIIAnnuler"'),
			
			fd_form_ligne('Mail ', fd_form_input(APP_Z_TEXT,'txtMail', $_POST['txtMail'], 30),'','class="colonneGauche"','class="boutonIIAnnuler"'),
			fd_form_ligne('Mot de passe ', fd_form_input(APP_Z_PASS,'txtPasse', $_POST['txtPasse'], 30),'','class="colonneGauche"','class="boutonIIAnnuler"'),
			fd_form_ligne('Retapez le mot de passe ', fd_form_input(APP_Z_PASS,'txtVerif', $_POST['txtVerif'], 30),'','class="colonneGauche"','class="boutonIIAnnuler"'),
						
			fd_form_ligne("<input type='submit' name='btnValider1' value=\"Mettre &agrave; jour\" size=15 class='boutonII'>", 
				"<input type='reset' name='btnEffacer1' value=\"Annuler\" size=15 class='boutonII'>",'','class="colonneGauche"','class="boutonIIAnnuler"'),
			'</table></form>';
	
//Confirmation de mise à jour	
if (isset($_POST['btnValider1']) && $alert == 0)
{
	echo '<div class="confirmationSave"> Utilisateur mis &agrave; jour avec succ&egrave;s ! </div>';
}			
			
echo '<div class="titreparam2 titreParametre"> Options d\'affichage du calendrier </div>';
	
	// Si il y a des erreurs on les affiche
	$alert2 = 0;
	if ($nbErr2 > 0) {
		$alert2=1;
		echo '<div class="aligncenter" style="padding-top:10px;"><strong>Les erreurs suivantes ont &eacute;t&eacute; d&eacute;tect&eacute;es</strong>';
		for ($i = 0; $i < $nbErr2; $i++) {
			echo '<br>', $erreurs2[$i];
		}
		echo '</div>';
	}

	$lundi = (($_POST['checkLundi'] == 1)?'checked':''); 
	$mardi = (($_POST['checkMardi'] == 1)?'checked':'');
	$mercredi = (($_POST['checkMercredi'] == 1)?'checked':'');
	$jeudi = (($_POST['checkJeudi'] == 1)?'checked':'');
	$vendredi = (($_POST['checkVendredi'] == 1)?'checked':'');
	$samedi = (($_POST['checkSamedi'] == 1)?'checked':'');
	$dimanche = (($_POST['checkDimanche'] == 1)?'checked':'');

	// Affichage du formulaire de modification des parametres du calendrier
	echo '<form class="newparamCalendrier" method="POST" action="parametres.php">',
			'<table style="border: 1; cellpadding: 4; cellspacing: 0;">',
			fd_form_ligne('Jours affich&eacute;s ', '<input type=\'checkbox\' name=\'checkLundi\' value=\'1\' '.$lundi.'> Lundi
												<input type=\'checkbox\' name=\'checkMardi\' value=\'1\' '.$mardi.'> Mardi
												<input type=\'checkbox\' name=\'checkMercredi\' value=\'1\' '.$mercredi.'> Mercredi
												<input type=\'checkbox\' name=\'checkJeudi\' value=\'1\' '.$jeudi.'> Jeudi
												<input type=\'checkbox\' name=\'checkVendredi\' value=\'1\' '.$vendredi.'> Vendredi
												<input type=\'checkbox\' name=\'checkSamedi\' value=\'1\' '.$samedi.'> Samedi
												<input type=\'checkbox\' name=\'checkDimanche\' value=\'1\' '.$dimanche.'> Dimanche',
												'','class="colonneGauche"','class="boutonIIAnnuler"'),
			
			fd_form_ligne('Heure minimale ', heure_min_max('hMin',$_POST['hMin']),'','class="colonneGauche"','class="boutonIIAnnuler"'),
			fd_form_ligne('Heure maximale ', heure_min_max('hMax',$_POST['hMax']),'','class="colonneGauche"','class="boutonIIAnnuler"'),

			
			fd_form_ligne("<input type='submit' name='btnValider2' value=\"Mettre &agrave; jour\" size=15 class='boutonII'>", 
				"<input type='reset' name='btnEffacer2' value=\"Annuler\" size=15 class='boutonII'>",'','class="colonneGauche"','class="boutonIIAnnuler"'),
			'</table></form>';
	
//Confirmation de mise à jour		
if ($alert2 == 0 && isset($_POST['btnValider2']))
{
	echo '<div class="confirmationSave"> Affichage agenda mis &agrave; jour avec succ&egrave;s ! </div>';
}			
	
			
			
echo '<div class="titreparam2 titreParametre"> Vos cat&eacute;gories </div>';
	
// test de soumission d'un bouton supprimer et suppression 
// de la catégorie et des rendezvous associés
if(isset($_POST['Supprimer'])){

	$S1= "DELETE FROM 	categorie 
						WHERE 	catID = '{$_POST['catID']}'";
	$R1 = mysqli_query($GLOBALS['bd'],$S1) or fd_bd_erreur($S1);
			
	$S2 = "DELETE FROM 	rendezvous
						WHERE 	rdvIDCategorie = '{$_POST['catID']}'";
	$R2 = mysqli_query($GLOBALS['bd'],$S2) or fd_bd_erreur($S2);
}
	
// test de soumission d'un bouton Delete et affichage
// de la demande de confirmation de suppression
if(isset($_POST['Delete'])){
	
	$S1 = "SELECT	count(*)
			FROM	categorie
			WHERE	catIDUtilisateur = '{$_SESSION['utiID']}'";
	$R1 = mysqli_query($GLOBALS['bd'],$S1) or fd_bd_erreur($S1);
	$D1=mysqli_fetch_row($R1);
	
	if($D1[0]<2){
		echo '<p class="confirmationSupp">Vous ne pouvez pas supprimer de cat&eacute;gories</p>';
	}else{
		echo '<form method="POST" action="parametres.php" class="confirmationSupp"><div> Supprimer la cat&eacute;gorie et les rendezvous et &eacute;v&egrave;nements associ&eacute;s : ',
				'<input type="submit" name="Supprimer" value="Supprimer" class="boutonII">',
				'<input type="hidden" name="catID" value="',$_POST['Delete'],'"></div></form>';
	}
}
	

// test de soumission d'un bouton save et test la validité des zones de formulaire 
// puis modification de la catégorie dans la base de donnée
if(isset($_POST['Save'])){
	
	echo '<div class="confirmationSave"> Cat&eacute;gorie mise &agrave; jour avec succ&egrave;s ! </div>';
			
	$err=array();
	
	if (!isset($_POST['catPublic'])){
		$_POST['catPublic']=0;
	}
			
	if (!preg_match('^[a-zA-Z0-9]{0,12}$^', $_POST['catNom'])){
		$err[]='Le nom saisi est invalide';
	}
	
	if (strlen($_POST['catFond'])>6){
		$err[]='Le code saisi pour la couleur du fond est invalide';
	}
	
	if (strlen($_POST['catBordure'])>6) {
		$err[]='Le code saisi pour la couleur des bordures est invalide';
	}
	
	if($err !=NULL){
		echo '<p class="aligncenter"><strong>Les erreurs suivantes ont &#233;t&#233; d&#233;tect&#233;es</strong><br>';

		foreach ($err as $cle => $valeur) {
			echo $valeur,'<br>';
		}

		echo'</p>';

	}
	else 
	{
		$catNom=mysqli_real_escape_string($GLOBALS['bd'], $_POST['catNom']);
		$catFond=mysqli_real_escape_string($GLOBALS['bd'], $_POST['catFond']);
		$catBordure=mysqli_real_escape_string($GLOBALS['bd'], $_POST['catBordure']);

		$S1="UPDATE 	categorie 
				SET 	catNom='$catNom',
						catCouleurFond='$catFond',
						catCouleurBordure='$catBordure',
						catIDutilisateur='".$_SESSION['utiID']."',
						catPublic='".$_POST['catPublic']."'
				WHERE 	catID = '".$_POST['Save']."'";
				
		$R1 = mysqli_query($GLOBALS['bd'],$S1) or fd_bd_erreur($S1);
		
	}
}

	// Si il y a des erreurs on les affiche
	if ($nbErr3 > 0) {
		echo '<div class="aligncenter" style="padding-top:10px;"><strong >Les erreurs suivantes ont &eacute;t&eacute; d&eacute;tect&eacute;es</strong>';
		for ($i = 0; $i < $nbErr3; $i++) {
			echo '<br>', $erreurs3[$i];
		}
		echo '</div>';
	}

	
	fd_bd_connexion();
	$S="SELECT 	catID,catNom,catCouleurFond,catCouleurBordure,catPublic
		FROM 	categorie 
		WHERE 	catIDUtilisateur = {$_SESSION['utiID']}";
	
	$R = mysqli_query($GLOBALS['bd'], $S) or fd_bd_erreur($S);

	// Affichage du formulaire de modification/suppression de catégories
	while($D = mysqli_fetch_assoc($R)){
		ec_htmlProteger ($D);
		aj_form_categorie($D['catID'],$D['catNom'],$D['catCouleurBordure'],$D['catCouleurFond'],$D['catPublic']);
	
	}
	mysqli_free_result($R);
	
	
	if (!isset($_POST['catPublic1'])) 
	{
		$_POST['catPublic1'] = 0;
	}

	//Afffichage du formulaire d'ajout de categories
	echo 	'<form class="newparamCategorie" method="POST" action="parametres.php">',
				'<table style="border: 1; cellpadding: 4; cellspacing: 0;">';
	

	echo 		fd_form_ligne('<p class="titreParametre">Nouvelle cat&eacute;gorie : </p>','','class=\'titreparam3\'','',''), 
				fd_form_ligne('Nom : '.fd_form_input(APP_Z_TEXT,"catNom1", $_POST['catNom1'], 6). 
							' Fond : '.fd_form_input(APP_Z_TEXT,"catFond1", $_POST['catFond1'], 3).
							' Bordure : '.fd_form_input(APP_Z_TEXT,"catBordure1", $_POST['catBordure1'], 3).
							fd_form_input('checkbox',"catPublic1", $_POST['catPublic1']).'Public',
							'<input type=\'submit\' name=\'ajouter\' value="Ajouter" size=15 class=\'boutonII\'>',
												'','class="colonneGauche"','class="boutonAjouter"');	
	
	echo 		'</table>
			</form>';		
			
	echo '</div>';

echo '</div>';
	fd_html_pied();	



						


		
	
	ob_end_flush();		
			
			
	/** 
	* Validation de la saisie et modification d'un utilisateur.
	*
	* Les zones reçues du formulaires de saisie sont vérifiées. Si
	* des erreurs sont détectées elles sont renvoyées sous la forme
	* d'un tableau. Si il n'y a pas d'erreurs,
	* une modification est faite pour l'utilisateur connecté.
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
		if($_POST['txtPasse'] != ''){
			$txtPasse = trim($_POST['txtPasse']);
			$long = mb_strlen($txtPasse, 'UTF-8');
			if ($long < 4 || $long > 20){
				$erreurs[] = 'Le mot de passe doit avoir de 4 &agrave; 20 caract&egrave;res';
			}

			$txtVerif = trim($_POST['txtVerif']);
			if ($txtPasse !== $txtVerif) {
				$erreurs[] = 'Le mot de passe est différent dans les 2 zones';
			}
			$mdp=1;
		}

		// Si il y a des erreurs, la fonction renvoie le tableau d'erreurs
		if (count($erreurs) > 0){
			return $erreurs;		// RETURN : des erreurs ont été détectées
		}

		//-----------------------------------------------------
		// Modification des paramètres du compte  
		//-----------------------------------------------------
		$txtNom = mysqli_real_escape_string($GLOBALS['bd'], $_POST['txtNom']);

		$txtMail = mysqli_real_escape_string($GLOBALS['bd'], $_POST['txtMail']);
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
		
}			



/**
	* Validation de la saisie et modification de l'affichage du calendrier.
	*
	* Les zones reçues du formulaires de saisie sont vérifiées. Si
	* des erreurs sont détectées elles sont renvoyées sous la forme
	* d'un tableau. Si il n'y a pas d'erreurs,une modification est faite dans la table utilisateur .
	* pour l'affichage du semainier.
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
}	

/**
	* Affichage du formulaire pour les categories.
	*
	* @param 	int		$ID			Id de la catégorie
	* @param string		$nom		Nom de la catégorie
	* @param string		$catCouleurBordure		Couleur de la bordure d'affichage de la catégorie
	* @param string		$catCouleurFond			Couleur de fond d'affichage de la catégorie
	* @param 	int		$public		Couleur de la bordure d'affichage de la catégorie
	*
	* @global array		$_POST		zones de saisie du formulaire
	* @global array		$_GLOBALS	base de bonnées 
	*/
function aj_form_categorie($ID,$nom,$catCouleurBordure,$catCouleurFond,$public) {

		//Modification de la couleur du texte Aperçu selon la couleur de fond du bloc
		$couleurHSL = ec_hexToHsl($catCouleurFond); 

		if ($couleurHSL[2] > 0.5) //0.5 = 50%  
		{
			$textColor = '000000';
		} 
		else
		{
			$textColor = 'FFFFFF';

		}
		
		echo	'<form class="newparamCategorie" method="POST" action="parametres.php" >',
		'<table style="border: 1; cellpadding: 4; cellspacing: 15;">',
			'<tr>',
				'<td>Nom :</td>',
				'<td class="catEspace"><input type="text" name="catNom" size="6"  maxlength="8" value="',$nom,'"></td>',
				'<td>Fond :</td>',
				'<td class="catEspace"><input type="text" name="catFond" size="3"  maxlength="8" value="',$catCouleurFond,'"></td>',
				'<td>Bordures :</td>',
				'<td class="catEspace"><input type="text" name="catBordure" size="3"  maxlength="8" value="',$catCouleurBordure,'"></td>',
				'<td class="catEspace"><input type="checkbox" name="catPublic" value="1"';
		if($public==1)
		{
			echo ' checked id="catPublic"';
		}
		echo	'><label>Public</label></td>',
				'<td><div style="border: solid 2px #',$catCouleurBordure,';background-color: #',$catCouleurFond,';font-size: 14px;color: #',$textColor,';width: 75px;height: 20px;text-align: center;">Aper&ccedil;u</div></td>',
				'<td style="padding-left: 20px;"><input type="submit" name="Save" value="',$ID,'" class="boutonCatSav"></td>',
				'<td style="padding-left: 20px;"><input type="submit" name="Delete" value="',$ID,'" class="boutonCatSupp"></td>',

			'</tr>',
		'</table>',
	'</form>';		
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
		$nom = trim($_POST['catNom1']);
		$nom = mysqli_real_escape_string($GLOBALS['bd'],$nom);
		$long = mb_strlen($nom, 'UTF-8');
		if($long==0){
			$erreurs[] = 'la cat&eacute;gorie n\'a pas de nom';
		}
		
		while($D = mysqli_fetch_assoc($R)){
			if($D['catNom']===$_POST['catNom1']){
				$erreurs[] = 'La cat&eacute;gorie existe d&eacute;j&agrave;';
			}
		}	
		mysqli_free_result($R);
		
		// Vérification de la couleur de fond
		$fond = trim($_POST['catFond1']);
		$fond = mysqli_real_escape_string($GLOBALS['bd'],$fond);
		$long = mb_strlen($fond, 'UTF-8');
		if ($long > 6){
			$erreurs[] = 'la couleur de fond est invalide';
		}
		if($long==0){
			$erreurs[] = 'la couleur de fond est vide';
		}	
		
		// Vérification de la couleur de bordure
		$bordure = trim($_POST['catBordure1']);
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
		
		if(!isset($_POST['catPublic1'])){
			$S = "INSERT INTO categorie SET
				catNom = '$nom',
				catCouleurFond = '$fond',
				catCouleurBordure = '$bordure',
				catIDUtilisateur = {$_SESSION['utiID']},
				catPublic = 0";

			$R = mysqli_query($GLOBALS['bd'], $S) or fd_bd_erreur($S);
		}
		else{
			$S = "INSERT INTO categorie SET
				catNom = '$nom',
				catCouleurFond = '$fond',
				catCouleurBordure = '$bordure',
				catIDUtilisateur = {$_SESSION['utiID']},
				catPublic = 1";

			$R = mysqli_query($GLOBALS['bd'], $S) or fd_bd_erreur($S);
		}

		// Déconnexion de la base de données
		mysqli_close($GLOBALS['bd']);
		
		header ('location: parametres.php');
		exit();
}	

	
		


?>
