<?php
/**
* @version      4.3.1 13.08.2013
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die('Restricted access');
jimport('joomla.filesystem.folder');

class jshopShippingMethod extends JTableAvto {

    function __construct( &$_db ){
        parent::__construct( '#__jshopping_shipping_method', 'shipping_id', $_db );
    }
    
    function getAllShippingMethods($publish = 1) {
        $db = JFactory::getDBO(); 
        $query_where = ($publish)?("WHERE published = '1'"):("");
        $lang = JSFactory::getLang();
        $query = "SELECT shipping_id, `".$lang->get('name')."` as name, `".$lang->get("description")."` as description, published, ordering
                  FROM `#__jshopping_shipping_method` 
                  $query_where 
                  ORDER BY ordering";
        $db->setQuery($query);
        return $db->loadObjectList();
    }

    function getAllShippingMethodsCountry($country_id, $payment_id, $publish = 1){
        $db = JFactory::getDBO(); 
        $lang = JSFactory::getLang();
		$jshopConfig = JSFactory::getConfig();
        $query_where = ($publish) ? ("AND sh_method.published = '1'") : ("");
		if ($payment_id && $jshopConfig->step_4_3==0){
			$query_where.= " AND (sh_method.payments='' OR FIND_IN_SET(".$payment_id.", sh_method.payments) ) ";
		}
        $query = "SELECT *, sh_method.`".$lang->get("name")."` as name, `".$lang->get("description")."` as description FROM `#__jshopping_shipping_method` AS sh_method
                  INNER JOIN `#__jshopping_shipping_method_price` AS sh_pr_method ON sh_method.shipping_id = sh_pr_method.shipping_method_id
                  INNER JOIN `#__jshopping_shipping_method_price_countries` AS sh_pr_method_country ON sh_pr_method_country.sh_pr_method_id = sh_pr_method.sh_pr_method_id
                  INNER JOIN `#__jshopping_countries` AS countries  ON sh_pr_method_country.country_id = countries.country_id
                  WHERE countries.country_id = '".$db->escape($country_id)."' $query_where
                  ORDER BY sh_method.ordering";
        $db->setQuery($query);
        return $db->loadObjectList();
    }
    
    function getPayments(){
        if ($this->payments==""){
            return array();
        }else{
            return explode(",", $this->payments);
        }
    }
    
    function setPayments($payments){
        $this->payments = implode(",", $payments);
    }
}

?>