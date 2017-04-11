<?php

/*
 *
 * ===============================================
 * Name: func.create_password.php
 * ===============================================
 * Beschreibung:
 * erstelle ein Passwort nach bestimmten Regeln
 *
 * ===============================================
 *
 * $Author$
 * $LastChangedDate$
 * $LastChangedBy$
 * $Id$
 * $Revision$
 *
 */


function create_password($length, $number = false, $char = false)
{
	$numberpool = '1234567890';
	$charpool	= 'abcdefghijklmnopqrstuvwxyz';

	$pool = 'qwertzupasdfghkyxcvbnm';
	$pool .= '1234567890';
	$pool .= 'WERTZUPLKJHGFDSAYXCVBNM';

    $pass_number    = '';
    $pass_char      = '';
    $pass_word      = '';

	if ($number == true)
	{
		$length = $length - 1;
		srand ((double)microtime()*1000000);
		for($index = 0; $index < 1; $index++)
		{
			$pass_number .= substr($numberpool,(rand()%(strlen ($numberpool))), 1);
		}
	}

	if ($char == true)
	{
		$length = $length - 1;
		srand ((double)microtime()*1000000);
		for($index = 0; $index < 1; $index++)
		{
			$pass_char .= substr($charpool,(rand()%(strlen ($charpool))), 1);
		}
	}

	srand ((double)microtime()*1000000);
	for($index = 0; $index < $length; $index++)
	{
		$pass_word .= substr($pool,(rand()%(strlen ($pool))), 1);
	}

	$pass_word = $pass_char.$pass_word.$pass_number;

	return $pass_word;
}
?>