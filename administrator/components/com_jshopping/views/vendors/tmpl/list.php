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
$i=0;
$rows=$this->rows;
$pageNav=$this->pageNav;
?>
<form name="adminForm" id="adminForm" method="post" action="index.php?option=com_jshopping&controller=vendors">
<table width="100%" style="padding-bottom:5px;">
   <tr>
      <td align="right">
        <input type="text" name="text_search" value="<?php echo htmlspecialchars($this->text_search);?>" />&nbsp;&nbsp;&nbsp;
        <input type="submit" class="button" value="<?php echo _JSHOP_SEARCH;?>" />
      </td>
   </tr>
 </table>
 
<table class="table table-striped" width="100%">
<thead>
<tr>
     <th width="20">
       #
     </th>
     <th width="20">
       <input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
     </th>
     <th width="150" align="left">
       <?php echo _JSHOP_USER_FIRSTNAME?>
     </th>
     <th width="150" align="left">
       <?php echo _JSHOP_USER_LASTNAME?>
     </th>
     <th align="left">
       <?php echo _JSHOP_STORE_NAME?>
     </th>
     <th width="150">
       <?php echo _JSHOP_EMAIL?>
     </th>
     <th width="60">
        <?php echo _JSHOP_DEFAULT;?>    
    </th>	 	      
     <th width="50">
        <?php echo _JSHOP_EDIT;?>
    </th>
     <th width="40">
        <?php echo _JSHOP_ID;?>
    </th>
</tr>
</thead> 
<?php $i=0; foreach($rows as $row){?>
<tr class="row<?php echo ($i  %2);?>">
     <td align="center">
       <?php echo $pageNav->getRowOffset($i);?>
     </td>
     <td align="center">
	   <input type="checkbox" onclick="isChecked(this.checked)" id="cb<?php echo $i++?>" name="cid[]" value="<?php echo $row->id?>" />
     </td>
     <td>
         <?php echo $row->f_name?>
     </td>
     <td>
       <?php echo $row->l_name;?>
     </td>
     <td>
       <?php echo $row->shop_name;?>
     </td>
     <td>
       <?php echo $row->email;?>
     </td>
     <td align="center">
     <?php if ($row->main==1) {?>
        <img src="components/com_jshopping/images/icon-16-default.png" />
     <?php }?>
     </td>
     <td align="center">
        <?php print "<a href='index.php?option=com_jshopping&controller=vendors&task=edit&id=".$row->id."'><img src='components/com_jshopping/images/icon-16-edit.png'></a>"?>
     </td>
     <td align="center">
        <?php print $row->id?>
     </td>
</tr>
<?php $i++;}?>
<tfoot>
 <tr>   
    <td colspan="11">
		<div class = "jshop_list_footer"><?php echo $pageNav->getListFooter(); ?></div>
        <div class = "jshop_limit_box"><?php echo $pageNav->getLimitBox(); ?></div>
	</td>
 </tr>
</tfoot>
</table>
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" />
</form>