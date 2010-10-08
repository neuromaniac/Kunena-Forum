<?php
/**
 * @version $Id$
 * KunenaINIMaker Component
 * 
 * @package	Kunena INImaker
 * @Copyright (C) 2010 www.kunena.com All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 */


// no direct access
defined('_JEXEC') or die('Restricted access');

require_once (dirname(__FILE__).DS.'helper.php');
//-----------------preparation---------------------
$helper		= new CompKunenainimakerHelper();


//---------------execute--------------------
$gclient	= JRequest::getWord('client');
$task		= JRequest::getCmd('task');
$langcode	= JRequest::getCmd('language', 'en-GB');

switch ($task){
	case 'parse':
		if($gclient){
			switch ($gclient){
				case 'frontend':
					$dir		= JPATH_SITE . DS . 'components' . DS . 'com_kunena';
					$kill		= array('language' , 'svn', 'template.xml');
					$inifile	= $dir .DS. 'language' .DS. $langcode .DS. $langcode.'.com_kunena.ini';
					break;
				case 'backend':
					$dir		= JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_kunena';
					$kill		= array('install', 'language', 'images', 'media', 'svn');
					$inifile	= $dir .DS. 'language' .DS. $langcode .DS. $langcode.'.com_kunena.ini';
					break;
				case 'install':
					$dir		= JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_kunena' .DS. 'install';
					$kill		= array('media', 'svn');
					$inifile	= $dir .DS. '..' .DS. 'language' .DS. $langcode .DS. $langcode.'.com_kunena.install.ini';
					break;
				case 'template':
					$dir		= JPATH_SITE . DS . 'components' . DS . 'com_kunena' .DS. 'template' .DS. 'default';
					$kill		= array('language' , 'svn', 'images', 'css', 'media');
					$inifile	= $dir .DS. 'language' .DS. $langcode .DS. $langcode.'.com_kunena.tpl_default.ini';
					break;
			}
			$helper->scan_dir($dir);
			$fulllist	= $helper->files;
			$reclist	= $helper->killfolder($kill,$fulllist);
			if($gclient != 'template') $phplist	= $helper->getfiles($reclist);
			else $phplist = '';
			$xmllist	= $helper->getfiles($reclist, 'xml');
			$inifilea	= $helper->readINIfile($inifile);
			$langstrings= $helper->readphpxml($phplist,$xmllist);
			$compared	= $helper->CompareArray($inifilea,$langstrings);
			
			break;
		}
}


//-----------------preparation---------------------
$client		= array(
					array('text'=>'Frontend','value'=>'frontend'), 
					array('text'=>'Backend', 'value'=>'backend'),
					array('text'=>'Install', 'value'=>'install'),
					array('text'=>'Template', 'value'=>'template')
					);

//---------------------View--------------------------------
JToolBarHelper::title( JText::_( 'Kunena INI Maker' ), 'generic.png' );
?>
<form action="" method="post" name="adminForm">
<table class="adminlist">
	<tbody>
		<tr>
			<td>Client</td>
			<td><?php echo JHTML::_('select.genericlist', $client, 'client','', 'value','text', $gclient)?></td>
		</tr>
		<tr>
			<td>Language</td>
			<td><?php echo JHTML::_('select.genericlist', $helper->getlanguages(), 'language' ,'','value','text',$langcode) ?></td>
		</tr>
	</tbody>
</table>
<input type="hidden" name="task" value="parse" />
<input type="submit" value=" Absenden ">
</form>
<p></p>
<?php if($compared) :?>
<form action="" method="post" name="adminForm">
<table class="adminlist">
	<tbody>
		<tr>
			<th>New file</th>
			<th>Old file</th>
		</tr><tr>
			<td><?php echo implode("<br />",$inifilea['comments']).implode("<br />", $helper->getRdyArray($compared['newfile']))?></td>
			<td><?php echo implode("<br />",$inifilea['comments']).implode("<br />",$helper->getRdyArray($inifilea['nocomments'])) ?></td>
		</tr>
	</tbody>
</table>
<table class="adminlist">
	<tbody>
		<tr>
			<th>PHP/XML-File lines not found in INI</th>
			<th>INI-File lines not found in PHP/XML</th>
		</tr><tr>
			<td><?php echo implode("<br />", $helper->getRdyArray($compared['new'], 'phpxml') )?></td>
			<td><?php echo implode("<br />", $helper->getRdyArray($compared['old']))?></td>
		</tr>
	</tbody>
</table>
</form>
<?php endif; ?>

