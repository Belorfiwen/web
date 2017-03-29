<?php
	/**
	 * Page d'accueil de l'application 24sur7
	 *
	 */
	ob_start();

	error_reporting(E_ALL);

	include ('bibli_24sur7.php');

	ec_html_head('24sur7 | Inscription', '-');

	echo '<h2>R&eacute;ception du formulaire d\'inscription utilisateur</h2>';

	foreach ($_POST as $key => $value) {
		echo 'Zone ', htmlentities($key, ENT_COMPAT, 'UTF-8'),
					' = ',
					htmlentities($value, ENT_COMPAT, 'UTF-8'), '<br>';
	}
	ec_htmlFin();
	ob_end_flush();
?>