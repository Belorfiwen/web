<?php
/** @file
 * Page de recherche des utilisateurs de l'application 24sur7
 */
ob_start();
include('bibli_24sur7.php');	// Inclusion de la bibliothèque
session_start();
ec_verifie_session();

//-----------------------------------------------------
// Détermination de la phase de traitement :
// 1er affichage ou soumission du formulaire
//-----------------------------------------------------
if (! isset($_POST['btnRechercher'])) {
	// On n'est dans un premier affichage de la page.
	// => On intialise les zones de saisie.
	$_POST['recherche'] = '';
} 

if (isset($_POST['btnAbo'])) {
	ecl_abonnement();
}

fd_html_head('24sur7 | Recherche');

fd_html_bandeau(APP_PAGE_RECHERCHE);

echo '<div id="bcContenu">',
		'<div>',
			'<form method="POST" action="recherche.php">',
				'<div id="zoneRecherche">Entrez le crit&egrave;re de recherche : ',
				fd_form_input(APP_Z_TEXT,'recherche', $_POST['recherche'], 30),fd_form_input(APP_Z_SUBMIT,'btnRechercher', 'Rechercher', 15,'class="boutonII"'),
				'</div>',
			'</form>';

if (isset($_POST['btnRechercher'])) {
	ecl_recherche();
}

echo	'</div>',
	'</div>';

fd_html_pied();
ob_end_flush();


//=================== FIN DU SCRIPT =============================

//_______________________________________________________________
//
//		FONCTIONS LOCALES
//_______________________________________________________________

/**
*
* Recherche et affichage en fonction de la chaine saisie
* 
* @global array		$_POST		zone de saisie du formulaire
*
*/
function ecl_recherche() {

	//-----------------------------------------------------
	// Vérification des zones
	//-----------------------------------------------------
	$erreurs = array();

	// Vérification du mail
	$recherche = trim($_POST['recherche']);
	if ($recherche == '') {
		echo 'Erreur : Vous devez entrer une recherche';
		return;
	} 

	//-----------------------------------------------------
	// Si recherche corrects, requète pour rechercher dans la bd
	//-----------------------------------------------------
	fd_bd_connexion();

	$recherche = mysqli_real_escape_string($GLOBALS['bd'], $recherche);

	$S = "SELECT	utiID, utiNom, utiMail, s1.suiIDSuivi AS s1Suivi,s1.suiIDSuiveur AS s1Suiveur,s2.suiIDSuivi AS s2Suivi,s2.suiIDSuiveur AS s2Suiveur
			FROM utilisateur
			LEFT JOIN suivi AS s1
            ON s1.suiIDSuiveur = {$_SESSION['utiID']} AND s1.suiIDSuivi = utilisateur.utiID
            LEFT JOIN suivi AS s2
            ON s2.suiIDSuiveur = utilisateur.utiID AND s2.suiIDSuivi = {$_SESSION['utiID']}
            WHERE utilisateur.utiNom LIKE '%$recherche%' OR utilisateur.utiMail LIKE '%$recherche%'
            ORDER BY utilisateur.utiID";

	$R = mysqli_query($GLOBALS['bd'], $S) or fd_bd_erreur($S);

	if (mysqli_num_rows($R)) 
	{
		$count = 0;
		while ($D = mysqli_fetch_assoc($R)) {
			ec_htmlProteger($D);

			$color = '#E5ECF6';
			if ($count%2 == 0) {
				$color = '#9AC5E7';
			}

			$abonne = '';
			if ($D['s2Suivi'] != NULL) {
				$abonne = '[est abonn&eacute; &agrave; votre agenda]';
			}

			$libelleBtn = 'S\'abonner';
			$valueBtn = 1;
			if ($D['s1Suiveur'] != NULL) {
				$libelleBtn = 'Se d&eacute;sabonner';
				$valueBtn = 0;
			}

			echo '<form method="POST" action="recherche.php">',
				 '<input type="hidden" name="utiIDAbonne" value="',$D['utiID'],'">',
				 '<input type="hidden" name="valueBtn" value="',$valueBtn,'">',
				 '<p class="recherche" style="background-color:',$color,'">',$D['utiNom'],' - ',$D['utiMail'],' ',$abonne,'<input type="submit" name="btnAbo" value="',$libelleBtn,'" size=15 class="boutonII boutonRA"></p></form>';
			$count++;
		}
	}
	else
	{
		echo '<p class ="recherche" style="text-align:center;">Aucun resultat trouv&eacute;.</p>';
	}
	// Libère la mémoire associée au résultat $R
    mysqli_free_result($R);
	
	// Déconnexion de la base de données
    mysqli_close($GLOBALS['bd']);

}

	/**
	* 
	* Supression ou ajout d'un abonnement
	*
	* @global array		$_POST		zones de saisie du formulaire
	* @global array		$_GLOBALS	base de bonnées 
	*
	*/
	function ecl_abonnement() {
		fd_bd_connexion();

		//-----------------------------------------------------
		// supression ou ajout d'un suivi dans la base de données   
		//-----------------------------------------------------

		
			if($_POST['valueBtn'] == 1){
				$S = "INSERT INTO suivi SET
						suiIDSuiveur = {$_SESSION['utiID']},
						suiIDSuivi = {$_POST['utiIDAbonne']}";

				$R = mysqli_query($GLOBALS['bd'], $S) or fd_bd_erreur($S);
			}
			else{
				$S = "DELETE FROM suivi
 			 		  WHERE suiIDSuiveur = {$_SESSION['utiID']}
 			  		  AND   suiIDSuivi = {$_POST['utiIDAbonne']}";

				$R = mysqli_query($GLOBALS['bd'], $S) or fd_bd_erreur($S);
			}	

		
		// Déconnexion de la base de données
		mysqli_close($GLOBALS['bd']);

	}

?>