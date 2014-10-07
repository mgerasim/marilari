<?php
/**
* @version      4.6.0 24.07.2013
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/

defined( '_JEXEC' ) or die( 'Restricted access' );
jimport('joomla.application.component.controller');

class JshoppingControllerContent extends JControllerLegacy{

    function display($cachable = false, $urlparams = false){
        JError::raiseError(404, _JSHOP_PAGE_NOT_FOUND);
    }

    function view(){
		$mainframe =JFactory::getApplication();
        $jshopConfig = JSFactory::getConfig();
        $db = JFactory::getDBO(); 
        JPluginHelper::importPlugin('jshopping');
        JPluginHelper::importPlugin('content');
        $dispatcher = JDispatcher::getInstance();

        $page = JRequest::getVar('page');
        switch($page){
            case 'agb':
                $pathway = _JSHOP_AGB;
            break;
            case 'return_policy':
                $pathway = _JSHOP_RETURN_POLICY;
            break;
            case 'shipping':
                $pathway = _JSHOP_SHIPPING;
            break;
            case 'privacy_statement':
                $pathway = _JSHOP_PRIVACY_STATEMENT;
            break;
        }
        appendPathWay($pathway);

        $seo = JTable::getInstance("seo", "jshop");
        $seodata = $seo->loadData("content-".$page);
        if ($seodata->title==""){
            $seodata->title = $pathway;
        }
        setMetaData($seodata->title, $seodata->keyword, $seodata->description);
        
        $statictext = JTable::getInstance("statictext","jshop");
        
        $order_id = JRequest::getInt('order_id');
        $cartp = JRequest::getInt('cart');
        
        if ($jshopConfig->return_policy_for_product && $page=='return_policy' && ($cartp || $order_id)){
            if ($cartp){
                $cart = JModelLegacy::getInstance('cart', 'jshop');
                $cart->load();
                $list = $cart->getReturnPolicy();
            }else{
                $order = JTable::getInstance('order', 'jshop');
                $order->load($order_id);
                $list = $order->getReturnPolicy();
            }
            $listtext = array();
            foreach($list as $v){
                $listtext[] = $v->text;
            }
            $row = new stdClass();
            $row->id = -1;
            $row->text = implode('<div class="return_policy_space"></div>', $listtext);
        }else{
            $row = $statictext->loadData($page);
        }
                
        if (!$row->id){
            JError::raiseError(404, _JSHOP_PAGE_NOT_FOUND);
            return;
        }
		if ($jshopConfig->use_plugin_content){
            $obj = new stdClass();
            $params = $mainframe->getParams('com_content');
            $obj->text = $row->text;
            $obj->title = $seodata->title;
            $dispatcher->trigger('onContentPrepare', array('com_content.article', &$obj, &$params, 0));
            $row->text = $obj->text;
        }
        $text = $row->text;
        $dispatcher->trigger('onBeforeDisplayContent', array($page, &$text));
        
        echo $text;
    }
}
?>