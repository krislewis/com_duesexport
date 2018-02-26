<?php
/**
 * Joomla! 1.5-205 component Dues EXPORT
 *
 * @package Joomla
 * @subpackage CBDUES EXPORT
 * 
 * @license GNU/GPL

 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.controller' );
require_once( JPATH_COMPONENT.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'helper.php'  );
global $_CB_framework,$_CB_database, $ueConfig;
/** @global string $_CB_adminpath
 *  @global array $ueConfig
 */
global $_CB_Admin_Done, $_CB_adminpath, $ueConfig, $mainframe;

if ( defined( 'JPATH_ADMINISTRATOR' ) ) {
	$_CB_adminpath		=	JPATH_ADMINISTRATOR . '/components/com_comprofiler';
	include_once $_CB_adminpath . '/plugin.foundation.php';
		if (is_file ($_CB_adminpath . '/ue_config.php')) include_once $_CB_adminpath . '/ue_config.php'; // cb version 2.0.0 + condition added
	include_once $_CB_adminpath . '/plugin.class.php';
	include_once $_CB_adminpath . '/comprofiler.class.php';

} else {
	$_CB_adminpath		=	$mainframe->getCfg( 'absolute_path' ). '/administrator/components/com_comprofiler';
	include_once $_CB_adminpath . '/plugin.foundation.php';
	include_once $_CB_adminpath . '/ue_config.php';
	include_once $_CB_adminpath . '/plugin.class.php';
	include_once $_CB_adminpath . '/comprofiler.class.php';
}
function duesexport_saveusers($delimiter){
	global $_CB_framework,$_CB_database, $ueConfig;
	/** @global string $_CB_adminpath
	 *  @global array $ueConfig
	 */
	global $_CB_Admin_Done, $_CB_adminpath, $ueConfig, $mainframe;

	if ( defined( 'JPATH_ADMINISTRATOR' ) ) {
		$_CB_adminpath		=	JPATH_ADMINISTRATOR . '/components/com_comprofiler';
			if (is_file ($_CB_adminpath . '/ue_config.php')) include_once $_CB_adminpath . '/ue_config.php'; // cb version 2.0.0 + condition added
		include_once $_CB_adminpath . '/plugin.class.php';
		include_once $_CB_adminpath . '/comprofiler.class.php';

	} else {
		$_CB_adminpath		=	$mainframe->getCfg( 'absolute_path' ). '/administrator/components/com_comprofiler';
		include_once $_CB_adminpath . '/ue_config.php';
		include_once $_CB_adminpath . '/plugin.class.php';
		include_once $_CB_adminpath . '/comprofiler.class.php';
	}
	//	echo "Starting to save<br>";
	$duesexport_debug=FALSE;
	cbimport( 'cb.database' );
	cbimport( 'cb.html' );
	jimport('joomla.user.helper');
	if (strlen($delimiter)<=0){
		// now issue warning messages and exit.
		echo "<b><H1>".JText::_('DUESEXPORT_WARNING1'). "</H1></b><br>",
		"<b><h2>".JTEXT::_('DUESEXPORT_WARNING2')."</h2></b><br><br><br>";
		return false;
	}
	$jver16=FALSE;
	$versionstuff=new JVersion;
	$thisversion=$versionstuff->getShortVersion();
	if((strcasecmp( substr($thisversion,0,3), '1.6' ) >= 0)){
		$jver16=TRUE;
	}

	$query_3_join="SELECT c.firstname, c.middlename, c.lastname, u.email, c.cb_memberstatus, c.cb_memberlevel, d.year, d.status, d.date_paid ";
	$query_3_join .= "FROM #__user_dues as d INNER JOIN #__users as u ON u.id=d.user_id INNER JOIN #__comprofiler as c on u.id=c.user_id";

	$_CB_database->setQuery($query_3_join);
	$result=$_CB_database->query();
	if(!$result){
		echo "Error retrieving the data serious error<br>";
		echo $_CB_database->getErrorMSG()."<br>";
		return FALSE;
	}
	$numbusers=$_CB_database->getNumRows();
	$fulldata=$_CB_database->loadAssocList();

	/*
	 * Write the page header
	 *
	 */
	$output_csv_name="users_".date( 'YmdHiS', $_CB_framework->now() ).".csv";
	echo "$output_csv_name <br>";
	if(!$duesexport_debug){
		while( @ob_end_clean() );
		ob_start();
		$browser="IE";
		header('Content-Type: '.(($browser=='IE' || $browser=='OPERA')?
        'text/plain':'text/plain'));
		header('Expires: '.gmdate('D, d M Y H:i:s').' GMT');

		if($browser=='IE') {
			header('Content-Disposition: attachment; filename="'.$output_csv_name.'"');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Pragma: public');
		} else {
			header('Content-Disposition: attachment; filename="'.$output_csv_name.'"');
			header('Cache-Control: no-cache, must-revalidate');
			header('Pragma: no-cache');
		}
	}
	// write header array
	$titlearray=array("First Name", "Middle","Last Name", "Email", "Status", "Level", "Year", "Paid Status", "Date Paid");
	$outputrow= "\"" . implode("\"$delimiter\"", $titlearray) . "\"" ."\r\n";
	print $outputrow;
	$usercount=0;
	Foreach($fulldata as $thisuser){
		if($duesexport_debug){
			print_r($thisuser);
			echo "<br>";
		}

		if($jver16){
		$thisusers_groups=$thisone->groups;
		$thisusers_groupnames=array();
			foreach ($thisusers_groups as $group_number)
				{
					$thisusers_groupnames[]=$_CB_framework->acl->get_group_name($group_number);
				}
		if(count($thisusers_groupnames)>0){
				$thisones_types=implode("|",($thisusers_groupnames));
				//	$thisones_types=implode("|",array_keys($thisusers_groups));
			}
		} else {
			$thisones_types=$thisone->usertype;
		}

		// now we have to escape each field
		$thisuser2=array();
		// alternative string to use is \0..\37\177..\377
		$counter=0;
		foreach($thisuser as $thiscell){
			$newcell=str_replace("\"","\"\"",$thiscell);
			$newcell=addcslashes($newcell, "\0..\37");
			if($counter == 7){ //paid status, change to paid or owed
				$newcell = ($thiscell == 0 ? JTEXT::_('DUESEXPORT_OWED') : JTEXT::_('DUESEXPORT_PAID'));
			}
			$thisuser2[]=$newcell;
			$counter++;
		}

		$outputrow= "\"" . implode("\"$delimiter\"", $thisuser2) . "\"" ."\r\n";

		if(!$duesexport_debug){
			print($outputrow);
		} else {
			print_r($outputrow);
			echo "<br>";
		}

	}
	if(!$duesexport_debug){
		ob_end_flush();
	}
	exit;
}

?>
