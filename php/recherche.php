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
if (!isset($_POST['btnRechercher']) && !isset($_POST['btnAbo'])) 
{
	// On n'est dans un premier affichage de la page.
	// => On intialise la zones de saisie.
	$_POST['recherche'] = '';
} 

// si bouton "s'abonner/se desabonner" cliqué, on appel la fonction ec_abonnement() qui permet de s'abonner à un utilisateur ou de se desabonner
if (isset($_POST['btnAbo'])) 
{
	ec_abonnement();
}

fd_html_head('24sur7 | Recherche');

fd_html_bandeau(APP_PAGE_RECHERCHE);

// affichage de la barre de recherche.
echo '<div id="bcContenu">',
		'<div>',
			'<form method="POST" action="recherche.php">',
				'<div id="zoneRecherche">Entrez le crit&egrave;re de recherche : ',
				fd_form_input(APP_Z_TEXT,'recherche', $_POST['recherche'], 30),fd_form_input(APP_Z_SUBMIT,'btnRechercher', 'Rechercher', 15,'class="boutonII"'),
				'</div>',
			'</form>';

// si bouton "rechercher" cliqué, on appel la fonction ecl_recherche() qui permet d'afficher les resultats de la recherche
if (isset($_POST['btnRechercher']) || isset($_POST['btnAbo'])) 
{
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
* Recherche et affichage en fonction de la chaine saisie dans la barre de recherche
* 
* @global array		$_POST		zone de saisie du formulaire
* @global array		$GLOBALS		base de données
*
*/
function ecl_recherche() 
{

	//-----------------------------------------------------
	// Vérification des zones
	//-----------------------------------------------------
	$erreurs = array();

	// Vérification du mail
	$recherche = trim($_POST['recherche']);
	if ($recherche == '') 
	{
		echo '<p class="recherche" style="text-align:center;">Erreur : Vous devez entrer une recherche</p>';
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

	if (mysqli_num_rows($R))  // on affiche les resultats de la requète si il y en a.
	{
		$count = 0;
		while ($D = mysqli_fetch_assoc($R)) 
		{
			ec_htmlProteger($D);

			// determination de la couleur de la ligne

			$color = '#E5ECF6';
			if ($count%2 == 0) 
			{
				$color = '#9AC5E7';
			}

			//si l'utilisateur trouvé est abonné à votre compte on affiche "[est abonné à votre agenda]"

			$abonne = '';
			if ($D['s2Suivi'] != NULL) 
			{
				$abonne = '[est abonn&eacute; &agrave; votre agenda]';
			}

			//determination du bouton : "s'abonner/se desabonner" ou pas de bouton si le resultat est nous même

			$btn = '<input type="submit" name="btnAbo" value="S\'abonner" size=17 class="boutonII boutonRA">';

			$valueBtn = 1;

			if ($D['s1Suiveur'] != NULL) 
			{
				$btn = '<input type="submit" name="btnAbo" value="Se d&eacute;sabonner" size=17 class="boutonII boutonRA">';

				$valueBtn = 0;
			}

			if ($D['utiID'] == $_SESSION['utiID']) 
			{
				$btn = '';
			}

			// affichage du resultat	

			echo '<form method="POST" action="recherche.php">',
				 '<input type="hidden" name="recherche" value="',$_POST['recherche'],'">',
				 '<input type="hidden" name="utiIDAbonne" value="',$D['utiID'],'">',
				 '<input type="hidden" name="valueBtn" value="',$valueBtn,'">',
				 '<table class="recherche" style="background-color:',$color,'"><tr><td><p class="texteAbonne">',$D['utiNom'],' - ',$D['utiMail'],' ',$abonne,'</p></td><td style="width:112px;">',$btn,'</td></tr></table></form>';
			$count++;
		}
	}
	else
	{
		// si pas des resultat on affiche :
		echo '<p class ="recherche" style="text-align:center;">Aucun resultat trouv&eacute;.</p>';
	}

	// Libère la mémoire associée au résultat $R
    mysqli_free_result($R);
	
	// Déconnexion de la base de données
    mysqli_close($GLOBALS['bd']);

}

?>
