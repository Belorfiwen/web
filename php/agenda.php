<?php
/** @file
 * Page d'accueil de l'application 24sur7
 *
 * @author : Frederic Dadeau - frederic.dadeau@univ-fcomte.fr
 */

include('bibli_24sur7.php');	// Inclusion de la bibliothéque
session_start();

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

echo	'</aside>',
		'<section id="bcCentre">
			<p id="titreAgenda">
				<a href="#" class="flechegauche"><img src="../images/fleche_gauche.png" alt="picto fleche gauche"></a>
				<strong>Semaine du 9  au 15 F&eacute;vrier</strong> pour <strong>les L2</strong>
				<a href="#" class="flechedroite"><img src="../images/fleche_droite.png" alt="picto fleche droite"></a>
			</p>
			<section id="agenda">
				<div id="intersection"></div>
				<div class="case-jour border-TRB border-L">Lundi 9</div>
				<div class="case-jour border-TRB">Mardi 10</div>
				<div class="case-jour border-TRB">Mercredi 11</div>
				<div class="case-jour border-TRB">Jeudi 12</div>
				<div class="case-jour border-TRB">Vendredi 13</div>
				<div class="case-jour border-TRB">Samedi 14</div>
				<div id="col-heures">
					<div>7h</div>
					<div>8h</div>
					<div>9h</div>
					<div>10h</div>
					<div>11h</div>
					<div>12h</div>
					<div>13h</div>
					<div>14h</div>
					<div>15h</div>
					<div>16h</div>
					<div>17h</div>
					<div>18h</div>
				</div>
				<div class="col-jour border-TRB border-L">
					<a href="#"></a>
					<a href="#"></a>
					<a href="#"></a>
					<a href="#"></a>
					<a href="#"></a>
					<a href="#"></a>
					<a href="#"></a>
					<a href="#"></a>
					<a href="#"></a>
					<a href="#"></a>
					<a href="#" class="case-heure-bas"></a>
					<a style="background-color: #00FF00;
    						  border: solid 2px #00DD00;
							  color: #000000;
							  top: 131px; 
					          height: 114px;" class="rendezvous" href="#">TP LW</a>
					<a style="color: #FFFFFF;
							  background-color: #FF0000;
							  border: solid 2px #DD0000;
							  top: 357px; 
							  height: 114px;" class="rendezvous" href="#">TP LW</a>
				</div>
				<div class="col-jour border-TRB">
					<a href="#"></a>
					<a href="#"></a>
					<a href="#"></a>
					<a href="#"></a>
					<a href="#"></a>
					<a href="#"></a>
					<a href="#"></a>
					<a href="#"></a>
					<a href="#"></a>
					<a href="#"></a>
					<a href="#" class="case-heure-bas"></a>
					<a style="color: #FFFFFF;
							  background-color: #0000FF;
							  border: solid 2px #0000DD;
							  top: 295px; 
							  height: 114px;" class="rendezvous" href="#">TP LW</a>
				</div>
				<div class="col-jour border-TRB">
					<a href="#"></a>
					<a href="#"></a>
					<a href="#"></a>
					<a href="#"></a>
					<a href="#"></a>
					<a href="#"></a>
					<a href="#"></a>
					<a href="#"></a>
					<a href="#"></a>
					<a href="#"></a>
					<a href="#" class="case-heure-bas"></a>
				</div>
				<div class="col-jour border-TRB">
					<a href="#"></a>
					<a href="#"></a>
					<a href="#"></a>
					<a href="#"></a>
					<a href="#"></a>
					<a href="#"></a>
					<a href="#"></a>
					<a href="#"></a>
					<a href="#"></a>
					<a href="#"></a>
					<a href="#" class="case-heure-bas"></a>
				</div>
				<div class="col-jour border-TRB">
					<a href="#"></a>
					<a href="#"></a>
					<a href="#"></a>
					<a href="#"></a>
					<a href="#"></a>
					<a href="#"></a>
					<a href="#"></a>
					<a href="#"></a>
					<a href="#"></a>
					<a href="#"></a>
					<a href="#" class="case-heure-bas"></a>
				</div>
				<div class="col-jour border-TRB">
					<a href="#"></a>
					<a href="#"></a>
					<a href="#"></a>
					<a href="#"></a>
					<a href="#"></a>
					<a href="#"></a>
					<a href="#"></a>
					<a href="#"></a>
					<a href="#"></a>
					<a href="#"></a>
					<a href="#" class="case-heure-bas"></a>
				</div>
			</section>
		</section><div style="clear: both;"> </div>
	</section>',

fd_html_pied();
?>