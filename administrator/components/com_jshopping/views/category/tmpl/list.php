<?php 
/**
* @version      4.4.0 05.11.2013
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die('Restricted access');
$categories = $this->categories; 
$i = 0;
$text_search = $this->text_search;
$count = count($categories); 
$pageNav = $this->pagination;
$saveOrder = $this->filter_order_Dir=="asc" && $this->filter_order=="ordering";
?>
<form action="index.php?option=com_jshopping&controller=categories" method="post" enctype="multipart/form-data" name="adminForm" id="adminForm">
<table width = "100%" style = "padding-bottom: 5px;">
    <tr>
        <td style = "text-align: right;">
            <input type = "text" name = "text_search" value = "<?php echo htmlspecialchars($text_search);?>" />
            <input type = "submit" class = "button" value = "<?php echo _JSHOP_SEARCH;?>" />
        </td>
    </tr>
</table>
<table class="table table-striped">
<thead>
  <tr>
    <th class="title" width ="10">#</th>
    <th width="20">
      <input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
    </th>
    <th width="200" align="left">
      <?php echo JHTML::_('grid.sort', _JSHOP_TITLE, 'name', $this->filter_order_Dir, $this->filter_order); ?>
    </th>
    <th align="left">
      <?php echo JHTML::_('grid.sort', _JSHOP_DESCRIPTION, 'description', $this->filter_order_Dir, $this->filter_order); ?>
    </th>
    <th width="80" align="left">
      <?php echo _JSHOP_CATEGORY_PRODUCTS;?>
    </th>    
    <th colspan="3" width="40">
      <?php echo JHTML::_( 'grid.sort', _JSHOP_ORDERING, 'ordering', $this->filter_order_Dir, $this->filter_order); ?>
      <?php if ($saveOrder){?>
      <?php echo JHtml::_('grid.order',  $categories, 'filesave.png', 'saveorder');?>
      <?php }?>
    </th>
    <th width="50">
      <?php echo _JSHOP_PUBLISH;?>
    </th>
    <th width="50">
        <?php echo _JSHOP_EDIT;?>
    </th>
    <th width="50">
        <?php echo _JSHOP_DELETE;?>
    </th>
    <th width="50">
        <?php echo JHTML::_( 'grid.sort', _JSHOP_ID, 'id', $this->filter_order_Dir, $this->filter_order); ?>
    </th>
  </tr>
  </thead>  
<?php foreach($categories as $category) { ?>
  <tr class="row<?php echo $i % 2;?>">
   <td>
     <?php echo $pageNav->getRowOffset($i);?>
   </td>
   <td>     
     <?php echo JHtml::_('grid.id', $i, $category->category_id);?>
   </td>
   <td>
     <?php print $category->space; ?><a href = "index.php?option=com_jshopping&controller=categories&task=edit&category_id=<?php echo $category->category_id; ?>"><?php echo $category->name;?></a>
   </td>
   <td>
     <?php echo $category->short_description;?>
   </td>
   <td align="center">
     <?php if (isset($this->countproducts[$category->category_id])){?>
     <a href="index.php?option=com_jshopping&controller=products&category_id=<?php echo $category->category_id?>">
       (<?php print intval($this->countproducts[$category->category_id]);?>) <img src="components/com_jshopping/images/tree.gif" border="0" />
     </a>
     <?php }else{?>
     (0)
     <?php }?>
   </td>
   <td align = "right" width = "20">
        <?php if ($saveOrder && $category->isPrev) echo '<a href = "index.php?option=com_jshopping&controller=categories&task=order&id='.$category->category_id.'&move=-1"><img alt="' . _JSHOP_UP . '" src="components/com_jshopping/images/uparrow.png"/></a>'; ?>
    </td>
    <td align = "left" width = "20"> 
        <?php if ($saveOrder && $category->isNext) echo '<a href = "index.php?option=com_jshopping&controller=categories&task=order&id='.$category->category_id.'&move=1"><img alt="' . _JSHOP_DOWN . '" src="components/com_jshopping/images/downarrow.png"/></a>'; ?>
    </td>
    
   <td align="center" width="10">
    <input type="text" name="order[]" id="ord<?php echo $category->category_id;?>" value="<?php echo $category->ordering;?>"  <?php if (!$saveOrder) echo 'disabled'?> class="inputordering" />
   </td>
   <td align="center">
     <?php
       echo $published=($category->category_publish) ? ('<a href="javascript:void(0)" onclick="return listItemTask(\'cb' . $i . '\', \'unpublish\')"><img title="' . _JSHOP_PUBLISH . '" alt="" src="components/com_jshopping/images/tick.png"></a>') : ('<a href="javascript:void(0)" onclick="return listItemTask(\'cb' . $i . '\', \'publish\')"><img title="' . _JSHOP_UNPUBLISH . '" alt="" src="components/com_jshopping/images/publish_x.png"></a>');
     ?>
   </td>
   <td align="center">
        <a href='index.php?option=com_jshopping&controller=categories&task=edit&category_id=<?php print $category->category_id?>'><img src='components/com_jshopping/images/icon-16-edit.png'></a>
   </td>
   <td align="center">
        <a href='index.php?option=com_jshopping&controller=categories&task=remove&cid[]=<?php print $category->category_id?>' onclick="return confirm('<?php print _JSHOP_DELETE?>');"><img src='components/com_jshopping/images/publish_r.png'></a>
   </td>
   <td align="center">
    <?php print $category->category_id?>
   </td>
  </tr>
<?php $i++; } ?>
<tfoot>
<tr>
    <td colspan = "12">
		<div class = "jshop_list_footer"><?php echo $pageNav->getListFooter(); ?></div>
        <div class = "jshop_limit_box"><?php echo $pageNav->getLimitBox(); ?></div>
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