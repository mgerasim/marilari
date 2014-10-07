<?php
/**
* @version      3.5.1 20.12.2011
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/

defined( '_JEXEC' ) or die( 'Restricted access' );
jimport('joomla.application.component.controller');
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.archive');
jimport('joomla.filesystem.path');

class JshoppingControllerUpdate extends JControllerLegacy{
    
    function __construct( $config = array() ){
        $mainframe = JFactory::getApplication();
        parent::__construct( $config );
        checkAccessController("update");
        addSubmenu("update");
        $language = JFactory::getLanguage(); 
        $language->load('com_installer');
    }

    function display($cachable = false, $urlparams = false){		                
		$view=$this->getView("update", 'html');  
        $view->assign('etemplatevar1', '');
        $view->assign('etemplatevar2', '');
		$view->display(); 
    }
    
	
	function update() {       
        $installtype = JRequest::getVar('installtype');
        $back = JRequest::getVar('back');
        
        // Make sure that zlib is loaded so that the package can be unpacked
        if (!extension_loaded('zlib')){
            JError::raiseWarning('SOME_ERROR_CODE', JText::_('COM_INSTALLER_MSG_INSTALL_WARNINSTALLZLIB'));
            $this->setRedirect("index.php?option=com_jshopping&controller=update");
            return false;
        }
        
        if ($installtype == 'package'){
            $userfile = JRequest::getVar('install_package', null, 'files', 'array' );
            if (!(bool) ini_get('file_uploads')) {
                JError::raiseWarning('SOME_ERROR_CODE', JText::_('COM_INSTALLER_MSG_INSTALL_WARNINSTALLFILE'));
                $this->setRedirect("index.php?option=com_jshopping&controller=update");
                return false;
            }
            if (!is_array($userfile) ) {
                JError::raiseWarning('SOME_ERROR_CODE', JText::_('No file selected'));
                $this->setRedirect("index.php?option=com_jshopping&controller=update");
                return false;
            }
            if ( $userfile['error'] || $userfile['size'] < 1 ){
                JError::raiseWarning('SOME_ERROR_CODE', JText::_('COM_INSTALLER_MSG_INSTALL_WARNINSTALLUPLOADERROR'));
                $this->setRedirect("index.php?option=com_jshopping&controller=update");
                return false;
            }
            $config = JFactory::getConfig();            
            $tmp_dest = $config->get('tmp_path').'/'.$userfile['name'];            
            $tmp_src = $userfile['tmp_name'];
            jimport('joomla.filesystem.file');
            $uploaded = JFile::upload($tmp_src, $tmp_dest); 
            $archivename = $tmp_dest;            
            $tmpdir = uniqid('install_');
            $extractdir = JPath::clean(dirname($archivename).'/'.$tmpdir);
            $archivename = JPath::clean($archivename);        
        }else {
            jimport( 'joomla.installer.helper' );
            $url = JRequest::getVar('install_url');
            if (!$url) {
                JError::raiseWarning('', JText::_('COM_INSTALLER_MSG_INSTALL_ENTER_A_URL'));
                $this->setRedirect("index.php?option=com_jshopping&controller=update");
                return false;
            }
            $p_file = JInstallerHelper::downloadPackage($url);
            if (!$p_file) {
                JError::raiseWarning('', JText::_('COM_INSTALLER_MSG_INSTALL_INVALID_URL'));
                $this->setRedirect("index.php?option=com_jshopping&controller=update");
                return false;
            }
            $config = JFactory::getConfig();
            $tmp_dest = $config->get('tmp_path');
            $tmpdir = uniqid('install_');
            $extractdir = JPath::clean(dirname(JPATH_BASE).'/tmp/'.$tmpdir);
            $archivename = JPath::clean($tmp_dest.'/'.$p_file);              
        }
        $result = JArchive::extract($archivename, $extractdir);
        if ( $result === false ) {
            JError::raiseWarning('500', "Error");
            $this->setRedirect("index.php?option=com_jshopping&controller=update");
            return false;
        }
        
        if (file_exists($extractdir."/checkupdate.php")) include($extractdir."/checkupdate.php");                        
                
        $this->copyFiles($extractdir);
		
        if (file_exists($extractdir."/update.sql")){
            $db = JFactory::getDBO();
            $lines = file($extractdir."/update.sql");
            $fullline = implode(" ", $lines);
            $queryes = $db->splitSql($fullline);            
            foreach($queryes as $query){
                if (trim($query)!=''){
                    $db->setQuery($query);
                    $db->query();
                    if ($db->getErrorNum()) {
                        JError::raiseWarning( 500, $db->stderr() );
                        saveToLog("error.log", "Update - ".$db->stderr());
                    }
                }
            }            
        }
        
        if (file_exists($extractdir."/update.php")) include($extractdir."/update.php");
        
        JPluginHelper::importPlugin('jshoppingadmin');
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger( 'onAfterUpdateShop', array($extractdir) );
                
        @unlink($archivename);
		JFolder::delete($extractdir);
        
        $session = JFactory::getSession();
        $checkedlanguage = array();
        $session->set("jshop_checked_language", $checkedlanguage);        
        
        if( $back == '' ){
            $this->setRedirect("index.php?option=com_jshopping&controller=update", _JSHOP_COMPLETED); 
        }else{
            $this->setRedirect( $back , _JSHOP_COMPLETED);
        }
    }
    
    function copyFiles($startdir, $subdir = ""){
        
        if ($subdir!="" && !file_exists(JPATH_ROOT.$subdir)){
            @mkdir(JPATH_ROOT.$subdir, 0755);
        }
        
        $files = JFolder::files($startdir.$subdir, '', false, false, array(), array());
        foreach($files as $file){        
            if ($subdir=="" && ($file=="update.sql" || $file=="update.php" || $file=="checkupdate.php")){
                continue;
            }            
            
            if (@copy($startdir.$subdir."/".$file, JPATH_ROOT.$subdir."/".$file)){
                //JError::raiseWarning( 500, "Copy file: ".$subdir."/".$file." OK");
            }else{
                JError::raiseWarning("", "Copy file: ".$subdir."/".$file." ERROR");
                saveToLog("error.log", "Update - Copy file: ".$subdir."/".$file." ERROR");
            }
        }
        
        $folders = JFolder::folders($startdir.$subdir, '');
        foreach($folders as $folder){
            $dir = $subdir."/".$folder;            
            $this->copyFiles($startdir, $dir);
        }
    }
         
}
?>