<?php
/** @file
 * Page des abonnements de l'application 24sur7
 */
ob_start();
include('bibli_24sur7.php');	// Inclusion de la bibliothèque
session_start();
ec_verifie_session();

fd_html_head('24sur7 | Abonnements');

fd_html_bandeau(APP_PAGE_ABONNEMENTS);

echo '<div id="bcContenu">',
		'<div>';
		
		
fd_bd_connexion();
		
	
if (isset($_POST['btnAbo'])) {
		
	// suppression ou ajout d'un suivi
	ec_abonnement();
}

		
echo '<div class="titreparam1 titreParametre">Utilisateurs abonn&eacute;s &agrave; moi : </div>';
		
	fd_bd_connexion();
	
	$S = "SELECT	utiID, utiNom, utiMail, s1.suiIDSuivi AS s1Suivi,s1.suiIDSuiveur AS s1Suiveur,s2.suiIDSuivi AS s2Suivi,s2.suiIDSuiveur AS s2Suiveur
			FROM suivi AS s2, utilisateur
			LEFT JOIN suivi AS s1
            ON s1.suiIDSuiveur = {$_SESSION['utiID']} AND s1.suiIDSuivi = utilisateur.utiID
            WHERE s2.suiIDSuiveur = utilisateur.utiID AND s2.suiIDSuivi = {$_SESSION['utiID']}
            ORDER BY utilisateur.utiID";

	$R = mysqli_query($GLOBALS['bd'], $S) or fd_bd_erreur($S);
	$i=1;
	if (mysqli_num_rows($R)) 
	{
		while ($D = mysqli_fetch_assoc($R)) {
			ec_htmlProteger($D);

			$color = '#9AC5E7';
			if ($i%2 == 0) {
				$color = '#E5ECF6';
			}
		
			$btn = '<input type="submit" name="btnAbo" value="S\'abonner" size=17 class="boutonII boutonRA">';

			$valueBtn = 1;

			if ($D['s1Suiveur'] != NULL) {
				$btn = '<input type="submit" name="btnAbo" value="Se d&eacute;sabonner" size=17 class="boutonII boutonRA">';

				$valueBtn = 0;
			}

			echo '<form method="POST" action="abonnements.php">',
				 '<input type="hidden" name="utiIDAbonne" value="',$D['utiID'],'">',
				 '<input type="hidden" name="valueBtn" value="',$valueBtn,'">',
				 '<table class="recherche" style="background-color:',$color,'"><tr><td><p class="texteAbonne">',$D['utiNom'],' - ',$D['utiMail'],' </p></td><td>',$btn,'</td></tr></table></form>';
			$i++;
		}
	}	
	
	mysqli_free_result($R);
	
echo '<div class="titreparam1 titreParametre">Je suis abonn&eacute; &agrave; : </div>';
	
	$S = "SELECT	utiID, utiNom, utiMail, suiIDSuivi
			FROM	utilisateur, suivi
			WHERE	suiIDSuivi = utiID
			AND 	suiIDSuiveur = {$_SESSION['utiID']}";

	$R = mysqli_query($GLOBALS['bd'], $S) or fd_bd_erreur($S);
	$i=1;
	if (mysqli_num_rows($R)) 
	{
		while ($D = mysqli_fetch_assoc($R)) {
			ec_htmlProteger($D);

			$color = '#9AC5E7';
			if ($i%2 == 0) {
				$color = '#E5ECF6';
			}

				$btn = '<input type="submit" name="btnAbo" value="Se d&eacute;sabonner" size=17 class="boutonII boutonRA">';

				$valueBtn = 0;

			echo '<form method="POST" action="abonnements.php">',
				 '<input type="hidden" name="utiIDAbonne" value="',$D['utiID'],'">',
				 '<input type="hidden" name="valueBtn" value="',$valueBtn,'">',
				 '<table class="recherche" style="background-color:',$color,'"><tr><td><p class="texteAbonne">',$D['utiNom'],' - ',$D['utiMail'],' </p></td><td>',$btn,'</td></tr></table></form>';
			$i++;
		}
	}	

	echo '</div>';	
		
echo '</div>';

fd_html_pied();
ob_end_flush();	
		
?>