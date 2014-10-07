<?php 
/**
* @version      4.6.0 31.05.2014
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
<?php print $this->_tmp_ext_html_previewfinish_start?>
<table class="jshop">
  <tr>
    <td>
       <strong><?php print _JSHOP_BILL_ADDRESS?></strong>: 
       <?php if ($this->invoice_info['firma_name']) print $this->invoice_info['firma_name'].", ";?> 
       <?php print $this->invoice_info['f_name'] ?> 
       <?php print $this->invoice_info['l_name'] ?>, 
       <?php if ($this->invoice_info['street'] && $this->invoice_info['street_nr']) print $this->invoice_info['street']." ".$this->invoice_info['street_nr'].","?>
       <?php if ($this->invoice_info['street'] && !$this->invoice_info['street_nr']) print $this->invoice_info['street'].","?>
       <?php if ($this->invoice_info['home'] && $this->invoice_info['apartment']) print $this->invoice_info['home']."/".$this->invoice_info['apartment'].","?>
       <?php if ($this->invoice_info['home'] && !$this->invoice_info['apartment']) print $this->invoice_info['home'].","?>
       <?php if ($this->invoice_info['state']) print $this->invoice_info['state']."," ?> 
       <?php print $this->invoice_info['zip']." ".$this->invoice_info['city']." ".$this->invoice_info['country']?>
    </td>
  </tr>
<?php if ($this->count_filed_delivery){?>
  <tr>
    <td>
       <strong><?php print _JSHOP_FINISH_DELIVERY_ADRESS?></strong>: 
       <?php if ($this->delivery_info['firma_name']) print $this->delivery_info['firma_name'].", ";?> 
       <?php print $this->delivery_info['f_name'] ?> 
       <?php print $this->delivery_info['l_name'] ?>, 
       <?php if ($this->delivery_info['street'] && $this->delivery_info['street_nr']) print $this->delivery_info['street']." ".$this->delivery_info['street_nr'].","?>
       <?php if ($this->delivery_info['street'] && !$this->delivery_info['street_nr']) print $this->delivery_info['street'].","?>
       <?php if ($this->delivery_info['home'] && $this->delivery_info['apartment']) print $this->delivery_info['home']."/".$this->delivery_info['apartment'].","?>
       <?php if ($this->delivery_info['home'] && !$this->delivery_info['apartment']) print $this->delivery_info['home'].","?>
       <?php if ($this->delivery_info['state']) print $this->delivery_info['state']."," ?> 
       <?php print $this->delivery_info['zip']." ".$this->delivery_info['city']." ".$this->delivery_info['country']?>
    </td>
  </tr>
<?php }?>
<?php if (!$this->config->without_shipping){?>  
  <tr>
    <td>
       <strong><?php print _JSHOP_FINISH_SHIPPING_METHOD?></strong>: <?php print $this->sh_method->name?>
       <?php if ($this->delivery_time){?>
       <div class="delivery_time"><strong><?php print _JSHOP_DELIVERY_TIME?></strong>: <?php print $this->delivery_time?></div>
       <?php }?>
       <?php if ($this->delivery_date){?>
       <div class="delivery_date"><strong><?php print _JSHOP_DELIVERY_DATE?></strong>: <?php print $this->delivery_date?></div>
       <?php }?>
    </td>
  </tr>
<?php } ?>
<?php if (!$this->config->without_payment){?>  
  <tr>
    <td>
       <strong><?php print _JSHOP_FINISH_PAYMENT_METHOD ?></strong>: <?php print $this->payment_name ?>
    </td>
  </tr>
<?php } ?> 
</table>
<br />
<br />

<form name = "form_finish" action = "<?php print $this->action ?>" method = "post">
   <table class = "jshop" align="center" style="width:auto;margin-left:auto;margin-right:auto;">
     <tr>
       <td>
		   <?php print _JSHOP_ADD_INFO ?><br />
		   <textarea class = "inputbox" id = "order_add_info" name = "order_add_info"></textarea>
       </td>       
     </tr>
     <?php if ($this->config->display_agb){?>
     <tr>
        <td>
            <div class="row_agb">            
            <input type = "checkbox" name="agb" id="agb" />
            <a class = "policy" href="#" onclick="window.open('<?php print SEFLink('index.php?option=com_jshopping&controller=content&task=view&page=agb&tmpl=component', 1);?>','window','width=800, height=600, scrollbars=yes, status=no, toolbar=no, menubar=no, resizable=yes, location=no');return false;"><?php print _JSHOP_AGB;?></a>
            <?php print _JSHOP_AND;?>
            <a class = "policy" href="#" onclick="window.open('<?php print SEFLink('index.php?option=com_jshopping&controller=content&task=view&page=return_policy&tmpl=component&cart=1', 1);?>','window','width=800, height=600, scrollbars=yes, status=no, toolbar=no, menubar=no, resizable=yes, location=no');return false;"><?php print _JSHOP_RETURN_POLICY?></a>
            <?php print _JSHOP_CONFIRM;?>        
            </div>
        </td>
     </tr>
     <?php }?>
     <?php if($this->no_return){?>
     <tr>
        <td>
            <div class="row_no_return">            
            <input type = "checkbox" name="no_return" id="no_return" />        
            <?php print _JSHOP_NO_RETURN_DESCRIPTION;?>     
            </div>
        </td>
     </tr>     
     <?php }?>
     <?php print $this->_tmp_ext_html_previewfinish_agb?>
     <tr>
       <td style="text-align:center;padding-top:3px;">		   
           <input class="button" type="submit" name="finish_registration" value="<?php print _JSHOP_ORDER_FINISH?>" onclick="return checkAGBAndNoReturn('<?php echo $this->config->display_agb;?>','<?php echo $this->no_return?>');" />
       </td>
     </tr>
   </table>
<?php print $this->_tmp_ext_html_previewfinish_end?>
</form>
</div>