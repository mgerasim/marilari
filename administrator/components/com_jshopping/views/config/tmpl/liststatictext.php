<?php 
/**
* @version      4.6.0 13.08.2013
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die('Restricted access');
$jshopConfig=JSFactory::getConfig();
include(dirname(__FILE__)."/submenu.php");
$rows=$this->rows;
$i=0;
?>
<form action="index.php?option=com_jshopping&controller=config" method="post" name="adminForm" id="adminForm">
<table class="table table-striped">
<thead>
  <tr>
    <th class="title" width ="10">
      #
    </th>
    <th width="20">
      <input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
    </th>
    <th align="left">
      <?php echo _JSHOP_PAGE; ?>
    </th>
    <th width = "50">
        <?php echo _JSHOP_USE_FOR_RETURN_POLICY;?>
    </th>
    <th width="50">
        <?php echo _JSHOP_EDIT;?>
    </th>
    <th width = "50">
        <?php echo _JSHOP_DELETE;?>
    </th>
    <th width="40">
        <?php echo _JSHOP_ID;?>
    </th>
  </tr>
</thead>  
<?php foreach($rows as $row){?>
  <tr class="row<?php echo $i % 2;?>">
   <td>
     <?php echo $i+1;?>
   </td>
   <td>
     <?php echo JHtml::_('grid.id', $i, $row->id);?>
   </td>
   <td>
    <a href='index.php?option=com_jshopping&controller=config&task=statictextedit&id=<?php print $row->id?>'>
    <?php if (defined("_JSHP_STPAGE_".$row->alias)) print constant("_JSHP_STPAGE_".$row->alias); else print $row->alias;?>
    </a>
   </td>
   <td align="center">
     <?php
       echo $use_for_return_policy=($row->use_for_return_policy) ? ('<a href="javascript:void(0)" onclick="return listItemTask(\'cb'.$i. '\', \'unpublish\')"><img title="' . _JSHOP_YES . '" alt="" src="components/com_jshopping/images/tick.png"></a>') : ('<a href="javascript:void(0)" onclick="return listItemTask(\'cb' . $i . '\', \'publish\')"><img title="'._JSHOP_NO.'" alt="" src="components/com_jshopping/images/publish_x.png"></a>');
     ?>       
   </td>
   <td align="center">
        <a href='index.php?option=com_jshopping&controller=config&task=statictextedit&id=<?php print $row->id?>'><img src='components/com_jshopping/images/icon-16-edit.png'></a>
   </td>
   <td align="center">
   <?php if (!in_array($row->alias, $jshopConfig->sys_static_text)){?>
    <a href='index.php?option=com_jshopping&controller=config&task=deletestatictext&id=<?php print $row->id?>' onclick="return confirm('<?php print _JSHOP_DELETE?>')"><img src='components/com_jshopping/images/publish_r.png'></a>
    <?php }?>
   </td>
   <td align="center">
    <?php print $row->id;?>
   </td>
   </tr>
<?php
$i++;
}
?>
<?php $pkey="etemplatevar";if ($this->$pkey){print $this->$pkey;}?>
</table>

<input type="hidden" name="task" value="" />
<input type="hidden" name="hidemainmenu" value="0" />
<input type="hidden" name="boxchecked" value="0" />
</form>