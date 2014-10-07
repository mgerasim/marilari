<?php 
/**
* @version      4.3.1 13.08.2013
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die('Restricted access');
?>
<?php print $this->checkout_navigator?>
<?php print $this->small_cart?>

<div class="jshop">
<form id = "shipping_form" name = "shipping_form" action = "<?php print $this->action ?>" method = "post" onsubmit = "return validateShippingMethods()">
<?php print $this->_tmp_ext_html_shipping_start?>
<table id = "table_shippings" cellspacing="0" cellpadding="0">
<?php foreach($this->shipping_methods as $shipping){?>
  <tr>
    <td style = "padding-top:5px; padding-bottom:5px">
      <input type = "radio" name = "sh_pr_method_id" id = "shipping_method_<?php print $shipping->sh_pr_method_id?>" value="<?php print $shipping->sh_pr_method_id ?>" <?php if ($shipping->sh_pr_method_id==$this->active_shipping){ ?>checked = "checked"<?php } ?> />
      <label for = "shipping_method_<?php print $shipping->sh_pr_method_id ?>"><?php
      if ($shipping->image){
        ?><span class="shipping_image"><img src="<?php print $shipping->image?>" alt="<?php print htmlspecialchars($shipping->name)?>" /></span><?php
      }
      ?><?php print $shipping->name?> (<?php print formatprice($shipping->calculeprice); ?>)</label>
      <?php if ($this->config->show_list_price_shipping_weight && count($shipping->shipping_price)){ ?>
          <br />
          <table class="shipping_weight_to_price">
          <?php foreach($shipping->shipping_price as $price){?>
              <tr>
                <td class="weight">
                    <?php if ($price->shipping_weight_to!=0){?>
                        <?php print formatweight($price->shipping_weight_from);?> - <?php print formatweight($price->shipping_weight_to);?>
                    <?php }else{ ?>
                        <?php print _JSHOP_FROM." ".formatweight($price->shipping_weight_from);?>
                    <?php } ?>
                </td>
                <td class="price">
                    <?php print formatprice($price->shipping_price); ?>
                </td>
            </tr>
          <?php } ?>
          </table>
      <?php } ?>
      <div class="shipping_descr"><?php print $shipping->description?></div>
      <?php if ($shipping->delivery){?>
      <div class="shipping_delivery"><?php print _JSHOP_DELIVERY_TIME.": ".$shipping->delivery?></div>
      <?php }?>
      <?php if ($shipping->delivery_date_f){?>
      <div class="shipping_delivery_date"><?php print _JSHOP_DELIVERY_DATE.": ".$shipping->delivery_date_f?></div>
      <?php }?>      
      </td>
  </tr>
<?php } ?>
</table>
<br/>
<?php print $this->_tmp_ext_html_shipping_end?>
<input type = "submit" class = "button" value = "<?php print _JSHOP_NEXT ?>" />
</form>
</div>