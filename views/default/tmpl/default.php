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
$never_save=array("id","cid");
$full_field_names=$_CB_database->getTableFields(array('#__users','#__comprofiler'),TRUE);
$users_fields=$full_field_names['#__users'];
$dues_fields=$_CB_database->getTableFields(array('#__user_dues','#__comprofiler'),TRUE);
$comprofiler_fields=$full_field_names['#__comprofiler'];
$users_keys=array_keys($users_fields);
$comprofiler_keys=array_keys($comprofiler_fields);
$users_fields_edited=array_diff($users_keys, $never_save);
$comprofiler_fields_edited=array_diff($comprofiler_keys,$never_save);
$available_fields=array_merge($users_fields_edited,$comprofiler_fields_edited);
$field_list=implode(($delimiter." "),$available_fields);
if(strlen($delimiter)<1){
	echo "<b><h1>".JText::_('DUESEXPORT_WARNING1'). "</h1></b><br>",
	"<b><h2>".JTEXT::_('DUESEXPORT_WARNING2')."</h2></b><br><br><br>";
}

echo "<p>".JText::_('DUESEXPORT_STRING2A')." ";
echo "".JText::_('DUESEXPORT_STRING2B')." ";
echo "".JText::_('DUESEXPORT_STRING2C')." ";
echo "".JText::_('DUESEXPORT_STRING2D')." ";
echo "".JText::_('DUESEXPORT_COMPOUNDHOWTO')."</p>";
echo "<h3>".JText::_('DUESEXPORT_STRING3')."</h3>";
echo "<h3>".JText::_('DUESEXPORT_AVAILABLE_FIELDS').": </h3>".$field_list."<br><br>";
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
		<td><input class="inputbox" type="radio" name="opptype" value="1" />
		<?php  echo JText::_('DUESEXPORT_ADD')?><br />
		<input class="inputbox" type="radio" name="opptype" value="2"
			checked="checked" /><?php  echo JText::_('DUESEXPORT_EDIT')?><br />
		<input class="inputbox" type="radio" name="opptype" value="3" /> <b>
		<?php  echo JText::_('DUESEXPORT_SAVE')?></b><br />
		<input class="inputbox" type="radio" name="opptype" value="4" /> <b>
		<?php  echo JText::_('DUESEXPORT_DELETE')?></b><br />
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
	<tr>
		<td valign="top" align="right" width="30%"><?php  echo JText::_('DUESEXPORT_MARKCONFIRMED')?></td>
		<td><input class="inputbox" type="radio" name="confirmed" value="1" checked="checked"/>
		<?php  echo JText::_('JYES')?><input class="inputbox" type="radio" name="confirmed" value="0"/>
		<?php  echo JText::_('JNO')?> : <?php  echo " ".JText::_('DUESEXPORT_CONFIRMEDTEXT')?></td>
  </tr>
  <tr>
		<td valign="bottom" align="right" width="30%"><?php  echo JText::_('DUESEXPORT_AUDIT1')?></td>
		<td><input class="inputbox" type="checkbox" name="auditlist" value="1"
			checked="checked" /><?php  echo JText::_('DUESEXPORT_AUDIT2')?>.</td>
	</tr>
	<tr>
		<td valign="bottom" align="right" width="30%"><?php  echo JText::_('DUESEXPORT_HASHED1')?></td>
		<td><input class="inputbox" type="checkbox" name="hashedpwinput"
			value="1" /> <b><?php  echo " ".JText::_('DUESEXPORT_HASHED2')?></b>.</td>
	</tr>
	<tr>
		<td valign="top" align="right" width="30%"><?php  echo JText::_('DUESEXPORT_DEFDOMAIN1')?></td>
		<td><input class="inputbox" type="text" name="defaultemaildomain"
			value="invalid.com" ></input><?php  echo " ".JText::_('DUESEXPORT_DEFDOMAIN2')?></td>
	</tr>
	<tr>
		<td valign="bottom" align="right" width="30%"><?php  echo JText::_('DUESEXPORT_FILEIMPORT')?></td>
		<td><input class="inputbox" type="file" name="csvusers" value=""
			size="40" maxlength="250"></input></td>
	</tr>
	<tr>
	<td valign="top" align="right" width="30%"><?php echo JText::_('DUESEXPORT_EXCLUDED_FIELDS')?></td>
	<td><textarea cols="50" rows="5" name="duesexport_excluded_fields" value="user_id"
			class="inputbox"><?php   echo $excluded_fields?>
                    </textarea></td>
	</tr>
	<tr>
		<td colspan="2">
		<hr>
		</td>
	</tr>
	<tr>
		<td valign="bottom" align="left" colspan="2"><br />
		<h3><?php  echo JText::_('DUESEXPORT_CONFIRMEMAIL1')?></h3>
		</td>
	</tr>
	<tr>
		<td valign="bottom" align="right" width="30%"></td>
		<td><input class="inputbox" type="checkbox" name="ConfirmationEmail"
			value="1"></input> <?php  echo JText::_('DUESEXPORT_CONFIRMEMAIL2')?></td>
	</tr>

	<tr>
		<td  align="right" width="30%"><?php  echo JText::_('DUESEXPORT_CONFIRMSUBJEMAIL')?></td>
		<td><input class="inputbox" type="text"
			name="duesexport_ConfirmationSubject" size="45"
			value=<?php echo '"'.$email_subject.'"';?>></input></td>
	</tr>
	<tr>
		<td  align="right" width="30%"><?php  echo JText::_('DUESEXPORT_CONFIRMSENDER')?></td>
		<td><input class="inputbox" type="text"
			name="duesexport_ConfirmationSender" size="45"
			value=<?php echo '"'.$email_sender.'"';?>></input></td>
	</tr>
	<tr>
		<td valign="bottom" align="right" width="30%"><?php  echo JText::_('DUESEXPORT_BCC1')?></td>
		<td><input class="inputbox" type="checkbox" name="BccEmail" value="1"></input>
		<?php  echo JText::_('DUESEXPORT_BCC2')?></td>
	</tr>
	<tr>
		<td  align="right" width="30%"><?php  echo JText::_('DUESEXPORT_DEFTEXT1')?></td>
		<td><textarea cols="80" rows="15" name="duesexport_message"
			class="inputbox"><?php   echo $email_body;?>
                    </textarea></td>
	</tr>
	<tr>
		<td colspan="2">
		<hr>
		</td>
	</tr>
</table>
<input type="hidden" name="boxchecked" value="0" /> <input type="hidden"
	name="task" value="process" /> <input type="hidden" name="boxchecked"
	value="0" /> <input type="hidden" name="hidemainmenu" value="0" /></form>
