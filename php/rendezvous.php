<?php
/** @file
 * Page d'accueil de l'application 24sur7
 *
 * @author : Frederic Dadeau - frederic.dadeau@univ-fcomte.fr
 */
// Bufferisation des sorties
ob_start();
session_start();
include('bibli_24sur7.php');	// Inclusion de la bibliothéque

fd_html_head('24sur7 | Agenda');

fd_html_bandeau();

echo '<section id="bcContenu">',
		'<aside id="bcGauche">';

fd_html_calendrier(7, 1, 2017);

echo		'<section id="categories">',
				'Ici : bloc catégories pour afficher les catégories de rendez-vous',
			'</section>',
		'</aside>',
		'<section id="bcCentre">';
			
			
		if (! isset($_POST['btnValider'])) {
				// On n'est dans un premier affichage de la page.
				// => On intialise les zones de saisie.
				$nbErr = 0;
				$_POST['txtLibelle']='';
				$_POST['rdvDate_a'] = 2000;
				$_POST['rdvDate_m'] = $_POST['rdvDate_j'] = 1;
				$_POST['rdvDeb_h']=7;
				$_POST['rdvFin_h']=12;
				$_POST['rdvDeb_m']=$_POST['rdvFin_m']=00;

		} else {
		// On est dans la phase de soumission du formulaire :
		// => vérification des valeurs reçues et création utilisateur.
		// Si aucune erreur n'est détectée, fdl_add_utilisateur()
		// redirige la page sur la page 'protegee.php'
		$erreurs = fdl_add_rdv();
		$nbErr = count($erreurs);
	}

	if (isset($GLOBALS['bd'])){
		// Déconnexion de la base de données
		mysqli_close($GLOBALS['bd']);
	}



	// Si il y a des erreurs on les affiche
	if ($nbErr > 0) {
		echo '<strong>Les erreurs suivantes ont été détectées</strong>';
		for ($i = 0; $i < $nbErr; $i++) {
			echo '<br>', $erreurs[$i];
		}
	}

	echo '<div class="titrerdv"> Modification </div>';
	// Affichage du formulaire
	echo '<form class="newrdv" method="POST" action="rendezvous.php">',
			'<table border="1" cellpadding="4" cellspacing="0">',
			fd_form_ligne('Libellé : ', 
				fd_form_input(APP_Z_TEXT,'txtLibelle', $_POST['txtLibelle'], 30),'','class="colonneGauche"','class="boutonIIAnnuler"'),
			
			 fd_form_ligne('Date : ', fd_form_date('rdvDate', 1, 1, 2017),'','class="colonneGauche"','class="boutonIIAnnuler"'),
			 fd_form_ligne('Catégorie : ', recup_categorie(),'','class="colonneGauche"','class="boutonIIAnnuler"'),
			 fd_form_ligne('Horaire Début : ', fd_form_heure('rdvDeb',7,0),'','class="colonneGauche"','class="boutonIIAnnuler"'),
			 fd_form_ligne('Horaire Fin : ', fd_form_heure('rdvFin',12,0),'','class="colonneGauche"','class="boutonIIAnnuler"'),
			 fd_form_ligne('Ou ', '<input type=\'checkbox\' name=\'rdvCheck\' value=\'1\'> Evenement sur une journée','','class="colonneGauche"','class="boutonIIAnnuler"'),

			 fd_form_ligne("<input type='submit' name='btnValider' value=\"Mettre à jour\" size=15 class='boutonII'>", 
				"<input type='reset' name='btnEffacer' value=\"Supprimer\" size=15 class='boutonII' class='boutonIIAnnuler'>",'','class="colonneGauche"','class="boutonIIAnnuler"'),
			'</table></form>';
			

	

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

			$S = "SELECT	catNom
					FROM	categorie
					WHERE	'$ID' = catIDUtilisateur";

			$R = mysqli_query($GLOBALS['bd'], $S) or fd_bd_erreur($S);
			$ch=$ch.'<select name="rdvCat" >';
			$g=0;
			while($D = mysqli_fetch_assoc($R)){	
			ec_htmlProteger($D);
				if($g==0){
					$ch=$ch.'<option value="'.$D['catNom'].'" selected>'.$D['catNom'].'</option>';
					$g++;
				}
				else{
					$ch=$ch.'<option value="'.$D['catNom'].'">'.$D['catNom'].'</option>';	
				}
			}
			$ch=$ch.'</select>';
			return $ch;
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
		//-----------------------------------------------------
		// Vérification des zones
		//-----------------------------------------------------
		$erreurs = array();

		// Vérification du libellé
		$txtLibelle = trim($_POST['txtLibelle']);
		$long = mb_strlen($txtLibelle, 'UTF-8');
		if ($long ==0){
			$erreurs[] = 'Le nom doit avoir de 4 à 30 caractères';
		}

		// Vérification de la date
		// Vérification des paramètres
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
			
			if($hdeb+1==$hFin){
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
		
		


		// Si il y a des erreurs, la fonction renvoie le tableau d'erreurs
		if (count($erreurs) > 0) {
			return $erreurs;		// RETURN : des erreurs ont été détectées
		}

		//-----------------------------------------------------
		// Insertion d'un nouvel utilisateur dans la base de données       ========> A finir 
		//-----------------------------------------------------
		$txtLibelle = mysqli_real_escape_string($GLOBALS['bd'], $txtLibelle);
		$rdvDate=(int)($jour.$mois.$annee);
		$rdvHDeb=(int)($hDeb.$mDeb);
		$rdvHFin=(int)($hFin.$mFin);
		
		$S = "INSERT INTO rendezvous SET
				rdvDate = '$rdvDate',
				rdvHeureDebut = '$rdvHDeb',
				rdvHeureFin = '$rdvHFin',
				rdvLibelle = '$txtLibelle'";

		$R = mysqli_query($GLOBALS['bd'], $S) or fd_bd_erreur($S);

		
		// Déconnexion de la base de données
		mysqli_close($GLOBALS['bd']);
		
	
		exit();
	}
			
			
			
			
			
			
		echo '<p><a href="agenda.php"> Retour à l\'agenda </a></p>',
			'</section>',
	'</section>';

fd_html_pied();
ob_end_flush();
?>