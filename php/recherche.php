<?php
/** @file
 * Page d'accueil de l'application 24sur7
 *
 * @author : Frederic Dadeau - frederic.dadeau@univ-fcomte.fr
 */
ob_start();
include('bibli_24sur7.php');	// Inclusion de la bibliothéque
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

fd_html_head('24sur7 | Recherche');

fd_html_bandeau(APP_PAGE_RECHERCHE);

echo '<section id="bcContenu">',
		'<section>',
			'<form method="POST" action="recherche.php">',
				'<div id="zoneRecherche">Entrez le crit&egrave;re de recherche : <form method="POST" action="identification.php">',
				fd_form_input(APP_Z_TEXT,'recherche', $_POST['recherche'], 30),fd_form_input(APP_Z_SUBMIT,'btnRechercher', 'Rechercher', 15,'class="boutonII"'),
			'</form></div>';

if (isset($_POST['btnRechercher'])) {
	ecl_recherche();
}

echo	'</section>',
	'</section>';

fd_html_pied();
ob_end_flush();


//=================== FIN DU SCRIPT =============================

//_______________________________________________________________
//
//		FONCTIONS LOCALES
//_______________________________________________________________

/**
* Validation de la saisie et recherche
*
* La zone reçue du formulaire de saisie est vérifiée. Si
* 
* @global array		$_POST		zone de saisie du formulaire
*
* @return array 	Tableau des erreurs détectées
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

	$S = "SELECT utiNom, utiMail, suiIDSuivi, suiIDSuiveur
		  FROM utilisateur
		  LEFT OUTER JOIN suivi
		  ON utilisateur.utiID = suivi.suiIDSuiveur
		  WHERE utiNom LIKE '%$recherche%'
		  AND utiID != {$_SESSION['utiID']}
		  AND (suiIDSuivi = {$_SESSION['utiID']} OR suiIDSuivi <=> NULL)";

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
			if ($D['suiIDSuivi'] != NULL) {
				$abonne = '[est abonn&eacute; &agrave; votre agenda]';
			}

			echo '<p class="recherche" style="background-color:',$color,'">',$D['utiNom'],' ',$D['utiMail'],' ',$abonne,' ',$D['suiIDSuiveur'],'</p>';
			$count++;
		}
	}
	else
	{
		echo 'Aucun resultat trouv&eacute;.';
	}
	// Libère la mémoire associée au résultat $R
    mysqli_free_result($R);
	
	// Déconnexion de la base de données
    mysqli_close($GLOBALS['bd']);

}

?>