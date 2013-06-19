<?php
include('includes/opclass.php');
include('includes/chatclass.php');
$userID = fSession::get('userid');
$op = new OpClass();
$chat = new ChatClass();
?>
<br/><br/>
<div id="enteringdiv">
	<h3>Llamadas Entrantes</h3>
	<div id="enteringcalls"></div>
</div>
<div id="offdiv">
	<?php 
		$rate = $op->getRating();
		$rate2 = $op->getRating();
		$c = 0;
		while($r = mysql_fetch_assoc($rate))
		{
			if($r['sumatotal'] != 0)
			{ 
				$c++;
				if($r['userID'] == $userID)
				{
					$mipuntuacion = $r['sumatotal'];
					$mipromedio = $r['promedio'];
					$miposicion = $c;
				}
			}
		}
		
		if($c > 0)
		{	
			echo '<table border="0" cellpadding="3" cellspacing="0" width="100%">';
			echo '<tr><td class="tableheader">No</td><td class="tableheader">Tabla de posiciones</td><td class="tableheader">Tu Puntuacion Total</td><td class="tableheader">Tu Promedio</td>';
			echo '<td rowspan="'.($c+1).'" valign="top" align="center">';
			
			if($miposicion == 1 && $mipromedio > 4) echo '<img src="images/medalla_oro.png" border="0" width="100" alt="Galardon Oro al buen servicio." />';
			if($miposicion == 2 && $mipromedio > 4) echo '<img src="images/medalla_plata.png" border="0" width="100" alt="Galardon Plata al buen servicio." />';
			if($miposicion == 3 && ($mipromedio > 3 && $mipromedio <= 4)) echo '<img src="images/medalla_bronce.png" border="0" width="100" alt="Galardon Bronce al buen servicio." />';
			if($mipromedio <= 3) echo '<img src="images/medalla_vacia.png" border="0" width="100" />';
			
			echo '</td>';
			echo '</tr>';
			
			$cc = 0;
			while($ra = mysql_fetch_assoc($rate2))
			{
				if($ra['sumatotal'] != 0) { 
					echo '<tr>';
					echo '<td class="'.$chat->RowColor($cc,'normal','even').'" valign="top">'.($cc+1).'</td>';
					echo '<td class="'.$chat->RowColor($cc,'normal','even').'" valign="top">'.$ra['sumatotal'].'</td>';
					if($cc == 0)
					{
						echo '<td rowspan="'.$c.'" valign="top">'.$mipuntuacion.'</td>';
						echo '<td rowspan="'.$c.'" valign="top">'.number_format($mipromedio,2).'</td>';
					}
					echo '</tr>';
					$cc++;
				}
			}
			echo '</table>';
		}else{
			echo '<div class="msg_alert">En estos momentos no hay datos para mostrar.</div>';
		}
	?>
</div>
<div class="clear">&nbsp;</div>