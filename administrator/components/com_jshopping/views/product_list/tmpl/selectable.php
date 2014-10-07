<?php
/**
* @version      4.3.1 13.08.2013
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die('Restricted access');

$rows=$this->rows;
$lists=$this->lists;
$pageNav=$this->pagination;
$text_search=$this->text_search;
$category_id=$this->category_id;
$manufacturer_id=$this->manufacturer_id;
$count=count($rows);
$eName=$this->eName;
$jsfname = $this->jsfname;
$i=0;
?>
<form action="index.php?option=com_jshopping&controller=product_list_selectable&tmpl=component" method="post" name="adminForm" id="adminForm">

<div class="jshop_block_filter">  
    <span class="jshop_box_filter"><?php echo _JSHOP_CATEGORY.": ".$lists['treecategories'];?></span>
    <span class="jshop_box_filter"><?php echo _JSHOP_NAME_MANUFACTURER.": ".$lists['manufacturers'];?></span>
    <?php if ($this->config->admin_show_product_labels){?>
        <span class="jshop_box_filter"><?php echo _JSHOP_LABEL.": ".$lists['labels']?></span>
    <?php }?>
    <span class="jshop_box_filter"><?php echo _JSHOP_SHOW.": ".$lists['publish'];?></span>
    <input type="text" name="text_search" value="<?php echo htmlspecialchars($text_search);?>" />
    <input type="submit" class="button" value="<?php echo _JSHOP_SEARCH;?>" />
</div>

<table class="table table-striped" >
<thead> 
  <tr>
	<th class="title" width ="10">
	  #
	</th>
	<th width="93">
		<?php print _JSHOP_IMAGE; ?>
	</th>
	<th>
	  <?php echo _JSHOP_TITLE; ?>
	</th>
	<?php if (!$category_id){?>
	<th width="80">
	  <?php echo _JSHOP_CATEGORY;?>
	</th>
	<?php }?>
	<?php if (!$manufacturer_id){?>
	<th width="80">
	  <?php echo _JSHOP_MANUFACTURER;?>
	</th>
	<?php }?>
	<th width="60">
		<?php echo _JSHOP_PRICE;?>
	</th>
	<th width="60">
		<?php echo _JSHOP_DATE;?>
	</th>
	<th width="40">
	  <?php echo _JSHOP_PUBLISH;?>
	</th>
	<th width="30">
	  <?php echo _JSHOP_ID;?>
	</th>
  </tr>
</thead> 
<?php foreach ($rows as $row){?>
  <tr class="row<?php echo $i % 2;?>">
   <td>
	 <?php echo $pageNav->getRowOffset($i);?>
   </td>
   <td>
	<?php if ($row->image){?>
		<a href="#" onclick="window.parent.<?php print $jsfname?>(<?php echo $row->product_id; ?>, '<?php echo $eName; ?>')">
			<img src="<?php print getPatchProductImage($row->image, 'thumb', 1)?>" width="90" border="0" />
		</a>
	<?php }?>
   </td>
   <td>
     <b><a href="#" onclick="window.parent.<?php print $jsfname?>(<?php echo $row->product_id; ?>, '<?php echo $eName; ?>')"><?php echo $row->name;?></a></b>
	 <br/><?php echo $row->short_description;?>
   </td>
   <?php if (!$category_id){?>
   <td>
	  <?php echo $row->namescats;?>
   </td>
   <?php }?>
   <?php if (!$manufacturer_id){?>
   <td>
	  <?php echo $row->man_name;?>
   </td>
   <?php }?>
   <td>		
    <?php echo formatprice($row->product_price, sprintCurrency($row->currency_id));?>
   </td>
   <td>
	<?php echo $row->product_date_added;?>
   </td>
   <td align="center">
	 <?php echo $published=($row->product_publish) ? ('<img title="'._JSHOP_PUBLISH.'" border="0" alt="" src="components/com_jshopping/images/tick.png">') : ('<img title="'._JSHOP_UNPUBLISH.'" border="0" alt="" src="components/com_jshopping/images/publish_x.png">');?>
   </td>
   <td align="center">
	 <?php echo $row->product_id; ?>
   </td>
  </tr>
 <?php
 $i++;
 }
 ?>
 <tfoot>
 <tr>
	<td colspan="17">
		<div class = "jshop_list_footer"><?php echo $pageNav->getListFooter(); ?></div>
        <div class = "jshop_limit_box"><?php echo $pageNav->getLimitBox(); ?></div>
	</td>
 </tr>
 </tfoot>   
</table>
<input type="hidden" name="task" value="" />
<input type="hidden" name="hidemainmenu" value="0" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="e_name" value="<?php print $eName?>" />
<input type="hidden" name="jsfname" value="<?php print $jsfname?>" />
</form>