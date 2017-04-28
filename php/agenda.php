<?php
/** 
 * Page Agenda de l'application 24sur7
 *
 */
// Bufferisation des sorties
ob_start();
session_start();
include('bibli_24sur7.php');	// Inclusion de la bibliothéque

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

fd_html_head('24sur7 | Agenda');

fd_html_bandeau(APP_PAGE_AGENDA);

echo '<section id="bcContenu">',
		'<aside id="bcGauche">';

fd_html_calendrier($jour, $mois, $annee);

ec_html_categorie();

echo	'</aside>';

ec_html_semainier($jour, $mois, $annee);

echo	'</section><div style="clear: both;"> </div>',
	'</section>';

fd_html_pied();
?>