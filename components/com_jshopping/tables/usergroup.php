<?php
/**
* @version      4.3.1 13.08.2013
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die('Restricted access');

class jshopUserGroup extends JTable{

    function __construct( &$_db ){
        parent::__construct( '#__jshopping_usergroups', 'usergroup_id', $_db );
    }
     
    function getDefaultUsergroup(){
        $db = JFactory::getDBO(); 
        $query = "SELECT `usergroup_id` FROM `#__jshopping_usergroups` WHERE `usergroup_is_default`= '1'";
        $db->setQuery($query);
        return $db->loadResult();
    }
    
    function getList(){
        $db = JFactory::getDBO(); 
        $query = "SELECT * FROM `#__jshopping_usergroups`";
        $db->setQuery($query);
        return $db->loadObjectList();
    }   
}
?>