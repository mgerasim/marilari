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
$rows=$this->rows;
?>
<form action="index.php?option=com_jshopping&controller=usergroups" method="post" name="adminForm" id="adminForm">

<table class="table table-striped">
<thead>
  	<tr>
    	<th class="title" width ="10">
      		#
    	</th>
    	<th width="20">
	  		<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
    	</th>
    	<th width="150" align="left">
      		<?php echo JHTML::_('grid.sort', _JSHOP_TITLE, 'usergroup_name', $this->filter_order_Dir, $this->filter_order); ?>
    	</th>
    	<th align="left">
      		<?php echo JHTML::_('grid.sort', _JSHOP_DESCRIPTION, 'usergroup_description', $this->filter_order_Dir, $this->filter_order); ?>
    	</th>
        <th width="80">
            <?php echo JHTML::_('grid.sort', _JSHOP_USERGROUP_DISCOUNT, 'usergroup_discount', $this->filter_order_Dir, $this->filter_order); ?>
        </th>
    	<th width="100">
			<?php echo _JSHOP_USERGROUP_IS_DEFAULT; ?>
		</th>
	    <th width="50">
	        <?php echo _JSHOP_EDIT; ?>
	    </th>
        <th width="40">
            <?php echo JHTML::_('grid.sort', _JSHOP_ID, 'usergroup_id', $this->filter_order_Dir, $this->filter_order); ?>
        </th>
  	</tr>
</thead>
<?php $i=0; foreach($rows as $row){?>
<tr class="row<?php echo ($i%2);?>">
	<td>
		<?php echo $i + 1;?>
	</td>
	<td align="center">
        <?php echo JHtml::_('grid.id', $i, $row->usergroup_id);?>
	</td>
	<td>
		<a href="index.php?option=com_jshopping&controller=usergroups&task=edit&usergroup_id=<?php echo $row->usergroup_id;?>"><?php echo $row->usergroup_name; ?></a>
	</td>
	<td>
		<?php echo $row->usergroup_description; ?>
	</td>
    <td>
        <?php print $row->usergroup_discount?> %
    </td>
	<td align="center"><?php $default_image=($row->usergroup_is_default) ? ('tick.png') : ('publish_x.png');?><img src="components/com_jshopping/images/<?php echo $default_image;?>" /></td>
	<td align="center">
	    <a href='index.php?option=com_jshopping&controller=usergroups&task=edit&usergroup_id=<?php print $row->usergroup_id?>'><img src='components/com_jshopping/images/icon-16-edit.png'></a>
   </td>
   <td align="center">
    <?php print $row->usergroup_id?>
   </td>
</tr>	
<?php $i++;} ?>
</table>

<input type="hidden" name="filter_order" value="<?php echo $this->filter_order?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->filter_order_Dir?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="hidemainmenu" value="0" />
<input type="hidden" name="boxchecked" value="0" />
</form>