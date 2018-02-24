<?php
/**
 * Joomla! 1.5 component dues export
 * @package Joomla
 * @subpackage DUESEXPORT
 * @license GNU/GPL
 *
 * This component manages users using a CSV file
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.controller' );
require_once( JPATH_COMPONENT.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'helper.php'  );

/**
 * DUESEXPORT Controller
 *
 * @package Joomla
 * @subpackage DEUSEXPORT
 */


class DuesexportController extends JControllerLegacy 
{
	/**
	 * Constructor
	 * @access private
	 * @subpackage DUES Export
	 */
	function __construct() {
		//Get View
		if(JRequest::getCmd('view') == '') {
			JRequest::setVar('view', 'default');
			JRequest::setVar('layout','default');
		}
		$this->item_type = 'Default';


		parent::__construct();
	}


	
	function save ($delimiter){
		include_once JPATH_COMPONENT_ADMINISTRATOR.DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR.'save.php';
		$params= JComponentHelper::getParams('com_duesexport');
		if($delimiter=="" or empty($delimiter)){
			$delimiter=$params->get('csv_delimiter');
		}
		if($delimiter=='TAB'||$delimiter=='tab'){$delimiter='\t';}
		//$delimiter=",";
		$result=duesexport_saveusers($delimiter);
	}


}



?>