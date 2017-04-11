<?php

/*
 *
 * ===============================================
 * Name: func.time_to_seconds.php
 * ===============================================
 * Beschreibung:
 * wandle 4 oder 6 stellige Zeitanzeigen in Sekunden um
 * der Trenner zwischen den Bloecken ist der doppelpunkt
 * ===============================================
 *
 * $Author:$
 * $LastChangedDate:$
 * $LastChangedBy:$
 * $Id:$
 * $Revision:$
 *
 */


function time_to_seconds($value)
{
	if (strlen($value) == 8)
	{
		$seconds = (substr($value, 0, 2) * 60 * 60) + (substr($value, 3, 2) * 60) + (substr($value, 6, 2));
		return $seconds;
	}
	elseif (strlen($value) == 5)
	{
		$seconds = (substr($value, 0, 2) * 60 ) + (substr($value, 3, 2));
		return $seconds;
	}
	else
	{
		return 0;
	}

}

?>