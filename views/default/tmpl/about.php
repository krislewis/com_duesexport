<?php
defined('_JEXEC') or die('Restricted access');
global $duesexport_initial_mode;
JToolBarHelper::title(JText::_('DUESEXPORT'), 'generic.png');
JToolBarHelper::preferences('com_duesexport','500','750');
$versionstuff=new JVersion;
$thisversion=$versionstuff->getShortVersion();
if(!(strpos($thisversion,"1.6")===FALSE)){
	JSubMenuHelper::addEntry('Save Users','index.php?option=com_duesexport&task=save&view=default');
	JSubMenuHelper::addEntry('About','index.php?option=com_duesexport&task=about&view=about');
}
echo Jtext::_('DUESEXPORT_ABOUT_STRING1');
echo Jtext::_('DUESEXPORT_ABOUT_STRING2');
echo Jtext::_('DUESEXPORT_ABOUT_STRING3');


?>

