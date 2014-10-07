<?php
/**
* @version      4.6.0 26.06.2014
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/

defined('_JEXEC') or die('Restricted access');
jimport( 'joomla.application.component.model');

class JshoppingModelProducts extends JModelLegacy{
    
    function _getAllProductsQueryForFilter($filter){
        $lang = JSFactory::getLang();
        $db = JFactory::getDBO();
        $where = "";
        if (isset($filter['without_product_id']) && $filter['without_product_id']){
            $where .= " AND pr.product_id <> '".$db->escape($filter['without_product_id'])."' ";    
        }
        if (isset($filter['category_id']) && $filter['category_id']){
            $category_id = $filter['category_id'];
            $where .= " AND pr_cat.category_id = '".$db->escape($filter['category_id'])."' ";    
        }
        if (isset($filter['text_search']) && $filter['text_search']){
            $text_search = $filter['text_search'];
            $word = addcslashes($db->escape($text_search), "_%");
            $where .=  "AND (LOWER(pr.`".$lang->get('name')."`) LIKE '%" . $word . "%' OR LOWER(pr.`".$lang->get('short_description')."`) LIKE '%" . $word . "%' OR LOWER(pr.`".$lang->get('description')."`) LIKE '%" . $word . "%' OR pr.product_ean LIKE '%" . $word . "%' OR pr.product_id LIKE '%" . $word . "%')";            
        }
        if (isset($filter['manufacturer_id']) && $filter['manufacturer_id']){
            $where .= " AND pr.product_manufacturer_id = '".$db->escape($filter['manufacturer_id'])."' ";    
        }
        if (isset($filter['label_id']) && $filter['label_id']){
            $where .= " AND pr.label_id = '".$db->escape($filter['label_id'])."' ";    
        }
        if (isset($filter['publish']) && $filter['publish']){
            if ($filter['publish']==1) $_publish = 1; else $_publish = 0;            
            $where .= " AND pr.product_publish = '".$db->escape($_publish)."' ";
        }
        if (isset($filter['vendor_id']) && $filter['vendor_id'] >= 0){
            $where .= " AND pr.vendor_id = '".$db->escape($filter['vendor_id'])."' ";
        }
    return $where;
    }
    
    function _allProductsOrder($order = null, $orderDir = null, $category_id = 0){
        if ($order && $orderDir){
            $fields = array("product_id"=>"pr.product_id", "name"=>"name",'category'=>"namescats","manufacturer"=>"man_name","vendor"=>"v_f_name","ean"=>"ean","qty"=>"qty","price"=>"pr.product_price","hits"=>"pr.hits","date"=>"pr.product_date_added", "product_name_image"=>"pr.image");
            if ($category_id) $fields['ordering'] = "pr_cat.product_ordering";
            if (strtolower($orderDir)!="asc") $orderDir = "desc";
			if ($orderDir=="desc") $fields['qty'] ='pr.unlimited desc, qty';
            if (!$fields[$order]) return "";
            return "order by ".$fields[$order]." ".$orderDir;
        }else{
            return "";
        }
    }
    
    function getAllProducts($filter, $limitstart = null, $limit = null, $order = null, $orderDir = null){
        $jshopConfig = JSFactory::getConfig();
        $lang = JSFactory::getLang();
        $db = JFactory::getDBO(); 
        if ($limit > 0){
            $limit = " LIMIT ".$limitstart.", ".$limit;
        }else{
            $limit = "";
        }        
        if (isset($filter['category_id'])) 
            $category_id = $filter['category_id'];
        else 
            $category_id = '';
        
        $where = $this->_getAllProductsQueryForFilter($filter);
        
        $query_filed = ""; $query_join = "";
        if ($jshopConfig->admin_show_vendors){
            $query_filed .= ", pr.vendor_id, V.f_name as v_f_name, V.l_name as v_l_name";
            $query_join .= " left join `#__jshopping_vendors` as V on pr.vendor_id=V.id ";
        }

        if ($category_id) {
            $query = "SELECT pr.product_id, pr.product_publish, pr_cat.product_ordering, pr.`".$lang->get('name')."` as name, pr.`".$lang->get('short_description')."` as short_description, man.`".$lang->get('name')."` as man_name, pr.product_ean as ean, pr.product_quantity as qty, pr.image as image, pr.product_price, pr.currency_id, pr.hits, pr.unlimited, pr.product_date_added, pr.label_id $query_filed FROM `#__jshopping_products` AS pr
                      LEFT JOIN `#__jshopping_products_to_categories` AS pr_cat USING (product_id)
                      LEFT JOIN `#__jshopping_manufacturers` AS man ON pr.product_manufacturer_id=man.manufacturer_id
                      $query_join
                      WHERE pr.parent_id=0 ".$where." ".$this->_allProductsOrder($order, $orderDir, $category_id)." ".$limit;
        }else{
            $mysqlversion = getMysqlVersion();
            if ($mysqlversion < "4.1.0"){
                $spec_where = "cat.`".$lang->get('name')."` AS namescats";
            }else{
                $spec_where = "GROUP_CONCAT(cat.`".$lang->get('name')."` SEPARATOR '<br>') AS namescats";
            }
            
            $query = "SELECT pr.product_id, pr.product_publish, pr.`".$lang->get('name')."` as name, pr.`".$lang->get('short_description')."` as short_description, man.`".$lang->get('name')."` as man_name, ".$spec_where.", pr.product_ean as ean, pr.product_quantity as qty, pr.image as image, pr.product_price, pr.currency_id, pr.hits, pr.unlimited, pr.product_date_added, pr.label_id $query_filed FROM `#__jshopping_products` AS pr 
                      LEFT JOIN `#__jshopping_products_to_categories` AS pr_cat USING (product_id)
                      LEFT JOIN `#__jshopping_categories` AS cat ON pr_cat.category_id=cat.category_id
                      LEFT JOIN `#__jshopping_manufacturers` AS man ON pr.product_manufacturer_id=man.manufacturer_id
                      $query_join
                      WHERE pr.parent_id=0 ".$where." GROUP BY pr.product_id ".$this->_allProductsOrder($order, $orderDir)." ".$limit;
        }
		$dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onBeforeDisplayListProductsGetAllProducts', array(&$this, &$query, $filter, $limitstart, $limit, $order, $orderDir));
        $db->setQuery($query);
        return $db->loadObjectList();
    }
    
    function getCountAllProducts($filter){
        $lang = JSFactory::getLang();
        $db = JFactory::getDBO();                
        if (isset($filter['category_id'])) 
            $category_id = $filter['category_id'];
        else
            $category_id = '';
        
        $where = $this->_getAllProductsQueryForFilter($filter);
        if ($category_id) {
            $query = "SELECT count(pr.product_id) FROM `#__jshopping_products` AS pr
                      LEFT JOIN `#__jshopping_products_to_categories` AS pr_cat USING (product_id)
                      LEFT JOIN `#__jshopping_manufacturers` AS man ON pr.product_manufacturer_id=man.manufacturer_id
                      WHERE pr.parent_id=0 ".$where;
        } else {
            $query = "SELECT count(pr.product_id) FROM `#__jshopping_products` AS pr
                      LEFT JOIN `#__jshopping_manufacturers` AS man ON pr.product_manufacturer_id=man.manufacturer_id
                      WHERE pr.parent_id=0 ".$where;
        }
		$dispatcher = JDispatcher::getInstance();
        $dispatcher->trigger('onBeforeDisplayListProductsGetCountAllProducts', array(&$this, &$query, $filter));
        $db->setQuery($query);        
        return $db->loadResult();
    }
    
    function productInCategory($product_id, $category_id) {
        $db = JFactory::getDBO();
        $query = "SELECT prod_cat.category_id FROM `#__jshopping_products_to_categories` AS prod_cat
                   WHERE prod_cat.product_id = '".$db->escape($product_id)."' AND prod_cat.category_id = '".$db->escape($category_id)."'";
        $db->setQuery($query);
        $res = $db->query();
        return $db->getNumRows($res);
    }
    
    function getMaxOrderingInCategory($category_id) {
        $db = JFactory::getDBO();
        $query = "SELECT MAX(product_ordering) as k FROM `#__jshopping_products_to_categories` WHERE category_id = '".$db->escape($category_id)."'";
        $db->setQuery($query);
        return $db->loadResult();
    }
    
    function setCategoryToProduct($product_id, $categories = array()){
        $db = JFactory::getDBO();
        foreach($categories as $cat_id){
            if (!$this->productInCategory($product_id, $cat_id)){
                $ordering = $this->getMaxOrderingInCategory($cat_id)+1;
                $query = "INSERT INTO `#__jshopping_products_to_categories` SET `product_id` = '".$db->escape($product_id)."', `category_id` = '".$db->escape($cat_id)."', `product_ordering` = '".$db->escape($ordering)."'";
                $db->setQuery($query);
                $db->query();
            }
        }

        //delete other cat for product        
        $query = "select `category_id` from `#__jshopping_products_to_categories` where `product_id` = '".$db->escape($product_id)."'";
        $db->setQuery($query);
        $listcat = $db->loadObjectList();
        foreach($listcat as $val){
            if (!in_array($val->category_id, $categories)){
                $query = "delete from `#__jshopping_products_to_categories` where `product_id` = '".$db->escape($product_id)."' and `category_id` = '".$db->escape($val->category_id)."'";
                $db->setQuery($query);
                $db->query();
            }
        }
                    
    }
    
    function getRelatedProducts($product_id){
        $db = JFactory::getDBO();
        $lang = JSFactory::getLang();
        $query = "SELECT relation.product_related_id AS product_id, prod.`".$lang->get('name')."` as name, prod.image as image
                FROM `#__jshopping_products_relations` AS relation
                LEFT JOIN `#__jshopping_products` AS prod ON prod.product_id=relation.product_related_id                
                WHERE relation.product_id = '".$db->escape($product_id)."' order by relation.id";
        $db->setQuery($query);
        return $db->loadObjectList();
    }
    
    function saveAditionalPrice($product_id, $product_add_discount, $quantity_start, $quantity_finish){
        $db = JFactory::getDBO();
        $query = "DELETE FROM `#__jshopping_products_prices` WHERE `product_id` = '".$db->escape($product_id)."'";
        $db->setQuery($query);
        $db->query();
        
        $counter = 0;
        if (count($product_add_discount)){
            foreach ($product_add_discount as $key=>$value){
                
                if ((!$quantity_start[$key] && !$quantity_finish[$key])) continue;
                
                $query = "INSERT INTO `#__jshopping_products_prices` SET 
                            `product_id` = '" . $db->escape($product_id) . "',
                            `discount` = '" . $db->escape(saveAsPrice($product_add_discount[$key])) . "',
                            `product_quantity_start` = '" . intval($quantity_start[$key]) . "',
                            `product_quantity_finish` = '" . intval($quantity_finish[$key]) . "'";
                $db->setQuery($query);
                $db->query();
                $counter++;
            }
        }
        $product = JTable::getInstance('product', 'jshop');
        $product->load($product_id);
        $product->product_is_add_price = ($counter>0) ? (1) : (0);
        $product->store();
    }
    
    function saveFreeAttributes($product_id, $attribs){
        $db = JFactory::getDBO();
        $query = "DELETE FROM `#__jshopping_products_free_attr` WHERE `product_id` = '".$db->escape($product_id)."'";
        $db->setQuery($query);
        $db->query();
        
        if (is_array($attribs)){
            foreach($attribs as $attr_id=>$v){
                $query = "insert into `#__jshopping_products_free_attr` set `product_id` = '".$db->escape($product_id)."', attr_id='".$db->escape($attr_id)."'";
                $db->setQuery($query);
                $db->query();
            }
        }
    }
    
    function saveProductOptions($product_id, $options){
        $db = JFactory::getDBO(); 
        foreach($options as $key=>$value){
            $query = "DELETE FROM `#__jshopping_products_option` WHERE `product_id` = '".$db->escape($product_id)."' AND `key`='".$db->escape($key)."'";
            $db->setQuery($query);
            $db->query();
            
            $query = "insert into `#__jshopping_products_option` set `product_id` = '".$db->escape($product_id)."', `key`='".$db->escape($key)."', `value`='".$db->escape($value)."'";
            $db->setQuery($query);
            $db->query();            
        }
    }
    
    function getMinimalPrice($price, $attrib_prices, $attrib_ind_price_data, $is_add_price, $add_discounts){
        $minprice = $price;
        if (is_array($attrib_prices)){            
            $minprice = min($attrib_prices);            
        }
        
        if (is_array($attrib_ind_price_data[0])){
            $attr_ind_id = array_unique($attrib_ind_price_data[0]);
            $startprice = $minprice;
            foreach($attr_ind_id as $attr_id){
                $tmpprice = array();
                foreach($attrib_ind_price_data[0] as $k=>$tmp_attr_id){
                    if ($tmp_attr_id==$attr_id){
                        if ($attrib_ind_price_data[1][$k]=="+"){
                            $tmpprice[] = $startprice + $attrib_ind_price_data[2][$k];
                        }elseif ($attrib_ind_price_data[1][$k]=="-"){
                            $tmpprice[] = $startprice - $attrib_ind_price_data[2][$k];
                        }elseif ($attrib_ind_price_data[1][$k]=="*"){
                            $tmpprice[] = $startprice * $attrib_ind_price_data[2][$k];
                        }elseif ($attrib_ind_price_data[1][$k]=="/"){
                            $tmpprice[] = $startprice / $attrib_ind_price_data[2][$k];
                        }elseif ($attrib_ind_price_data[1][$k]=="%"){
                            $tmpprice[] = $startprice * $attrib_ind_price_data[2][$k] / 100;
                        }elseif ($attrib_ind_price_data[1][$k]=="="){
                            $tmpprice[] = $attrib_ind_price_data[2][$k];
                        }
                    }
                }
                $startprice = min($tmpprice);
            }
            $minprice = $startprice;
        }
        
        if ($is_add_price && is_array($add_discounts)){
            $jshopConfig = JSFactory::getConfig();
            $max_discount = max($add_discounts);
            if ($jshopConfig->product_price_qty_discount == 1){
                $minprice = $minprice - $max_discount; //discount value
            }else{
                $minprice = $minprice - ($minprice * $max_discount / 100); //discount percent
            }            
        }
        return $minprice;
    }
    
    function copyProductBuildQuery($table, $array, $product_id){
        $db = JFactory::getDBO();
        $query = "INSERT INTO `#__jshopping_products_".$table."` SET ";
        $array_keys = array('image_id', 'price_id', 'review_id', 'video_id', 'product_attr_id', 'value_id', 'id');
        foreach ($array as $key=>$value){
            if (in_array($key, $array_keys)) continue;
            if ($key=='product_id') $value = $product_id;
            $query .= "`".$key."` = '".$db->escape($value)."', ";
        }
        return $query = substr($query, 0, strlen($query) - 2);
    }
    
    function uploadVideo($product, $product_id, $post){
        $jshopConfig = JSFactory::getConfig();
        $image_prev_video = "";
        for($i=0;$i<$jshopConfig->product_video_upload_count;$i++){
			if (!(isset($post['product_insert_code_'.$i]) && ($post['product_insert_code_'.$i] == 1))) {
				$upload = new UploadFile($_FILES['product_video_'.$i]);
				$upload->setDir($jshopConfig->video_product_path);
                $upload->setFileNameMd5(0);
                $upload->setFilterName(1);
				if ($upload->upload()){
					$file_video = $upload->getName();
					@chmod($jshopConfig->video_product_path."/".$file_video, 0777);
					
					$upload2 = new UploadFile($_FILES['product_video_preview_'.$i]);
					$upload2->setAllowFile(array('jpeg','jpg','gif','png'));
					$upload2->setDir($jshopConfig->video_product_path);
                    $upload2->setFileNameMd5(0);
                    $upload2->setFilterName(1);
					if ($upload2->upload()){
						$image_prev_video = $upload2->getName();
						@chmod($jshopConfig->video_product_path."/".$image_prev_video, 0777);
					}else{
						if ($upload2->getError() != 4){
							JError::raiseWarning("", _JSHOP_ERROR_UPLOADING_VIDEO_PREVIEW);
							saveToLog("error.log", "SaveProduct - Error upload video preview. code: ".$upload2->getError());
						}    
					}
					unset($upload2);
					$this->addToProductVideo($product_id, $file_video, $image_prev_video);
				}else{
					if ($upload->getError() != 4){
						JError::raiseWarning("", _JSHOP_ERROR_UPLOADING_VIDEO);
						saveToLog("error.log", "SaveProduct - Error upload video. code: ".$upload->getError());
					}
				}
				unset($upload);
			} else {
				$code_video = JRequest::getVar('product_video_code_'.$i, null, 'default', 'none', JREQUEST_ALLOWRAW);
				if ($code_video) {
					$upload2 = new UploadFile($_FILES['product_video_preview_'.$i]);
					$upload2->setAllowFile(array('jpeg','jpg','gif','png'));
					$upload2->setDir($jshopConfig->video_product_path);
                    $upload2->setFileNameMd5(0);
                    $upload2->setFilterName(1);
					if ($upload2->upload()){
						$image_prev_video = $upload2->getName();
						@chmod($jshopConfig->video_product_path."/".$image_prev_video, 0777);
					}else{
						if ($upload2->getError() != 4){
							JError::raiseWarning("", _JSHOP_ERROR_UPLOADING_VIDEO_PREVIEW);
							saveToLog("error.log", "SaveProduct - Error upload video preview. code: ".$upload2->getError());
						}    
					}
					unset($upload2);
					$this->addToProductVideoCode($product_id, $code_video, $image_prev_video);
				}
			}
        }
    }
    
    function addToProductVideo($product_id, $name_video, $preview_image = '') {
        $db = JFactory::getDBO();
        $query = "INSERT INTO `#__jshopping_products_videos`
                   SET `product_id` = '" . $db->escape($product_id) . "', `video_name` = '" . $db->escape($name_video) . "', `video_preview` = '" . $db->escape($preview_image) . "'";
        $db->setQuery($query);
        $db->query();
    }

	function addToProductVideoCode($product_id, $code_video, $preview_image = '') {
        $db = JFactory::getDBO();
        $query = "INSERT INTO `#__jshopping_products_videos`
                   SET `product_id` = '" . $db->escape($product_id) . "', `video_code` = '" . $db->escape($code_video) . "', `video_preview` = '" . $db->escape($preview_image) . "'";
        $db->setQuery($query);
        $db->query();
    }

    function uploadImages($product, $product_id, $post){
        $jshopConfig = JSFactory::getConfig();
        $dispatcher = JDispatcher::getInstance();
        
        for($i=0; $i<$jshopConfig->product_image_upload_count; $i++){
            $upload = new UploadFile($_FILES['product_image_'.$i]);
            $upload->setAllowFile(array('jpeg','jpg','gif','png'));
            $upload->setDir($jshopConfig->image_product_path);
            $upload->setFileNameMd5(0);
            $upload->setFilterName(1);
            if ($upload->upload()){
                $name_image = $upload->getName();
                $name_thumb = 'thumb_'.$name_image;
                $name_full = 'full_'.$name_image;
                @chmod($jshopConfig->image_product_path."/".$name_image, 0777);

                $path_image = $jshopConfig->image_product_path."/".$name_image;
                $path_thumb = $jshopConfig->image_product_path."/".$name_thumb;
                $path_full =  $jshopConfig->image_product_path."/".$name_full;
                rename($path_image, $path_full);

				if ($jshopConfig->image_product_original_width || $jshopConfig->image_product_original_height){
                    if (!ImageLib::resizeImageMagic($path_full, $jshopConfig->image_product_original_width, $jshopConfig->image_product_original_height, $jshopConfig->image_cut, $jshopConfig->image_fill, $path_full, $jshopConfig->image_quality, $jshopConfig->image_fill_color)){
                        JError::raiseWarning("",_JSHOP_ERROR_CREATE_THUMBAIL);
                        saveToLog("error.log", "SaveProduct - Error create thumbail");
                        $error = 1;
                    }
                }

                $error = 0;
                if ($post['size_im_product']==3){
                    copy($path_full, $path_thumb);
                    @chmod($path_thumb, 0777);
                }else{
                    if ($post['size_im_product']==1){
                        $product_width_image = $jshopConfig->image_product_width;
                        $product_height_image = $jshopConfig->image_product_height;
                    }else{
                        $product_width_image = JRequest::getInt('product_width_image'); 
                        $product_height_image = JRequest::getInt('product_height_image');
                    }
 
                    if (!ImageLib::resizeImageMagic($path_full, $product_width_image, $product_height_image, $jshopConfig->image_cut, $jshopConfig->image_fill, $path_thumb, $jshopConfig->image_quality, $jshopConfig->image_fill_color)){
                        JError::raiseWarning("",_JSHOP_ERROR_CREATE_THUMBAIL);
                        saveToLog("error.log", "SaveProduct - Error create thumbail");
                        $error = 1;
                    }    
                    @chmod($path_thumb, 0777);
                    unset($img);
                }

                if ($post['size_full_product']==3){
                    copy($path_full, $path_image);
                    @chmod($path_image, 0777);
                }else{
                    if ($post['size_full_product']==1){
                        $product_full_width_image = $jshopConfig->image_product_full_width; 
                        $product_full_height_image = $jshopConfig->image_product_full_height;
                    }else{
                        $product_full_width_image = JRequest::getInt('product_full_width_image'); 
                        $product_full_height_image = JRequest::getInt('product_full_height_image');
                    }

                    if (!ImageLib::resizeImageMagic($path_full, $product_full_width_image, $product_full_height_image, $jshopConfig->image_cut, $jshopConfig->image_fill, $path_image, $jshopConfig->image_quality, $jshopConfig->image_fill_color)){
                        JError::raiseWarning("",_JSHOP_ERROR_CREATE_THUMBAIL);
                        $error = 1;
                    }    
                    @chmod($path_image, 0777);
                    unset($img);
                }

                if (!$error){
                    $this->addToProductImage($product_id, $name_image, $post["product_image_descr_".$i]);
                    $dispatcher->trigger('onAfterSaveProductImage', array($product_id, $name_image));
                }
            }else{
                if ($upload->getError() != 4){
                    JError::raiseWarning("", _JSHOP_ERROR_UPLOADING_IMAGE);
                    saveToLog("error.log", "SaveProduct - Error upload image. code: ".$upload->getError());
                }
            }
                        
            unset($upload);    
        }        
        
		for($i=0; $i<$jshopConfig->product_image_upload_count; $i++){
			if ($post['product_folder_image_'.$i] != '') {
				if (file_exists($jshopConfig->image_product_path .'/'.$post['product_folder_image_'.$i])) {
					$name_image = $post['product_folder_image_'.$i];
					$name_thumb = 'thumb_'.$name_image;
					$name_full = 'full_'.$name_image;
					$this->addToProductImage($product_id, $name_image, $post["product_image_descr_".$i]);
					$dispatcher->trigger('onAfterSaveProductFolerImage', array($product_id, $name_full, $name_image, $name_thumb));
				}
			}
		}
		
        if (!$product->image){
            $list_images = $product->getImages();
            if (count($list_images)){
                $product = JTable::getInstance('product', 'jshop');
                $product->load($product_id);
                $product->image = $list_images[0]->image_name;
                $product->store();    
            }
        }

        if (isset($post['old_image_descr'])){
            $this->renameProductImageOld($post['old_image_descr'], $post['old_image_ordering']);
        }
    }
    
    function addToProductImage($product_id, $name_image, $image_descr) {
        $image = JTable::getInstance('image', 'jshop');
        $image->set("image_id", 0);
        $image->set("product_id", $product_id);
        $image->set("image_name", $name_image);
        $image->set("name", $image_descr);
        $image->set("ordering", $image->getNextOrder("product_id='".intval($product_id)."'"));
        $image->store();
    }
    
    function renameProductImageOld($image_descr, $image_ordering){
        $db = JFactory::getDBO();
        foreach($image_descr as $id=>$v){
            $query = "update `#__jshopping_products_images` set `name`='".$db->escape($image_descr[$id])."', `ordering`='".$db->escape($image_ordering[$id])."' where `image_id`='".$db->escape($id)."'";
            $db->setQuery($query);
            $db->query();
        }
    }
    
    function uploadFiles($product, $product_id, $post){
        $jshopConfig = JSFactory::getConfig();
        $dispatcher = JDispatcher::getInstance();
        if (!isset($post['product_demo_descr'])) $post['product_demo_descr'] = '';
        if (!isset($post['product_file_descr'])) $post['product_file_descr'] = '';
        if (!isset($post['product_file_sort'])) $post['product_file_sort'] = '';
        
        for($i=0; $i<$jshopConfig->product_file_upload_count; $i++){
            $file_demo = "";
            $file_sale = "";
            if ($jshopConfig->product_file_upload_via_ftp!=1){
                $upload = new UploadFile($_FILES['product_demo_file_'.$i]);
                $upload->setDir($jshopConfig->demo_product_path);
                $upload->setFileNameMd5(0);
                $upload->setFilterName(1);
                if ($upload->upload()){
                    $file_demo = $upload->getName();
                    @chmod($jshopConfig->demo_product_path."/".$file_demo, 0777);
                }else{
                    if ($upload->getError() != 4){
                        JError::raiseWarning("", _JSHOP_ERROR_UPLOADING_FILE_DEMO);
                        saveToLog("error.log", "SaveProduct - Error upload demo. code: ".$upload->getError());    
                    }    
                }
                unset($upload);
                
                $upload = new UploadFile($_FILES['product_file_'.$i]);
                $upload->setDir($jshopConfig->files_product_path);
                $upload->setFileNameMd5(0);
                $upload->setFilterName(1);
                if ($upload->upload()){
                    $file_sale = $upload->getName();
                    @chmod($jshopConfig->files_product_path."/".$file_sale, 0777);
                }else{
                    if ($upload->getError() != 4){
                        JError::raiseWarning("", _JSHOP_ERROR_UPLOADING_FILE_SALE);
                        saveToLog("error.log", "SaveProduct - Error upload file sale. code: ".$upload->getError());    
                    }    
                }
                unset($upload);
            }
            
            if (!$file_demo && isset($post['product_demo_file_name_'.$i]) && $post['product_demo_file_name_'.$i]){
                $file_demo = $post['product_demo_file_name_'.$i];
            }
            if (!$file_sale && isset($post['product_file_name_'.$i]) && $post['product_file_name_'.$i]){
                $file_sale = $post['product_file_name_'.$i];
            }
            
            if ($file_demo!="" || $file_sale!=""){
                $this->addToProductFiles($product_id, $file_demo, $post['product_demo_descr_'.$i], $file_sale, $post['product_file_descr_'.$i], $post['product_file_sort_'.$i]);
            }
        }
        //Update description files
        $this->productUpdateDescriptionFiles($post['product_demo_descr'], $post['product_file_descr'], $post['product_file_sort']);
    }
    
    function addToProductFiles($product_id, $file_demo, $demo_descr, $file_sale, $file_descr, $sort){
        $db = JFactory::getDBO();
        $query = "INSERT INTO `#__jshopping_products_files` SET `product_id` = '".$db->escape($product_id)."', `demo` = '".$db->escape($file_demo)."',  `demo_descr` = '".$db->escape($demo_descr)."', `file` = '".$db->escape($file_sale)."', `file_descr` = '".$db->escape($file_descr)."', `ordering`='".$db->escape($sort)."'";
        $db->setQuery($query);
        $db->query();
    }
    
    function productUpdateDescriptionFiles($demo_descr, $file_descr, $ordering){
        $db = JFactory::getDBO();
        if (is_array($demo_descr)){
            foreach($demo_descr as $file_id=>$value){
                $query = "update `#__jshopping_products_files`SET 
                            `demo_descr` = '".$db->escape($demo_descr[$file_id])."', 
                            `file_descr` = '".$db->escape($file_descr[$file_id])."',
                            `ordering` = '".$db->escape($ordering[$file_id])."'
                            where id='".$db->escape($file_id)."'";
                $db->setQuery($query);
                $db->query();    
            }
        }
    }
    
    function saveAttributes($product, $product_id, $post){
        JPluginHelper::importPlugin('jshoppingadmin');
        $dispatcher = JDispatcher::getInstance();
        $productAttribut = JTable::getInstance('productAttribut', 'jshop');
        $productAttribut->set("product_id", $product_id);
        
        $list_exist_attr = $product->getAttributes();
        if (isset($post['product_attr_id'])){
            $list_saved_attr = $post['product_attr_id'];
        }else{
            $list_saved_attr = array();
        }        
        foreach($list_exist_attr as $v){
            if (!in_array($v->product_attr_id, $list_saved_attr)){
                $productAttribut->deleteAttribute($v->product_attr_id);
            }
        }
        
        if (is_array($post['attrib_price'])){
            foreach($post['attrib_price'] as $k=>$v){
                $a_price = saveAsPrice($post['attrib_price'][$k]);
                $a_old_price = saveAsPrice($post['attrib_old_price'][$k]);
                $a_buy_price = saveAsPrice($post['attrib_buy_price'][$k]);
                $a_count = $post['attr_count'][$k];
                $a_ean = $post['attr_ean'][$k];
                $a_weight_volume_units = $post['attr_weight_volume_units'][$k];
                $a_weight = $post['attr_weight'][$k];
                
                if ($post['product_attr_id'][$k]){
                    $productAttribut->load($post['product_attr_id'][$k]);
                }else{
                    $productAttribut->set("product_attr_id", 0);
                    $productAttribut->set("ext_attribute_product_id", 0);
                }
                $productAttribut->set("price", $a_price);
                $productAttribut->set("old_price", $a_old_price);
                $productAttribut->set("buy_price", $a_buy_price);
                $productAttribut->set("count", $a_count);
                $productAttribut->set("ean", $a_ean);
                $productAttribut->set("weight_volume_units", $a_weight_volume_units);
                $productAttribut->set("weight", $a_weight);
                foreach($post['attrib_id'] as $field_id=>$val){
                    $productAttribut->set("attr_".intval($field_id), $val[$k]);
                }
                $dispatcher->trigger('onBeforeProductAttributStore', array(&$productAttribut, &$product, &$product_id, &$post));
                if ($productAttribut->check()){
                    $productAttribut->store();
                }
            }
        }        
        
        $productAttribut2 = JTable::getInstance('productAttribut2', 'jshop');
        $productAttribut2->set("product_id", $product_id);
        $productAttribut2->deleteAttributeForProduct();
        
        if (is_array($post['attrib_ind_id'])){
            foreach($post['attrib_ind_id'] as $k=>$v){
                $a_id = intval($post['attrib_ind_id'][$k]);
                $a_value_id = intval($post['attrib_ind_value_id'][$k]);
                $a_price = saveAsPrice($post['attrib_ind_price'][$k]);
                $a_mod_price = $post['attrib_ind_price_mod'][$k];
                
                $productAttribut2->set("id", 0);
                $productAttribut2->set("product_id", $product_id);
                $productAttribut2->set("attr_id", $a_id);
                $productAttribut2->set("attr_value_id", $a_value_id);
                $productAttribut2->set("price_mod", $a_mod_price);
                $productAttribut2->set("addprice", $a_price);
                $dispatcher->trigger('onBeforeProductAttribut2Store', array(&$productAttribut2, &$product, &$product_id, &$post));
                if ($productAttribut2->check()){
                    $productAttribut2->store();
                }
            }
        }    
    }
    
    function saveRelationProducts($product, $product_id, $post){
        $db = JFactory::getDBO();
        
        if ($post['edit']) {
            $query = "DELETE FROM `#__jshopping_products_relations` WHERE `product_id` = '".$db->escape($product_id)."'";
            $db->setQuery($query);
            $db->query();
        }
        
        $post['related_products'] = array_unique($post['related_products']);
        foreach($post['related_products'] as $key => $value){
            if ($value!=0){
                $query = "INSERT INTO `#__jshopping_products_relations` SET `product_id` = '" . $db->escape($product_id) . "', `product_related_id` = '" . $db->escape($value) . "'";
                $db->setQuery($query);
                $db->query();
            }
        }
    }

    function getModPrice($price, $newprice, $mod){
        $result = 0;
        switch($mod){
            case '=':
            $result = $newprice;
            break;
            case '+':
            $result = $price + $newprice;
            break;
            case '-':
            $result = $price - $newprice;
            break;
            case '*':
            $result = $price * $newprice;
            break;
            case '/':
            $result = $price / $newprice;
            break;
            case '%':
            $result = $price * $newprice / 100;
            break;
        }
    return $result;
    }
    
    function updatePriceAndQtyDependAttr($product_id, $post){
        $db = JFactory::getDBO();
        $_adv_query = array();
        if ($post['product_price']!=""){
            $price = saveAsPrice($post['product_price']);
            if ($post['mod_price']=='%') 
                $_adv_query[] = " `price`=`price` * '".$price."' / 100 ";
            elseif($post['mod_price']=='=') 
                $_adv_query[] = " `price`= '".$price."' ";
            else 
                $_adv_query[] = " `price`=`price` ".$post['mod_price']." '".$price."' ";
        }
        
        if ($post['product_old_price']!=""){
            $price = saveAsPrice($post['product_old_price']);
            if ($post['mod_old_price']=='%') 
                $_adv_query[] = " `old_price`=`old_price` * '".$price."' / 100 ";
            elseif($post['mod_old_price']=='=') 
                $_adv_query[] = " `old_price`= '".$price."' ";
            else 
                $_adv_query[] = " `old_price`=`old_price` ".$post['mod_old_price']." '".$price."' ";
        }

        if ($post['product_quantity']!=""){
            $_adv_query[] = " `count`= '".$db->escape($post['product_quantity'])."' ";
        }
        
        if (count($_adv_query)>0){
            $adv_query = implode(" , ", $_adv_query);
            $query = "update `#__jshopping_products_attr` SET ".$adv_query." where product_id='".$db->escape($product_id)."'";
            $db->setQuery($query);
            $db->query();
        }
    }
    
}
?>