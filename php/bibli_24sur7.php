<?php
/** @file
 * Bibliothèque générale de fonctions
 *
 */

//____________________________________________________________________________
//
// Défintion des constantes de l'application
//____________________________________________________________________________

define('APP_TEST', TRUE);

// Gestion des infos base de données
define('APP_BD_URL', 'localhost');
define('APP_BD_USER', 'u_cholley');
define('APP_BD_PASS', 'p_cholley');
define('APP_BD_NOM', '24sur7_cholley');
/*define('APP_BD_USER', 'u_merlet');
define('APP_BD_PASS', 'p_merlet');
define('APP_BD_NOM', '24sur7_merlet');*/

define('APP_NOM_APPLICATION','24sur7');

// Gestion des pages de l'application
define('APP_PAGE_AGENDA', 'agenda.php');
define('APP_PAGE_RECHERCHE', 'recherche.php');
define('APP_PAGE_ABONNEMENTS', 'abonnements.php');
define('APP_PAGE_PARAMETRES', 'parametres.php');

//---------------------------------------------------------------
// Définition des types de zones de saisies
//---------------------------------------------------------------
define('APP_Z_TEXT', 'text');
define('APP_Z_PASS', 'password');
define('APP_Z_SUBMIT', 'submit');
define('APP_Z_RESET', 'reset');



//_______________________________________________________________
/**
* Génére le code HTML d'une ligne de tableau d'un formulaire.
*
* Les formulaires sont mis en page avec un tableau : 1 ligne par
* zone de saisie, avec dans la collone de gauche le lable et dans
* la colonne de droite la zone de saisie.
*
* @param string		$gauche				Contenu de la colonne de gauche
* @param string		$droite				Contenu de la colonne de droite
* @param string		$idOrClasse			Classe ou id de la ligne
* @param string		$idOrClasseLeft		Classe ou id de la colonne de gauche (valeur par defaut : '')
* @param string		$idOrClasseRight	Classe ou id de la colonne de droite (valeur par defaut : '')
*
* @return string	Le code HTML de la ligne du tableau
*/
function fd_form_ligne($gauche, $droite, $idOrClasse='', $idOrClasseLeft='', $idOrClasseRight='') {
	return "<tr $idOrClasse><td $idOrClasseLeft>{$gauche}</td><td $idOrClasseRight>{$droite}</td></tr>";
}

//_______________________________________________________________
/**
* Génére le code d'une zone input de formulaire (type text, password ou button)
*
* @param string		$type	le type de l'input (constante FD_Z_xxx)
* @param string		$name	Le nom de l'input
* @param String		$value	La valeur par défaut
* @param integer	$size	La taille de l'input
* @param string		$idOrClasse			Classe ou id du input
*
* @return string	Le code HTML de la zone de formulaire
*/
function fd_form_input($type, $name, $value, $size=0, $idOrClasse ='') {
   $value = htmlentities($value, ENT_QUOTES, 'UTF-8');
   $size = ($size == 0) ? '' : "size='{$size}'";

   return "<input $idOrClasse type='{$type}' name='{$name}' {$size} value=\"{$value}\">";
}

//_______________________________________________________________
/**
* Génére le code pour un ensemble de trois zones de sélection
* représentant uen date : jours, mois et années
*
* @param string		$nom	Préfixe pour les noms des zones
* @param integer	$jour 	Le jour sélectionné par défaut
* @param integer	$mois 	Le mois sélectionné par défaut
* @param integer	$annee	l'année sélectionnée par défaut
*
* @return string 	Le code HTML des 3 zones de liste
*/
function fd_form_date($name, $jsel=0, $msel=0, $asel=0){
	$jsel=(int)$jsel;
	$msel=(int)$msel;
	$asel=(int)$asel;
	$d = date('Y-m-d');
	list($aa, $mm, $jj) = explode('-', $d);
	if ($jsel==0) $jsel = $jj;
	if ($msel==0) $msel = $mm;
	if ($asel==0) $asel = $aa;
	
	$res = "<select id='{$name}_j' name='{$name}_j'>";
	for ($i=1; $i <= 31 ; $i++){
		if ($i == $jsel)
			$res .= "<option value='$i' selected>$i</option>";
		else
			$res .= "<option value='$i'>$i</option>";
	}
	$res .= "</select> <select id='{$name}_m' name='{$name}_m'>"; //l'espace entre les balises  </select> et <select> est utile
	for ($i=1; $i <= 12 ; $i++){
		if ($i == $msel)
			$res .= "<option value='$i' selected>".fd_get_mois($i).'</option>';
		else
			$res .= "<option value='$i'>".fd_get_mois($i).'</option>';
	}
	$res .= "</select> <select id='{$name}_a' name='{$name}_a'>"; //l'espace entre les balises  </select> et <select> est utile
	for ($i=$aa +5; $i >= $aa - 7 ; $i--){
		if ($i == $asel)
			$res .= "<option value='$i' selected>$i</option>";
		else
			$res .= "<option value='$i'>$i</option>";
	}
	$res .= '</select>';
	return $res;		
}



//_______________________________________________________________
/**
* Génére le code pour un ensemble de trois zones de sélection
* représentant une heure : heures minutes
*
* @param string		$name	Préfixe pour les noms des zones
* @param integer	$hsel 	Lheure sélectionné par défaut
* @param integer	$msel	Les minutes sélectionné par défaut
*
* @return string 	Le code HTML des 2 zones de liste
*/
function fd_form_heure($name, $hsel=0, $msel=0){
	$hsel=(int)$hsel;
	$msel=(int)$msel;


	$res = "<select id='{$name}_h' name='{$name}_h'>";
	for ($i=0; $i <= 23 ; $i++){
		if ($i == $hsel)
			$res .= "<option value='$i' selected>$i</option>";
		else
			$res .= "<option value='$i'>$i</option>";
	}
	$res .= "</select> : <select id='{$name}_m' name='{$name}_m'>"; //l'espace entre les balises  </select> et <select> est utile
	for ($i=0; $i <= 59 ; $i=$i+15){
		if ($i == $msel)
		{
			$res .= "<option value='$i' selected>";
		}
		else 
		{
			$res .= "<option value='$i'>";
		}

		if($i<10){
			$res .= "0$i</option>";
		}
		else
		{
			$res .= "$i</option>";
		}	
	}
	$res .= "</select>" ;
	return $res;		
}



//_______________________________________________________________
/**
* Génére le code html des zones de selection de l'heure de debut ou de fin à afficher dns le semainier
*
* @param string		$name	Préfixe pour les noms des zones
* @param integer	$hsel 	Lheure sélectionné par défaut
*
* @return string 	Le code HTML de la zone de selection
*/
function heure_min_max($name, $hsel=0){
	$hsel=(int)$hsel;


	$res = "<select id='{$name}' name='{$name}'>";
	for ($i=0; $i <= 23 ; $i++){
		if ($i == $hsel)
			$res .= "<option value='$i' selected>$i:00</option>";
		else
			$res .= "<option value='$i'>$i:00</option>";
	}
	$res .= "</select>";
	return $res;		
}



//_______________________________________________________________
/**
* Vérifie la présence des variables de session indiquant qu'un utilisateur est connecté.
* Cette fonction est à appeler au début des scripts des pages nécessitant une authentification
* de l'utilisateur
* 
* Si l'utilisateur n'est pas authentifié, la fonction fd_exit_session() est invoquée
*/
function ec_verifie_session(){
	if (! isset($_SESSION['utiID']) || ! isset($_SESSION['utiMail'])) {
		ec_exit_session();
	}
}

//_______________________________________________________________
/**
* Arrête une session et effectue une redirection vers la page 'inscription.php'
* Elle utilise :
*   -   la fonction session_destroy() qui détruit la session existante
*   -   la fonction session_unset() qui efface toutes les variables de session
* Puis, le cookie de session est supprimé
* Enfin, elle effectue la redirection vers la page 'inscription.php'
*/
function ec_exit_session() {
	session_destroy();
	session_unset();
	$cookieParams = session_get_cookie_params();
	setcookie(session_name(), 
			'', 
			time() - 86400,
         	$cookieParams['path'], 
         	$cookieParams['domain'],
         	$cookieParams['secure'],
         	$cookieParams['httponly']
    	);
	
	header('location: identification.php');
	exit();
}
//____________________________________________________________________________

/**
 * Connexion à la base de données.
 * Le connecteur obtenu par la connexion est stocké dans une
 * variable global : $GLOBALS['bd']
 * Le connecteur sera ainsi accessible partout.
 */
function fd_bd_connexion() {
  $bd = mysqli_connect(APP_BD_URL, APP_BD_USER, APP_BD_PASS, APP_BD_NOM);

  if ($bd !== FALSE) {
    mysqli_set_charset($bd, 'utf8') or fd_bd_erreurExit('<h4>Erreur lors du chargement du jeu de caractères utf8</h4>');
    $GLOBALS['bd'] = $bd;
    return;			// Sortie connexion OK
  }

  // Erreur de connexion
  // Collecte des informations facilitant le debugage
  $msg = '<h4>Erreur de connexion base MySQL</h4>'
          .'<div style="margin: 20px auto; width: 350px;">'
              .'APP_BD_URL : '.APP_BD_URL
              .'<br>APP_BD_USER : '.APP_BD_USER
              .'<br>APP_BD_PASS : '.APP_BD_PASS
              .'<br>APP_BD_NOM : '.APP_BD_NOM
              .'<p>Erreur MySQL num&eacute;ro : '.mysqli_connect_errno($bd)
              .'<br>'.mysqli_connect_error($bd)
          .'</div>';

  fd_bd_erreurExit($msg);
}

//____________________________________________________________________________

/**
 * Traitement erreur mysql, affichage et exit.
 *
 * @param string		$sql	Requête SQL ou message
 */
function fd_bd_erreur($sql) {
	$errNum = mysqli_errno($GLOBALS['bd']);
	$errTxt = mysqli_error($GLOBALS['bd']);

	// Collecte des informations facilitant le debugage
	$msg = '<h4>Erreur de requ&ecirc;te</h4>'
			."<pre><b>Erreur mysql :</b> $errNum"
			."<br> $errTxt"
			."<br><br><b>Requ&ecirc;te :</b><br> $sql"
			.'<br><br><b>Pile des appels de fonction</b>';

	// Récupération de la pile des appels de fonction
	$msg .= '<table style="border: 1; cellpadding: 2; cellspacing: 0;">'
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

	fd_bd_erreurExit($msg);
}

//___________________________________________________________________
/**
 * Arrêt du script si erreur base de données.
 * Affichage d'un message d'erreur si on est en phase de
 * développement, sinon stockage dans un fichier log.
 *
 * @param string	$msg	Message affiché ou stocké.
 */
function fd_bd_erreurExit($msg) {
	ob_end_clean();		// Supression de tout ce qui a pu être déja généré

	// Si on est en phase de développement, on affiche le message
	if (APP_TEST) {
		echo '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>',
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
	fd_html_head('24sur7');

	echo '<h1>24sur7 est overbook&eacute;</h1>',
			'<div id="bcDescription">',
				'<h3 class="gauche">Merci de r&eacute;essayez dans un moment</h3>',
			'</div>';

	fd_html_pied();

	exit();
}
//____________________________________________________________________________

/**
 * Génère le code HTML du début des pages.
 *
 * @param string	$titre		Titre de la page
 * @param string	$css		url de la feuille de styles liée
 */
function fd_html_head($titre, $css = '../css/style.css') {
	if ($css == '-') {
		$css = '';
	} else {
		$css = "<link rel='stylesheet' href='$css'>";
	}

	echo '<!DOCTYPE HTML>',
		'<html lang="fr">',
			'<head>',
				'<meta charset="UTF-8">',
				'<title>', $titre, '</title>',
				$css,
				'<link rel="shortcut icon" href="../images/favicon.ico" type="image/x-icon">',
			'</head>',
			'<body>',
				'<main id="bcPage">';
}

//____________________________________________________________________________

/**
 * Génère le code HTML du bandeau des pages.
 *
 * @param string	$page		Constante APP_PAGE_xxx
 */
function fd_html_bandeau($page="") {
	echo '<header id="bcEntete">',
			'<nav id="bcOnglets">',
				($page == APP_PAGE_AGENDA) ? '<h2>Agenda</h2>' : '<a href="'.APP_PAGE_AGENDA.'">Agenda</a>',
				($page == APP_PAGE_RECHERCHE) ? '<h2>Recherche</h2>' : '<a href="'.APP_PAGE_RECHERCHE.'">Recherche</a>',
				($page == APP_PAGE_ABONNEMENTS) ? '<h2>Abonnements</h2>' : '<a href="'.APP_PAGE_ABONNEMENTS.'">Abonnements</a>',
				($page == APP_PAGE_PARAMETRES) ? '<h2>Param&egrave;tres</h2>' : '<a href="'.APP_PAGE_PARAMETRES.'">Param&egrave;tres</a>',
			'</nav>',
			'<div id="bcLogo"></div>',
			'<a href="deconnexion.php" id="btnDeconnexion" title="Se d&eacute;connecter"></a>',
		 '</header>';
}

//____________________________________________________________________________

/**
 * Génère le code HTML du pied des pages.
 */
function fd_html_pied() {
	echo '<footer id="bcPied">',
			'<a id="apropos" href="../html/presentation.html">A propos</a>',
			'<a id="confident" href="../html/presentation.html#confidentialite">Confidentialit&eacute;</a>',
			'<a id="conditions" href="../html/presentation.html#respect">Conditions</a>',
			'<p id="copyright">24sur7 &amp; Partners &copy; 2012</p>',
		'</footer>';

	echo '</main>',	// fin du bloc bcPage
		'</body></html>';
}

//____________________________________________________________________________

/**
 * Génère le code HTML d'un calendrier.
 *
 * @param integer	$jour		Numéro du jour à afficher
 * @param integer	$mois		Numéro du mois à afficher
 * @param integer	$annee		Année à afficher
 */
function fd_html_calendrier($jour = 0, $mois = 0, $annee = 0, $idRdv='') {
	list($JJ, $MM, $AA) = explode('-', date('j-n-Y'));

	// Vérification des paramètres
	$jour = (int) $jour;
	$mois = (int) $mois;
	$annee = (int) $annee;
	($jour == 0) && $jour = $JJ;
	($mois == 0) && $mois = $MM;
	($annee < 2012) && $annee = $AA;

	if (!checkdate($mois, $jour, $annee)) {
		$jour = $JJ;
		$mois = $MM;
		$annee = $AA;
	}

	// Initialisations diverses
	$timeAujourdHui = mktime(0, 0, 0, $MM, $JJ, $AA);
	$timePremierJourMoisCourant = mktime(0, 0, 0, $mois, 1, $annee);
	$timeJourCourant = mktime(0, 0, 0, $mois, $jour, $annee);
	$timeDernierJourMoisCourant = mktime(0, 0, 0, ($mois + 1), 0, $annee);
	
	$nbJoursMoisCourant = date('j', $timeDernierJourMoisCourant);	// nombre de jours dans le mois
	
	$semaineFin = date('W', $timeDernierJourMoisCourant);
	$semaineCourante = date('W', $timeJourCourant);

	
	$jourSemaineJourDebut = date ('w', $timePremierJourMoisCourant);
	($jourSemaineJourDebut == 0) && $jourSemaineJourDebut = 7;
	
  /*
  Les variables $jourAff, $moisAff, $dernierJourMoisAff, $jourCourant, $jourAujourdhui sont utilisées dans
  dans les boucles : 
  for ($sem = $semaineDebut ; $sem <= $semaineFin; $sem++){
		for($i = 1; $i <= 7 ; $i++){
		}
  }
  - $moisAff représente le mois en cours d'affichage : peut prendre successivement les valeurs $mois -1, $mois, 
    $mois + 1 pour représenter respectivement le mois précédent le mois courant, le mois courant et le mois suivant
    le mois courant
  - $jourAff : sa valeur initiale représente le 1er numéro de jour à afficher de $moisAff
  - $dernierJourMoisAff : dernier numéro de jour à afficher de $moisAff
  - $jourCourant : utilisé pour repérer le jour courant (sélectionné) quand $moisAff == $mois
  - $jourAujourdhui : utilisé pour repérer le jour d'aujourd'hui dans le mois précédent, le mois courant, ou le mois
    courant, ou le mois suivant le mois courant
  
  */
  
	if ($jourSemaineJourDebut == 1){
		$jourAff = 1;
		$moisAff = $mois;
		$dernierJourMoisAff = $nbJoursMoisCourant;
		$jourCourant = $jour;
		$jourAujourdhui = ($timeAujourdHui < $timePremierJourMoisCourant || 
							$timeAujourdHui > $timeDernierJourMoisCourant) ? 0 : $JJ;
	}
	else{
        $timeDernierJourMoisPrecedent = mktime(0, 0, 0, $mois, 0, $annee);
        $nbJoursMoisPrecedent = date('j', $timeDernierJourMoisPrecedent);
		$jourAff = $nbJoursMoisPrecedent - $jourSemaineJourDebut + 2;
		$moisAff = $mois - 1;
		$dernierJourMoisAff = $nbJoursMoisPrecedent;
		$jourCourant = 0;
		$timePremierJourAffMoisPrecedent = mktime(0, 0, 0, $moisAff, $jourAff, $annee);
		
		$jourAujourdhui = ($timeAujourdHui < $timePremierJourAffMoisPrecedent ||
				$timeAujourdHui > $timeDernierJourMoisPrecedent) ? 0 : $JJ;
	}

	$returnDateMoins = 'mois='.($mois-1).'&annee='.($annee);
	$returnDatePlus = 'mois='.($mois+1).'&annee='.($annee);
	if ($mois - 1 == 0) {
		$returnDateMoins = 'mois=12&annee='.($annee-1);
	}
	if ($mois + 1 == 13) {
		$returnDatePlus = 'mois=1&annee='.($annee+1);
	}
		
	// Affichage du titre du calendrier
	echo '<div id="calendrier">',
	'<p>',
	'<a href="?id=',$idRdv,'&uti=',$GLOBALS['lienRendezVous'],'&',$returnDateMoins,'" class="flechegauche"><img src="../images/fleche_gauche.png" alt="picto fleche gauche"></a>',
	fd_get_mois($mois), ' ', $annee,
	'<a href="?id=',$idRdv,'&uti=',$GLOBALS['lienRendezVous'],'&',$returnDatePlus,'" class="flechedroite"><img src="../images/fleche_droite.png" alt="picto fleche droite"></a>',
	'</p>';
	
	// Affichage des jours du calendrier
	echo '<table>',
	'<tr>',
	'<th>Lu</th><th>Ma</th><th>Me</th><th>Je</th><th>Ve</th><th>Sa</th><th>Di</th>',
	'</tr>';
	
	
	for (;;)
	{
		$sem = date('W', mktime(0, 0, 0, $moisAff, $jourAff, $annee));
		if ($sem == $semaineCourante)
		{
			echo '<tr class="semaineCourante">';
		}
		else
		{
			echo '<tr>';
		}

		for($i = 1; $i <= 7 ; $i++)
		{
			if ($jourAff == $jourAujourdhui) 
			{
				echo '<td class="aujourdHui">';
			} 
			elseif ($jourAff == $jourCourant) 
			{
				echo '<td class="jourCourant">';
			} 
			else 
			{
				echo '<td>';
			}

			$returnDate = 'jour='.$jourAff.'&mois='.$moisAff.'&annee='.($annee);

			if ($moisAff == $mois)
			{
              echo '<a href="?id=',$idRdv,'&uti=',$GLOBALS['lienRendezVous'],'&',$returnDate,'">', $jourAff, '</a></td>';
            }
            else
            {
            	$returnDate = 'jour='.$jourAff.'&mois='.$moisAff.'&annee='.($annee);
            	if ($moisAff == 0) 
            	{
					$returnDate = 'jour='.$jourAff.'&mois=12&annee='.($annee-1);
				}
				if ($moisAff == 13) 
				{
					$returnDate = 'jour='.$jourAff.'&mois=1&annee='.($annee+1);
				}

              echo '<a class="lienJourHorsMois" href="?id=',$idRdv,'&uti=',$GLOBALS['lienRendezVous'],'&',$returnDate,'">', $jourAff, '</a></td>';
            }
			$jourAff++;
			if ($jourAff > $dernierJourMoisAff){
				$moisAff++;
				$jourAff = 1;
				if ($moisAff == $mois){
					$dernierJourMoisAff = $nbJoursMoisCourant;
					$jourCourant = $jour;
					$jourAujourdhui = ($timeAujourdHui < $timePremierJourMoisCourant ||
							$timeAujourdHui > $timeDernierJourMoisCourant) ? 0 : $JJ;
				}
				else{
                    if ($i == 7) break;
					$dernierJourMoisAff = 7 - $i;
					$timePremierJourMoisSuivant = mktime(0, 0, 0, ($mois + 1), 1, $annee);
					$timeDernierJourMoisSuivant = mktime(0, 0, 0, ($mois + 1), $dernierJourMoisAff, $annee);
					$jourCourant = 0;
					$jourAujourdhui = ($timeAujourdHui < $timePremierJourMoisSuivant ||
							$timeAujourdHui > $timeDernierJourMoisSuivant) ? 0 : $JJ;
				}
			}
		}
		echo '</tr>';
		if ($sem == $semaineFin){
            break;
        }
	}
	echo '</table></div>';
}



//_______________________________________________________________

/**
 * Genere le code html pour l'affichage des agendas et categories.
 *
 * @param integer	$jour		Numéro du jour à afficher
 * @param integer	$mois		Numéro du mois à afficher
 * @param integer	$annee		Année à afficher
 *
 */
function ec_html_categorie($jour, $mois, $annee) {

	echo 		'<div id="categories">',
					'<h3>Vos agendas</h3>';

	// Connexion à la base de données
	fd_bd_connexion();

	// Requête de sélection de l'utilisateur courant
	$sql = "SELECT utiID, utiNom, catNom, catCouleurFond, catCouleurBordure
			FROM utilisateur LEFT OUTER JOIN categorie
			ON utilisateur.utiID = catIDUtilisateur
			WHERE utiID = {$_SESSION['utiID']}";

	// Exécution de la requête
	$R = mysqli_query($GLOBALS['bd'], $sql) or fd_bd_erreur($sql);

	// Boucle de traitement 
	$count = 0;
	while ($D = mysqli_fetch_assoc($R)) 
	{
		ec_htmlProteger ($D);

		if ($count == 0)  // affichage du nom de l'utilisateur courant
		{
			echo 	'<p>',
						'<a href="agenda.php?uti=',$D['utiID'],'&jour=',$jour,'&mois=',$mois,'&annee=',$annee,'">Agenda de ',$D['utiNom'],'</a> ',
					'</p>',
					'<ul>';
			$count++;
		}

		// affichage des categorie de l'utilisateur courant
		echo 			'<li> <div class="categorie" style="border: solid 2px #',$D['catCouleurBordure'],';background-color: #',$D['catCouleurFond'],';"></div>',$D['catNom'];
	}

	echo 			'</ul>';

	mysqli_free_result($R);

// Requête de sélection des utilisateurs don l'utilisateur courant est abonné 
	$sql = "SELECT utiID, utiNom, catNom, catCouleurFond, catCouleurBordure
			FROM utilisateur, suivi
			LEFT OUTER JOIN categorie
			ON categorie.catIDUtilisateur = suivi.suiIDSuivi
			WHERE suiIDSuiveur = {$_SESSION['utiID']}
			AND catPublic = 1
            AND suivi.suiIDSuivi = utilisateur.utiID";

	// Exécution de la requête
	$R = mysqli_query($GLOBALS['bd'], $sql) or fd_bd_erreur($sql);

	// Boucle de traitement
	$prev = -1;
	$count = 0;
	while ($D = mysqli_fetch_assoc($R)) 
	{	

		ec_htmlProteger ($D);

		// affichage des utilisateur et de leur categorie

		if ($count == 0) {
			echo 	'<p>Agendas suivis :</p>',
					'<ul>';
			$count++;
		}
		if ($prev != $D['utiID']) 
		{
			echo 	'<li><p>',
						'<a class="catSui" href="agenda.php?uti=',$D['utiID'],'&jour=',$jour,'&mois=',$mois,'&annee=',$annee,'">',$D['utiNom'],'</a> ',
					'</p></li>';
		}
		
		echo 			'<li> <div class="categorie categorieSui" style="border: solid 2px #',$D['catCouleurBordure'],';background-color: #',$D['catCouleurFond'],';"></div>',$D['catNom'];
		$prev = $D['utiID'];
	}

	echo			'</ul>',
				'</div>';

	// Libère la mémoire associée au résultat $R
	mysqli_free_result($R);

	// Déconnexion de la base de données
	mysqli_close($GLOBALS['bd']);

}

//_______________________________________________________________

/**
 * Genere le code html pour l'affichage du semainier.
 *
 * @param integer	$jour		Numéro du jour à afficher
 * @param integer	$mois		Numéro du mois à afficher
 * @param integer	$annee		Année à afficher
 *
 */
function ec_html_semainier($jour, $mois, $annee) {

	// Connexion à la base de données
	fd_bd_connexion();

	list($JJ, $MM, $AA) = explode('-', date('j-n-Y'));

	$minTopRDV = 29;

	// Vérification des paramètres
	$jour = (int) $jour;
	$mois = (int) $mois;
	$annee = (int) $annee;
	($jour == 0) && $jour = $JJ;
	($mois == 0) && $mois = $MM;
	($annee < 2012) && $annee = $AA;

	if (!checkdate($mois, $jour, $annee)) 
	{
		$jour = $JJ;
		$mois = $MM;
		$annee = $AA;
	}

	$date = mktime(0,0,0,$mois,$jour,$annee);

	$numJourSem = date('w',$date);

	if ($numJourSem == 0) 
	{
		$numJourSem = 7;
	}

	$numJourSem--;

	$date = mktime(0,0,0,$mois,$jour-$numJourSem,$annee);

	$numMoisDate1 = date('n',$date);
	$moisDate1 = fd_get_mois($numMoisDate1);
	$jourDate1 = date('j',$date);
	$anneeDate1 = date('Y',$date);

	$numMoisDate2 = date('n',$date+86400*6);
	$moisDate2 = fd_get_mois($numMoisDate2);
	$jourDate2 = date('j',$date+86400*6);
	$anneeDate2 = date('Y',$date+86400*6);

	if ($numMoisDate1 != $numMoisDate2) {
		if ($anneeDate1 != $anneeDate2) {
			$affDate = $jourDate1.' '.$moisDate1.' '.$anneeDate1.' au '.$jourDate2.' '.$moisDate2.' '.$anneeDate2;
		}
		else
		{
			$affDate = $jourDate1.' '.$moisDate1.' au '.$jourDate2.' '.$moisDate2;
		}
	}
	else
	{
		$affDate = $jourDate1.' au '.$jourDate2.' '.$moisDate1;
	}

	// Requête de sélection pour recuperer les paramètre d'affichage de l'utilisateur
	$sql = "SELECT utiJours, utiHeureMin, utiHeureMax
			FROM utilisateur
			WHERE utiID = {$_SESSION['utiID']}";

	// Exécution de la requête
	$R = mysqli_query($GLOBALS['bd'], $sql) or fd_bd_erreur($sql);

	// traitement
	$D = mysqli_fetch_assoc($R);

	ec_htmlProteger ($D);

	$utiJours = decbin($D['utiJours']);

	$n = mb_strlen($utiJours);
	if ($n < 7) {
		for ($i=0; $i < 7-$n ; $i++) { 
			$utiJours = '0'.$utiJours;
		}
	}
	$nbJours = mb_substr_count($utiJours, '1');
	$utiHeureMin =$D['utiHeureMin'];
	$utiHeureMax =$D['utiHeureMax'];

	mysqli_free_result($R);

	$count = 0;

	//switch pour selectionner la largeur des jour et rendez-vous en fonction du nombre de jour à afficher
	switch ($nbJours) {
		case 1:
			$classColonne = 'taille1';
			$classeRDV = 'taille1RDV';
			$jEntier = 0;
			break;
		case 2:
			$classColonne = 'taille2';
			$classeRDV = 'taille2RDV';
			$jEntier = 338;
			break;
		case 3:
			$classColonne = 'taille3';
			$classeRDV = 'taille3RDV';
			$jEntier = 226;
			break;
		case 4:
			$classColonne = 'taille4';
			$classeRDV = 'taille4RDV';
			$jEntier = 170;
			break;
		case 5:
			$classColonne = 'taille5';
			$classeRDV = 'taille5RDV';
			$jEntier = 136.4;
			break;
		case 6:
			$classColonne = 'taille6';
			$classeRDV = 'taille6RDV';
			$jEntier = 114;
			break;
		
		default:
			$classColonne = 'taille7';
			$classeRDV = 'taille7RDV';
			$jEntier = 98;
			break;
	}

	list($jNeg, $mNeg, $aNeg) = explode('-', date('j-n-Y',mktime(0,0,0,$mois,$jour-$numJourSem-7,$annee)));
	list($jPos, $mPos, $aPos) = explode('-', date('j-n-Y',mktime(0,0,0,$mois,$jour-$numJourSem+7,$annee)));


	// affichage de l'entète du semainier
	echo 	'<p id="titreAgenda">',
				'<a href="?uti=',$GLOBALS['lienRendezVous'],'&jour=',$jNeg,'&mois=',$mNeg,'&annee=',$aNeg,'" class="flechegauche"><img src="../images/fleche_gauche.png" alt="picto fleche gauche"></a>',
				'<strong>Semaine du ',$affDate,'</strong> pour <strong>les L2</strong>',
				'<a href=?uti=',$GLOBALS['lienRendezVous'],'&jour=',$jPos,'&mois=',$mPos,'&annee=',$aPos,'" class="flechedroite"><img src="../images/fleche_droite.png" alt="picto fleche droite"></a>',
			'</p>',
			'<div id="agenda">',
				'<div id="intersection"></div>';
	

	$countJour =-1;

	// boucle d'affichage des cases jour
	for ($i=0; $i < 7; $i++) { 
		if (mb_substr($utiJours, $i, 1) == 1) 
		{	
			$countJour++;
			$posJour[(int)(date('Ymd',mktime(0,0,0,$mois,$jour-$numJourSem+$i,$annee)))] = $countJour;
			switch ($i) {
				case 0:
					$day = 'Lundi';
					break;
				case 1:
					$day = 'Mardi';
					break;
				case 2:
					$day = 'Mercredi';
					break;
				case 3:
					$day = 'Jeudi';
					break;
				case 4:
					$day = 'Vendredi';
					break;
				case 5:
					$day = 'Samedi';
					break;
				
				default:
					$day = 'Dimanche';
					break;
			}		
			if ($count == 0) 
			{
				$count++;
				echo '<div class="case-jour border-TRB ',$classColonne,' border-L">',$day,' ',date('j',$date+$i*86400),'</div>';
			}
			else
			{
				echo	'<div class="case-jour border-TRB ',$classColonne,'">',$day,' ',date('j',$date+$i*86400),'</div>';
			}
		}
	}

	// si on affiche le semainier d'un autre utilisateur on affiche pas les rendez-vous privé sinon on affiche tout
	if ($GLOBALS['lienRendezVous'] == $_SESSION['utiID']) {
		$public = '';
	}
	else
	{
		$public = 'AND categorie.catPublic = 1';
	}

	// Requête de sélection des rendez-vous sur une journée
	$sql = "SELECT rdvID, rdvLibelle, rdvDate, catCouleurFond, catCouleurBordure, catPublic, rdvIDUtilisateur
			FROM rendezvous, categorie
			WHERE rendezvous.rdvIDCategorie = categorie.catID
			$public
			AND rendezvous.rdvHeureFin = -1
			AND rendezvous.rdvIDUtilisateur = {$GLOBALS['lienRendezVous']}
			AND rendezvous.rdvDate >= $anneeDate1".(($numMoisDate1 < 10)?'0'.$numMoisDate1:$numMoisDate1).(($jourDate1 < 10)?'0'.$jourDate1:$jourDate1)."
			AND rendezvous.rdvDate <= $anneeDate2".(($numMoisDate2 < 10)?'0'.$numMoisDate2:$numMoisDate2).(($jourDate2 < 10)?'0'.$jourDate2:$jourDate2);

	// Exécution de la requête
	$R = mysqli_query($GLOBALS['bd'], $sql) or fd_bd_erreur($sql);

	// traitement et affichage des rendez-vous sur une journée
		if (mysqli_num_rows($R)) 
		{

			$minTopRDV = 58;
		
			echo '<div id=jEntier>';

			while ($D = mysqli_fetch_assoc($R)) 
			{
				ec_htmlProteger ($D);

				if (isset($posJour[$D['rdvDate']])) 
				{		
					$couleurHSL = ec_hexToHsl($D['catCouleurFond']); 

					if ($couleurHSL[2] > 0.5) //0.5 = 50%  
					{
						$textColor = '000000';
					} 
					else
					{
						$textColor = 'FFFFFF';
					} 

					$balise = 'a';

					if ($D['rdvIDUtilisateur'] != $_SESSION['utiID']) {
						$balise = 'div';
					}

					echo '<',$balise,' style="background-color: #',$D['catCouleurFond'],';',
    						  'border: solid 2px #',$D['catCouleurBordure'],';',
							  'color: #',$textColor,';height: 20px;left: ',46+$jEntier*$posJour[$D['rdvDate']],'px;" class="rendezvous ',$classeRDV,' rdvJEntier" href="rendezvous.php?id=',$D['rdvID'],'">',$D['rdvLibelle'],'</',$balise,'>';
				}

				$nbJours = mb_substr_count($utiJours, '1');
			}
			echo '</div>';
		}

	echo		'<div id="col-heures">';


	// affichage de la colonne des heure
	for ($i=$utiHeureMin; $i <= $utiHeureMax; $i++) { 
		echo 		'<div>',$i,'h</div>';
	}
	echo		'</div>';

	$count = 0;

	//affichage des colonnes des jours ainsi que les rendez vous
	for ($j=0; $j < 7; $j++) { 

		if (mb_substr($utiJours, $j, 1) == 1) {

			if ($count == 0) 
			{
				$count++;
				echo'<div class="col-jour border-TRB ',$classColonne,' border-L">';
			}
			else
			{
				echo'<div class="col-jour border-TRB ',$classColonne,'">';
			}

			for ($i=$utiHeureMin; $i < $utiHeureMax-1; $i++) 
			{ 
				echo	'<a href="rendezvous.php?id=-1&heure=',$i,'&jour=',date('j',$date+$j*86400),'&mois=',date('n',$date+$j*86400),'&annee=',date('Y',$date+$j*86400),'"></a>';
			}
			echo 		'<a href="rendezvous.php?id=-1&heure=',$i,'&jour=',date('j',$date+$j*86400),'&mois=',date('n',$date+$j*86400),'&annee=',date('Y',$date+$j*86400),'" class="case-heure-bas"></a>';

				$heureMin = ($utiHeureMin.'00');

				$heureMax = (($utiHeureMax).'00');

			// Requête de sélection des rendez-vous d'une journée

			$jourRdv = $jourDate1+$j;

			// selection des rendez vous de la journée
			$sql = "SELECT rdvID, rdvLibelle, rdvDate, catCouleurFond, catCouleurBordure, catPublic, rdvHeureDebut, rdvHeureFin, rdvIDUtilisateur
					FROM rendezvous, categorie
					WHERE rendezvous.rdvIDCategorie = categorie.catID
					AND rendezvous.rdvHeureFin != -1
					AND rendezvous.rdvIDUtilisateur = {$GLOBALS['lienRendezVous']}
					AND rendezvous.rdvDate = $anneeDate1".(($numMoisDate1 < 10)?'0'.$numMoisDate1:$numMoisDate1).(($jourRdv < 10)?'0'.$jourRdv:$jourRdv);

			// Exécution de la requête
			$R = mysqli_query($GLOBALS['bd'], $sql) or fd_bd_erreur($sql);

			while($D = mysqli_fetch_assoc($R))
			{
					ec_htmlProteger($D);

					// multiples verification pour determiner si on affiche ou non le rendez-vous

					if ($D['rdvHeureDebut'] < $heureMin && $D['rdvHeureFin'] > $heureMin) 
					{
						$D['rdvHeureDebut'] = $heureMin;
					}
					if ($D['rdvHeureFin'] > $heureMax && $D['rdvHeureDebut'] >= $heureMin) 
					{
						$D['rdvHeureFin'] = $heureMax;
					}
					if ($D['rdvHeureDebut'] >= $heureMin && $D['rdvHeureDebut'] < $heureMax && $D['rdvHeureFin'] >= $heureMin && $D['rdvHeureFin'] <= $heureMax && ($GLOBALS['lienRendezVous'] == $_SESSION['utiID'] || $D['catPublic'] == 1)) 
					{
						if ($D['rdvHeureDebut'] < 1000) {
							$heureDebut = mb_substr($D['rdvHeureDebut'], 0, 1);
							$minDebut = mb_substr($D['rdvHeureDebut'], 1, 2);
						}
						else
						{
							$heureDebut = mb_substr($D['rdvHeureDebut'], 0, 2);
							$minDebut = mb_substr($D['rdvHeureDebut'], 2, 2);
						}

						// paramètre pour positionner les rendezvous
						switch ($minDebut) {
							case 15:
								$minDebut = 10.25;
								break;
							case 30:
								$minDebut =  20.5;
								break;
							case 45:
								$minDebut = 30.75;
								break;
							
							default:
								$minDebut = 0;
								break;
						}

						if ($D['rdvHeureFin'] < 1000) {
							$heureFin = mb_substr($D['rdvHeureFin'], 0, 1);
							$minFin = mb_substr($D['rdvHeureFin'], 1, 2);
						}
						else
						{
							$heureFin = mb_substr($D['rdvHeureFin'], 0, 2);
							$minFin = mb_substr($D['rdvHeureFin'], 2, 2);
						}

						// paramètre pour positionner les rendezvous
						switch ($minFin) {
							case 15:
								$minFin = 10.25;
								break;
							case 30:
								$minFin =  20.5;
								break;
							case 45:
								$minFin = 30.75;
								break;
							
							default:
								$minFin = 0;
								break;
						}

						// determination de la couleur du texte du rendez vous suivant la luminosité du bloc
						$couleurHSL = ec_hexToHsl($D['catCouleurFond']); 

						if ($couleurHSL[2] > 0.5) //0.5 = 50%  
						{
							$textColor = '000000';
						} 
						else
						{
							$textColor = 'FFFFFF';
						}

						$balise = 'a';
						$lienRdv = 'href="rendezvous.php?id='.$D['rdvID'].'"';

						if ($D['rdvIDUtilisateur'] != $_SESSION['utiID']) {
							$balise = 'div';
							$lienRdv = '';
						}
						//affichage rendez vous
						echo '<',$balise,' style="background-color: #',$D['catCouleurFond'],';',
	    						  'border: solid 2px #',$D['catCouleurBordure'],';',
								  'color: #',$textColor,';',
								  'top: ',$minTopRDV+($heureDebut-$utiHeureMin)*41 + $minDebut,'px;', 
						          'height: ',32*($heureFin-$heureDebut)+9*($heureFin-$heureDebut-1)+($minFin-$minDebut),'px;" class="rendezvous ',$classeRDV,'" ',$lienRdv,'>',$D['rdvLibelle'],'</',$balise,'>';
					}
			}

			echo '</div>';

		}

	}

	echo	'</div>';

	// Libère la mémoire associée au résultat $R
	mysqli_free_result($R);

	// Déconnexion de la base de données
	mysqli_close($GLOBALS['bd']);

}

//_______________________________________________________________

/**
 * Creer une variable globale contenant l'url vers rendezvous.php avec l'ID de l'utilisateur à affiché en paramètre 
 *
 */
function ec_uti_rdv() {

	fd_bd_connexion();

	if(isset($_GET['uti']) && estEntier($_GET['uti']) && $_GET['uti'] != $_SESSION['utiID']) 
	{
		// Requête de sélection des utilisateurs
		$sql = "SELECT count(*) as count
				FROM suivi
				WHERE suiIDSuiveur = {$_SESSION['utiID']}
	            AND suiIDSuivi = {$_GET['uti']}";

		// Exécution de la requête
		$R = mysqli_query($GLOBALS['bd'], $sql) or fd_bd_erreur($sql);

		// traitement
		$D = mysqli_fetch_assoc($R);

		ec_htmlProteger ($D);
		if ($D['count'] == 1) {
			$GLOBALS['lienRendezVous'] = $_GET['uti'];
		}
		else
		{
			$GLOBALS['lienRendezVous'] = $_SESSION['utiID'];
		}

		// Libère la mémoire associée au résultat $R
		mysqli_free_result($R);

		// Déconnexion de la base de données
		mysqli_close($GLOBALS['bd']);
	}
	else
	{
		$GLOBALS['lienRendezVous'] = $_SESSION['utiID'];
	}
}
	

//_______________________________________________________________

/**
 * Renvoie le nom d'un mois.
 *
 * @param integer	$numero		Numéro du mois (entre 1 et 12)
 *
 * @return string 	Nom du mois correspondant
 */
function fd_get_mois($numero) {
	$numero = (int) $numero;
	($numero < 1 || $numero > 12) && $numero = 0;

	$mois = array('Erreur', 'Janvier', 'F&eacute;vrier', 'Mars',
				'Avril', 'Mai', 'Juin', 'Juillet', 'Ao&ucirc;t',
				'Septembre', 'Octobre', 'Novembre', 'D&eacute;cembre');

	return $mois[$numero];
}

//____________________________________________________________________________

/**
 * Formatte une date AAAAMMJJ en format lisible
 *
 * @param integer	$amj		Date au format AAAAMMJJ
 *
 * @return string	Date formattée JJ nomMois AAAA
 */
function fd_date_claire($amj) {
	$a = (int) substr($amj, 0, 4);
	$m = (int) substr($amj, 4, 2);
	$m = fd_get_mois($m);
	$j = (int) substr($amj, -2);

	return "$j $m $a";
}

//____________________________________________________________________________

/**
* Formatte une heure HHMM en format lisible
*
* @param integer	$h		Heure au format HHMM
*
* @return string	Heure formattée HH h SS
*/
function fd_heure_claire($h) {
	$m = (int) substr($h, -2);
	($m == 0) && $m = '';
	$h = (int) ($h / 100);

	return "{$h}h{$m}";
}

//____________________________________________________________________________

/**
 * Redirige l'utilisateur sur une page
 *
 * @param string	$page		Page où rediriger
 */
function fd_redirige($page) {
	header("Location: $page");
	exit();
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

//___________________________________________________________________
/**
 * Covertit une couleur hexadecimal en HSL
 *
 * @param int  $hex  couleur en hexadecimal
 * @return int  $h hue
 * @return int  $s saturation
 * @return int  $l lightness
 */
function ec_hexToHsl($hex) {
    $hex = array($hex[0].$hex[1], $hex[2].$hex[3], $hex[4].$hex[5]);
    $rgb = array_map(function($part) {
        return hexdec($part) / 255;
    }, $hex);

    $max = max($rgb);
    $min = min($rgb);

    $l = ($max + $min) / 2;

    if ($max == $min) {
        $h = $s = 0;
    } else {
        $diff = $max - $min;
        $s = $l > 0.5 ? $diff / (2 - $max - $min) : $diff / ($max + $min);

        switch($max) {
            case $rgb[0]:
                $h = ($rgb[1] - $rgb[2]) / $diff + ($rgb[1] < $rgb[2] ? 6 : 0);
                break;
            case $rgb[1]:
                $h = ($rgb[2] - $rgb[0]) / $diff + 2;
                break;
            case $rgb[2]:
                $h = ($rgb[0] - $rgb[1]) / $diff + 4;
                break;
        }

        $h /= 6;
    }
    return array($h, $s, $l);
}

//___________________________________________________________________

/**
	* 
	* Supression ou ajout d'un abonnement
	*
	* @global array		$_POST		zones de saisie du formulaire
	* @global array		$_GLOBALS	base de bonnées 
	*
	*/
	function ec_abonnement() {
		fd_bd_connexion();

		//-----------------------------------------------------
		// supression ou ajout d'un suivi dans la base de données   
		//-----------------------------------------------------

		
			if($_POST['valueBtn'] == 1){
				$S = "INSERT INTO suivi SET
						suiIDSuiveur = {$_SESSION['utiID']},
						suiIDSuivi = {$_POST['utiIDAbonne']}";

				$R = mysqli_query($GLOBALS['bd'], $S) or fd_bd_erreur($S);
			}
			else{
				$S = "DELETE FROM suivi
 			 		  WHERE suiIDSuiveur = {$_SESSION['utiID']}
 			  		  AND   suiIDSuivi = {$_POST['utiIDAbonne']}";

				$R = mysqli_query($GLOBALS['bd'], $S) or fd_bd_erreur($S);
			}	

		
		// Déconnexion de la base de données
		mysqli_close($GLOBALS['bd']);

	}

?>
