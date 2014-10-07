<?php
/**
* @version      4.0.0 22.08.2012
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/

defined( '_JEXEC' ) or die( 'Restricted access' );
jimport('joomla.application.component.controller');
include_once(JPATH_COMPONENT_SITE."/payments/payment.php");

class JshoppingControllerPayments extends JControllerLegacy{

    function __construct( $config = array() ){
        parent::__construct( $config );
        $this->registerTask( 'add',   'edit' );
        $this->registerTask( 'apply', 'save' );
        checkAccessController("payments");
        addSubmenu("other");
    }
	
    function display($cachable = false, $urlparams = false) {
        $payments = $this->getModel("payments");
        $mainframe = JFactory::getApplication();
        $context = "jshoping.list.admin.payments";
        $filter_order = $mainframe->getUserStateFromRequest($context.'filter_order', 'filter_order', "payment_ordering", 'cmd');
        $filter_order_Dir = $mainframe->getUserStateFromRequest($context.'filter_order_Dir', 'filter_order_Dir', "asc", 'cmd');
        
        $rows = $payments->getAllPaymentMethods(0, $filter_order, $filter_order_Dir);
        $view=$this->getView("payments", 'html');
        $view->setLayout("list");
	    $view->assign('rows', $rows);
        $view->assign('filter_order', $filter_order);
        $view->assign('filter_order_Dir', $filter_order_Dir);
		JPluginHelper::importPlugin('jshoppingadmin');
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onBeforeDisplayPayments', array(&$view));
        $view->displayList();
    }
	
    function edit(){
        $jshopConfig = JSFactory::getConfig();
        $payment_id = JRequest::getInt("payment_id");
        $db = JFactory::getDBO();
        $payment = JTable::getInstance('paymentMethod', 'jshop');
        $payment->load($payment_id);
        $parseString = new parseString($payment->payment_params);
        $params = $parseString->parseStringToParams();
        $edit = ($payment_id)?($edit = 1):($edit = 0);
                
        $_lang = $this->getModel("languages");
        $languages = $_lang->getAllLanguages(1);
        $multilang = count($languages)>1;
		
        $_payments = $this->getModel("payments");
		
        if ($edit){
            if (file_exists(JPATH_SITE."/components/com_jshopping/payments/".$payment->payment_class."/".$payment->payment_class.".php")){
			    require_once (JPATH_SITE."/components/com_jshopping/payments/".$payment->payment_class."/".$payment->payment_class.".php");
                ob_start();
                $payment_class_name = $payment->payment_class;
                $_payment_method = new $payment_class_name();
			    $_payment_method->showAdminFormParams($params);
                $lists['html'] = ob_get_contents();
                ob_get_clean();
            }else{
                $lists['html'] = '';
            }
		} else {
			$lists['html'] = '';
        }
        
        $currencyCode = getMainCurrencyCode();
        
        if ($jshopConfig->tax){
            $_tax = $this->getModel("taxes");
            $all_taxes = $_tax->getAllTaxes();
            $list_tax = array();
            foreach($all_taxes as $tax) {
                $list_tax[] = JHTML::_('select.option', $tax->tax_id, $tax->tax_name . ' (' . $tax->tax_value . '%)','tax_id','tax_name');
            }
            $list_tax[] = JHTML::_('select.option', -1,_JSHOP_PRODUCT_TAX_RATE,'tax_id','tax_name');        
            $lists['tax'] = JHTML::_('select.genericlist', $list_tax, 'tax_id', 'class = "inputbox"','tax_id','tax_name', $payment->tax_id);
        }
        
        $list_price_type = array();
        $list_price_type[] = JHTML::_('select.option', "1", $currencyCode, 'id','name');
        $list_price_type[] = JHTML::_('select.option', "2", "%", 'id','name');
        $lists['price_type'] = JHTML::_('select.genericlist', $list_price_type, 'price_type', 'class = "inputbox"', 'id', 'name', $payment->price_type);
        
        $nofilter = array();
        JFilterOutput::objectHTMLSafe($payment, ENT_QUOTES, $nofilter);
        
        $view=$this->getView("payments", 'html');
        $view->setLayout("edit");
        $view->assign('payment', $payment);
        $view->assign('edit', $edit);
        $view->assign('params', $params);
        $view->assign('lists', $lists);
        $view->assign('languages', $languages);
        $view->assign('multilang', $multilang);
        $view->assign('config', $jshopConfig);
        $view->assign('etemplatevar', '');
        JPluginHelper::importPlugin('jshoppingadmin');
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onBeforeEditPayments', array(&$view));
        $view->displayEdit();
    }	
	
    function save() {
        $payment_id = JRequest::getInt("payment_id");
        JPluginHelper::importPlugin('jshoppingadmin');
        $dispatcher = JDispatcher::getInstance();
        $db = JFactory::getDBO();
        $payment = JTable::getInstance('paymentMethod', 'jshop');
        $post = JRequest::get("post");
        if (!isset($post['payment_publish'])) $post['payment_publish'] = 0;
        if (!isset($post['show_descr_in_email'])) $post['show_descr_in_email'] = 0;
        $post['price'] = saveAsPrice($post['price']);
        $post['payment_class'] = JRequest::getCmd("payment_class");
        if (!$post['payment_id']) $post['payment_type'] = 1;
        
        $dispatcher->trigger( 'onBeforeSavePayment', array(&$post) );
        
        $_lang = $this->getModel("languages");
        $languages = $_lang->getAllLanguages(1);
        foreach($languages as $lang){
            $post['description_'.$lang->language] = JRequest::getVar('description'.$lang->id,'','post',"string",2);
        }
        
		$payment->bind($post);
        
        $_payments = $this->getModel("payments");
        if (!$payment->payment_id){
            $payment->payment_ordering = $_payments->getMaxOrdering() + 1;
        }
        
		if (isset($post['pm_params'])) {
			$parseString = new parseString($post['pm_params']);
			$payment->payment_params = $parseString->splitParamsToString();
		}
        
        if (!$payment->check()){
            JError::raiseWarning("", $payment->getError());
            $this->setRedirect("index.php?option=com_jshopping&controller=payments&task=edit&payment_id=".$payment->payment_id);
            return 0;
        }
		
		$payment->store();

        $dispatcher->trigger('onAfterSavePayment', array(&$payment) );
		
        if ($this->getTask()=='apply'){
            $this->setRedirect("index.php?option=com_jshopping&controller=payments&task=edit&payment_id=".$payment->payment_id); 
        }else{
            $this->setRedirect("index.php?option=com_jshopping&controller=payments");
        }
	}
	
	function remove(){
		$cid = JRequest::getVar("cid");
		$db = JFactory::getDBO();
		$text = '';
        JPluginHelper::importPlugin('jshoppingadmin');
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger( 'onBeforeRemovePayment', array(&$cid) );
		foreach ($cid as $key => $value) {
			$query = "DELETE FROM `#__jshopping_payment_method`
					  WHERE `payment_id` = '" . $db->escape($value) . "'";
			$db->setQuery($query);
			if ($db->query())
				$text .= _JSHOP_PAYMENT_DELETED."<br>";
			else
				$text .= _JSHOP_ERROR_PAYMENT_DELETED."<br>";
		}

        $dispatcher->trigger( 'onAfterRemovePayment', array(&$cid) );

		$this->setRedirect("index.php?option=com_jshopping&controller=payments", $text);
	}
	
	function publish(){
        $this->publishPayment(1);
    }
    
    function unpublish(){
        $this->publishPayment(0);
    }
	
	function publishPayment($flag) {
		$db = JFactory::getDBO();
		$cid = JRequest::getVar("cid");
        JPluginHelper::importPlugin('jshoppingadmin');
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger( 'onBeforePublishPayment', array(&$cid, &$flag) );
		foreach($cid as $key => $value) {
			$query = "UPDATE `#__jshopping_payment_method`
					   SET `payment_publish` = '" . $db->escape($flag) . "'
					   WHERE `payment_id` = '" . $db->escape($value) . "'";
			$db->setQuery($query);
			$db->query();
		}
        
        $dispatcher->trigger( 'onAfterPublishPayment', array(&$cid, &$flag) );
		
		$this->setRedirect("index.php?option=com_jshopping&controller=payments");
	}
	
	function order() {
		$order = JRequest::getVar("order");
		$cid = JRequest::getInt("id");
		$number = JRequest::getInt("number");
		$db = JFactory::getDBO();
		switch ($order) {
			case 'up':
				$query = "SELECT a.payment_id, a.payment_ordering
					   FROM `#__jshopping_payment_method` AS a
					   WHERE a.payment_ordering < '" . $number . "'
					   ORDER BY a.payment_ordering DESC
					   LIMIT 1";
				break;
			case 'down':
				$query = "SELECT a.payment_id, a.payment_ordering
					   FROM `#__jshopping_payment_method` AS a
					   WHERE a.payment_ordering > '" . $number . "'
					   ORDER BY a.payment_ordering ASC
					   LIMIT 1";
		}
		$db->setQuery($query);
		$row = $db->loadObject();
		$query1 = "UPDATE `#__jshopping_payment_method` AS a
					 SET a.payment_ordering = '" . $row->payment_ordering . "'
					 WHERE a.payment_id = '" . $cid . "'";
		$query2 = "UPDATE `#__jshopping_payment_method` AS a
					 SET a.payment_ordering = '" . $number . "'
					 WHERE a.payment_id = '" . $row->payment_id . "'";
		$db->setQuery($query1);
		$db->query();
		$db->setQuery($query2);
		$db->query();
	
		$this->setRedirect("index.php?option=com_jshopping&controller=payments");
	}

    function saveorder(){
        $cid = JRequest::getVar('cid', array(), 'post', 'array' );
        $order = JRequest::getVar('order', array(), 'post', 'array' );
        
        foreach($cid as $k=>$id){
            $table = JTable::getInstance('paymentMethod', 'jshop');
            $table->load($id);
            if ($table->payment_ordering!=$order[$k]){
                $table->payment_ordering = $order[$k];
                $table->store();
            }
        }

        $this->setRedirect("index.php?option=com_jshopping&controller=payments");
    }
	   
}
?>