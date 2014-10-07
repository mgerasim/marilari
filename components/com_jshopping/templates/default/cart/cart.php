<?php 
/**
* @version      4.4.1 10.02.2014
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die('Restricted access');
?>
<?php $countprod = count($this->products);?>
<div class="jshop">
<form action="<?php print SEFLink('index.php?option=com_jshopping&controller=cart&task=refresh')?>" method="post" name="updateCart">
<?php print $this->_tmp_ext_html_cart_start?>
<?php if ($countprod>0){?>
<table class="jshop cart">
  <tr>
    <th width="20%">
      <?php print _JSHOP_IMAGE?>
    </th>
    <th>
      <?php print _JSHOP_ITEM?>
    </th>    
    <th width="15%">
      <?php print _JSHOP_SINGLEPRICE?>
    </th>
    <th width="15%">
      <?php print _JSHOP_NUMBER?>
    </th>
    <th width="15%">
      <?php print _JSHOP_PRICE_TOTAL?>
    </th>
    <th width="10%">
      <?php print _JSHOP_REMOVE?>
    </th>
  </tr>
  <?php 
  $i=1;   
  foreach($this->products as $key_id=>$prod){
  ?> 
  <tr class="jshop_prod_cart <?php if ($i%2==0) print "even"; else print "odd"?>">
    <td class="jshop_img_description_center">
      <a href="<?php print $prod['href']?>">
        <img src="<?php print $this->image_product_path ?>/<?php if ($prod['thumb_image']) print $prod['thumb_image']; else print $this->no_image; ?>" alt="<?php print htmlspecialchars($prod['product_name']);?>" class="jshop_img" />
      </a>
    </td>
    <td class="product_name">
        <a href="<?php print $prod['href']?>"><?php print $prod['product_name']?></a>
        <?php if ($this->config->show_product_code_in_cart){?>
        <span class="jshop_code_prod">(<?php print $prod['ean']?>)</span>
        <?php }?>
        <?php if ($prod['manufacturer']!=''){?>
        <div class="manufacturer"><?php print _JSHOP_MANUFACTURER?>: <span><?php print $prod['manufacturer']?></span></div>
        <?php }?>
        <?php print sprintAtributeInCart($prod['attributes_value']);?>
        <?php print sprintFreeAtributeInCart($prod['free_attributes_value']);?>
        <?php print sprintFreeExtraFiledsInCart($prod['extra_fields']);?>
        <?php print $prod['_ext_attribute_html']?>
    </td>
    <td>
        <?php print formatprice($prod['price'])?>
        <?php print $prod['_ext_price_html']?>
        <?php if ($this->config->show_tax_product_in_cart && $prod['tax']>0){?>
            <span class="taxinfo"><?php print productTaxInfo($prod['tax']);?></span>
        <?php }?>
		<?php if ($this->config->cart_basic_price_show && $prod['basicprice']>0){?>
            <div class="basic_price"><?php print _JSHOP_BASIC_PRICE?>: <span><?php print sprintBasicPrice($prod);?></span></div>
        <?php }?>
    </td>
    <td>
      <input type = "text" name = "quantity[<?php print $key_id ?>]" value = "<?php print $prod['quantity'] ?>" class = "inputbox" style = "width: 25px" />
      <?php print $prod['_qty_unit'];?>
      <span class = "cart_reload"><img style="cursor:pointer" src="<?php print $this->image_path ?>/images/reload.png" title="<?php print _JSHOP_UPDATE_CART ?>" alt = "<?php print _JSHOP_UPDATE_CART ?>" onclick="document.updateCart.submit();" /></span>
    </td>
    <td>
        <?php print formatprice($prod['price']*$prod['quantity']); ?>
        <?php print $prod['_ext_price_total_html']?>
        <?php if ($this->config->show_tax_product_in_cart && $prod['tax']>0){?>
            <span class="taxinfo"><?php print productTaxInfo($prod['tax']);?></span>
        <?php }?>
    </td>
    <td>
      <a href="<?php print $prod['href_delete']?>" onclick="return confirm('<?php print _JSHOP_CONFIRM_REMOVE?>')"><img src = "<?php print $this->image_path ?>images/remove.png" alt = "<?php print _JSHOP_DELETE?>" title = "<?php print _JSHOP_DELETE?>" /></a>
    </td>
  </tr>
  <?php 
  $i++;
  } 
  ?>
</table>
  
  <?php if ($this->config->show_weight_order){?>  
    <div class="weightorder">
        <?php print _JSHOP_WEIGHT_PRODUCTS?>: <span><?php print formatweight($this->weight);?></span>
    </div>
  <?php }?>
  
  <?php if ($this->config->summ_null_shipping>0){?>
    <div class="shippingfree">
        <?php printf(_JSHOP_FROM_PRICE_SHIPPING_FREE, formatprice($this->config->summ_null_shipping, null, 1));?>
    </div>
  <?php } ?>
  
  <div class="cartdescr"><?php print $this->cartdescr?></div>
  <br/>
<table class="jshop jshop_subtotal">
  <?php if (!$this->hide_subtotal){?>
  <tr>
    <td class="name">
      <?php print _JSHOP_SUBTOTAL ?>
    </td>
    <td class="value">
      <?php print formatprice($this->summ);?><?php print $this->_tmp_ext_subtotal?>
    </td>
  </tr>
  <?php } ?>
  <?php if ($this->discount > 0){ ?>
  <tr>
    <td class="name">
      <?php print _JSHOP_RABATT_VALUE ?><?php print $this->_tmp_ext_discount_text?>
    </td>
    <td class="value">
      <?php print formatprice(-$this->discount);?><?php print $this->_tmp_ext_discount?>
    </td>
  </tr>
  <?php } ?>
  <?php if (!$this->config->hide_tax){?>
  <?php foreach($this->tax_list as $percent=>$value){ ?>
  <tr>
    <td class = "name">
      <?php print displayTotalCartTaxName();?>
      <?php if ($this->show_percent_tax) print formattax($percent)."%"?>
    </td>
    <td class = "value">
      <?php print formatprice($value);?><?php print $this->_tmp_ext_tax[$percent]?>
    </td>
  </tr>
  <?php } ?>
  <?php } ?>
  <tr class="total">
    <td class = "name">
      <?php print _JSHOP_PRICE_TOTAL ?>
    </td>
    <td class = "value">
      <?php print formatprice($this->fullsumm)?><?php print $this->_tmp_ext_total?>
    </td>
  </tr>
  <?php if ($this->config->show_plus_shipping_in_product){?>  
  <tr>
    <td colspan="2" align="right">    
        <span class="plusshippinginfo"><?php print sprintf(_JSHOP_PLUS_SHIPPING, $this->shippinginfo);?></span>  
    </td>
  </tr>
  <?php }?>
  <?php if ($this->free_discount > 0){?>  
  <tr>
    <td colspan="2" align="right">    
        <span class="free_discount"><?php print _JSHOP_FREE_DISCOUNT;?>: <?php print formatprice($this->free_discount); ?></span>  
    </td>
  </tr>
  <?php }?>  
</table>
<?php }else{?>
<div class="cart_empty_text"><?php print _JSHOP_CART_EMPTY?></div>
<?php }?>

<table class="jshop" style="margin-top:10px">
  <tr id = "checkout">
    <td width = "50%" class = "td_1">
       <a href = "<?php print $this->href_shop ?>">
         <img src = "<?php print $this->image_path ?>/images/arrow_left.gif" alt="<?php print _JSHOP_BACK_TO_SHOP ?>" />
         <?php print _JSHOP_BACK_TO_SHOP ?>
       </a>
    </td>
    <td width = "50%" class = "td_2">
    <?php if ($countprod>0){?>
       <a href = "<?php print $this->href_checkout ?>">
         <?php print _JSHOP_CHECKOUT ?>
         <img src = "<?php print $this->image_path ?>/images/arrow_right.gif" alt="<?php print _JSHOP_CHECKOUT ?>" />
       </a>
    <?php }?>
    </td>
  </tr>
</table>
</form>

<?php print $this->_tmp_ext_html_before_discount?>
<?php if ($this->use_rabatt && $countprod>0){ ?>
<br /><br />
<form name="rabatt" method="post" action="<?php print SEFLink('index.php?option=com_jshopping&controller=cart&task=discountsave')?>">
<table class="jshop">
  <tr>
    <td>
      <?php print _JSHOP_RABATT ?>
      <input type = "text" class = "inputbox" name = "rabatt" value = "" />
      <input type = "submit" class = "button" value = "<?php print _JSHOP_RABATT_ACTIVE ?>" />
    </td>
  </tr>
</table>
</form>
<?php }?>
</div>