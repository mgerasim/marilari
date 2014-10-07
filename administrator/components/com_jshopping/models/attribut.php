<?php
/**
* @version      4.1.0 04.06.2011
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/

defined('_JEXEC') or die( 'Restricted access' );
jimport('joomla.application.component.model');

class JshoppingModelAttribut extends JModelLegacy {
    
    function getNameAttribut($attr_id) {
        $db = JFactory::getDBO();
        $lang = JSFactory::getLang();
        $query = "SELECT `".$lang->get("name")."` as name FROM `#__jshopping_attr` WHERE attr_id = '".$db->escape($attr_id)."'";
        $db->setQuery($query);
        return $db->loadResult();
    }
    
    function getAllAttributes($result = 0, $categorys = null, $order = null, $orderDir = null){
        $lang = JSFactory::getLang();
        $db = JFactory::getDBO(); 
        $ordering = "attr_ordering asc";
        if ($order && $orderDir){
            $ordering = $order." ".$orderDir;
        }
        $query = "SELECT attr_id, `".$lang->get("name")."` as name, attr_type, attr_ordering, independent, allcats, cats FROM `#__jshopping_attr` ORDER BY ".$ordering;
        $db->setQuery($query);
        $list = $db->loadObjectList();
                
        if (is_array($categorys) && count($categorys)){
            foreach($list as $k=>$v){
                if (!$v->allcats){
                    if ($v->cats!=""){
                        $cats = unserialize($v->cats);
                    }else{
                        $cats = array();
                    }
                    $enable = 0;
                    foreach($categorys as $cid){
                        if (in_array($cid, $cats)) $enable = 1;
                    }
                    if (!$enable){
                        unset($list[$k]);
                    }
                }
            } 
        }
        
        if ($result==0){
            return $list;
        }
        if ($result==1){
            $attributes_format1 = array();
            foreach($list as $v){
                $attributes_format1[$v->attr_id] = $v;
            }
            return $attributes_format1;
        }
        if ($result==2){
            $attributes_format2 = array();
            $attributes_format2['independent']= array();
            $attributes_format2['dependent']= array();
            foreach($list as $v){
                if ($v->independent) $key_dependent = "independent"; else $key_dependent = "dependent";
                $attributes_format2[$key_dependent][$v->attr_id] = $v;
            }
            return $attributes_format2;
        }
    }
    
}
?>