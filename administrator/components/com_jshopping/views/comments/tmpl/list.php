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
<?php
	displaySubmenuOptions();
	$count=count ($this->reviews);
	$i=0;
?>
<form action="index.php?option=com_jshopping&controller=reviews" method="post" name="adminForm" id="adminForm">
<div class="jshop_block_filter">  
    <span class="jshop_box_filter"><?php echo $this->categories;?></span>
    <span class="jshop_box_filter"><?php echo $this->products_select;?></span>  
    <span class="jshop_box_filter"><input type="text" name="text_search" value="<?php echo $this->text_search;?>" /></span>
    <span class="jshop_box_filter"><input type="submit" class="button" value="<?php echo _JSHOP_SEARCH;?>" /></span>
</div>

<table class="table table-striped" >
<thead> 
  <tr>
    <th class="title" width ="10">
      #
    </th>
    <th width="20">
      <input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
    </th>
    <th width = "200" align = "left">
        <?php echo JHTML::_('grid.sort', _JSHOP_NAME_PRODUCT, 'name', $this->filter_order_Dir, $this->filter_order); ?>
    </th>
    <th>
        <?php echo JHTML::_('grid.sort', _JSHOP_USER, 'pr_rew.user_name', $this->filter_order_Dir, $this->filter_order); ?>
    </th>        
    <th>
        <?php echo JHTML::_('grid.sort', _JSHOP_EMAIL, 'pr_rew.user_email', $this->filter_order_Dir, $this->filter_order); ?>
    </th>
    <th align = "left">
        <?php echo JHTML::_('grid.sort', _JSHOP_PRODUCT_REVIEW, 'pr_rew.review', $this->filter_order_Dir, $this->filter_order); ?>
    </th>
    <th>
        <?php echo JHTML::_('grid.sort', _JSHOP_REVIEW_MARK, 'pr_rew.mark', $this->filter_order_Dir, $this->filter_order); ?>
    </th> 
    <th>
        <?php echo JHTML::_('grid.sort', _JSHOP_DATE, 'pr_rew.time', $this->filter_order_Dir, $this->filter_order); ?> 
    </th>
    <th>
        <?php echo JHTML::_('grid.sort', 'IP', 'pr_rew.ip', $this->filter_order_Dir, $this->filter_order); ?>
    </th>
    <th width="50">
        <?php echo _JSHOP_PUBLISH;?>       
    </th>
    <th width="50">
        <?php echo _JSHOP_EDIT; ?>
    </th>
    <th width="50">
        <?php echo _JSHOP_DELETE; ?>
    </th>
    <th width = "40">
        <?php echo JHTML::_('grid.sort', _JSHOP_ID, 'pr_rew.review_id', $this->filter_order_Dir, $this->filter_order); ?>
    </th>
  </tr>
</thead> 
<?php foreach ($this->reviews as $row){?>
<tr class="row<?php echo $i % 2;?>">
   <td>
     <?php echo $this->pagination->getRowOffset($i);?>             
   </td>
   <td>         
     <?php echo JHtml::_('grid.id', $i, $row->review_id);?>
   </td>
   <td>
     <?php echo $row->name;?>
   </td>
   <td>
     <?php echo $row->user_name;?>
   </td> 
   <td>
     <?php echo $row->user_email;?>
   </td>     
   <td>
     <?php echo $row->review;?>
   </td> 
   <td>
     <?php echo $row->mark;?>
   </td> 
   <td>
     <?php echo $row->dateadd;?>
   </td>
   <td>
     <?php echo $row->ip;?>
   </td>
   <td align="center">
     <?php echo $published=($row->publish) ? ('<a href="javascript:void(0)" onclick="return listItemTask(\'cb' . $i . '\', \'unpublish\')"><img src="components/com_jshopping/images/tick.png" title="'._JSHOP_PUBLISH.'" ></a>') : ('<a href="javascript:void(0)" onclick="return listItemTask(\'cb' . $i . '\', \'publish\')"><img title="'._JSHOP_UNPUBLISH.'" src="components/com_jshopping/images/publish_x.png"></a>');?>
   </td> 
   <td align="center">
    <a href='index.php?option=com_jshopping&controller=reviews&task=edit&cid[]=<?php print $row->review_id?>'><img src='components/com_jshopping/images/icon-16-edit.png'></a>
   </td>
   <td align="center">
    <a href='index.php?option=com_jshopping&controller=reviews&task=remove&cid[]=<?php print $row->review_id?>' onclick="return confirm('<?php print _JSHOP_DELETE?>')"><img src='components/com_jshopping/images/publish_r.png'></a>
   </td>
   <td align="center">
    <?php print $row->review_id;?>
   </td>
</tr>
<?php
$i++;
}
?>
 <tfoot>
 <tr>
    <td colspan="13">
		<div class = "jshop_list_footer"><?php echo $this->pagination->getListFooter(); ?></div>
        <div class = "jshop_limit_box"><?php echo $this->pagination->getLimitBox(); ?></div>
	</td>
 </tr>
 </tfoot>   
 </table>

<input type="hidden" name="filter_order" value="<?php echo $this->filter_order?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->filter_order_Dir?>" />      
<input type="hidden" name="task" value="" />
<input type="hidden" name="hidemainmenu" value="0" />
<input type="hidden" name="boxchecked" value="0" />
</form>