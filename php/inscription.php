<?php


// Bufferisation des sorties
ob_start();

// Inclusion de la bibliothèque
include('bibli_24sur7.php');

//-----------------------------------------------------
// Détermination de la phase de traitement :
// 1er affichage ou soumission du formulaire
//-----------------------------------------------------
if (! isset($_POST['btnValider'])) {
	// On n'est dans un premier affichage de la page.
	// => On intialise les zones de saisie.
	$nbErr = 0;
	$_POST['txtNom'] = $_POST['txtMail'] = '';
	$_POST['txtVerif'] = $_POST['txtPasse'] = '';
	$_POST['selDate_a'] = 2000;
	$_POST['selDate_m'] = $_POST['selDate_j'] = 1;

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

//-----------------------------------------------------
// Affichage de la page
//-----------------------------------------------------

fd_html_head('24sur7 | Inscription');

echo 	'<header id="bcEntete">',
			'<nav id="bcOnglets">',
			'</nav>',
			'<div id="bcLogo"></div>',
			'<a href="deconnexion.php" id="btnDeconnexion" title="Se d&eacute;connecter"></a>',
		 '</header>',
		 
		 '<div id="bcContenu">',

			'<h2 id="titreII">Pour vous inscrire &agrave; <strong>24sur7</strong>, veuillez remplir le formulaire ci-dessous.</h2>';

// Si il y a des erreurs on les affiche
if ($nbErr > 0) {
	echo '<strong>Les erreurs suivantes ont &eacute;t&eacute; d&eacute;tect&eacute;es</strong>';
	for ($i = 0; $i < $nbErr; $i++) {
		echo '<br>', $erreurs[$i];
	}
}

// Affichage du formulaire
echo '<div class="II"><form method="POST" action="inscription.php">',
		'<table style="border: 1; cellpadding: 4; cellspacing: 0;">',
		fd_form_ligne('Nom  ', 
            fd_form_input(APP_Z_TEXT,'txtNom', $_POST['txtNom'], 30),'','class="colonneGauche"'),
		fd_form_ligne('Email  ', 
            fd_form_input(APP_Z_TEXT,'txtMail', $_POST['txtMail'], 30),'','class="colonneGauche"'),
		fd_form_ligne('Mot de passe  ', 
            fd_form_input(APP_Z_PASS,'txtPasse', '', 30),'','class="colonneGauche"'),
        fd_form_ligne('Retapez le mot de passe  ', 
            fd_form_input(APP_Z_PASS,'txtVerif', '', 30),'','class="colonneGauche"'),

        fd_form_ligne(fd_form_input(APP_Z_SUBMIT,'btnValider', 'S\'inscrire', 15,'class="boutonII"'),
        	fd_form_input(APP_Z_RESET,'btnEffacer', 'Annuler', 15, 'class="boutonII"'),'','class="colonneGauche"','class="boutonIIAnnuler"'),
		'</table></form></div>',
		'<p class="basII"> D&eacute;j&agrave; inscris ? <a href="identification.php">Identifiez-vous !</a> </p>',
		'<p class="basII"> Vous h&eacute;sitez &agrave; vous inscrire ? Laissez vous s&eacute;duire par <a href="../html/presentation.html">une pr&eacute;sentation</a> des possibilit&eacute;s de 24sur7</p></div>';
		
	fd_html_pied();
	ob_end_flush();

//=================== FIN DU SCRIPT =============================

//_______________________________________________________________
//
//		FONCTIONS LOCALES
//_______________________________________________________________

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
	if ($long < 4 || $long > 30){
		$erreurs[] = 'Le nom doit avoir de 4 &agrave; 30 caract&egrave;res';
	}

	// Vérification du mail
	$txtMail = trim($_POST['txtMail']);
	if ($txtMail == '') {
		$erreurs[] = 'L\'adresse mail est obligatoire';
	} elseif (mb_strpos($txtMail, '@', 0, 'UTF-8') === FALSE || mb_strpos($txtMail, '.', 0, 'UTF-8') === FALSE){
		$erreurs[] = 'L\'adresse mail n\'est pas valide';
	} else {
		// Vérification que le mail n'existe pas dans la BD
		fd_bd_connexion();

		$mail = mysqli_real_escape_string($GLOBALS['bd'], $txtMail);

		$S = "SELECT	count(*)
				FROM	utilisateur
				WHERE	utiMail = '$mail'";

		$R = mysqli_query($GLOBALS['bd'], $S) or fd_bd_erreur($S);

		$D = mysqli_fetch_row($R);

		if ($D[0] > 0) {
			$erreurs[] = 'Cette adresse mail est d&eacute;j&agrave; inscrite.';
		}
		// Libère la mémoire associée au résultat $R
        mysqli_free_result($R);
	}

	// Vérification du mot de passe
	$txtPasse = trim($_POST['txtPasse']);
	$long = mb_strlen($txtPasse, 'UTF-8');
	if ($long < 4 || $long > 20){
		$erreurs[] = 'Le mot de passe doit avoir de 4 &agrave; 20 caract&egrave;res';
	}

	$txtVerif = trim($_POST['txtVerif']);
	if ($txtPasse != $txtVerif) {
		$erreurs[] = 'Le mot de passe est diff&eacute;rent dans les 2 zones';
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

	$idInscr = mysqli_insert_id($GLOBALS['bd']);

	$S = "INSERT INTO categorie (catNom,catCouleurFond,catCouleurBordure,catIDUtilisateur,catPublic)
  		VALUES ('Défaut','FFFFFF','000000',(SELECT utiID FROM utilisateur ORDER BY utiID DESC LIMIT 1),0)";

  	$R = mysqli_query($GLOBALS['bd'], $S) or fd_bd_erreur($S);

	//-----------------------------------------------------
	// Ouverture de la session et redirection vers la page protégée
	//-----------------------------------------------------
	session_start();
	$_SESSION['utiID'] = $idInscr;
	$_SESSION['utiMail'] = $txtMail;
	
	// Déconnexion de la base de données
    mysqli_close($GLOBALS['bd']);
	
	header ('location: agenda.php');
	exit();			// EXIT : le script est terminé
}

?>
