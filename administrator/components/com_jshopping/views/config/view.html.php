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

class JshoppingViewConfig extends JViewLegacy
{
    function display($tpl=null){
        JToolBarHelper::title( _JSHOP_EDIT_CONFIG, 'generic.png' ); 
        JToolBarHelper::save();
        JToolBarHelper::spacer();
        JToolBarHelper::apply();
		JToolBarHelper::spacer();
		JToolBarHelper::cancel();
		JToolBarHelper::spacer();
		JToolBarHelper::custom('panel', 'jshop_panel.png', 'jshop_panel.png', _JSHOP_PANEL, false);
        JToolBarHelper::divider();
        if (JFactory::getUser()->authorise('core.admin')){
            JToolBarHelper::preferences('com_jshopping');        
            JToolBarHelper::divider();
        }
        parent::display($tpl);
	}
    
    function displayListSeo($tpl=null){
        
        JToolBarHelper::title( _JSHOP_SEO, 'generic.png' );
        JToolBarHelper::addNew("seoedit");
        JToolBarHelper::custom('panel', 'jshop_panel.png', 'jshop_panel.png', _JSHOP_PANEL, false);
        parent::display($tpl);
    }
    
    function displayEditSeo($tpl=null){
        
        JToolBarHelper::title(_JSHOP_SEO, 'generic.png' );
        JToolBarHelper::save("saveseo");
        JToolBarHelper::spacer();
        JToolBarHelper::apply("applyseo");
        JToolBarHelper::spacer();
        JToolBarHelper::cancel("seo");
        JToolBarHelper::spacer();
        JToolBarHelper::custom('panel', 'jshop_panel.png', 'jshop_panel.png', _JSHOP_PANEL, false);
        parent::display($tpl);
    }
    
    function displayListStatictext($tpl=null){
        
        JToolBarHelper::title( _JSHOP_STATIC_TEXT, 'generic.png' );
        JToolBarHelper::addNew("statictextedit");
        JToolBarHelper::custom('panel', 'jshop_panel.png', 'jshop_panel.png', _JSHOP_PANEL, false);
        parent::display($tpl);
    }
    
    function displayEditStatictext($tpl=null){
        
        JToolBarHelper::title(_JSHOP_STATIC_TEXT, 'generic.png' );
        JToolBarHelper::save("savestatictext");
        JToolBarHelper::spacer();
        JToolBarHelper::apply("applystatictext");
        JToolBarHelper::spacer();
        JToolBarHelper::cancel("statictext");
        JToolBarHelper::spacer();
        JToolBarHelper::custom('panel', 'jshop_panel.png', 'jshop_panel.png', _JSHOP_PANEL, false);
        parent::display($tpl);
    }
    
}
?>