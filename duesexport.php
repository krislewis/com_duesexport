<?php
/**
 * Joomla! 1.5 component Dues Export
 *
 * @author Paul Jacobson and Stephen Thompson
 * @copyright Paul Jacobson and Stephen Thompson
 * @package Joomla
 * @subpackage Dues export
 * @license GNU/GPL
 *
 * This component manages users using a CSV file
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');
global $_CB_framework,$_CB_database, $ueConfig;
/** @global string $_CB_adminpath
 *  @global array $ueConfig
 */
global $_CB_Admin_Done, $_CB_adminpath, $ueConfig, $mainframe;

if ( defined( 'JPATH_ADMINISTRATOR' ) ) {
	$_CB_adminpath		=	JPATH_ADMINISTRATOR . '/components/com_comprofiler';
	include_once $_CB_adminpath . '/plugin.foundation.php'; // version 2.0.0 new line
	if ( $_CB_framework->getUi() != 2 ) 	include_once $_CB_adminpath . '/ue_config.php'; // cb version 2.0.0 new 
	include_once $_CB_adminpath . '/plugin.class.php';
	include_once $_CB_adminpath . '/comprofiler.class.php';

} else {
	$_CB_adminpath		=	$mainframe->getCfg( 'absolute_path' ). '/administrator/components/com_comprofiler';
	include_once $_CB_adminpath . '/ue_config.php';
	include_once $_CB_adminpath . '/plugin.class.php';
	include_once $_CB_adminpath . '/comprofiler.class.php';
}

global $duesexport_initial_mode, $duesexport_opptype,$duesexport_controller;
// Require the base controller
if (!isset($task))$task="";
$duesexport_initial_mode=$task;
require_once JPATH_COMPONENT_ADMINISTRATOR.DIRECTORY_SEPARATOR.'controller.php';

// Require the helpers
require_once JPATH_COMPONENT_ADMINISTRATOR.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'helper.php';

// Initialize the controller
$duesexport_controller = new DuesExportController( );

$input=new JInput();
$task= trim(JRequest::getVar('task',null));
$duesexport_opptype=trim(JRequest::getVar('opptype',null));
$duesexport_filename=(JRequest::getVar('csvusers',null,'FILES'));
$duesexport_keytype=trim(jRequest::getVar('keytype'));

if($task==''){
	$duesexport_controller->display();
	return TRUE;

}

if($task=='about'){
	JRequest::setVar('view','default');
	JRequest::setVar('layout','about');
	$duesexport_controller->display();
	return TRUE;
}
if($task=='save'){
	$delimiter=cbGetParam($_REQUEST,'delimiter',null);
	if($delimiter=='TAB'||$delimiter=='tab'){$delimiter='\t';}
	$duesexport_controller->save($delimiter);
	return TRUE;
}


//echo "<br>return from task";
$duesexport_controller->redirect();
?>