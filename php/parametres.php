<?php
/** @file
 * Page d'accueil de l'application 24sur7
 *
 * @author : Frederic Dadeau - frederic.dadeau@univ-fcomte.fr
 */
ob_start();
session_start();
include('bibli_24sur7.php');	// Inclusion de la bibliothéque

fd_html_head('24sur7 | Agenda');

fd_html_bandeau(APP_PAGE_PARAMETRES);

echo '<section id="bcContenu">',
		
	'</section>';

fd_html_pied();
?>