<?php
/** @file
 * Bibliothèque générale de fonctions
 *
 * @author : CHOLLEY Emilien
 */

//____________________________________________________________________________
//
// Défintion des constantes de l'application
//____________________________________________________________________________
	
	// Gestion des pages de l'application
	define('APP_PAGE_AGENDA', 'agenda');
	define('APP_PAGE_RECHERCHE', 'recherche');
	define('APP_PAGE_ABONNEMENTs', 'abonnements');
	define('APP_PAGE_PARAMETRES', 'parametres');

	// Gestion des infos base de données
	define('APP_BD_URL', 'localhost');
	define('APP_BD_NOM', '24sur7_cholley');
	define('APP_BD_USER', 'u_cholley');
	define('APP_BD_PASS', 'p_cholley');

	define('APP_Z_TEXT', 'text');
	define('APP_Z_PASS', 'password');
	define('APP_Z_SUBMIT', 'submit');

	define('APP_TEST', TRUE);

	//___________________________________________________________________
	/**
	 * Connexion &agrave une base de donn&eacutees MySQL.
	 *
	 * @return resource connecteur &agrave la base de donn&eacutees
	 */
	function ec_db_connexion () {
		$bd = mysqli_connect (APP_BD_URL, APP_BD_USER, APP_BD_PASS, APP_BD_NOM);

		if ($bd !== FALSE) {
   			mysqli_set_charset($bd, 'utf8') or fd_bd_erreurExit('<h4>Erreur lors du chargement du jeu de caractères utf8</h4>');
    		$GLOBALS['bd'] = $bd;
    		return;			// Sortie connexion OK
 		}
		
		// Erreur de connexion
	    // Collecte des informations facilitant le debugage
	    $msg = '<h4>Erreur de connexion base MySQL</h4>'
	            .'<div style="margin: 20px auto; width: 350px;">'
	            .'BD_SERVEUR : '.APP_BD_URL
	            .'<br>BD_USER : '.APP_BD_USER
	            .'<br>BD_PASS : '.APP_BD_PASS
	            .'<br>BD_NOM : '.APP_BD_NOM
	            .'<p>Erreur MySQL numéro : '.mysqli_connect_errno($bd)
	            .'<br>'.mysqli_connect_error($bd)
	            .'</div>';

	    ec_bd_erreurExit($msg);
	}

	//____________________________________________________________________________
	/**
	 * Traitement erreur mysql, affichage et exit.
	 *
	 * @param string	$sql	Requête SQL ou message
	 */
	function ec_bd_erreur($sql) {
	    $errNum = mysqli_errno($GLOBALS['bd']);
	    $errTxt = mysqli_error($GLOBALS['bd']);
			
	    // Collecte des informations facilitant le debugage
	    $msg = '<h4>Erreur de requ&ecirc;te</h4>'
	        ."<pre><b>Erreur mysql :</b> $errNum"
	        ."<br> $errTxt"
		        ."<br><br><b>Requ&ecirc;te :</b><br> $sql"
	        .'<br><br><b>Pile des appels de fonction</b>';

	    // Récupération de la pile des appels de fonction
	    $msg .= '<table border="1" cellspacing="0" cellpadding="2">'
	                .'<tr><td>Fonction</td><td>Appel&eacute;e ligne</td>'
	                .'<td>Fichier</td></tr>';
				
	    // http://www.php.net/manual/fr/function.debug-backtrace.php
	    $appels = debug_backtrace();
	    for ($i = 0, $iMax = count($appels); $i < $iMax; $i++) {
	        $msg .= '<tr align="center"><td>'
	                    .$appels[$i]['function'].'</td><td>'
	                    .$appels[$i]['line'].'</td><td>'
	                    .$appels[$i]['file'].'</td></tr>';
	    }
		
	    $msg .= '</table></pre>';

	    ec_bd_erreurExit($msg);
	}
	//___________________________________________________________________
	/**
	 * Arrêt du script si erreur base de données.
	 * Affichage d'un message d'erreur si on est en phase de
	 * développement, sinon stockage dans un fichier log.
	 *
	 * @param string	$msg	Message affiché ou stocké.
	 */
	function ec_bd_erreurExit($msg) {
	    ob_end_clean();		// Supression de tout ce qui a pu être déja généré
		
	    // Si on est en phase de développement, on affiche le message
	    if (APP_TEST) {
	        echo '<!DOCTYPE html><html><head><meta charset="ISO-8859-1"><title>',
	                'Erreur base de données</title></head><body>',
	                $msg,
	                '</body></html>';
	        exit();
	    }
			
	    // Si on est en phase de production on stocke les
	    // informations de débuggage dans un fichier d'erreurs
	    // et on affiche un message sibyllin.
	    $buffer = date('d/m/Y H:i:s')."\n$msg\n";
	    error_log($buffer, 3, 'erreurs_bd.txt');
		
	    // Génération d'une page spéciale erreur
	    ec_html_head('24sur7');
			
	    echo '<h1>24sur7 est overbook&eacute;</h1>',
	        '<div id="bcDescription">',
	            '<h3 class="gauche">Merci de r&eacute;essayez dans un moment</h3>',
	        '</div>';
		
	    ec_html_pied();
		
	    exit();
	}

	//___________________________________________________________________
	/**
	 *verifie la presence des variables de session indiquant qu'un utilisateur est connect&eacute
	 */
	function ec_verifie_session () {
		if (!isset($_SESSION['utiID']) || !isset($_SESSION['utiNom'])) {
			header ('location: inscription.php');
			exit();
		}
	}

	//___________________________________________________________________
	/**
	 * Protection HTML des chaînes contenues dans un tableau
	 * Le tableau est pass&eacute par r&eacutef&eacuterence.
	 *
	 * @param array     $tab    Tableau des chaînes &agrave prot&eacuteger
	 */
	function ec_htmlProteger (&$tab) {
	    foreach ($tab as $cle => $val) {

	        $tab[$cle] = htmlentities($val, ENT_COMPAT, 'UTF-8');
	    }
	}

	//___________________________________________________________________
	/**
	 * Teste si une valeur est une valeur entière
	 *
	 * @param mixed     $x  valeur à tester
	 * @return boolean  TRUE si entier, FALSE sinon
	 */
	function estEntier($x) {
	    return is_numeric($x) && ($x == (int) $x);
	}


	//_______________________________________________________________________
	/**
	 * Envoie &agrave la sortie standard le d&eacutebut du code HTML d'une page
	 *
	 * @param string    $titre  Titre de la page
	 * @param string    $css    adresse du fichier CSS pour le style de la page (facultatif)
	 */
	function ec_html_head ($titre,$css = '../css/style.css')
	{	
		$affCss = '';
		if ($css != '-') 
		{
			$affCss = "<link rel=\"stylesheet\" href=\"$css\" type=\"text/css\">";
		}
			echo'<!DOCTYPE html>',
				'<html>',
					'<head>',
						'<meta charset="UTF-8">',
						'<title>',$titre,'</title>',
						$affCss,
						'<link rel="shortcut icon" href="../images/favicon.ico" type="image/x-icon">',
				'</head>',
				'<body>',
				'<main id="bcPage">';
	}

	//___________________________________________________________________
	/**
	 *affiche le header commun &agrave la plupart de pages de l'application agenda
	 *
	 ** @param string    $page  definie l'onglet actif
	 *
	 */
	function ec_bandeau ($page)
	{

		echo '<header id="bcEntete">',			
				'<div id="bcLogo"></div>',

				'<nav id="bcOnglets">';
				if ($page === APP_PAGE_AGENDA) {
					echo '<h2>Agenda</h2>',
					'<a href="#">Recherche</a>',
					'<a href="#">Abonnements</a>',
					'<a href="#">Param&egrave;tres</a>';
				}
				elseif ($page === APP_PAGE_RECHERCHE) {
					echo '<a href="agenda.php">Agenda</a>',
					'<h2>Recherche</h2>',
					'<a href="#">Abonnements</a>',
					'<a href="#">Param&egrave;tres</a>';
				}
				elseif ($page === APP_PAGE_ABONNEMENTs) {
					echo '<a href="agenda.php">Agenda</a>',
					'<a href="#">Recherche</a>',
					'<h2>Abonnements</h2>',
					'<a href="#">Param&egrave;tres</a>';
				}
				elseif ($page === APP_PAGE_PARAMETRES) {
					echo '<a href="agenda.php">Agenda</a>',
					'<a href="#">Recherche</a>',
					'<a href="#">Abonnements</a>',
					'<h2>Param&egrave;tres</h2>';
				}
				echo '</nav>',
				
				'<a href="#" id="btnDeconnexion" title="Se d&eacute;connecter"></a>',
			'</header>';
	}

	//___________________________________________________________________________
	/**
	 * affiche le footer commun &agrave la plupart de pages de l'application agenda
	 */
	function ec_html_pied ()
	{
		echo'<footer id="bcPied">',
				'<a id="apropos" href="#">A propos</a>',
				'<a id="confident" href="#">Confidentialit&eacute;</a>',
				'<a id="conditions" href="#">Conditions</a>',
				'<p id="copyright">24sur7 &amp; Partners &copy; 2012</p>',
			'</footer>',
		'</main>';
	}

	//___________________________________________________________________________
	/**
	 * Envoie &agrave la sortie standard la fin du code HTML d'une page
	 */
	function ec_htmlFin ()
	{
		echo'</body></html>';
	}

	//___________________________________________________________________________
	/**
	 * fonction pour afficher un calandrier
	 *
	 * @param int    $jour   numero du jour (0 à 31)
	 * @param int    $mois   numero du mois (1 à 12)
	 * @param int    $annee  numero de l'annee (à partir de 2012)
	 */
	function ec_html_calandrier($jour = 0, $mois = 0, $annee = 0)
	{
		$tabDate = getdate();
		$jour = (int)$jour;
		$mois = (int)$mois;
		$annee = (int)$annee;

		$affMois = array('','Janvier','Fevrier','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Decembre');

		if (!(($jour >= 1) && ($jour <= 31)))
		{
			$jour = $tabDate ['mday'];
		}
		if (!(($mois >= 1) && ($mois <= 12)))
		{
			$mois = $tabDate ['mon'];
		}
		if (!($annee >= 2012))
		{
			$annee = $tabDate ['year'];
		}

		if (checkdate($mois, $jour, $annee)) 
		{
			$date = mktime(0, 0, 0, $mois, $jour, $annee);
		}
		else
		{
			$date = time();
			$jour = $tabDate ['mday'];
			$mois = $tabDate ['mon'];
			$annee = $tabDate ['year'];
		}

		$firstDay = date('N', mktime(0, 0, 0, $mois, 1, $annee)); // numero du jour de la semaine correspondant au premier jour du mois
		$lastDay = date('t', $date); // dernier jour du mois
		$lastDayPrev = date('t', mktime(0, 0, 0, $mois, 0, $annee)); //dernier jour du mois precedent
		$day = date('j', mktime(0, 0, 0, $mois, (2-$firstDay), $annee)); // compteur pour l'affichege des jour
		$sem = date('W', $date); //numero de la semaine
		$countSem = date('W', mktime(0, 0, 0, $mois, 1, $annee));

		if ($lastDay+$firstDay-1 > 35) 
		{
			$maxSem = 6;
		}
		else
		{
			$maxSem = 5;
		}

			//affichage de l'entète du calandrier
			echo'<section id="calendrier">',
					'<p>',
						'<a href="#" class="flechegauche"><img src="../images/fleche_gauche.png" alt="picto fleche gauche"></a>',
						$affMois[date('n', $date)]," ",date('Y', $date),
						'<a href="#" class="flechedroite"><img src="../images/fleche_droite.png" alt="picto fleche droite"></a>',
					'</p>',
					'<table>',
							'<tr>',
								'<th>Lu</th><th>Ma</th><th>Me</th><th>Je</th><th>Ve</th><th>Sa</th><th>Di</th>',
							'</tr>';

		//affichage du calandrier
		

		for ($j=0; $j < $maxSem ; $j++) { 
			if ($sem == $countSem) 
			{
				echo '<tr class="semaineCourante">';
			}
			else
			{
				echo '<tr>';
			}

			for ($i=0; $i < 7; $i++) 
			{ 
				if ((($day > 7) && ($j == 0)) || (($day <= 7) && ($j >=4)))
				{
					echo '<td><a class="lienJourHorsMois" href="#">',$day,'</a></td>';
				}
				else
				{	
					if (($day == $tabDate['mday']) && ($mois == $tabDate['mon']) && ($annee == $tabDate['year']))
					{
						echo '<td class="aujourdHui"><a href="#">',$day,'</a></td>';
					}
					elseif ($jour == $day) {
						echo '<td class="jourCourant"><a href="#">',$day,'</a></td>';
					}
					else
					{
						echo '<td><a href="#">',$day,'</a></td>';
					}
				}
				if (($day == $lastDayPrev) && ($j == 0))
				{
					$day = 1;
				}
				elseif ($day == $lastDay)
				{
					$day = 1;
				}
				else
				{
					$day++;
				}
			}
			echo '</tr>';
			$countSem++;
		}
			echo '</table>',
			'</section>';
	}

	

	//___________________________________________________________________
	/**
	 *conversion et affcihage d'une date au pass&eacute en param&egravetre au format aaaammjj, au format jj mois aaaa.
	 * @param int $date date &agrave convertir.
	 *
	 * @return string $res date convertie
	 */
	function ec_amj_claire ($date) {
		if (substr ($date, 6,1) == 0) {
			$jour = substr ($date, 7);
		}
		else {
			$jour = substr ($date, 6);
		}

		if (substr ($date, 4,1) == 0) {
			$mois = substr ($date, 5,1);
		}
		else {
			$mois = substr ($date, 4,2);
		}

		$affMois = array('','janvier','fevrier','mars','avril','mai','juin','juillet','août','septembre','octobre','novembre','decembre');

		$res = $jour.' '.$affMois [$mois].' '.substr ($date, 0,4);
		return $res;
	}

	//___________________________________________________________________
	/**
	 *conversion et affcihage d'un horaire au pass&eacute en param&egravetre au format HHMM, au format HHhMM . retourne -1 si de base vaut -1 (= journée entière).
	* @param int $date date &agrave convertir.
	*
	* @return string $res date convertie
	*/
	function ec_heure_claire ($date) {
		if ($date == -1) {
			$res = -1;
		}
		elseif ($date < 1000) {
			$res = substr ($date, 0,1).'h';
			if (substr ($date, 1) != 0){
				$res = $res.substr ($date, 1);
			}
		}
		else {
			$res = substr ($date, 0,2).'h';
			if (substr ($date, 2) != 0){
				$res = $res.substr ($date, 2);
			}
		}
		return $res;
	}

		//___________________________________________________________________
	/**
	 * renvoi sous forme d'ue chaine de carctere, une ligne de tableau avec les deux colonnes remplies par les paramètre passés
	 *
	 * @param string 	$gauche	contenu de la colonne de gauche 
	 * @param string 	$droite	contenu de la colonne de droite
	 */
	function ec_form_ligne ($gauche,$droite) {
		$res = '<tr><td>'.$gauche.'</td><td>'.$droite.'</td></tr>';
		return $res;
	}

	//____________________________________________________________________
	/**
	 * genere le code HTML pour les zones de saisies de type text, password et submit. renvoi le code genere sous forme d'une chaine de caractère
	 *
	 * @param consst 	$type 		type de la zone de saisie
	 * @param string 	$name 		nom de la zone de saisie
	 * @param string 	$value 	valeur par defaut de la zone de saisie 
	 * @param int 		$size 	taille de la eone de saisie
	 */
	function ec_form_input ($type,$name,$value,$size=0) {
		$res = '<input type="'.$type.'" name="'.$name.'" value="'.$value.'"'.(($size==0) ? '' : " maxlength=\"$size\"").' '.(($size==0) ? '' : " size=\"$size\"");
		return $res;
	}

	//____________________________________________________________________
	/**
	 * genere le code HTML pour les trois liste deroulante permetant de saisir la date. renvoi le code genere sous forme d'une chaine de caractère
	 *
	 * @param string 	$nom 		nom de la zone
	 * @param int 		$jour 		jour pre selectionne
	 * @param int 		$mois 		mois pre selectionne
	 * @param int 		$annee 		annee pre selectionne
	 */
	function ec_form_date ($nom,$jour=0,$mois=0,$annee=0,$size=0) {
		
		//--liste deroulante du jour
		if ($jour == 0) {
			$jour = date ('j');
		}
		$res = '<select name="'.$nom.'_j" size="'.$size.'">';
		for ($i = 1;$i<32;$i++) {
			$res = $res.'<option value="'.$i.'" '.(($i==$jour)?'selected':'').'>'.$i.'</option>';
		}
		$res = $res.'</select>';

		//--liste deroulante du mois
		if ($mois == 0) {
			$mois = date ('n');
		}
		$tmois = array('','janvier','f&eacute;vrier','mars','avril','mai','juin','juillet','ao&ucirc;t','septembre','octobre','novembre','d&eacute;cembre');

		$res = $res.'<select name="'.$nom.'_m" size="'.$size.'">';
		for ($i = 1;$i<13;$i++) {
			$res = $res.'<option value="'.$i.'" '.(($i==$mois)?'selected':'').'>'.$tmois[$i].'</option>';
		}
		$res = $res.'</select>';

		//--liste deroulante de l'annee
		if ($annee == 0) {
			$annee = date ('Y');
		}

		$i = date('Y');
		$iMin = $i - 99;
		$res = $res.'<select name="'.$nom.'_a" size="'.$size.'">';
		for ($i = $i ;$i>$iMin;$i--) {
			$res = $res.'<option value="'.$i.'" '.(($i==$annee)?'selected':'').'>'.$i.'</option>';
		}
		$res = $res.'</select>';
		return $res;
	}

?>
