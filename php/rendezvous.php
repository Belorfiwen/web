<?php

// Bufferisation des sorties
ob_start();
include('bibli_24sur7.php');	// Inclusion de la bibliothèque
session_start();
ec_verifie_session();

$GLOBALS['lienRendezVous']= $_SESSION['utiID'];

if (isset($_POST['btnValider']) || isset($_POST['btnDelete'])) 
{
	$idRdv = $_POST['idRdv'];
}
else
{
	$idRdv = $_GET['id'];
}

$jour = 0;
$mois = 0;
$annee = 0;
$heure = 0;

if (isset($_GET['jour'])) {
	$jour = $_GET['jour'];
}

if (isset($_GET['heure'])) {
	$heure = $_GET['heure'];
}

if (isset($_GET['mois'])) {
	$mois = $_GET['mois'];
}

if (isset($_GET['annee'])) {
	$annee = $_GET['annee'];
}


			
		if (!isset($_POST['btnValider']) && !isset($_POST['btnDelete']) && $idRdv == -1) 
		{
				// On n'est dans un premier affichage de la page dans le cas de la creation d'un rendezvous.
				// => On intialise les zones de saisie.
				$nbErr = 0;
				$_POST['txtLibelle']='';
				$_POST['rdvDate_a'] = date('Y');
				$_POST['rdvDate_m'] = $_POST['rdvDate_j'] = date('j');

				$_POST['rdvDeb_h']=7;
				$_POST['rdvFin_h']=12;
				$_POST['rdvDeb_m']=$_POST['rdvFin_m']=00;
				$idCat = -1;

				if (estEntier($annee) && $annee <= $_POST['rdvDate_a'] +5 && $annee >= $_POST['rdvDate_a'] -7) {
					$_POST['rdvDate_a'] = $annee;
				}
				if (estEntier($mois) && $mois <= 12 && $mois >= 1) {
					$_POST['rdvDate_m'] = $mois;
				}
				if (estEntier($jour) && $jour <= 31 && $jour >= 1 && checkdate($_POST['rdvDate_m'], $jour, $_POST['rdvDate_a'])) {
					$_POST['rdvDate_j'] = $jour;
				}
				if (isset($_GET['heure']) && estEntier($_GET['heure']) && $_GET['heure'] <= 24 && $_GET['heure'] >= 0) {
					$_POST['rdvDeb_h'] = $_GET['heure'];
					$_POST['rdvFin_h'] = $_GET['heure']+1;
				}
					$check = '';

		} 
		elseif (!isset($_POST['btnValider']) && !isset($_POST['btnDelete']) && $idRdv != -1) 
		{
			// On n'est dans un premier affichage de la page dans le cas de la modification d'un rendezvous.
			// => On intialise les zones de saisie.
			fd_bd_connexion();
			$sql = "SELECT	rdvDate, rdvHeureDebut, rdvHeureFin, rdvIDUtilisateur, rdvIDCategorie, rdvLibelle
					FROM	rendezvous
					WHERE 	rdvID = $idRdv";

			$R = mysqli_query($GLOBALS['bd'], $sql) or fd_bd_erreur($sql);
			$D = mysqli_fetch_assoc($R);
			ec_htmlProteger($D);

			$nbErr = 0;
			$_POST['txtLibelle']= $D['rdvLibelle'];

			if (isset($_GET['jour'])) 
			{
				$_POST['rdvDate_a'] = $annee = $_GET['annee'];
				$_POST['rdvDate_m'] = $mois = $_GET['mois'];
				$_POST['rdvDate_j'] = $jour = $_GET['jour'];
			}
			else
			{
				$_POST['rdvDate_a'] = $annee = mb_substr($D['rdvDate'], 0, 4);
				$_POST['rdvDate_m'] = $mois = mb_substr($D['rdvDate'], 4, 2);
				$_POST['rdvDate_j'] = $jour = mb_substr($D['rdvDate'], 6, 2);
			}

			$rdvDeb = (($D['rdvHeureDebut'] < 1000)?'0'.$D['rdvHeureDebut']:$D['rdvHeureDebut']);
			$_POST['rdvDeb_h'] = mb_substr($rdvDeb, 0, 2);
			$_POST['rdvDeb_m'] = mb_substr($rdvDeb, 2, 2);
			$rdvFin = (($D['rdvHeureFin'] < 1000)?'0'.$D['rdvHeureFin']:$D['rdvHeureFin']);
			$_POST['rdvFin_h'] = mb_substr($rdvFin, 0, 2);
			$_POST['rdvFin_m'] = mb_substr($rdvFin, 2, 2);
			$idCat = $D['rdvIDCategorie'];

			if ($D['rdvHeureFin'] == -1 ) 
			{
				$check = 'checked';
			}
			else
			{
				$check = '';
			}

		} 
		else
		{
		// On est dans la phase de soumission du formulaire :
		// => vérification des valeurs reçues et création utilisateur.
		// Si aucune erreur n'est détectée, fdl_add_rdv() ou fdl_modifie_rdv()
			$idCat = $_POST['idCat'];
			if (isset($_POST['rdvCheck'])) 
			{
				$check = 'checked';
			}
			else
			{
				$check = '';
			}

			if (isset($_POST['btnDelete'])) 
			{
		 		ecl_delete_rdv();
			}
			elseif ($idRdv == -1)
			{
				$erreurs = ecajl_add_rdv();
				$nbErr = count($erreurs);
			} 
			else 
			{
				$erreurs = ecajl_modifie_rdv();
				$nbErr = count($erreurs);
			}	
		}

fd_html_head('24sur7 | Rendez-vous');

fd_html_bandeau();
echo	'<div id="bcContenu">',
			'<aside id="bcGauche">';

fd_html_calendrier($jour, $mois, $annee,$idRdv);

ec_html_categorie($jour, $mois, $annee);
		
echo	
		'</aside>',
		'<div id="bcCentre">';
	// Si il y a des erreurs on les affiche
	if ($nbErr > 0) {
		echo '<strong>Les erreurs suivantes ont &eacute;t&eacute; d&eacute;tect&eacute;es</strong>';
		for ($i = 0; $i < $nbErr; $i++) {
			echo '<br>', $erreurs[$i];
		}
	}
	
	if ($idRdv == -1) 
	{
		$bouton1 = 'Ajouter';
		$bouton2 = "<input type='reset' name='btnEffacer' value=\"Annuler\" size=15 class='boutonII'>";
		echo '<div class="titrerdv">Nouvelle saisie </div>';
	}
	else
	{
		$bouton1 = 'Mettre &agrave; jour';
		$bouton2 = "<input type='submit' name='btnDelete' value=\"Supprimer\" size=15 class='boutonII'>";
		echo '<div class="titrerdv">Modification </div>';
	}

	// Affichage du formulaire
	echo '<form class="newrdv" method="POST" action="rendezvous.php">',
			'<table style="border: 1; cellpadding: 4; cellspacing: 0;">',
			fd_form_ligne('Libell&eacute; : ', 
				fd_form_input(APP_Z_TEXT,'txtLibelle', $_POST['txtLibelle'], 30),'','class="colonneGauche"','class="boutonIIAnnuler"'),
			
			 fd_form_ligne('Date : ', fd_form_date('rdvDate', $_POST['rdvDate_j'], $_POST['rdvDate_m'], $_POST['rdvDate_a']),'','class="colonneGauche"','class="boutonIIAnnuler"'),
			 fd_form_ligne('Cat&eacute;gorie : ', aj_recup_categorie($idCat),'','class="colonneGauche"','class="boutonIIAnnuler"'),
			 fd_form_ligne('Horaire D&eacute;but : ', fd_form_heure('rdvDeb',$_POST['rdvDeb_h'],$_POST['rdvDeb_m']),'','class="colonneGauche"','class="boutonIIAnnuler"'),
			 fd_form_ligne('Horaire Fin : ', fd_form_heure('rdvFin',$_POST['rdvFin_h'],$_POST['rdvFin_m']),'','class="colonneGauche"','class="boutonIIAnnuler"'),
			 fd_form_ligne('Ou ', '<input type=\'checkbox\' name=\'rdvCheck\' value=\'1\' '.$check.'> Evenement sur une journ&eacute;e','','class="colonneGauche"','class="boutonIIAnnuler"'),

			 fd_form_ligne("<input type='submit' name='btnValider' value=\"$bouton1\" size=15 class='boutonII'>", 
				$bouton2,'','class="colonneGauche"','class="boutonIIAnnuler"'),
			'</table>',
			'<input type="hidden" name="idRdv" value="',$idRdv,'">',
			'<input type="hidden" name="idCat" value="',$idCat,'">',
		 '</form>',

			'<p><a href="agenda.php"> Retour &agrave; l\'agenda </a></p>',
		'</div><div style="clear: both;"> </div>',
	'</div>';

	fd_html_pied();
	ob_end_flush();
			

	//=================== FIN DU SCRIPT =============================

	//_______________________________________________________________
	//
	//		FONCTIONS LOCALES
	//_______________________________________________________________

	
	
	/**
	* Récupération des catégorie que possède l'utilisateur.
	*
	* Recherche les categories presentes vers l'utilisateur connecté 
	* et crée une liste de selection pour choisir la categorie de rendezvous a ajouter
	*
	* @global array		$_SESSION		Id de l'utilisateur connecté
	* @global array		$_GLOBALS		base de bonnées 
	*
	* @return chaine 	chaine html d'une partie de formulaire
	*/
	
function aj_recup_categorie($idCat){
		
	fd_bd_connexion();
	$ch="";
	$ID = $_SESSION['utiID'];

	$S = "SELECT	catNom, catID
			FROM	categorie
			WHERE	'$ID' = catIDUtilisateur";

	$R = mysqli_query($GLOBALS['bd'], $S) or fd_bd_erreur($S);
	$ch=$ch.'<select name="rdvCat" >';
	$g=0;
	while($D = mysqli_fetch_assoc($R)){	
		ec_htmlProteger($D);
		if($g==0 && $idCat == -1){
			$ch=$ch.'<option value="'.$D['catID'].'" selected>'.$D['catNom'].'</option>';
			$g++;
		}
		elseif ($idCat == $D['catID']) {
			$ch=$ch.'<option value="'.$D['catID'].'" selected>'.$D['catNom'].'</option>';
		}
		else{
			$ch=$ch.'<option value="'.$D['catID'].'">'.$D['catNom'].'</option>';	
		}
	}
	$ch=$ch.'</select>';
	return $ch;
	mysqli_free_result($R);
	mysqli_close($GLOBALS['bd']);
			
}
	
	/**
	* Validation de la saisie et création d'un nouveau rendezvous.
	*
	* Les zones reçues du formulaires de saisie sont vérifiées. Si
	* des erreurs sont détectées elles sont renvoyées sous la forme
	* d'un tableau. Si il n'y a pas d'erreurs, un enregistrement est
	* créé dans la table rendez vous.
	*
	* @global array		$_POST		zones de saisie du formulaire
	* @global array		$_GLOBALS	base de bonnées 
	*
	* @return array 	Tableau des erreurs détectées
	*/
	function ecajl_add_rdv() {
		
		fd_bd_connexion();
		//-----------------------------------------------------
		// Vérification des zones
		//-----------------------------------------------------
		$erreurs = array();
		
		// Vérification de la date

		$jour = $_POST['rdvDate_j'];
		$mois = $_POST['rdvDate_m'];
		$annee = $_POST['rdvDate_a'];

		if (!checkdate($mois, $jour, $annee)) {
			$erreurs[] = 'La date de rendez-vous est invalide';
		}
		
		// Vérification de l'heure du rendez-vous
		$hDeb=$_POST['rdvDeb_h'];
		$mDeb=$_POST['rdvDeb_m'];
		$hFin=$_POST['rdvFin_h'];
		$mFin=$_POST['rdvFin_m'];
		
		if(! isset($_POST['rdvCheck'])){
		
			if($hDeb>$hFin){
				$erreurs[] = 'Dur&eacute;e du rendez-vous invalide';
			}
			
			if($hDeb==$hFin){
				if($mDeb+15>$mFin){
					$erreurs[] = 'Dur&eacute;e du rendez-vous invalide';
				}
			}
			
		}
		

		// test jours
		if($jour<10){
			$jour='0'.$jour;
		}
		
		if($mois<10){
			$mois='0'.$mois;
		}
		
		
		if($mDeb<10){
			$mDeb='0'.$mDeb;
		}
		
		
		if($mFin<10){
			$mFin='0'.$mFin;
		}
		
		$rdvDate=$annee.$mois.$jour;
		$rdvHDeb=$hDeb.$mDeb;
		$rdvHFin=$hFin.$mFin;
		$Cat=$_POST['rdvCat'];

		// Vérification du libellé
		$txtLibelle = trim($_POST['txtLibelle']);
		$long = mb_strlen($txtLibelle, 'UTF-8');
		if ($long ==0){
			$erreurs[] = 'Le nom doit avoir de 4 &agrave; 30 caract&egrave;res';
		}

		
		
		// Vérification du rendez-vous
		$countRdv = 0;
		$ID = $_SESSION['utiID'];

		$S = "SELECT	rdvDate, rdvHeureDebut, rdvHeureFin, rdvIDUtilisateur
				FROM	rendezvous
				WHERE	'$ID' = rdvIDUtilisateur
				AND		'$rdvDate' = rdvDate";

		$R = mysqli_query($GLOBALS['bd'], $S) or fd_bd_erreur($S);
		
		while($D = mysqli_fetch_assoc($R))
		{	
			if(isset($_POST['rdvCheck']) && $countRdv == 0)
			{
					$erreurs[] = 'Il y a un rendez-vous dans cette journ&eacute;e';
					$countRdv++;
			}
			if($D['rdvHeureDebut'] == -1)
			{
					$erreurs[] = 'Il y a un &eacute;v&egrave;nement sur cette journ&eacute;e';
			}
			else
			{

				if($D['rdvHeureDebut'] < $rdvHDeb){
					if($D['rdvHeureFin'] > $rdvHDeb)
					{
						$erreurs[] = 'Le rendez-vous commence pendant un autre en cours';
					}
				}
					
				if($D['rdvHeureDebut'] < $rdvHFin)
				{
					if($D['rdvHeureFin'] > $rdvHFin)
					{
						$erreurs[] = 'Le rendez-vous fini apres le debut d\'un autre';
					}
				}
					
				if(($D['rdvHeureDebut'] > $rdvHDeb)&&($D['rdvHeureFin']<$rdvHFin))
				{
					$erreurs[] = 'Le rendez-vous en remplace un autre';
				}	
			}
		}
		
		mysqli_free_result($R);

		
		


		// Si il y a des erreurs, la fonction renvoie le tableau d'erreurs
		if (count($erreurs) > 0) {
			return $erreurs;		// RETURN : des erreurs ont été détectées
		}

		//-----------------------------------------------------
		// Insertion d'un nouveau rendez-vous dans la base de données   
		//-----------------------------------------------------
		$txtLibelle = mysqli_real_escape_string($GLOBALS['bd'], $txtLibelle);

		$rdvDate=$annee.$mois.$jour;
		$rdvHDeb=$hDeb.$mDeb;
		$rdvHFin=$hFin.$mFin;
		$Cat=$_POST['rdvCat'];

			if(! isset($_POST['rdvCheck'])){
				$S = "INSERT INTO rendezvous SET
						rdvDate = '$rdvDate',
						rdvHeureDebut = '$rdvHDeb',
						rdvHeureFin = '$rdvHFin',
						rdvIDUtilisateur = {$_SESSION['utiID']},
						rdvIDCategorie = '$Cat',
						rdvLibelle = '$txtLibelle'";

				$R = mysqli_query($GLOBALS['bd'], $S) or fd_bd_erreur($S);
			}
			else{
				$S = "INSERT INTO rendezvous SET
						rdvDate = '$rdvDate',
						rdvHeureDebut = -1,
						rdvHeureFin = -1,
						rdvIDUtilisateur = {$_SESSION['utiID']},
						rdvIDCategorie = '$Cat',
						rdvLibelle = '$txtLibelle'";

				$R = mysqli_query($GLOBALS['bd'], $S) or fd_bd_erreur($S);
			}	

		
		// Déconnexion de la base de données
		mysqli_close($GLOBALS['bd']);
		
		header ("location: agenda.php?jour=$jour&mois=$mois&annee=$annee");
		exit();
	}
	
	
	
	
	
	/**
	* Validation de la saisie et modification d'un rendezvous.
	*
	* Les zones reçues du formulaires de saisie sont vérifiées. Si
	* des erreurs sont détectées elles sont renvoyées sous la forme
	* d'un tableau. Si il n'y a pas d'erreurs, une modification est faite si le rendez vous existe.
	*
	* @global array		$_POST		zones de saisie du formulaire
	* @global array		$_GLOBALS	base de bonnées 
	*
	* @return array 	Tableau des erreurs détectées
	*/
	function ecajl_modifie_rdv() {
		fd_bd_connexion();
		//-----------------------------------------------------
		// Vérification des zones
		//-----------------------------------------------------
		$erreurs = array();
		
		
		
		// Vérification de la date

		$jour = $_POST['rdvDate_j'];
		$mois = $_POST['rdvDate_m'];
		$annee = $_POST['rdvDate_a'];

		if (!checkdate($mois, $jour, $annee)) {
			$erreurs[] = 'La date de rendez-vous est invalide';
		}
		
		// Vérification de l'heure du rendez-vous
		$hDeb=$_POST['rdvDeb_h'];
		$mDeb=$_POST['rdvDeb_m'];
		$hFin=$_POST['rdvFin_h'];
		$mFin=$_POST['rdvFin_m'];
		
		if(! isset($_POST['rdvCheck'])){
		
			if($hDeb>$hFin){
				$erreurs[] = 'Dur&eacute;e du rendez-vous invalide';
			}
			
			if($hDeb==$hFin){
				if($mDeb+15>$mFin){
					$erreurs[] = 'Dur&eacute;e du rendez-vous invalide';
				}
			}
			
		}
		

		// test jours
		if($jour<10){
			$jour='0'.$jour;
		}
		
		if($mois<10){
			$mois='0'.$mois;
		}
		
		
		if($mDeb<10){
			$mDeb='0'.$mDeb;
		}
		
		
		if($mFin<10){
			$mFin='0'.$mFin;
		}
		
		$rdvDate=$annee.$mois.$jour;
		$rdvHDeb=$hDeb.$mDeb;
		$rdvHFin=$hFin.$mFin;
		$Cat=$_POST['rdvCat'];

		// Vérification du libellé
		$txtLibelle = trim($_POST['txtLibelle']);
		$long = mb_strlen($txtLibelle, 'UTF-8');
		if ($long ==0){
			$erreurs[] = 'Le nom doit avoir de 4 &agrave; 30 caract&egrave;res';
		}

		
		
		// Vérification du rendez-vous
		$countRdv = 0;
		$ID = $_SESSION['utiID'];

		$S = "SELECT	rdvDate, rdvHeureDebut, rdvHeureFin, rdvIDUtilisateur
				FROM	rendezvous
				WHERE	'$ID' = rdvIDUtilisateur
				AND		'$rdvDate' = rdvDate
				AND 	rdvID != {$_POST['idRdv']}";

		$R = mysqli_query($GLOBALS['bd'], $S) or fd_bd_erreur($S);
		
		while($D = mysqli_fetch_assoc($R))
		{	
			if(isset($_POST['rdvCheck']) && $countRdv == 0)
			{
					$erreurs[] = 'Il y a un rendez-vous dans cette journ&eacute;e';
					$countRdv++;
			}
			
			if($D['rdvHeureDebut'] == -1){
					$erreurs[] = 'Il y a un &eacute;v&egrave;nement sur cette journ&eacute;e';
			}
			else
			{

				if($D['rdvHeureDebut'] < $rdvHDeb)
				{
					if($D['rdvHeureFin'] > $rdvHDeb)
					{
						$erreurs[] = 'Le rendez-vous commence pendant un autre en cours';
					}
				}
					
				if($D['rdvHeureDebut'] < $rdvHFin)
				{
					if($D['rdvHeureFin'] > $rdvHFin)
					{
						$erreurs[] = 'Le rendez-vous fini apres le debut d\'un autre';
					}
				}
					
				if(($D['rdvHeureDebut'] > $rdvHDeb)&&($D['rdvHeureFin']<$rdvHFin))
				{
					$erreurs[] = 'Le rendez-vous en remplace un autre';
				}	
			}
		}
		
		mysqli_free_result($R);

		// Si il y a des erreurs, la fonction renvoie le tableau d'erreurs
		if (count($erreurs) > 0) {
			return $erreurs;		// RETURN : des erreurs ont été détectées
		}

		//-----------------------------------------------------
		// Insertion d'un nouveau rendez-vous dans la base de données   
		//-----------------------------------------------------
		$txtLibelle = mysqli_real_escape_string($GLOBALS['bd'], $txtLibelle);

		$rdvDate=$annee.$mois.$jour;
		$rdvHDeb=$hDeb.$mDeb;
		$rdvHFin=$hFin.$mFin;
		$Cat=$_POST['rdvCat'];
			
			if(! isset($_POST['rdvCheck'])){
				$S = "UPDATE rendezvous SET
					rdvDate = $rdvDate,
					rdvHeureDebut = $rdvHDeb,
					rdvHeureFin = $rdvHFin,
					rdvIDCategorie = $Cat,
					rdvLibelle = '$txtLibelle'
					WHERE rdvID = {$_POST['idRdv']}";
					
				$R = mysqli_query($GLOBALS['bd'], $S) or fd_bd_erreur($S);
			}
			else{
				$S = "UPDATE rendezvous SET
					rdvDate = $rdvDate,
					rdvHeureDebut = -1,
					rdvHeureFin = -1,
					rdvIDCategorie = $Cat,
					rdvLibelle = '$txtLibelle'
					WHERE rdvID = {$_POST['idRdv']}";

				$R = mysqli_query($GLOBALS['bd'], $S) or fd_bd_erreur($S);
			}	
				mysqli_free_result($R);
		// Déconnexion de la base de données
		mysqli_close($GLOBALS['bd']);
		
		header ("location: agenda.php?jour=$jour&mois=$mois&annee=$annee");
		exit();
	}



	/**
	* Validation de la saisie et suppression d'un rendezvous.
	*
	* une suppression est faite du rendezvous selectionné.
	*
	* @global array		$_POST		zones de saisie du formulaire
	* @global array		$_GLOBALS	base de bonnées 
	*
	*/
	function ecl_delete_rdv() {
		fd_bd_connexion();
		
		$jour = $_POST['rdvDate_j'];
		$mois = $_POST['rdvDate_m'];
		$annee = $_POST['rdvDate_a'];

		//-----------------------------------------------------
		// Insertion d'un nouveau rendez-vous dans la base de données   
		//-----------------------------------------------------

		$S = "DELETE FROM rendezvous
 			  WHERE rdvID = {$_POST['idRdv']}";

		$R = mysqli_query($GLOBALS['bd'], $S) or fd_bd_erreur($S);

		mysqli_free_result($R);
		// Déconnexion de la base de données
		mysqli_close($GLOBALS['bd']);
		
		header ("location: agenda.php?jour=$jour&mois=$mois&annee=$annee");
		exit();
	}
		
?>