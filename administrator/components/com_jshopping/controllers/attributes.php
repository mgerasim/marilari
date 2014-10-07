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

class JshoppingControllerAttributes extends JControllerLegacy{
    
    function __construct( $config = array() ){
        parent::__construct( $config );

        $this->registerTask( 'add',   'edit' );
        $this->registerTask( 'apply', 'save' );
        checkAccessController("attributes");
        addSubmenu("other");
    }

    function display($cachable = false, $urlparams = false){
        $mainframe = JFactory::getApplication();
        $context = "jshoping.list.admin.attributes";
        $filter_order = $mainframe->getUserStateFromRequest($context.'filter_order', 'filter_order', "attr_ordering", 'cmd');
        $filter_order_Dir = $mainframe->getUserStateFromRequest($context.'filter_order_Dir', 'filter_order_Dir', "asc", 'cmd');
        
    	$attributes = $this->getModel("attribut");
    	$attributesvalue = $this->getModel("attributValue");
        $rows = $attributes->getAllAttributes(0, null, $filter_order, $filter_order_Dir);
        foreach ($rows as $key => $value){
            $rows[$key]->values = splitValuesArrayObject( $attributesvalue->getAllValues($rows[$key]->attr_id), 'name');
            $rows[$key]->count_values = count($attributesvalue->getAllValues($rows[$key]->attr_id));
        }        
        $view = $this->getView("attributes", 'html');
        $view->setLayout("list");
        $view->assign('rows', $rows);
        $view->assign('filter_order', $filter_order);
        $view->assign('filter_order_Dir', $filter_order_Dir);
		JPluginHelper::importPlugin('jshoppingadmin');
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onBeforeDisplayAttributes', array(&$view));
        $view->displayList();
    }

    function edit() {
        $jshopConfig = JSFactory::getConfig();
        $db = JFactory::getDBO();
        $attr_id = JRequest::getInt("attr_id");
	
        $attribut = JTable::getInstance('attribut', 'jshop');
        $attribut->load($attr_id);

        if (!$attribut->independent) $attribut->independent = 0;
    
        $_lang = $this->getModel("languages");
        $languages = $_lang->getAllLanguages(1);
        $multilang = count($languages)>1;
	
        $types[] = JHTML::_('select.option', '1','Select','attr_type_id','attr_type');
        $types[] = JHTML::_('select.option', '2','Radio','attr_type_id','attr_type');
        $type_attribut = JHTML::_('select.genericlist', $types, 'attr_type','class = "inputbox" size = "1"','attr_type_id','attr_type',$attribut->attr_type);

        $dependent[] = JHTML::_('select.option', '0',_JSHOP_YES,'id','name');
        $dependent[] = JHTML::_('select.option', '1',_JSHOP_NO,'id','name');
        $dependent_attribut = JHTML::_('select.radiolist', $dependent, 'independent','class = "inputbox" size = "1"','id','name', $attribut->independent);

        $all = array();
        $all[] = JHTML::_('select.option', 1, _JSHOP_ALL, 'id','value');
        $all[] = JHTML::_('select.option', 0, _JSHOP_SELECTED, 'id','value');
        if (!isset($attribut->allcats)) $attribut->allcats = 1;
        $lists['allcats'] = JHTML::_('select.radiolist', $all, 'allcats','onclick="PFShowHideSelectCats()"','id','value', $attribut->allcats);
        
        $categories_selected = $attribut->getCategorys();
        $categories = buildTreeCategory(0,1,0);
        $lists['categories'] = JHTML::_('select.genericlist', $categories,'category_id[]','class="inputbox" size="10" multiple = "multiple"','category_id','name', $categories_selected);
        
        JFilterOutput::objectHTMLSafe($attribut, ENT_QUOTES);
	    
        $view=$this->getView("attributes", 'html');
        $view->setLayout("edit");
        $view->assign('attribut', $attribut);
        $view->assign('type_attribut', $type_attribut);
        $view->assign('dependent_attribut', $dependent_attribut);
        $view->assign('etemplatevar', '');    
        $view->assign('languages', $languages);
        $view->assign('multilang', $multilang);
        $view->assign('lists', $lists);
        JPluginHelper::importPlugin('jshoppingadmin');
        $dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onBeforeEditAtribut', array(&$view, &$attribut));
        $view->displayEdit();		
    }
	
	function save(){
        $db = JFactory::getDBO(); 
		$attr_id = JRequest::getInt('attr_id');
        
        JPluginHelper::importPlugin('jshoppingadmin');
        $dispatcher = JDispatcher::getInstance();
        
        $attribut = JTable::getInstance('attribut', 'jshop');    
        $post = JRequest::get("post");
        
        $dispatcher->trigger( 'onBeforeSaveAttribut', array(&$post) );
        
        if (!$attr_id){
            $query = "SELECT MAX(attr_ordering) AS attr_ordering FROM `#__jshopping_attr`";
            $db->setQuery($query);
            $row = $db->loadObject();
            $post['attr_ordering'] = $row->attr_ordering + 1;
        }
        
        if (!$attribut->bind($post)) {
            JError::raiseWarning("",_JSHOP_ERROR_BIND);
            $this->setRedirect("index.php?option=com_jshopping&controller=attributes");
            return 0;
        }
        
        if (isset($post['category_id'])) 
            $categorys = $post['category_id'];
        else
            $categorys = '';
        
        if (!is_array($categorys)) $categorys = array();
        
        $attribut->setCategorys($categorys);

        if (!$attribut->store()) {
            JError::raiseWarning("",_JSHOP_ERROR_SAVE_DATABASE);
            $this->setRedirect("index.php?option=com_jshopping&controller=attributes");
            return 0;
        }
        
        if (!$attr_id){
            $query="ALTER TABLE `#__jshopping_products_attr` ADD `attr_".$attribut->attr_id."` INT( 11 ) NOT NULL";
            $db->setQuery($query);
            $db->query();
            $attr_id = $attribut->attr_id;
        }
        
        
        $dispatcher->trigger( 'onAfterSaveAttribut', array(&$attribut) );
        
		if ($this->getTask()=='apply'){
            $this->setRedirect("index.php?option=com_jshopping&controller=attributes&task=edit&attr_id=".$attr_id); 
        }else{
            $this->setRedirect("index.php?option=com_jshopping&controller=attributes");
        }
        
	}
	
	function remove() {
		$cid = JRequest::getVar("cid");
        $jshopConfig = JSFactory::getConfig();
		$db = JFactory::getDBO();
        JPluginHelper::importPlugin('jshoppingadmin');
        $dispatcher = JDispatcher::getInstance();
        
        $dispatcher->trigger( 'onBeforeRemoveAttribut', array(&$cid) );
        
		$text = '';
		foreach ($cid as $key => $value) {
            $value = intval($value);
			$query = "DELETE FROM `#__jshopping_attr` WHERE `attr_id` = '".$db->escape($value)."'";
			$db->setQuery($query);
			$db->query();
            
            $query="ALTER TABLE `#__jshopping_products_attr` DROP `attr_".$value."`";
            $db->setQuery($query);
            $db->query();
            
            $query = "select * from `#__jshopping_attr_values` where `attr_id` = '".$db->escape($value)."' ";
            $db->setQuery($query);
            $attr_values = $db->loadObjectList();
            foreach ($attr_values as $attr_val){
                @unlink($jshopConfig->image_attributes_path."/".$attr_val->image);
            }
            $query = "delete from `#__jshopping_attr_values` where `attr_id` = '".$db->escape($value)."' ";
            $db->setQuery($query);
            $db->query();
                                                  
            $text = _JSHOP_ATTRIBUT_DELETED;
		}
        
        $dispatcher->trigger( 'onAfterRemoveAttribut', array(&$cid) );
        
		$this->setRedirect("index.php?option=com_jshopping&controller=attributes", $text);
	}
	
	function order() {
		$order = JRequest::getVar("order");
		$cid = JRequest::getInt("id");
		$number = JRequest::getInt("number");
		$db = JFactory::getDBO();
		switch ($order) {
			case 'up':
				$query = "SELECT a.attr_id, a.attr_ordering
					   FROM `#__jshopping_attr` AS a
					   WHERE a.attr_ordering < '" . $number . "'
					   ORDER BY a.attr_ordering DESC
					   LIMIT 1";
				break;
			case 'down':
				$query = "SELECT a.attr_id, a.attr_ordering
					   FROM `#__jshopping_attr` AS a
					   WHERE a.attr_ordering > '" . $number . "'
					   ORDER BY a.attr_ordering ASC
					   LIMIT 1";
		}
		$db->setQuery($query);
		$row = $db->loadObject();
		$query1 = "UPDATE `#__jshopping_attr` AS a
					 SET a.attr_ordering = '" . $row->attr_ordering . "'
					 WHERE a.attr_id = '" . $cid . "'";
		$query2 = "UPDATE `#__jshopping_attr` AS a
					 SET a.attr_ordering = '" . $number . "'
					 WHERE a.attr_id = '" . $row->attr_id . "'";
		$db->setQuery($query1);
		$db->query();
		$db->setQuery($query2);
		$db->query();
		
		$this->setRedirect("index.php?option=com_jshopping&controller=attributes");
	}
    
    function saveorder(){
        $cid = JRequest::getVar('cid', array(), 'post', 'array' );
        $order = JRequest::getVar('order', array(), 'post', 'array' );        
        
        foreach($cid as $k=>$id){
            $table = JTable::getInstance('attribut', 'jshop');
            $table->load($id);
            if ($table->attr_ordering!=$order[$k]){
                $table->attr_ordering = $order[$k];
                $table->store();
            }
        }
                
        $this->setRedirect("index.php?option=com_jshopping&controller=attributes");
    }
      
}
?>