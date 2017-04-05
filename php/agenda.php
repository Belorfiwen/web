<?php

error_reporting(E_ALL);

include('bibli_24sur7.php'); //inclure la bibliothèque

fd_html_head('24sur7 | Agenda');

fd_html_bandeau('recherche');

echo '<section id="bcContenu">',
    	'<aside id="bcGauche">',
        	'<section id="calendrier">Ici : bloc calendrier pour afficher le mois de février 2015</section>',
        	'<section id="categories">Ici : bloc catégories pour afficher les catégories de rendez-vous</section>',
    	'</aside>',
    	'<section id="bcCentre">Ici : bloc avec le détail des rendez-vous de la semaine du 9 au 15 février 2015',
    	'</section>',
      '</section>';

aj_html_pied();

?>
