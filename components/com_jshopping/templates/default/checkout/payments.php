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

<script type="text/javascript">
var payment_type_check = {};
<?php foreach($this->payment_methods as  $payment){?>
    payment_type_check['<?php print $payment->payment_class;?>'] = '<?php print $payment->existentcheckform;?>';
<?php }?>
</script>

<div class="jshop">
<form id = "payment_form" name = "payment_form" action = "<?php print $this->action ?>" method = "post">
<?php print $this->_tmp_ext_html_payment_start?>
<table id = "table_payments" cellspacing="0" cellpadding="0">
  <?php 
  $payment_class = "";
  foreach($this->payment_methods as  $payment){
  if ($this->active_payment==$payment->payment_id) $payment_class = $payment->payment_class;
  ?>
  <tr>
    <td style = "padding-top:5px; padding-bottom:5px">
      <input type = "radio" name = "payment_method" id = "payment_method_<?php print $payment->payment_id ?>" onclick = "showPaymentForm('<?php print $payment->payment_class ?>')" value = "<?php print $payment->payment_class ?>" <?php if ($this->active_payment==$payment->payment_id){?>checked<?php } ?> />
      <label for = "payment_method_<?php print $payment->payment_id ?>"><?php
      if ($payment->image){
        ?><span class="payment_image"><img src="<?php print $payment->image?>" alt="<?php print htmlspecialchars($payment->name)?>" /></span><?php
      }
      ?><b><?php print $payment->name;?></b> 
        <?php if ($payment->price_add_text!=''){?>
            (<?php print $payment->price_add_text?>)
        <?php }?>
      </label>
    </td>
  </tr>
  <tr id = "tr_payment_<?php print $payment->payment_class ?>" <?php if ($this->active_payment != $payment->payment_id){?>style = "display:none"<?php } ?>>
    <td class = "jshop_payment_method">
        <?php print $payment->payment_description?>
        <?php print $payment->form?>
    </td>
  </tr>
  <?php } ?>
</table>
<br />
<?php print $this->_tmp_ext_html_payment_end?>
<input type = "button" id = "payment_submit" class = "button" name = "payment_submit" value = "<?php print _JSHOP_NEXT ?>" onclick="checkPaymentForm();" />
</form>
</div>

<?php if ($payment_class){ ?>
<script type="text/javascript">
    showPaymentForm('<?php print $payment_class;?>');
</script>
<?php } ?>