<?php
/**
* @version      4.3.0 24.07.2013
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/

defined( '_JEXEC' ) or die( 'Restricted access' );
jimport('joomla.application.component.controller');

class JshoppingControllerShippings extends JControllerLegacy{

    function __construct( $config = array() ){
        parent::__construct( $config );

        $this->registerTask('add', 'edit');
        $this->registerTask('apply', 'save');
        $this->registerTask('orderup', 'reorder');
        $this->registerTask('orderdown', 'reorder');
        $this->registerTask('publish', 'republish');
        $this->registerTask('unpublish', 'republish');
        checkAccessController("shippings");
        addSubmenu("other");
    }
    
    function display($cachable = false, $urlparams = false){
		$db = JFactory::getDBO();
        $mainframe = JFactory::getApplication();
        $context = "jshoping.list.admin.shippings";
        $filter_order = $mainframe->getUserStateFromRequest($context.'filter_order', 'filter_order', "ordering", 'cmd');
        $filter_order_Dir = $mainframe->getUserStateFromRequest($context.'filter_order_Dir', 'filter_order_Dir', "asc", 'cmd');
        
		$shippings = $this->getModel("shippings");
		$rows = $shippings->getAllShippings(0, $filter_order, $filter_order_Dir);
        
        $not_set_price = 0;
        $rowsprices = $shippings->getAllShippingPrices(0);
        $shippings_prices = array();
        foreach($rowsprices as $row){
            $shippings_prices[$row->shipping_method_id][] = $row;
        }
        foreach($rows as $k=>$v){
            if (is_array($shippings_prices[$v->shipping_id])){
                $rows[$k]->count_shipping_price = count($shippings_prices[$v->shipping_id]);
            }else{
                $not_set_price = 1;
                $rows[$k]->count_shipping_price = 0;
            }
        }
        
        if ($not_set_price){
            JError::raiseNotice("", _JSHOP_CERTAIN_METHODS_DELIVERY_NOT_SET_PRICE);
        }
		
		$view=$this->getView("shippings", 'html');
        $view->setLayout("list");
		$view->assign('rows', $rows);
        $view->assign('filter_order', $filter_order);
        $view->assign('filter_order_Dir', $filter_order_Dir);
		JPluginHelper::importPlugin('jshoppingadmin');
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onBeforeDisplayShippings', array(&$view));
		$view->displayList();
	}
	
	function edit() {
		$jshopConfig = JSFactory::getConfig();
		$shipping_id = JRequest::getInt("shipping_id");
		$shipping = JTable::getInstance('shippingMethod', 'jshop');
		$shipping->load($shipping_id);
		$edit = ($shipping_id)?($edit = 1):($edit = 0);
        $_lang = $this->getModel("languages");
        $languages = $_lang->getAllLanguages(1);
        $multilang = count($languages)>1;
        
        $_payments = $this->getModel("payments");
        $list_payments = $_payments->getAllPaymentMethods(0);
        
        $lists['payments'] = JHTML::_('select.genericlist', $list_payments, 'listpayments[]', 'class="inputbox" size="10" multiple = "multiple"', 'payment_id', 'name', $shipping->getPayments());

        $nofilter = array();
        JFilterOutput::objectHTMLSafe( $shipping, ENT_QUOTES, $nofilter);
        
		$view=$this->getView("shippings", 'html');
        $view->setLayout("edit");        
		$view->assign('shipping', $shipping);
		$view->assign('edit', $edit);
        $view->assign('languages', $languages);
        $view->assign('multilang', $multilang);
        $view->assign('lists', $lists);
		$view->assign('config', $jshopConfig);
        $view->assign('etemplatevar', '');
        JPluginHelper::importPlugin('jshoppingadmin');
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onBeforeEditShippings', array(&$view));
		$view->displayEdit();
	}
	
	function save(){
		$shipping_id = JRequest::getInt("shipping_id", 0);
		$shipping = JTable::getInstance('shippingMethod', 'jshop');
        $post = JRequest::get("post");
        if (!isset($post['published'])) $post['published'] = 0;
        if (!$post['listpayments']){
            $post['listpayments'] = array();
        }
        $shipping->setPayments($post['listpayments']);
        
        JPluginHelper::importPlugin('jshoppingadmin');
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onBeforeSaveShipping', array(&$post));
        
        $_lang = $this->getModel("languages");
        $languages = $_lang->getAllLanguages(1);
        foreach($languages as $lang){
            $post['description_'.$lang->language] = JRequest::getVar('description'.$lang->id,'','post',"string",2);
        }
		if (!$shipping->bind($post)) {
			JError::raiseWarning("",_JSHOP_ERROR_BIND);
			$this->setRedirect("index.php?option=com_jshopping&controller=shippings");
			return 0;
		}
        
        $_shippings = $this->getModel("shippings");
        if (!$shipping->shipping_id){
            $shipping->ordering = $_shippings->getMaxOrdering() + 1;
        }	
	
		if (!$shipping->store()) {
			JError::raiseWarning("",_JSHOP_ERROR_SAVE_DATABASE);
			$this->setRedirect("index.php?option=com_jshopping&controller=shippings");
			return 0;
		}
        
        $dispatcher->trigger( 'onAfterSaveShipping', array(&$shipping) );
        
		if ($this->getTask()=='apply'){
            $this->setRedirect("index.php?option=com_jshopping&controller=shippings&task=edit&shipping_id=".$shipping->shipping_id); 
        }else{
            $this->setRedirect("index.php?option=com_jshopping&controller=shippings");
        }

	}
	
	function remove() {
		$cid = JRequest::getVar("cid");
		$db = JFactory::getDBO();
		$text = array();
        
        JPluginHelper::importPlugin('jshoppingadmin');
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger( 'onBeforeRemoveShipping', array(&$cid) );

		foreach ($cid as $key => $value) {
			$query = "DELETE FROM `#__jshopping_shipping_method` WHERE `shipping_id` = '" . $db->escape($value) . "'";
			$db->setQuery($query);
			if($db->query()) {
				$text[] = _JSHOP_SHIPPING_DELETED;
				
				$query = "SELECT `sh_pr_method_id` FROM `#__jshopping_shipping_method_price` WHERE `shipping_method_id` = '" . $db->escape($value) . "'";
				$db->setQuery($query);
				$sh_pr_ids = $db->loadObjectList();
				
				if (count($sh_pr_ids)){
					foreach ($sh_pr_ids as $key2=>$value2){
						$query = "DELETE FROM `#__jshopping_shipping_method_price_weight` WHERE `sh_pr_method_id` = '" . $db->escape($value2). "'";
						$db->setQuery($query);
						$db->query();
					}
				}
				
				$query = "DELETE FROM `#__jshopping_shipping_method_price` WHERE `shipping_method_id` = '" . $db->escape($value). "'";
				$db->setQuery($query);
				$db->query();
			} else {
				$text[] = _JSHOP_ERROR_SHIPPING_DELETED;
			}
		}
        
        $dispatcher->trigger( 'onAfterRemoveShipping', array(&$cid) );
		
		$this->setRedirect("index.php?option=com_jshopping&controller=shippings", implode("</li><li>", $text) );
	}
	
	function republish(){
		$cid = JRequest::getVar("cid");
        $flag = ($this->getTask() == 'publish') ? 1 : 0;
        JPluginHelper::importPlugin('jshoppingadmin');
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onBeforePublishShipping', array(&$cid,&$flag));
		$obj = JTable::getInstance('shippingMethod', 'jshop');
        $obj->publish($cid, $flag);
        $dispatcher->trigger('onAfterPublishShipping', array(&$cid,&$flag));
		$this->setRedirect("index.php?option=com_jshopping&controller=shippings");
	}
	
    function reorder(){
        $ids = JRequest::getVar('cid', null, 'post', 'array');
        $move = ($this->getTask() == 'orderup') ? -1 : +1;
        $obj = JTable::getInstance('shippingMethod', 'jshop');
        $obj->load($ids[0]);
        $obj->move($move);
        $this->setRedirect("index.php?option=com_jshopping&controller=shippings");
    }
    
    function saveorder(){
        $pks = JRequest::getVar('cid', null, 'post', 'array');
        $order = JRequest::getVar('order', null, 'post', 'array');
        JArrayHelper::toInteger($pks);
        JArrayHelper::toInteger($order);
        $model = $this->getModel("shippings");
        $model->saveorder($pks, $order);
        $this->setRedirect("index.php?option=com_jshopping&controller=shippings");
    }
    
    function ext_price_calc(){
        $this->setRedirect("index.php?option=com_jshopping&controller=shippingextprice");
    }
    
}
?>