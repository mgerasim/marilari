<?php
/**
* @version      3.7.0 02.05.2012
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/

defined('_JEXEC') or die('Restricted access');
jimport( 'joomla.application.component.model');

class JshoppingModelAddons extends JModelLegacy{

    function getList(){
        $db = JFactory::getDBO(); 
        $query = "SELECT * FROM `#__jshopping_addons`";
        $db->setQuery($query);
        return $db->loadObjectList();
    }
}
?>