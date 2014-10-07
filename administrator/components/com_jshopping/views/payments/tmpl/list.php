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
	$rows=$this->rows;
	$count=count($rows);
	$i=0;
	$saveOrder = $this->filter_order_Dir=="asc" && $this->filter_order=="payment_ordering";
?>
<form action="index.php?option=com_jshopping&controller=payments" method="post" name="adminForm" id="adminForm">
<table class="table table-striped" width="70%">
<thead>
  <tr>
    <th class="title" width ="10">
      #
    </th>
    <th width="20">
	  <input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
    </th>
    <th align="left">
      <?php echo JHTML::_('grid.sort', _JSHOP_TITLE, 'name', $this->filter_order_Dir, $this->filter_order); ?>
    </th>
    <th width="200" align="left">
      <?php echo JHTML::_('grid.sort', _JSHOP_CODE, 'payment_code', $this->filter_order_Dir, $this->filter_order); ?>
    </th>
    <th width="200" align="left">
      <?php echo JHTML::_('grid.sort', _JSHOP_ALIAS, 'payment_class', $this->filter_order_Dir, $this->filter_order); ?>
    </th>
    
    <th width="40" colspan="3">
      <?php echo JHTML::_('grid.sort', _JSHOP_ORDERING, 'payment_ordering', $this->filter_order_Dir, $this->filter_order); ?>
      <?php if ($saveOrder){?>
      <?php echo JHtml::_('grid.order',  $rows, 'filesave.png', 'saveorder');?>
      <?php }?>
    </th>
    <th width="50">
      <?php echo _JSHOP_PUBLISH;?>
    </th>
    <th width="50">
    	<?php print _JSHOP_EDIT;?>
    </th>
    <th width="40">
        <?php echo JHTML::_('grid.sort', _JSHOP_ID, 'payment_id', $this->filter_order_Dir, $this->filter_order); ?>
    </th>
  </tr>
</thead>
<?php foreach($rows as $row){?>
  <tr class="row<?php echo $i % 2;?>">
   <td>
     <?php echo $i+1;?>
   </td>
   <td>     
     <?php echo JHtml::_('grid.id', $i, $row->payment_id);?>
   </td>
   <td>
     <a title="<?php echo _JSHOP_EDIT_PAYMENT;?>" href="index.php?option=com_jshopping&controller=payments&task=edit&payment_id=<?php echo $row->payment_id; ?>"><?php echo $row->name;?></a>
   </td>
   <td>
     <?php echo $row->payment_code;?>
   </td>
   <td>
     <?php echo $row->payment_class;?>
   </td>
   <td align="right" width="20">
    <?php
      if ($i!=0 && $saveOrder) echo '<a href="index.php?option=com_jshopping&controller=payments&task=order&id=' . $row->payment_id . '&order=up&number=' . $row->payment_ordering . '"><img alt="' . _JSHOP_UP . '" src="components/com_jshopping/images/uparrow.png"/></a>';
    ?>
   </td>
   <td align="left" width="20">
      <?php
        if ($i!=$count-1 && $saveOrder) echo '<a href="index.php?option=com_jshopping&controller=payments&task=order&id=' . $row->payment_id . '&order=down&number=' . $row->payment_ordering . '"><img alt="' . _JSHOP_DOWN . '" src="components/com_jshopping/images/downarrow.png"/></a>';
      ?>
   </td>
   <td align="center" width="10">
    <input type="text" name="order[]" id="ord<?php echo $row->payment_id;?>" value="<?php echo $row->payment_ordering?>" <?php if (!$saveOrder) echo 'disabled'?> class="inputordering" style="text-align: center" />
   </td>
   <td align="center">
     <?php
       echo $published=($row->payment_publish) ? ('<a href="javascript:void(0)" onclick="return listItemTask(\'cb' . $i . '\', \'unpublish\')"><img src="components/com_jshopping/images/tick.png" title="'._JSHOP_PUBLISH.'" ></a>') : ('<a href="javascript:void(0)" onclick="return listItemTask(\'cb' . $i . '\', \'publish\')"><img title="'._JSHOP_UNPUBLISH.'" src="components/com_jshopping/images/publish_x.png"></a>');
     ?>
   </td>
   <td align="center">
        <?php print "<a href='index.php?option=com_jshopping&controller=payments&task=edit&payment_id=".$row->payment_id."'><img src='components/com_jshopping/images/icon-16-edit.png'></a>"?>
   </td>
   <td align="center">
        <?php print $row->payment_id;?>
   </td>
  </tr>
<?php
$i++;
}
?>
</table>

<input type="hidden" name="filter_order" value="<?php echo $this->filter_order?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->filter_order_Dir?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="hidemainmenu" value="0" />
<input type="hidden" name="boxchecked" value="0" />
</form>