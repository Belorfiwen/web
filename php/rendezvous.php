<?php
/** @file
 * Page d'accueil de l'application 24sur7
 *
 * @author : Frederic Dadeau - frederic.dadeau@univ-fcomte.fr
 */
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
		$erreurs = fdl_add_utilisateur();
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

	// Affichage du formulaire
	echo '<form class="newrdv" method="POST" action="rendezvous.php">',
		'<fielset>',
			'<legend> Modification </legend>',
			'<table border="1" cellpadding="4" cellspacing="0">',
			fd_form_ligne('Libellé : ', 
				fd_form_input(APP_Z_TEXT,'txtLibelle', $_POST['txtLibelle'], 30)),
			
			 fd_form_ligne('Date : ', fd_form_date('rdvDate', 1, 1, 2017)),
			 fd_form_ligne('Catégorie : ', recup_categorie()),
			 fd_form_ligne('Horaire Début : ', fd_form_heure('rdvDeb',7,0)),
			 fd_form_ligne('Horaire Fin : ', fd_form_heure('rdvFin',12,0)),
			 fd_form_ligne('Ou ', '<input type=\'checkbox\' name=\'rdvCheck\' value=\'1\'> Evenement sur une journée'),

			 fd_form_ligne("<input type='submit' name='btnValider' value=\"Mettre à jour\" size=15 class='boutonII'>", 
				"<input type='reset' name='btnEffacer' value=\"Supprimer\" size=15 class='boutonII' id='boutonIIAnnuler'>"),
			'</table></fieldset></form>';
			

	ob_end_flush();

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
	function fdl_add_utilisateur() {
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
			$erreurs[] = 'Le nom doit avoir de 4 à 30 caractères';
		}

		// Vérification du mail
		$txtMail = trim($_POST['txtMail']);
		if ($txtMail == '') {
			$erreurs[] = 'L\'adresse mail est obligatoire';
		} elseif (mb_strpos($txtMail, '@', 0, 'UTF-8') === FALSE
				|| mb_strpos($txtMail, '.', 0, 'UTF-8') === FALSE)
		{
			$erreurs[] = 'L\'adresse mail n\'est pas valide';
		} else {
			// Vérification que le mail n'existe pas dans la BD
			fd_bd_connexion();
			
			$ret = mysqli_set_charset($GLOBALS['bd'], "utf8");
			if ($ret == FALSE){
				fd_bd_erreurExit('Erreur lors du chargement du jeu de caractères utf8');
			}

			$mail = mysqli_real_escape_string($GLOBALS['bd'], $txtMail);

			$S = "SELECT	count(*)
					FROM	utilisateur
					WHERE	utiMail = '$mail'";

			$R = mysqli_query($GLOBALS['bd'], $S) or fd_bd_erreur($S);

			$D = mysqli_fetch_row($R);

			if ($D[0] > 0) {
				$erreurs[] = 'Cette adresse mail est déjà inscrite.';
			}
			// Libère la mémoire associée au résultat $R
			mysqli_free_result($R);
		}

		// Vérification du mot de passe
		$txtPasse = trim($_POST['txtPasse']);
		$long = mb_strlen($txtPasse, 'UTF-8');
		if ($long < 4
		|| $long > 20)
		{
			$erreurs[] = 'Le mot de passe doit avoir de 4 à 20 caractères';
		}

		$txtVerif = trim($_POST['txtVerif']);
		if ($txtPasse != $txtVerif) {
			$erreurs[] = 'Le mot de passe est différent dans les 2 zones';
		}


		// Si il y a des erreurs, la fonction renvoie le tableau d'erreurs
		if (count($erreurs) > 0) {
			return $erreurs;		// RETURN : des erreurs ont été détectées
		}

		//-----------------------------------------------------
		// Insertion d'un nouvel utilisateur dans la base de données
		//-----------------------------------------------------
		$txtPasse = mysqli_real_escape_string($GLOBALS['bd'], md5($txtPasse));
		$nom = mysqli_real_escape_string($GLOBALS['bd'], $txtNom);
		$txtMail = mysqli_real_escape_string($GLOBALS['bd'], $txtMail);
		$utiDateInscription = date('Ymd');

		$S = "INSERT INTO utilisateur SET
				utiNom = '$nom',
				utiPasse = '$txtPasse',
				utiMail = '$txtMail',
				utiDateInscription = $utiDateInscription,
				utiJours = 127,
				utiHeureMin = 6,
				utiHeureMax = 22";

		$R = mysqli_query($GLOBALS['bd'], $S) or fd_bd_erreur($S);

		//-----------------------------------------------------
		// Ouverture de la session et redirection vers la page protégée
		//-----------------------------------------------------
		
		$_SESSION['utiID'] = mysqli_insert_id($GLOBALS['bd']);
		$_SESSION['utiMail'] = $txtMail;
		
		// Déconnexion de la base de données
		mysqli_close($GLOBALS['bd']);
		
		header ('location: agenda.php');
		exit();			// EXIT : le script est terminé
	}
			
			
			
			
			
			
		echo '</section>',
	'</section>';

fd_html_pied();
?>