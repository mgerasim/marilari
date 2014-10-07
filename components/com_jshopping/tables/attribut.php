<?php
/**
* @version      3.7.1 10.06.2012
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/

defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.model');

class jshopAttribut extends JTableAvto{

    function __construct( &$_db ){
        parent::__construct('#__jshopping_attr', 'attr_id', $_db );
    }

    function getName($attr_id){
        $db = JFactory::getDBO();
        $lang = JSFactory::getLang();
        $query = "SELECT `".$lang->get("name")."` as name FROM `#__jshopping_attr` WHERE attr_id = '".$db->escape($attr_id)."'";
        $db->setQuery($query);
        return $db->loadResult();
    }

    function getAllAttributes(){
        $lang = JSFactory::getLang();
        $db = JFactory::getDBO(); 
        $query = "SELECT attr_id, `".$lang->get("name")."` as name, `".$lang->get("description")."` as description, attr_type, independent, allcats, cats, attr_ordering FROM `#__jshopping_attr` ORDER BY attr_ordering";
        $db->setQuery($query);
        $rows = $db->loadObjectList();
        foreach($rows as $k=>$v){
            if ($v->allcats){
                $rows[$k]->cats = array();
            }else{
                $rows[$k]->cats = unserialize($v->cats);
            }
        }
    return $rows;
    }
    
    function getTypeAttribut($attr_id){
        $db = JFactory::getDBO();
        $query = "select attr_type from #__jshopping_attr where `attr_id`='".$db->escape($attr_id)."'";
        $db->setQuery($query);
        return $db->loadResult();
    }
    
    function setCategorys($cats){
        $this->cats = serialize($cats);
    }
      
    function getCategorys(){
        if ($this->cats!=""){
            return unserialize($this->cats);
        }else{
            return array();
        }
    }

}
?>