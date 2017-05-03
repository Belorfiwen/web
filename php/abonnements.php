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

fd_html_head('24sur7 | Agenda');

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
		
echo '<h1>Utilisateurs abonnés à moi : </h1>';
		
	fd_bd_connexion();
	
	$S = "SELECT	utiID, utiNom, utiMail
			FROM	utilisateur, suivi
			WHERE	suiIDSuivi = {$_SESSION['utiID']}
			AND 	suiIDSuiveur = utiID";

	$R = mysqli_query($GLOBALS['bd'], $S) or fd_bd_erreur($S);
	$i=1;
	while($D = mysqli_fetch_assoc($R)){
		
		echo '<input type="hidden" name="abnID',$i,'" value="'.$D['utiID'].'">',
			htmlentities($D['utiNom'], ENT_COMPAT, 'UTF-8'),' - ',htmlentities($D['utiMail'], ENT_COMPAT, 'UTF-8'),
				" <form method='POST' action='abonnements.php' style=\"display: inline-block;\><input type='submit' name='abn$i' value=\"S'abonner\" size=15 class='boutonII'></form></br>";
		$i++;
	}	
	
	mysqli_free_result($R);
	
echo '<h1>Je suis abonné à : </h1>';
	
	$S = "SELECT	utiID, utiNom, utiMail
			FROM	utilisateur, suivi
			WHERE	suiIDSuivi = utiID
			AND 	suiIDSuiveur = {$_SESSION['utiID']}";

	$R = mysqli_query($GLOBALS['bd'], $S) or fd_bd_erreur($S);
	$i=1;
	while($D = mysqli_fetch_assoc($R)){
		echo '<input type="hidden" name="abnSuppID',$i,'" value="'.$D['utiID'].'">',
			htmlentities($D['utiNom'], ENT_COMPAT, 'UTF-8'),' - ',htmlentities($D['utiMail'], ENT_COMPAT, 'UTF-8'),
				" <form method='POST' action='abonnements.php' style=\"display: inline-block;\"><input type='submit' name='abnSupp$i' value=\"Se désabonner\" size=15 class='boutonII'></form></br>";
		$i++;
	}	
		
		
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
		
	echo '</section>';	
		
echo '</section>';

fd_html_pied();
ob_end_flush();
?>