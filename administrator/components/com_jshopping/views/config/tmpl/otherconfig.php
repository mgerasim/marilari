<?php 
/**
* @version      4.3.1 13.08.2013
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die('Restricted access');
JHTML::_('behavior.tooltip');
$lists=$this->lists;
$jshopConfig=$this->config;
include(dirname(__FILE__)."/submenu.php");
?>
<form action="index.php?option=com_jshopping&controller=config" method="post" name="adminForm" id="adminForm">
<input type="hidden" name="task" value="">
<input type="hidden" name="tab" value="10">
<div class="col100">
<fieldset class="adminform">
<legend><?php echo _JSHOP_OC;?></legend>
<table class="admintable">
<tr>
    <td class="key">
        <?php echo _JSHOP_EXTENDED_TAX_RULE_FOR?>
    </td>
    <td>
        <?php print $lists['tax_rule_for'];?>
    </td>
</tr>
<tr>
    <td class="key">
        <?php echo _JSHOP_SAVE_ALIAS_AUTOMATICAL?>
    </td>
    <td>
        <input type="hidden" name="create_alias_product_category_auto" value="0">
        <input type="checkbox" name="create_alias_product_category_auto" value="1" <?php if ($jshopConfig->create_alias_product_category_auto) echo 'checked="checked"';?> />
    </td>
</tr>
<?php foreach($this->other_config as $k){?>
<tr>
	<td class="key">
		<?php if (defined("_JSHOP_OC_".$k)) print constant("_JSHOP_OC_".$k); else print $k;?>
	</td>
	<td>
        <?php if (in_array($k, $this->other_config_checkbox)){?>
        <input type="hidden" name="<?php print $k?>" value="0">
        <input type="checkbox" name="<?php print $k?>" value="1" <?php if ($jshopConfig->$k==1) print 'checked'?>>
        <?php }else{?>
		<input type="text" name="<?php print $k?>" value="<?php echo $jshopConfig->$k?>">
        <?php }?>
		<?php if (defined("_JSHOP_OC_".$k."_INFO")) echo JHTML::tooltip(constant("_JSHOP_OC_".$k."_INFO"));?>
	</td>
</tr>
<?php } ?>
<?php $pkey="etemplatevar";if ($this->$pkey){print $this->$pkey;}?>
</table>
</fieldset>
</div>
<div class="clr"></div>
</form>