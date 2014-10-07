<?php
/**
* @version      4.5.0 05.11.2013
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die('Restricted access');

class pm_paypal extends PaymentRoot{
    
    function showPaymentForm($params, $pmconfigs){
        include(dirname(__FILE__)."/paymentform.php");
    }

	//function call in admin
	function showAdminFormParams($params){
	  $array_params = array('testmode', 'email_received', 'transaction_end_status', 'transaction_pending_status', 'transaction_failed_status');
	  foreach ($array_params as $key){
	  	if (!isset($params[$key])) $params[$key] = '';
	  }
      if (!isset($params['use_ssl'])) $params['use_ssl'] = 0;
      if (!isset($params['address_override'])) $params['address_override'] = 0; 
	  $orders = JModelLegacy::getInstance('orders', 'JshoppingModel'); //admin model
      include(dirname(__FILE__)."/adminparamsform.php");	  
	}

	function checkTransaction($pmconfigs, $order, $act){
        $jshopConfig = JSFactory::getConfig();
        
        if ($pmconfigs['testmode']){
            $host = "www.sandbox.paypal.com";
        } else{
            $host = "www.paypal.com";
        }
        $hostname = $host;
        
        if ($pmconfigs['use_ssl']){
            $host = "ssl://".$host;
        }
		
		$order->order_total = $this->fixOrderTotal($order);
		$email_received = $_POST['business'];
		if ($email_received=="") $email_received = $_POST['receiver_email'];

        $opending = 0;
        if ($order->order_total != $_POST['mc_gross']){
            $opending = 1;
        }
        if (strtolower($pmconfigs['email_received']) != strtolower($email_received)){
            return array(0, 'Error email received. Order ID '.$order->order_id);
        }
        if ($order->currency_code_iso != $_POST['mc_currency']){
            $opending = 1;
        }
        
        $req = 'cmd=_notify-validate';
        foreach($_POST as $key=>$value){
            $value = urlencode(stripslashes($value));
            $req .= "&$key=$value";
        }
        $payment_status = trim(stripslashes($_POST['payment_status']));
        
        $header = '';
        $header .= "POST /cgi-bin/webscr HTTP/1.1\r\n";
        $header .= "Content-Type: application/x-www-form-urlencoded\r\n";
        $header .= "Host: ".$hostname."\r\n";
		$header .= "Connection: close\r\n";
        $header .= "Content-Length: ".strlen($req)."\r\n\r\n";
        $debug = "";

        $port = 80;
        if ($pmconfigs['use_ssl']) $port = 443;
        $fp = fsockopen ($host, $port, $errno, $errstr, 30);
        if (!$fp) {
            return array(0, 'Http error. Order number '.$order->order_id);
        } else {
            @fputs ($fp, $header . $req);
            while (!@feof($fp)) {
                $res = @fgets($fp, 1024);
				$res = trim($res);
                $debug .= $res."\n";
                if (strcmp ($res, "VERIFIED") == 0) {
                    if ($payment_status == 'Completed'){
                        if ($opending){
                            saveToLog("payment.log", "Status pending. Order ID ".$order->order_id.". Error mc_gross or mc_currency.");
                            return array(2, "Status pending. Order ID ".$order->order_id);
                        }else{
                            return array(1, '');
                        }
                    } elseif ($payment_status == 'Pending') {
                        saveToLog("payment.log", "Status pending. Order ID ".$order->order_id.". Reason: ".$_POST['pending_reason']);
                        return array(2, trim(stripslashes($_POST['pending_reason'])) );
                    } elseif ($payment_status == 'Failed') {
                        return array(3, 'Status Failed. Order ID '.$order->order_id );
                    } elseif ($payment_status == 'Refunded') {
                        return array(3, "Status Refunded. Order ID ".$order->order_id );
                    }else {
                        return array(0, "Order number ".$order->order_id."\nPaypal error\nPayment status - $payment_status.");
                    }                    
                } else if (strcmp ($res, "INVALID") == 0) {
                    return array(0, 'Invalid response. Order ID '.$order->order_id);
                }
            }
            fclose ($fp);
            if ($jshopConfig->savelog && $jshopConfig->savelogpaymentdata){
                saveToLog("paymentdata.log", $debug);
            }
			return array(0, "Error response. Order ID ".$order->order_id);    
        }
        
	}

	function showEndForm($pmconfigs, $order){
        $jshopConfig = JSFactory::getConfig();	    
        $item_name = sprintf(_JSHOP_PAYMENT_NUMBER, $order->order_number);
        
        if ($pmconfigs['testmode']){
            $host = "www.sandbox.paypal.com";
        } else{
            $host = "www.paypal.com";
        }        
        $email = $pmconfigs['email_received'];
        $address_override = (int)$pmconfigs['address_override'];
        
        $uri = JURI::getInstance();        
        $liveurlhost = $uri->toString(array("scheme",'host', 'port'));

		if ($pmconfigs['notifyurlsef']){
            $notify_url = $liveurlhost.SEFLink("index.php?option=com_jshopping&controller=checkout&task=step7&act=notify&js_paymentclass=pm_paypal&no_lang=1");
        }else{
            $notify_url = JURI::root()."index.php?option=com_jshopping&controller=checkout&task=step7&act=notify&js_paymentclass=pm_paypal&no_lang=1";
        }
        $return = $liveurlhost.SEFLink("index.php?option=com_jshopping&controller=checkout&task=step7&act=return&js_paymentclass=pm_paypal");
        $cancel_return = $liveurlhost.SEFLink("index.php?option=com_jshopping&controller=checkout&task=step7&act=cancel&js_paymentclass=pm_paypal");
		
        $_country = JTable::getInstance('country', 'jshop');
        $_country->load($order->d_country);
        $country = $_country->country_code_2;
        $order->order_total = $this->fixOrderTotal($order);
        ?>
        <html>
        <head>
            <meta http-equiv="content-type" content="text/html; charset=utf-8" />            
        </head>
        <body>
        <form id="paymentform" action="https://<?php print $host?>/cgi-bin/webscr" name = "paymentform" method = "post">
        <input type='hidden' name='cmd' value='_xclick'>
        <input type='hidden' name='business' value='<?php print $email?>'>        
        <input type='hidden' name='notify_url' value='<?php print $notify_url?>'>
        <input type='hidden' name='return' value='<?php print $return?>'>
        <input type='hidden' name='cancel_return' value='<?php print $cancel_return?>'>
        <input type='hidden' name='rm' value='2'>
        <input type='hidden' name='handling' value='0.00'>
        <input type='hidden' name='tax' value='0.00'>        
        <input type='hidden' name='charset' value='utf-8'>
        <input type='hidden' name='no_shipping' value='1'>
        <input type='hidden' name='no_note' value='1'>
        <input type='hidden' name='item_name' value='<?php print $item_name;?>'>
        <input type='hidden' name='custom' value='<?php print $order->order_id?>'>
        <input type='hidden' name='amount' value='<?php print $order->order_total?>'>
        <input type='hidden' name='currency_code' value='<?php print $order->currency_code_iso?>'>
        <input type='hidden' name='address_override' value='<?php print $address_override?>'>
        <input type='hidden' name='country' value='<?php print $country?>'>
        <input type='hidden' name='first_name' value='<?php print $order->d_f_name?>'>
        <input type='hidden' name='last_name' value='<?php print $order->d_l_name?>'>  
        <input type='hidden' name='address1' value='<?php print $order->d_street?>'>  
        <input type='hidden' name='city' value='<?php print $order->d_city?>'>  
        <input type='hidden' name='state' value='<?php print $order->d_state?>'>
        <input type='hidden' name='zip' value='<?php print $order->d_zip?>'>
        <input type='hidden' name='night_phone_b' value='<?php print $order->d_phone?>'>
        <input type='hidden' name='email' value='<?php print $order->email?>'>
        </form>        
        <?php print _JSHOP_REDIRECT_TO_PAYMENT_PAGE?>
        <br>
        <script type="text/javascript">document.getElementById('paymentform').submit();</script>
        </body>
        </html>
        <?php
        die();
	}
    
    function getUrlParams($pmconfigs){
        $params = array(); 
        $params['order_id'] = JRequest::getInt("custom");
        $params['hash'] = "";
        $params['checkHash'] = 0;
        $params['checkReturnParams'] = $pmconfigs['checkdatareturn'];
    return $params;
    }
    
	function fixOrderTotal($order){
        $total = $order->order_total;
        if ($order->currency_code_iso=='HUF'){
            $total = round($total);
        }else{
            $total = number_format($total, 2, '.', '');
        }
    return $total;
    }
}
?>