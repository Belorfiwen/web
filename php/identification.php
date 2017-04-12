<?php


// Bufferisation des sorties
ob_start();

// Inclusion de la bibliothéque
include('bibli_24sur7.php');

fd_html_head('24sur7 | Connexion');

echo '<header id="bcEntete">',
			'<nav id="bcOnglets">',
			'</nav>',
			'<div id="bcLogo"></div>',
			'<a href="deconnexion.php" id="btnDeconnexion" title="Se d&eacute;connecter"></a>',
		 '</header>',
		 
		 '<section id="bcContenu">';

//-----------------------------------------------------
// Détermination de la phase de traitement :
// 1er affichage ou soumission du formulaire
//-----------------------------------------------------
if (! isset($_POST['btnValider'])) {
	// On n'est dans un premier affichage de la page.
	// => On intialise les zones de saisie.
	$nbErr = 0;
	$_POST['txtMail'] = '';
	$_POST['txtPasse'] = '';

} else {
	// On est dans la phase de soumission du formulaire :
	// => vérification des valeurs reçues et connexion de utilisateur.
	// Si aucune erreur n'est détectée, fdl_add_utilisateur()
	// redirige la page sur la page 'protegee.php'
	$erreurs = ecl_connect_utilisateur();
	$nbErr = count($erreurs);
}

if (isset($GLOBALS['bd'])){
    // Déconnexion de la base de données
    mysqli_close($GLOBALS['bd']);
}

//-----------------------------------------------------
// Affichage de la page
//-----------------------------------------------------
fd_html_head('24sur7 | Identification','-');

echo '<h2 id="titreII">Pour vous connecter, veuillez vous identifier</h2>';

// Si il y a des erreurs on les affiche
if ($nbErr > 0) {
	echo '<strong>Les erreurs suivantes ont été détectées</strong>';
	for ($i = 0; $i < $nbErr; $i++) {
		echo '<br>', $erreurs[$i];
	}
}

// Affichage du formulaire
echo '<div class="II"><form method="POST" action="identification.php">',
		'<table>',
		fd_form_ligne('Mail  ', 
            fd_form_input(APP_Z_TEXT,'txtMail', $_POST['txtMail'], 30)),
		fd_form_ligne('Mot de passe  ', 
            fd_form_input(APP_Z_PASS,'txtPasse', '', 30)),

        fd_form_ligne(fd_form_input(APP_Z_SUBMIT,'btnValider', 'S\'identifier', 15,'class="boutonII"'),
        	fd_form_input(APP_Z_RESET,'btnEffacer', 'Annuler', 15, 'class="boutonII" id="boutonIIAnnuler"')),
		'</table></form></div>',
		'<p class="basII"> Pas encore de compte ? <a href="inscription.php">Inscrivez-vous</a> sans plus tarder !</p>',
		'<p class="basII"> Vous hésitez à vous inscrire ? Laissez vous séduire par <a href="../html/presentation.html">une présentation</a> des possibilités de 24sur7</p></section>';
		
fd_html_pied();
ob_end_flush();

//=================== FIN DU SCRIPT =============================

//_______________________________________________________________
//
//		FONCTIONS LOCALES
//_______________________________________________________________

/**
* Validation de la saisie et connexion de utilisateur.
*
* Les zones reçues du formulaires de saisie sont vérifiées. Si
* des erreurs sont détectées elles sont renvoyées sous la forme
* d'un tableau. Si il n'y a pas d'erreurs, une session est ouverte et une
* redirection est effectuée.
*
* @global array		$_POST		zones de saisie du formulaire
*
* @return array 	Tableau des erreurs détectées
*/
function ecl_connect_utilisateur() {
	//-----------------------------------------------------
	// Vérification des zones
	//-----------------------------------------------------
	$erreurs = array();

	// Vérification du mail
	$txtMail = trim($_POST['txtMail']);
	if ($txtMail == '') {
		$erreurs[] = 'L\'adresse mail est obligatoire';
	} 

	// Vérification du mot de passe
	$txtPasse = trim($_POST['txtPasse']);
	if ($txtPasse == '') {
		$erreurs[] = 'Le mot de passe est obligatoire';
	}

	//-----------------------------------------------------
	// Verification des identifiants dans la bd
	//-----------------------------------------------------
	fd_bd_connexion();

	$passe = mysqli_real_escape_string($GLOBALS['bd'], md5($txtPasse));
	$mail = mysqli_real_escape_string($GLOBALS['bd'], $txtMail);

	$S = "SELECT	utiID
			FROM	utilisateur
			WHERE	utiMail = '$mail'
			AND 	utiPasse = '$passe'";

	$R = mysqli_query($GLOBALS['bd'], $S) or fd_bd_erreur($S);

	$D = mysqli_fetch_assoc($R);

	if ($D == NULL) 
	{
		$erreurs[] = 'Les identifiants sont incorects';
	}
	else
	{
		$utiID = $D['utiID'];
	}
	// Libère la mémoire associée au résultat $R
    mysqli_free_result($R);

	// Si il y a des erreurs, la fonction renvoie le tableau d'erreurs
	if (count($erreurs) > 0) {
		return $erreurs;		// RETURN : des erreurs ont été détectées
	}

	//-----------------------------------------------------
	// Si identifiants corrects, ouverture de la session et redirection vers la page Agenda
	//-----------------------------------------------------
	session_start();
	$_SESSION['utiID'] = $utiID;
	$_SESSION['utiMail'] = $mail;
	
	// Déconnexion de la base de données
    mysqli_close($GLOBALS['bd']);
	
	header ('location: agenda.php');
	exit();			// EXIT : le script est terminé
}

?>
