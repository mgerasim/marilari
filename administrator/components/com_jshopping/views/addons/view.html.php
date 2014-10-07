<?php
/**
* @version      4.3.1 13.08.2013
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die('Restricted access');
jimport( 'joomla.application.component.view');

class JshoppingViewAddons extends JViewLegacy
{
    function displayList($tpl=null){
        JToolBarHelper::title( _JSHOP_ADDONS, 'generic.png' );
        parent::display($tpl);
	}
    
    function displayEdit($tpl=null){        
        JToolBarHelper::title(_JSHOP_ADDONS, 'generic.png' );
        JToolBarHelper::save();
        JToolBarHelper::spacer();
        JToolBarHelper::apply();
        JToolBarHelper::spacer();
        JToolBarHelper::cancel();        
        parent::display($tpl);
    }
}
?>