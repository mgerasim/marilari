<?php
/**
* @version      4.1.0 20.08.2012
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/

defined('_JEXEC') or die('Restricted access');

if (!JFactory::getUser()->authorise('core.manage', 'com_jshopping')) {
    return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}
JTable::addIncludePath(JPATH_COMPONENT_SITE.'/tables');
require_once(JPATH_COMPONENT_SITE."/lib/factory.php");
require_once(JPATH_COMPONENT_ADMINISTRATOR.'/functions.php');

$ajax = JRequest::getInt('ajax');
$adminlang = JFactory::getLanguage();
if (!JRequest::getVar("js_nolang")){
    JSFactory::loadAdminLanguageFile();
}
$db = JFactory::getDBO();
$jshopConfig = JSFactory::getConfig();
$jshopConfig->cur_lang = $jshopConfig->frontend_lang;

if ($jshopConfig->adminLanguage!=$adminlang->getTag()){
	$config = new jshopConfig($db);
	$config->id = 1;	
	$config->adminLanguage = $adminlang->getTag();
	if (!$config->store()) {
		JError::raiseWarning("",_JSHOP_ERROR_SAVE_DATABASE);
		return 0;
	}
}
if (!$ajax){
    installNewLanguages();
}else{
    header('Content-Type: text/html;charset=UTF-8');
}

JPluginHelper::importPlugin('jshopping');
$dispatcher = JDispatcher::getInstance();
$dispatcher->trigger('onAfterLoadShopParamsAdmin', array());

JHtml::_('behavior.framework');
JHtml::_('bootstrap.framework');
$document = JFactory::getDocument();
$document->addScript($jshopConfig->live_path.'js/functions.js');
$document->addScript($jshopConfig->live_admin_path.'js/functions.js');
$document->addStyleSheet($jshopConfig->live_admin_path.'css/style.css');

$controller = JRequest::getCmd('controller');
if (!$controller) $controller = "panel";

if (file_exists(JPATH_COMPONENT.'/controllers/'.$controller.'.php'))
    require_once( JPATH_COMPONENT.'/controllers/'.$controller.'.php' );
else
    JError::raiseError( 403, JText::_('Access Forbidden') );

$classname = 'JshoppingController'.$controller;
$controller = new $classname();
$controller->execute(JRequest::getCmd('task'));
$controller->redirect();
?>