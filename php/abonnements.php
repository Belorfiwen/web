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

echo '<section id="bcContenu">',
		'<section>';
		
		
fd_bd_connexion();
	
	$S = "SELECT 	count(*)
			FROM	suivi
			WHERE	suiIDSuivi = {$_SESSION['utiID']}";

	$R = mysqli_query($GLOBALS['bd'], $S) or fd_bd_erreur($S);
	$i=1;
while($D = mysqli_fetch_assoc($R)){
		
	
	if (isset($_POST['abn'.$i])) {
		
		// suppression de l'abonné avec aj_ajout_abonnement($i)
		aj_ajout_abonnement($i);		

	} 
	
	if (isset($_POST['abnSupp'.$i])) {
		
		// suppression de l'abonné avec aj_supprimer_abonnement($i)
		aj_suppression_abonnement($i);		

	} 
	$i++;
}
		
echo '<div class="titreparam1 titreParametre">Utilisateurs abonn&eacute;s &agrave; moi : </div>';
		
	fd_bd_connexion();
	
	$S = "SELECT	utiID, utiNom, utiMail
			FROM	utilisateur, suivi AS s1
			LEFT OUTER JOIN suivi AS s2 ON utilisateur.utiID = suivi.suiIDSuiveur
			WHERE	s1.suiIDSuivi = {$_SESSION['utiID']}
			AND 	s1.suiIDSuiveur = utiID";

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
		
			echo '<form method="POST" action="abonnements.php">',
				 '<input type="hidden" name="utiID" value="',$D['utiID'],'">',
				 '<p class="recherche" style="background-color:',$color,'">',$D['utiNom'],' - ',$D['utiMail'],
				 '<input type="submit" name="btnAbonnement',$i,'" value="Se d&eacute;sabonner" size=15 class="boutonII boutonRA"></p></form>';
			$i++;
		}
	}	
	
	mysqli_free_result($R);
	
echo '<div class="titreparam1 titreParametre">Je suis abonn&eacute; &agrave; : </div>';
	
	$S = "SELECT	utiID, utiNom, utiMail
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

			echo '<form method="POST" action="abonnements.php">',
				 '<input type="hidden" name="utiID" value="',$D['utiID'],'">',
				 '<p class="recherche" style="background-color:',$color,'">',$D['utiNom'],' - ',$D['utiMail'],
				 '<input type="submit" name="btnAbonne',$i,'" value="Se d&eacute;sabonner" size=15 class="boutonII boutonRA"></p></form>';
			$i++;
		}
	}	

	echo '</section>';	
		
echo '</section>';

fd_html_pied();
ob_end_flush();
		
/** 
	* ajout d'un abonnement
	*
	* @global array		$_POST		zones de saisie du formulaire
	* @global array		$_GLOBALS	base de bonnées 
	*/			
function aj_ajout_abonnement($i){
	
	$idSuivi = $_POST['abnID'.$i];
	$S = "INSERT INTO suivi SET
			suiIDSuiveur = {$_SESSION['utiID']},
			suiIDSuivi = '$idSuivi'";

	$R = mysqli_query($GLOBALS['bd'], $S) or fd_bd_erreur($S);
	
	// Déconnexion de la base de données
	mysqli_close($GLOBALS['bd']);
		
	header ('location: abonnements.php');
	exit();
}	

/** 
	* suppression d'un abonnement
	*
	* @global array		$_POST		zones de saisie du formulaire
	* @global array		$_GLOBALS	base de bonnées 
	*/			
function aj_suppression_abonnement($i){
	
	$idSuivi = $_POST['abnSuppID'.$i];
	$S = "DELETE FROM suivi SET
			suiIDSuiveur = {$_SESSION['utiID']},
			suiIDSuivi = '$idSuivi'";

	$R = mysqli_query($GLOBALS['bd'], $S) or fd_bd_erreur($S);
	
	// Déconnexion de la base de données
	mysqli_close($GLOBALS['bd']);
		
	header ('location: abonnements.php');
	exit();
}			
		
?>