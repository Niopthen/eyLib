<?php

/*
 *
 * ===============================================
 * Name:
 * ===============================================
 * Beschreibung:
 *
 *
 * ===============================================
 *
 * $Author:$
 * $LastChangedDate:$
 * $LastChangedBy:$
 * $Id:$
 * $Revision:$
 *
 */


function logwriter()
{
	if ((!key_exists('LOGGER_START', $_SESSION)) || ($_SESSION['LOGGER_START'] == ''))
	{
		$_SESSION['LOGGER_START'] = microtime(TRUE);
	}


	if ((!key_exists('LOGGER_STOP', $_SESSION)) || ($_SESSION['LOGGER_STOP'] == ''))
	{
		if ($_SESSION['LOGGER_START'] != '')
		{
			$_SESSION['LOGGER_STOP'] = microtime(TRUE);
		}
	}


	if (($_SESSION['LOGGER_START'] != '') && ($_SESSION['LOGGER_STOP'] != ''))
	{
		$myContent = 'Start: ' . $_SESSION['LOGGER_START'] . ' - STOP: ' . $_SESSION['LOGGER_STOP'] . "\n";
		unset($_SESSION['LOGGER_START']);
		unset($_SESSION['LOGGER_STOP']);

//		if (defined('LOG_PATH'))
//		{

			$fp = '';
			$FileName = date('y.m.d', time()) . '-eyLogger.log';
			$completeFileName = LOG_PATH . $FileName;

			if (file_exists($completeFileName) != TRUE)
			{
				$fp = fopen($completeFileName, 'w');
			}
			else
			{
				$fp = fopen($completeFileName, 'a');
			}
			fputs($fp, $myContent);
			fclose($fp);
//		}
	}

}

?>