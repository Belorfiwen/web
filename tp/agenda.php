<?php
	/**
	 * Page d'accueil de l'application 24sur7
	 *
	 */
	ob_start();

	error_reporting(E_ALL);

	include ('bibli_24sur7.php');

	ec_html_head('24sur7 | Agenda');

	ec_bandeau (APP_PAGE_AGENDA);

	echo '<section id="bcContenu">',		
				'<aside id="bcGauche">';
	ec_html_calandrier();
					echo'<section id="categories">Ici : bloc catégories pour afficher les catégories de rendez-vous</section>',
				'</aside>',
	       		'<section id="bcCentre">Ici : bloc avec le détail des rendez-vous de la semaine du 9 au 15 février 2015',
	       		'</section>',
			'</section>';

	ec_html_pied ();

	ec_htmlFin();
	ob_end_flush();
?>
