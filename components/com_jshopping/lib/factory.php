<?php
/**
* @version      4.5.0 09.04.2014
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die('Restricted access');

error_reporting(error_reporting() & ~E_NOTICE);
JTable::addIncludePath(JPATH_ROOT.'/components/com_jshopping/tables');
include_once(JPATH_ROOT."/components/com_jshopping/lib/jtableauto.php");
include_once(JPATH_ROOT."/components/com_jshopping/lib/multilangfield.php");
include_once(JPATH_ROOT."/components/com_jshopping/tables/config.php");
require_once(JPATH_ROOT."/components/com_jshopping/lib/functions.php");

class JSFactory{

    public static function getConfig(){
    static $config;
        if (!is_object($config)){
            $db = JFactory::getDBO();
            $config = new jshopConfig($db);
            include(dirname(__FILE__)."/default_config.php");
            if (file_exists(dirname(__FILE__)."/user_config.php")) include(dirname(__FILE__)."/user_config.php");
            $config->load("1");
            $config->loadOtherConfig();
            $config->loadCurrencyValue();

			$params = JComponentHelper::getParams('com_languages');
            $frontend_lang = $params->get('site', 'en-GB');
            $config->frontend_lang = $frontend_lang;

            $lang = JFactory::getLanguage();
            $config->cur_lang = $lang->getTag();

            list($config->pdf_header_width, $config->pdf_header_height, $config->pdf_footer_width, $config->pdf_footer_height) = explode(":", $config->pdf_parameters);
            $config->pdf_header_width = ($config->pdf_header_width > 208) ? (208) : (intval($config->pdf_header_width));
            $config->pdf_footer_width = ($config->pdf_footer_width > 208) ? (208) : (intval($config->pdf_footer_width));
            if (!$config->allow_reviews_prod){
                unset($config->sorting_products_field_select[5]);
                unset($config->sorting_products_name_select[5]);
                unset($config->sorting_products_field_s_select[5]);
                unset($config->sorting_products_name_s_select[5]);
            }

            if ($config->product_count_related_in_row<1) $config->product_count_related_in_row = 1;

            if ($config->user_as_catalog){
                $config->show_buy_in_category = 0;
            }
            if (!$config->stock){
                $config->hide_product_not_avaible_stock = 0;
                $config->hide_buy_not_avaible_stock = 0;
                $config->hide_text_product_not_available = 1;
                $config->product_list_show_qty_stock = 0;
                $config->product_show_qty_stock = 0;
            }

            if ($config->hide_product_not_avaible_stock || $config->hide_buy_not_avaible_stock){
                $config->controler_buy_qty = 1;
            }else{
                $config->controler_buy_qty = 0;
            }

            $config->display_price_front_current = $config->getDisplayPriceFront();// 0 - Brutto, 1 - Netto

            if ($config->template==""){
                $config->template = "default";
            }

            if ($config->show_product_code || $config->show_product_code_in_cart){
                $config->show_product_code_in_order = 1;
            }else{
                $config->show_product_code_in_order = 0;
            }

            if ($config->admin_show_vendors==0){
                $config->vendor_order_message_type = 0; //0 - none, 1 - mesage, 2 - order if not multivendor
                $config->admin_not_send_email_order_vendor_order = 0;
                $config->product_show_vendor = 0;
                $config->product_show_vendor_detail = 0;
            }
			$config->copyrightText = '<span id="mxcpr"><a target="_blank" href="http://www.webdesigner-profi.de/">Copyright MAXXmarketing Webdesigner GmbH</a></span>';
            if ($config->image_resize_type==0){
                $config->image_cut = 1;
                $config->image_fill = 2;
            }elseif ($config->image_resize_type==1){
                $config->image_cut = 0;
                $config->image_fill = 2;
            }else{
                $config->image_cut = 0;
                $config->image_fill = 0;
            }
            if (!$config->tax){
                $config->show_tax_in_product = 0;
                $config->show_tax_product_in_cart = 0;
                $config->hide_tax = 1;
            }
            if (!$config->admin_show_delivery_time){
                $config->show_delivery_time = 0;
                $config->show_delivery_time_checkout = 0;
                $config->show_delivery_time_step5 = 0;
                $config->display_delivery_time_for_product_in_order_mail = 0;
                $config->show_delivery_date = 0;
            }
			if (!$config->admin_show_product_basic_price){
                $config->cart_basic_price_show = 0;
            }
            $config->use_ssl = intval($config->use_ssl);

            JPluginHelper::importPlugin('jshopping');
            $dispatcher = JDispatcher::getInstance();
            $dispatcher->trigger('onLoadJshopConfig', array(&$config));
        }
    return $config;
    }

    public static function getUserShop(){
    static $usershop;
        if (!is_object($usershop)){
            $user = JFactory::getUser();
            $db = JFactory::getDBO();
            require_once(JPATH_ROOT."/components/com_jshopping/tables/usershop.php");
            $usershop = new jshopUserShop($db);
            if($user->id){
                if(!$usershop->isUserInShop($user->id)) {
                    $usershop->addUserToTableShop($user);
                }
                $usershop->load($user->id);
                $usershop->percent_discount = $usershop->getDiscount();
            }else{
                $usershop->percent_discount = 0;
            }
        }
    return $usershop;
    }

    public static function getUserShopGuest(){
    static $userguest;
        if (!is_object($userguest)){
            require_once(JPATH_ROOT."/components/com_jshopping/models/userguest.php");
            $userguest = new jshopUserGust();
            $userguest->load();
            $userguest->percent_discount = 0;
        }
    return $userguest;
    }

    public static function loadCssFiles(){
    static $load;
        $jshopConfig = JSFactory::getConfig();
        if (!$jshopConfig->load_css) return 0;
        if (!$load){
            $document = JFactory::getDocument();
            $jshopConfig = JSFactory::getConfig();
            $document->addStyleSheet(JURI::root().'components/com_jshopping/css/'.$jshopConfig->template.'.css');
            if (file_exists(JPATH_ROOT.'/components/com_jshopping/css/'.$jshopConfig->template.'.custom.css')){
                $document->addStyleSheet(JURI::root().'components/com_jshopping/css/'.$jshopConfig->template.'.custom.css');
            }
            $load = 1;
        }
    }

    public static function loadJsFiles(){
    static $load;
        if (!$load){
            $jshopConfig = JSFactory::getConfig();
            $document = JFactory::getDocument();
            JHtml::_('behavior.framework');
            JHtml::_('bootstrap.framework');
            if ($jshopConfig->load_javascript){
                $document->addScript(JURI::root().'components/com_jshopping/js/jquery/jquery.media.js');
                $document->addScript(JURI::root().'components/com_jshopping/js/functions.js');
                $document->addScript(JURI::root().'components/com_jshopping/js/validateForm.js');
            }
            $load = 1;
        }
    }

    public static function loadJsFilesRating(){
    static $load;
        if (!$load){
            $jshopConfig = JSFactory::getConfig();
            if ($jshopConfig->load_javascript){
                $document = JFactory::getDocument();
                $document->addScript(JURI::root().'components/com_jshopping/js/jquery/jquery.MetaData.js');
                $document->addScript(JURI::root().'components/com_jshopping/js/jquery/jquery.rating.pack.js');
                $document->addStyleSheet(JURI::root().'components/com_jshopping/css/jquery.rating.css');
            }
            $load = 1;
        }
    }

    public static function loadJsFilesLightBox(){
    static $load;
        $jshopConfig = JSFactory::getConfig();
        if (!$jshopConfig->load_jquery_lightbox) return 0;
        if (!$load){
            $document = JFactory::getDocument();
            $document->addScript(JURI::root().'components/com_jshopping/js/jquery/jquery.lightbox-0.5.pack.js');
            $document->addStyleSheet(JURI::root().'components/com_jshopping/css/jquery.lightbox-0.5.css');
            $document->addScriptDeclaration('function initJSlightBox(){
                jQuery("a.lightbox").lightBox({
                    imageLoading: "'.JURI::root().'components/com_jshopping/images/loading.gif",
                    imageBtnClose: "'.JURI::root().'components/com_jshopping/images/close.gif",
                    imageBtnPrev: "'.JURI::root().'components/com_jshopping/images/prev.gif",
                    imageBtnNext: "'.JURI::root().'components/com_jshopping/images/next.gif",
                    imageBlank: "'.JURI::root().'components/com_jshopping/images/blank.gif",
                    txtImage: "'._JSHOP_IMAGE.'",
                    txtOf: "'._JSHOP_OF.'"
                });
            }
            jQuery(function() { initJSlightBox(); });');
            $load = 1;
        }
    }
    
    public static function reloadConfigFieldTLF(){
        $jshopConfig = JSFactory::getConfig();
        $reload = array('user_field_client_type','user_field_title','sorting_products_name_select','sorting_products_name_s_select','count_product_select');
        foreach($reload as $field){
            $tmp = $jshopConfig->$field;
            foreach($tmp as $k=>$v){
                if (defined($v)) $tmp[$k] = constant($v);
            }
            $jshopConfig->$field = $tmp;
        }
    }

    public static function loadLanguageFile($langtag = ""){
        $lang = JFactory::getLanguage();
        if ($langtag==""){
            $langtag = $lang->getTag();
        }
        $langpatch = JPATH_ROOT.'/components/com_jshopping/lang/';        
        if (file_exists($langpatch.'override/'.$langtag.'.php'))
            require_once($langpatch.'override/'.$langtag.'.php');
        if (file_exists($langpatch.$langtag.'.php'))
            require_once($langpatch.$langtag.'.php');
        else 
            require_once($langpatch.'en-GB.php');
        JSFactory::reloadConfigFieldTLF();
    }

    public static function loadExtLanguageFile($extname, $langtag = ""){
        $lang = JFactory::getLanguage();
        if ($langtag==""){
            $langtag = $lang->getTag();
        }
        if(file_exists(JPATH_ROOT . '/components/com_jshopping/lang/'.$extname.'/'.$langtag.'.php'))
            require_once (JPATH_ROOT . '/components/com_jshopping/lang/'.$extname.'/'.$langtag.'.php');
        else 
            require_once (JPATH_ROOT . '/components/com_jshopping/lang/'.$extname.'/en-GB.php');
    }

    public static function loadAdminLanguageFile($langtag = ""){
        $lang = JFactory::getLanguage();
        if ($langtag==""){
            $langtag = $lang->getTag();
        }
        $langpatch = JPATH_ROOT.'/administrator/components/com_jshopping/lang/';        
        if (file_exists($langpatch.'override/'.$langtag.'.php'))
            require_once($langpatch.'override/'.$langtag.'.php');
        if (file_exists($langpatch.$langtag.'.php'))
            require_once($langpatch.$langtag.'.php');
        else 
            require_once($langpatch.'en-GB.php');
        JSFactory::reloadConfigFieldTLF();
    }

    public static function loadExtAdminLanguageFile($extname, $langtag = ""){
        $lang = JFactory::getLanguage();
        if ($langtag==""){
            $langtag = $lang->getTag();
        }
        if(file_exists(JPATH_ROOT . '/administrator/components/com_jshopping/lang/'.$extname.'/'.$langtag.'.php'))
            require_once (JPATH_ROOT . '/administrator/components/com_jshopping/lang/'.$extname.'/'.$langtag.'.php');
        else 
            require_once (JPATH_ROOT . '/administrator/components/com_jshopping/lang/'.$extname.'/en-GB.php');
    }

    public static function getLang($langtag = ""){
    static $ml;
        if (!is_object($ml) || $langtag!=""){
            $jshopConfig = JSFactory::getConfig();
            $ml = new multiLangField();
            if ($langtag==""){
                $langtag = $jshopConfig->cur_lang;
            }
            $ml->setLang($langtag);
        }
    return $ml;
    }

    public static function getReservedFirstAlias(){
    static $alias;
        if (!is_array($alias)){
            jimport('joomla.filesystem.folder');
            $files = JFolder::files(JPATH_ROOT."/components/com_jshopping/controllers");
            $alias = array();
            foreach($files as $file){
                $alias[] = str_replace(".php","", $file);
            }
        }
    return $alias;
    }

    public static function getAliasCategory(){
    static $alias;
        if (!is_array($alias)){
            $db = JFactory::getDBO();
            $lang = JSFactory::getLang();
            $dbquery = "select category_id as id, `".$lang->get('alias')."` as alias from #__jshopping_categories where `".$lang->get('alias')."`!=''"; 
            $db->setQuery($dbquery);
            $rows = $db->loadObjectList();
            $alias = array();
            foreach($rows as $row){
                $alias[$row->id] = $row->alias;
            }
            unset($rows);
        }
    return $alias;
    }

    public static function getAliasManufacturer(){
    static $alias;
        if (!is_array($alias)){
            $db = JFactory::getDBO();
            $lang = JSFactory::getLang();
            $dbquery = "select manufacturer_id as id, `".$lang->get('alias')."` as alias from #__jshopping_manufacturers where `".$lang->get('alias')."`!=''";
            $db->setQuery($dbquery);
            $rows = $db->loadObjectList();
            $alias = array();
            foreach($rows as $row){
                $alias[$row->id] = $row->alias;
            }
            unset($rows);
        }
    return $alias;
    }

    public static function getAliasProduct(){
    static $alias;
        if (!is_array($alias)){
            $db = JFactory::getDBO();
            $lang = JSFactory::getLang();
            $dbquery = "select product_id as id, `".$lang->get('alias')."` as alias from #__jshopping_products where `".$lang->get('alias')."`!=''"; 
            $db->setQuery($dbquery);
            $rows = $db->loadObjectList();
            $alias = array();
            foreach($rows as $k=>$row){
                $alias[$row->id] = $row->alias;
                unset($rows[$k]);
            }
            unset($rows);
        }
    return $alias;
    }

    public static function getAllAttributes($resformat = 0){
    static $attributes;
        if (!is_array($attributes)){
            $_attrib = JTable::getInstance("attribut","jshop");
            $attributes = $_attrib->getAllAttributes();
        }
        if ($resformat==0){
            return $attributes;
        }
        if ($resformat==1){
            $attributes_format1 = array();
            foreach($attributes as $v){
                $attributes_format1[$v->attr_id] = $v;
            }
            return $attributes_format1;
        }
        if ($resformat==2){
            $attributes_format2 = array();
            $attributes_format2['independent']= array();
            $attributes_format2['dependent']= array();
            foreach($attributes as $v){
                if ($v->independent) $key_dependent = "independent"; else $key_dependent = "dependent";
                $attributes_format2[$key_dependent][$v->attr_id] = $v;
            }
            return $attributes_format2;
        }
    }

    public static function getAllUnits(){
    static $rows;
        if (!is_array($rows)){
            $_unit = JTable::getInstance("unit","jshop");
            $rows = $_unit->getAllUnits();
        }
    return $rows;
    }
    
    public static function getAllTaxesOriginal(){
    static $rows;
        if (!is_array($rows)){
            $_tax = JTable::getInstance('tax', 'jshop');
            $_rows = $_tax->getAllTaxes();
            $rows = array();
            foreach($_rows as $row){
                $rows[$row->tax_id] = $row->tax_value;
            }
        }
    return $rows;
    }
    
    public static function getAllTaxes(){
    static $rows;
        if (!is_array($rows)){
            $jshopConfig = JSFactory::getConfig();
            JPluginHelper::importPlugin('jshopping');
            $dispatcher = JDispatcher::getInstance();
            $_tax = JTable::getInstance('tax', 'jshop');
            $rows = JSFactory::getAllTaxesOriginal();
            if ($jshopConfig->use_extend_tax_rule){
                $country_id = 0;
                $adv_user = JSFactory::getUserShop();
                $country_id = $adv_user->country;
                if ($jshopConfig->tax_on_delivery_address && $adv_user->delivery_adress && $adv_user->d_country){
                    $country_id = $adv_user->d_country;
                }
                $client_type = $adv_user->client_type;
                $enter_tax_id = $adv_user->tax_number!="";
                if (!$country_id){
                    $adv_user = JSFactory::getUserShopGuest();
                    $country_id = $adv_user->country;
                    if ($jshopConfig->tax_on_delivery_address && $adv_user->delivery_adress && $adv_user->d_country){
                        $country_id = $adv_user->d_country;
                    }
                    $client_type = $adv_user->client_type;
                    $enter_tax_id = $adv_user->tax_number!="";
                }
                if ($country_id){
                    $_rowsext = $_tax->getExtTaxes();
                    $dispatcher->trigger('beforeGetAllTaxesRowsext', array(&$_rowsext, &$country_id, &$adv_user, &$rows) );
                    foreach($_rowsext as $v){
                        if (in_array($country_id, $v->countries)){
                            if ($jshopConfig->ext_tax_rule_for==1){
                                if ($enter_tax_id){
                                    $rows[$v->tax_id] = $v->firma_tax;
                                }else{
                                    $rows[$v->tax_id] = $v->tax;
                                }    
                            }else{
                                if ($client_type==2){
                                    $rows[$v->tax_id] = $v->firma_tax;
                                }else{
                                    $rows[$v->tax_id] = $v->tax;
                                }
                            }
                        }
                    }
                    $dispatcher->trigger('afterGetAllTaxesRowsext', array(&$_rowsext, &$country_id, &$adv_user, &$rows) );
                    unset($_rowsext);
                }
            }
        $dispatcher->trigger('afterGetAllTaxes', array(&$rows) );
        }
    return $rows;
    }

    public static function getAllManufacturer(){
    static $rows;
        if (!is_array($rows)){
            $db = JFactory::getDBO();
            $lang = JSFactory::getLang();
            JPluginHelper::importPlugin('jshopping');
            $dispatcher = JDispatcher::getInstance();
            $adv_result = "manufacturer_id as id, `".$lang->get('name')."` as name, manufacturer_logo, manufacturer_url";
            $dispatcher->trigger('onBeforeQueryGetAllManufacturer', array(&$adv_result));
            $query = "select ".$adv_result." from #__jshopping_manufacturers where manufacturer_publish='1'";
            $db->setQuery($query);
            $_rows = $db->loadObjectList();
            $rows = array();
            foreach($_rows as $row){
                $rows[$row->id] = $row;
            }
            unset($_rows);
        }
    return $rows;
    }

    public static function getMainVendor(){
    static $row;
        if (!isset($row)){
            $row = JTable::getInstance('vendor', 'jshop');
            $row->loadMain();
        }
    return $row;
    }

    public static function getAllVendor(){
    static $rows;
        if (!is_array($rows)){
            $db = JFactory::getDBO();
            $query = "select id, shop_name, l_name, f_name from #__jshopping_vendors";
            $db->setQuery($query);
            $_rows = $db->loadObjectList();
            $rows = array();
            $mainvendor = JSFactory::getMainVendor();
            $rows[0] = $mainvendor;
            foreach($_rows as $row){
                $rows[$row->id] = $row;
            }
            unset($_rows);
        }
    return $rows;
    }

    public static function getAllDeliveryTime(){
    static $rows;
        if (!is_array($rows)){
            $db = JFactory::getDBO();
            $lang = JSFactory::getLang();
            $query = "select id, `".$lang->get('name')."` as name from #__jshopping_delivery_times";
            $db->setQuery($query);
            $_rows = $db->loadObjectList();
            $rows = array();
            foreach($_rows as $row){
                $rows[$row->id] = $row->name;
            }
            unset($_rows);
        }
    return $rows;
    }

    public static function getAllDeliveryTimeDays(){
    static $rows;
        if (!is_array($rows)){
            $db = JFactory::getDBO();
            $lang = JSFactory::getLang();
            $query = "select id, days from #__jshopping_delivery_times";
            $db->setQuery($query);
            $_rows = $db->loadObjectList();
            $rows = array();
            foreach($_rows as $row){
                $rows[$row->id] = $row->days;
            }
            unset($_rows);
        }
    return $rows;
    }

    public static function getAllProductExtraField(){
    static $list;
        if (!is_array($list)){
            $productfield = JTable::getInstance('productfield', 'jshop');
            $list = $productfield->getList();
        }
    return $list;
    }

    public static function getAllProductExtraFieldValue(){
    static $list;
        if (!is_array($list)){
            $productfieldvalue = JTable::getInstance('productfieldvalue', 'jshop');
            $list = $productfieldvalue->getAllList(1);
        }
    return $list;
    }

    public static function getAllProductExtraFieldValueDetail(){
    static $list;
        if (!is_array($list)){
            $productfieldvalue = JTable::getInstance('productfieldvalue', 'jshop');
            $list = $productfieldvalue->getAllList(2);
        }
    return $list;
    }

    public static function getDisplayListProductExtraFieldForCategory($cat_id){
    static $listforcat;
        if (!isset($listforcat[$cat_id])){
            $fields = array();
            $list = JSFactory::getAllProductExtraField();
            foreach($list as $val){
                if ($val->allcats){
                    $fields[] = $val->id;
                }else{
                    if (in_array($cat_id, $val->cats)) $fields[] = $val->id;
                }
            }

            $jshopConfig = JSFactory::getConfig();
            $config_list = $jshopConfig->getProductListDisplayExtraFields();
            foreach($fields as $k=>$val){
                if (!in_array($val, $config_list)) unset($fields[$k]);
            }
            $listforcat[$cat_id] = $fields;
        }
    return $listforcat[$cat_id];
    }

    public static function getDisplayFilterExtraFieldForCategory($cat_id){
    static $listforcat;
        if (!isset($listforcat[$cat_id])){
            $fields = array();
            $list = JSFactory::getAllProductExtraField();
            foreach($list as $val){
                if ($val->allcats){
                    $fields[] = $val->id;
                }else{
                    if (in_array($cat_id, $val->cats)) $fields[] = $val->id;
                }
            }
            
            $jshopConfig = JSFactory::getConfig();
            $config_list = $jshopConfig->getFilterDisplayExtraFields();
            foreach($fields as $k=>$val){
                if (!in_array($val, $config_list)) unset($fields[$k]);
            }
            $listforcat[$cat_id] = $fields;
        }
    return $listforcat[$cat_id];
    }

    public static function getAllCurrency(){
    static $list;
        if (!is_array($list)){
            $currency =JTable::getInstance('currency', 'jshop');
            $_list = $currency->getAllCurrencies();
            $list = array();
            foreach($_list as $row){
                $list[$row->currency_id] = $row;
            }
        }
    return $list;
    }

    public static function getShippingExtList($for_shipping = 0){
    static $list;
        if (!is_array($list)){
            $jshopConfig = JSFactory::getConfig();
            $path = $jshopConfig->path."shippings";
            $shippingext = JTable::getInstance('shippingext', 'jshop');
            $_list = $shippingext->getList(1);
            $list = array();
            foreach($_list as $row){
                $extname = $row->alias;
                $filepatch = $path."/".$extname."/".$extname.".php";
                if (file_exists($filepatch)){
                    include_once($filepatch);
                    $row->exec = new $extname();
                    $list[$row->id] = $row;
                }else{
                    JError::raiseWarning("",'Load ShippingExt "'.$extname.'" error.');
                }
            }
        }
        if ($for_shipping==0){
            return $list;
        }
        $returnlist = array();
        foreach($list as $row){
            if ($row->shipping_method!=""){
                $sm = unserialize($row->shipping_method);
            }else{
                $sm = array();
            }
            if(!isset($sm[$for_shipping])){
                $sm[$for_shipping]=1;
            }
            if ($sm[$for_shipping]!=="0"){
                $returnlist[] = $row;
            }
        }
    return $returnlist;
    }

}
?>