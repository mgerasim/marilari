<?php
/**
* @version      4.3.0 13.06.2013
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2012 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/

defined('_JEXEC') or die('Restricted access');
jimport( 'joomla.application.component.model');

class JshoppingModelInfo extends JModelLegacy{

	function _remote_file_exists($url){
		return (bool)preg_match('~HTTP/1\.\d\s+200\s+OK~', @current(get_headers($url)));
	}

    function getUpdateObj($version, $jshopConfig) {
		$result = new stdclass;
		$xml = null;
		$str = file_get_content_curl($jshopConfig->xml_update_path);        
        if ($str){
            $xml = simplexml_load_string($str);
        }elseif (self::_remote_file_exists($jshopConfig->xml_update_path)){
            $xml = simplexml_load_file($jshopConfig->xml_update_path);
        }
        if ($xml){
            if (count($xml->update)) {
                foreach($xml->update as $v){
                    if (((string)$v['version'] == $version) && ((string)$v['newversion'])) {
                        $result->text = sprintf(_JSHOP_UPDATE_ARE_AVAILABLE, (string)$v['newversion']);
                    }
                }
                if ($result->text) $result->link = $jshopConfig->updates_site_path;
            }
        }
		return $result;
	}
}
?>