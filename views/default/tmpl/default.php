<?php
//administrator/components/com_duesexport/views/default/tmpl/default.php

defined('_JEXEC') or die('Restricted access');
global $duesexport_initial_mode;
global $_CB_Admin_Done, $_CB_adminpath, $ueConfig, $_CB_framework, $_CB_database, $mainframe;

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
//echo "Process users called<br>";
cbimport( 'cb.database' );
cbimport( 'cb.html' );
$versionstuff=new JVersion;
$thisversion=$versionstuff->getShortVersion();
JToolBarHelper::title(JText::_('DUESEXPORT'), 'generic.png');

JToolBarHelper::preferences('com_duesexport','500','750');
if((strcasecmp( substr($thisversion,0,3), '1.6' ) >= 0)){
	JSubMenuHelper::addEntry(JText::_('DUESEXPORT_TAB2'),'index.php?option=com_duesexport&task=save&view=default');
	JSubMenuHelper::addEntry(JText::_('DUESEXPORT_TAB3'),'index.php?option=com_duesexport&task=about&view=about');
}
$params= JComponentHelper::getParams('com_duesexport');
$delimiter=$params->get('csv_delimiter');
$excluded_fields=$params->get('save_exclude_list');

/* get field list for display purposes
 * 
 */

if(strlen($delimiter)<1){
	echo "<b><h1>".JText::_('DUESEXPORT_WARNING1'). "</h1></b><br>",
	"<b><h2>".JTEXT::_('DUESEXPORT_WARNING2')."</h2></b><br><br><br>";
}


?>


<form action="index.php?option=com_duesexport" method="post"
	enctype="multipart/form-data" name="adminForm" id="duesexport-form"
	class="form-validate">

<table border="0">
	<tr>
		<td valign="bottom" align="right" width="30%"></td>
		<td><input style="background-color:green;color:white;" 
				title='Process or Save the CSV'
				type="button" name="process" value="GO : Process the CSV"
				onclick="document.adminForm.submit();" />		</td>
	</tr>
	<tr>
		<td  align="right" width="30%"><?php  echo JText::_('DUESEXPORT_DELIMITER')?>: </td>
		<td><input class="inputbox" type="text" name="delimiter"
			value=<?php echo '"'.$delimiter.'"';?> size="3" maxlength="3" /><?php  echo " ".JText::_('DUESEXPORT_TAB')?>
		</td>
	</tr>
	<tr>
		<td valign="top" align="right" width="30%"><?php  echo JText::_('DUESEXPORT_MODE')?></td>
		<td>
		<input class="inputbox" type="radio" name="opptype" value="3" checked="checked"/> <b>
		<?php  echo JText::_('DUESEXPORT_SAVE')?></b><br />
		
		</td>
	</tr>
	<tr>
		<td valign="bottom" align="right" width="30%"><?php  echo JText::_('DUESEXPORT_KEYON')?></td>
		<td><input class="inputbox" type="radio" name="keytype"
			checked="checked" value="1" />  <b><?php  echo JText::_('DUESEXPORT_KEYUSER')?></b>
			<input class="inputbox" type="radio" name="keytype"
			value="2" /> <b><?php  echo JText::_('DUESEXPORT_KEYEMAIL')?></b> <br />
		</td>
	</tr>
	<tr>
		<td valign="bottom" align="right" width="30%"><?php  echo JText::_('DUESEXPORT_DISPLAYDEFAULT')?></td>
		<td><input class="inputbox" type="checkbox" name="defaultname"
			value="1" checked="checked" /> <?php  echo JText::_('DUESEXPORT_NAMEDEFAULTMESSAGE')?></td>
	</tr>

	
</table>
<input type="hidden" name="boxchecked" value="0" /> <input type="hidden"
	name="task" value="process" /> <input type="hidden" name="boxchecked"
	value="0" /> <input type="hidden" name="hidemainmenu" value="0" /></form>
