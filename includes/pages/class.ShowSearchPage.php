<?php

/**
 * @project XG Proyect
 * @version 2.10.x build 0000
 * @copyright Copyright (C) 2008 - 2012
 */

if(!defined('INSIDE')){ die(header("location:../../"));}

class ShowSearchPage
{
	function __construct ()
	{
		global $lang;

		$parse 	= $lang;
		$type 	= $_POST['type'];

		$searchtext = mysql_escape_string($_POST["searchtext"]);
		//queries fixed by Jstar
		if ( $_POST )
		{
			switch($type)
			{
				case "playername":
					$table 	= gettemplate('search/search_user_table');
					$row 	= gettemplate('search/search_user_row');
					$search	= doquery("SELECT * ,{{table}}statpoints.total_rank as rank FROM {{table}}users , {{table}}statpoints  WHERE {{table}}users.id = {{table}}statpoints.id_owner AND {{table}}users.username LIKE '%".$searchtext."%' LIMIT 25 ; ","");
				break;
				case "planetname":
					$table 	= gettemplate('search/search_user_table');
					$row 	= gettemplate('search/search_user_row');
					$search	= doquery("SELECT * ,{{table}}statpoints.total_rank as rank FROM {{table}}planets , {{table}}statpoints  WHERE {{table}}planets.id_owner = {{table}}statpoints.id_owner AND {{table}}planets.name LIKE '%".$searchtext."%' LIMIT 25 ; ","");
				break;
				case "allytag":
					$table 	= gettemplate('search/search_ally_table');
					$row 	= gettemplate('search/search_ally_row');
					$search	= doquery("SELECT * ,{{table}}statpoints.total_points as points FROM {{table}}alliance , {{table}}statpoints  WHERE {{table}}alliance.id = {{table}}statpoints.id_owner AND {{table}}alliance.ally_tag LIKE '%".$searchtext."%' LIMIT 25 ; ","");
				break;
				case "allyname":
					$table 	= gettemplate('search/search_ally_table');
					$row 	= gettemplate('search/search_ally_row');
					$search = doquery("SELECT * ,{{table}}statpoints.total_points as points FROM {{table}}alliance , {{table}}statpoints  WHERE {{table}}alliance.id = {{table}}statpoints.id_owner AND {{table}}alliance.ally_name LIKE '%".$searchtext."%' LIMIT 25 ; ","");
				break;
				default:
					$table 	= gettemplate('search/search_user_table');
					$row 	= gettemplate('search/search_user_row');
					$search = doquery("SELECT * ,{{table}}statpoints.total_rank as rank FROM {{table}}users , {{table}}statpoints  WHERE {{table}}users.id = {{table}}statpoints.id_owner AND {{table}}users.username LIKE '%".$searchtext."%' LIMIT 25 ; ","");
				break;
			}
		}

		if(isset($searchtext) && isset($type))
		{
			while($s = mysql_fetch_array($search, MYSQL_BOTH))
			{
				if($type == 'playername' or $type == 'planetname')
				{
					if($s['ally_id'] != 0 && $s['ally_request'] == 0)
					{
						$aquery = doquery("SELECT id,ally_name FROM {{table}} WHERE id = ".intval($s['ally_id'])."","alliance",TRUE);
					}
					else
					{
						$aquery = array();
					}

					if ($type == "planetname")
					{
						$pquery 			= doquery("SELECT username,ally_id,ally_name FROM {{table}} WHERE id = ".intval($s['id_owner'])."","users",TRUE);
						$s['planet_name'] 	= $s['name'];
						$s['username'] 		= $pquery['username'];
						$s['ally_name'] 	= ($pquery['ally_name']!='')?"<a href=\"game.php?page=alliance&mode=ainfo&a={$pquery['ally_id']}\">{$pquery['ally_name']}</a>":'';
					}
					else
					{
						$pquery 			= doquery("SELECT name FROM {{table}} WHERE id = ".intval($s['id_planet'])."","planets",TRUE);
						$s['planet_name']	= $pquery['name'];
						$s['ally_name'] 	= ($aquery['ally_name']!='')?"<a href=\"game.php?page=alliance&mode=ainfo&a={$aquery['id']}\">{$aquery['ally_name']}</a>":'';
					}

					$s['position'] 		= "<a href=\"game.php?page=statistics&start=".$s['rank']."\">".$s['rank']."</a>";
					$s['dpath'] 		= DPATH;
					$s['coordinated'] 	= "{$s['galaxy']}:{$s['system']}:{$s['planet']}";
					$result_list 	   .= parsetemplate($row, $s);
				}
				elseif($type=='allytag'||$type=='allyname')
				{
					$s['ally_points'] = pretty_number($s['ally_points']);

					$s['ally_tag'] = "<a href=\"game.php?page=alliance&mode=ainfo&tag={$s['ally_tag']}\">{$s['ally_tag']}</a>";
					$result_list .= parsetemplate($row, $s);
				}
			}
			if($result_list!='')
			{
				$parse['result_list'] = $result_list;
				$search_results = parsetemplate($table, $parse);
			}
		}

		$parse['type_playername'] 	= ($_POST["type"] == "playername") ? " SELECTED" : "";
		$parse['type_planetname'] 	= ($_POST["type"] == "planetname") ? " SELECTED" : "";
		$parse['type_allytag'] 		= ($_POST["type"] == "allytag") ? " SELECTED" : "";
		$parse['type_allyname'] 	= ($_POST["type"] == "allyname") ? " SELECTED" : "";
		$parse['searchtext'] 		= $searchtext;
		$parse['search_results'] 	= $search_results;

		display(parsetemplate(gettemplate('search/search_body'), $parse));
	}
}
?>