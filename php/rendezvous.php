<?php

// Bufferisation des sorties
ob_start();
session_start();
include('bibli_24sur7.php');	// Inclusion de la bibliothéque

$jour = 0;
$mois = 0;
$annee = 0;

if (isset($_GET['jour'])) {
	$jour = $_GET['jour'];
}

if (isset($_GET['mois'])) {
	$mois = $_GET['mois'];
}

if (isset($_GET['annee'])) {
	$annee = $_GET['annee'];
}

fd_html_head('24sur7 | Rendez-vous');

fd_html_bandeau();

echo '<section id="bcContenu">',
		'<aside id="bcGauche">';

fd_html_calendrier($jour, $mois, $annee);

ec_html_categorie();
		
echo	
		'</aside>',
		'<section id="bcCentre">';
			
			
		if (! isset($_POST['btnValider'])) {
				// On n'est dans un premier affichage de la page.
				// => On intialise les zones de saisie.
				$nbErr = 0;
				$_POST['txtLibelle']='';
				$_POST['rdvDate_a'] = date('Y');
				$_POST['rdvDate_m'] = $_POST['rdvDate_j'] = 1;
				$_POST['rdvDeb_h']=7;
				$_POST['rdvFin_h']=12;
				$_POST['rdvDeb_m']=$_POST['rdvFin_m']=00;

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

		} else {
		// On est dans la phase de soumission du formulaire :
		// => vérification des valeurs reçues et création utilisateur.
		// Si aucune erreur n'est détectée, fdl_add_utilisateur()
		// redirige la page sur la page 'protegee.php'
		$erreurs = fdl_add_rdv();
		$nbErr = count($erreurs);
	}


	// Si il y a des erreurs on les affiche
	if ($nbErr > 0) {
		echo '<strong>Les erreurs suivantes ont été détectées</strong>';
		for ($i = 0; $i < $nbErr; $i++) {
			echo '<br>', $erreurs[$i];
		}
	}

	if ($_GET['mode'] == -1) {
		echo '<div class="titrerdv">Nouvelle saisie </div>';
	}
	else
	{
		echo '<div class="titrerdv">Modification </div>';
	}

	// Affichage du formulaire
	echo '<form class="newrdv" method="POST" action="rendezvous.php">',
			'<table border="1" cellpadding="4" cellspacing="0">',
			fd_form_ligne('Libellé : ', 
				fd_form_input(APP_Z_TEXT,'txtLibelle', $_POST['txtLibelle'], 30),'','class="colonneGauche"','class="boutonIIAnnuler"'),
			
			 fd_form_ligne('Date : ', fd_form_date('rdvDate', $_POST['rdvDate_j'], $_POST['rdvDate_m'], $_POST['rdvDate_a']),'','class="colonneGauche"','class="boutonIIAnnuler"'),
			 fd_form_ligne('Catégorie : ', recup_categorie(),'','class="colonneGauche"','class="boutonIIAnnuler"'),
			 fd_form_ligne('Horaire Début : ', fd_form_heure('rdvDeb',$_POST['rdvDeb_h'],$_POST['rdvDeb_h']),'','class="colonneGauche"','class="boutonIIAnnuler"'),
			 fd_form_ligne('Horaire Fin : ', fd_form_heure('rdvFin',$_POST['rdvFin_h'],$_POST['rdvFin_m']),'','class="colonneGauche"','class="boutonIIAnnuler"'),
			 fd_form_ligne('Ou ', '<input type=\'checkbox\' name=\'rdvCheck\' value=\'1\'> Evenement sur une journée','','class="colonneGauche"','class="boutonIIAnnuler"'),

			 fd_form_ligne("<input type='submit' name='btnValider' value=\"Mettre à jour\" size=15 class='boutonII'>", 
				"<input type='reset' name='btnEffacer' value=\"Supprimer\" size=15 class='boutonII' class='boutonIIAnnuler'>",'','class="colonneGauche"','class="boutonIIAnnuler"'),
			'</table></form>',

			'<p><a href="agenda.php"> Retour à l\'agenda </a></p>',
		'</section><div style="clear: both;"> </div>',
	'</section>';

	fd_html_pied();
			

	

	//=================== FIN DU SCRIPT =============================

	//_______________________________________________________________
	//
	//		FONCTIONS LOCALES
	//_______________________________________________________________

	
	
	/**
	* Ajout d'une selection dans un formulaire.
	*
	* Recherche les categories presentes vers l'utilisateur connecté 
	* et crée une liste de selection pour choisir la categorie de rendezvous a ajouter
	*
	* @global array		$_SESSION		Id de l'utilisateur connecté
	* @global array		$_GLOBALS		base de bonnées 
	*
	* @return chaine 	chaine html d'une partie de formulaire
	*/
	
	function recup_categorie(){
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
				if($g==0){
					$ch=$ch.'<option value="'.$D['catID'].'" selected>'.$D['catNom'].'</option>';
					$g++;
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
	* Validation de la saisie et création d'un nouvel utilisateur.
	*
	* Les zones reçues du formulaires de saisie sont vérifiées. Si
	* des erreurs sont détectées elles sont renvoyées sous la forme
	* d'un tableau. Si il n'y a pas d'erreurs, un enregistrement est
	* créé dans la table utilisateur, une session est ouverte et une
	* redirection est effectuée.
	*
	* @global array		$_POST		zones de saisie du formulaire
	*
	* @return array 	Tableau des erreurs détectées
	*/
	function fdl_add_rdv() {
		
		fd_bd_connexion();
		
		$ret = mysqli_set_charset($GLOBALS['bd'], "utf8");
        if ($ret == FALSE){
            fd_bd_erreurExit('Erreur lors du chargement du jeu de caractères utf8');
        }
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
				$erreurs[] = 'Durée du rendez-vous invalide';
			}
			
			if($hDeb==$hFin){
				if($mDeb+15>$mFin){
					$erreurs[] = 'Durée du rendez-vous invalide';
				}
			}
			
			if($hDeb+1==$hFin){
				$test=1;
				if($mDeb==0){
					$test=0;
				}
				else{
					$test=60-$mDeb;
				}
				$test2=$mFin;
				
				if($test+$test2<15){
					$erreurs[] = 'Durée du rendez-vous inferieur a 15min';
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
			$erreurs[] = 'Le nom doit avoir de 4 à 30 caractères';
		}

		
		
		// Vérification du rendez-vous
		
		$ID = $_SESSION['utiID'];

		$S = "SELECT	rdvDate, rdvHeureDebut, rdvHeureFin, rdvIDUtilisateur
				FROM	rendezvous
				WHERE	'$ID' = rdvIDUtilisateur
				AND		'$rdvDate' = rdvDate";

		$R = mysqli_query($GLOBALS['bd'], $S) or fd_bd_erreur($S);
		
		while($D = mysqli_fetch_assoc($R)){	
			if($D['rdvHeureDebut'] <= $rdvHDeb){
				if($D['rdvHeureFin'] >= $rdvHDeb){
					$erreurs[] = 'Le rendez-vous commence pendant un autre en cours';
				}
			}
				
			if($D['rdvHeureDebut'] <= $rdvHFin){
				if($D['rdvHeureFin'] >= $rdvHFin){
					$erreurs[] = 'Le rendez-vous fini apres le debut d\'un autre';
				}
			}
				
			if(($D['rdvHeureDebut'] >= $rdvHDeb)&&($D['rdvHeureFin']<=$rdvHFin)){
				$erreurs[] = 'Le rendez-vous en remplace un autre';
			}	
		}
		
		mysqli_free_result($R);

		
		


		// Si il y a des erreurs, la fonction renvoie le tableau d'erreurs
		if (count($erreurs) > 0) {
			return $erreurs;		// RETURN : des erreurs ont été détectées
		}

		//-----------------------------------------------------
		// Insertion d'un nouveau rendez-vous dans la base de données       ========> A finir 
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
		
		header ('location: agenda.php');
		exit();
	}
	
ob_end_flush();
?>