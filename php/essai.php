<?php
/** 
 * Page Agenda de l'application 24sur7
 *
 */

include('bibli_24sur7.php');	// Inclusion de la bibliothéque
session_start();

$jour = 0;
$mois = 0;
$annee = 0;

echo '<a style="background-color: #00FF00;',
    						  'border: solid 2px #00DD00;',
							  'color: #000000;',
							  'top: 131px;', 
					          'height: 114px;" class="rendezvous" href="#">TP LW</a>',
					'<a style="color: #FFFFFF;',
							  'background-color: #FF0000;',
							  'border: solid 2px #DD0000;',
							  'top: 357px;',
							  'height: 114px;" class="rendezvous" href="#">TP LW</a>',

							  '<a style="color: #FFFFFF;
							  background-color: #0000FF;
							  border: solid 2px #0000DD;
							  top: 295px; 
							  height: 114px;" class="rendezvous" href="#">TP LW</a>';

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

while ($D = mysqli_fetch_assoc($R)) 
	{
		ec_htmlProteger ($D);

		if ($D['utiHeureMax'] == -1) 
		{
			echo 	'<p>',
						'<a href="?uti=',$D['utiID'],'">Agenda de ',$D['utiNom'],'</a> ',
					'</p>',
					'<ul>';
			$count++;
		}

		echo 			'<li> <div class="categorie" style="border: solid 2px #',$D['catCouleurBordure'],';background-color: #',$D['catCouleurFond'],';"></div>',$D['catNom'];
	}


?>
<a style="background-color: #00FF00;',
    						  'border: solid 2px #00DD00;',
							  'color: #000000;',
							  'top: 28px;', 
					          'height: 34px;" class="rendezvous ',$classeRDV,'" href="rendezvous.php">TP LW</a>
