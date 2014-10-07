<?php
/**
* @version      4.3.1 13.08.2013
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die('Restricted access');

displaySubmenuOptions();
$rows=$this->rows; $count=count ($rows); $i=0;
$lists=$this->lists;
$saveOrder = $this->filter_order_Dir == "asc" && $this->filter_order == "F.ordering";
?>
<form action="index.php?option=com_jshopping&controller=productfields" method="post" name="adminForm" id="adminForm">
<table width="100%" style="padding-bottom:5px;">
  <tr>
    <td width="95%" align="right">
        <?php echo _JSHOP_GROUP.": ".$lists['group'];?>&nbsp;&nbsp;&nbsp;
    </td>
    <td>
        <input type="text" name="text_search" value="<?php echo htmlspecialchars($this->text_search);?>" />
    </td>
    <td>
        <input type="submit" class="button" value="<?php echo _JSHOP_SEARCH;?>" />
    </td>
  </tr>
</table>
<table class="table table-striped">
<thead>
  <tr>
    <th class="title" width ="10">
      #
    </th>
    <th width="20">
	  <input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
    </th>
    <th width="200" align="left">
      <?php echo JHTML::_('grid.sort', _JSHOP_TITLE, 'name', $this->filter_order_Dir, $this->filter_order); ?>
    </th>
    <th align="left">
      <?php echo JHTML::_('grid.sort', _JSHOP_TYPE, 'F.type', $this->filter_order_Dir, $this->filter_order); ?>
    </th>
    <th align="left">
      <?php echo _JSHOP_OPTIONS;?>
    </th>
    <th align="left">
      <?php echo _JSHOP_CATEGORIES;?>
    </th>
    <th align="left">
      <?php echo JHTML::_('grid.sort', _JSHOP_GROUP, 'groupname', $this->filter_order_Dir, $this->filter_order); ?>
    </th>
    <th colspan="3" width="40">
      <?php echo JHTML::_('grid.sort', _JSHOP_ORDERING, 'F.ordering', $this->filter_order_Dir, $this->filter_order); ?>
      <?php if ($saveOrder){?>
      <?php echo JHtml::_('grid.order',  $rows, 'filesave.png', 'saveorder');?>
      <?php }?>
    </th>
    <th width="50">
        <?php echo _JSHOP_EDIT;?>
    </th>
    <th width="40">
        <?php echo JHTML::_('grid.sort', _JSHOP_ID, 'id', $this->filter_order_Dir, $this->filter_order); ?>
    </th>
  </tr>
</thead>
<?php foreach ($rows as $row){ ?>
  <tr class="row<?php echo $i % 2;?>">
   <td>
     <?php echo $i + 1;?>
   </td>
   <td>
     <?php echo JHtml::_('grid.id', $i, $row->id);?>
   </td>
   <td>
     <?php if (!$row->count_option && $row->type==0) {?><img src="components/com_jshopping/images/icon-16-denyinactive.png" alt="" /><?php }?>
     <a href="index.php?option=com_jshopping&controller=productfields&task=edit&id=<?php echo $row->id; ?>"><?php echo $row->name;?></a>
   </td>
   <td>
     <?php print $this->types[$row->type];?>
   </td>
   <td>
    <?php if ($row->type==0){?>
     <a href="index.php?option=com_jshopping&controller=productfieldvalues&field_id=<?php echo $row->id?>"><?php echo _JSHOP_OPTIONS?></a>
     (<?php if (is_array($this->vals[$row->id])) echo implode(", ", $this->vals[$row->id]);?>)
     <?php }else{?>
        -
     <?php }?>
   </td>
   <td>
    <?php print $row->printcat;?>        
   </td>
   <td>
    <?php print $row->groupname;?>
   </td>
   <td align="right" width="20">
    <?php
        if ($i!=0 && $saveOrder) echo '<a href="index.php?option=com_jshopping&controller=productfields&task=order&id='.$row->id.'&move=-1"><img alt="'._JSHOP_UP.'" src="components/com_jshopping/images/uparrow.png"/></a>';
    ?>
   </td>
   <td align="left" width="20">
    <?php
        if ($i!=$count-1 && $saveOrder) echo '<a href="index.php?option=com_jshopping&controller=productfields&task=order&id='.$row->id.'&move=1"><img alt="'._JSHOP_DOWN.'" src="components/com_jshopping/images/downarrow.png"/></a>';
    ?>
   </td>
   <td align="center" width="10">
    <input type="text" name="order[]" id="ord<?php echo $row->id;?>"  size="5" value="<?php echo $row->ordering; ?>" <?php if (!$saveOrder) echo 'disabled'?> class="inputordering" style="text-align: center" />
   </td>
   <td align="center">
        <a href='index.php?option=com_jshopping&controller=productfields&task=edit&id=<?php print $row->id;?>'><img src='components/com_jshopping/images/icon-16-edit.png'></a>
   </td>
   <td align="center">
    <?php print $row->id;?>
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