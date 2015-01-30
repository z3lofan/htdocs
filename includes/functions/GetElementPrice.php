<?php

/**
 * @project XG Proyect
 * @version 2.10.x build 0000
 * @copyright Copyright (C) 2008 - 2012
 */

if(!defined('INSIDE')){ die(header("location:../../"));}

	function GetElementPrice ($user, $planet, $Element, $userfactor = TRUE, $level = FALSE)
	//function GetElementPrice ($user, $planet, $Element, $userfactor = TRUE)
	{
		global $pricelist, $resource, $lang;

		//if ($userfactor) // OLD CODE
		if ($userfactor && ($level === FALSE)) // FIX BY JSTAR
			$level = ($planet[$resource[$Element]]) ? $planet[$resource[$Element]] : $user[$resource[$Element]];

		$is_buyeable = TRUE;

		$array = array(
			'metal'      => $lang['Metal'],
			'crystal'    => $lang['Crystal'],
			'deuterium'  => $lang['Deuterium'],
			'energy_max' => $lang['Energy']
		);

		$text = $lang['fgp_require'];
		foreach ($array as $ResType => $ResTitle)
		{
			if ($pricelist[$Element][$ResType] != 0)
			{
				$text .= $ResTitle . ": ";
				if ($userfactor)
					$cost = floor($pricelist[$Element][$ResType] * pow($pricelist[$Element]['factor'], $level));
				else
					$cost = floor($pricelist[$Element][$ResType]);

				if ($cost > $planet[$ResType])
				{
					$text .= "<b style=\"color:red;\"> <t title=\"-" . pretty_number ($cost - $planet[$ResType]) . "\">";
					$text .= "<span class=\"noresources\">" . pretty_number($cost) . "</span></t></b> ";
					$is_buyeable = FALSE;
				}
				else
					$text .= "<b style=\"color:lime;\">" . pretty_number($cost) . "</b> ";
			}
		}
		return $text;
	}
?>