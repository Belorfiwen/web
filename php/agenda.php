<?php
/** 
 * Page Agenda de l'application 24sur7
 *
 */
// Bufferisation des sorties
ob_start();
include('bibli_24sur7.php');	// Inclusion de la bibliothéque
session_start();
ec_verifie_session();

$jour = 0;
$mois = 0;
$annee = 0;

if (isset($_GET['jour'])) {
	$jour = $_GET['jour'];
}

if (isset($_GET['mois'])) {
	$mois = $_GET['mois'];
}

if (isset($_GET['annee'])) {
	$annee = $_GET['annee'];
}

ec_uti_rdv();

fd_html_head('24sur7 | Agenda');

fd_html_bandeau(APP_PAGE_AGENDA);

echo '<section id="bcContenu">',
		'<aside id="bcGauche">';

//Affiche le calendrier selon les variables rentrée : $jour, $mois, $annee
fd_html_calendrier($jour, $mois, $annee);

//Affiche les catégories de l'utilisateur et les autre utilisateurs abonnées avec leurs catégories
ec_html_categorie($jour, $mois, $annee);

echo	'</aside>';
//Affiche le semainier et les rendez vous correspondant au jour sélectionné dans le calendrier
ec_html_semainier($jour, $mois, $annee);

echo	'</section><div style="clear: both;"> </div>',
	'</section>';

fd_html_pied();
ob_end_flush();
?>