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
$i=0;
?>
<form action="index.php?option=com_jshopping&controller=languages" method="post" name="adminForm" id="adminForm">

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
      <?php echo _JSHOP_LANGUAGE_NAME;?>
    </th>
    <th width="120">
      <?php echo _JSHOP_DEFAULT_FRONT_LANG;?>
    </th>
    <th width="120">
      <?php echo _JSHOP_DEFAULT_LANG_FOR_COPY;?>
    </th>
    <th width="50">
      <?php echo _JSHOP_PUBLISH;?>
    </th>
    <th width="40">
        <?php echo _JSHOP_ID;?>
    </th>
  </tr>
</thead>  
<?php
$count=count($rows);
foreach($rows as $row){
?>
  <tr class="row<?php echo $i % 2;?>">
   <td align="center">
     <?php echo $i+1;?>
   </td>
   <td align="center">
     <?php echo JHtml::_('grid.id', $i, $row->id);?>
   </td>
   <td>
     <?php echo $row->name; ?>
   </td>
   <td align="center">
     <?php if ($this->default_front == $row->language) {?><img src="components/com_jshopping/images/icon-16-default.png"><?php }?>
   </td>
   <td align="center">
     <?php if ($this->defaultLanguage == $row->language) {?><img src="components/com_jshopping/images/icon-16-default.png"><?php }?>
   </td>
   <td align="center">
     <?php
       echo $published=($row->publish) ? ('<a href="javascript:void(0)" onclick="return listItemTask(\'cb' . $i . '\', \'unpublish\')"><img src="components/com_jshopping/images/tick.png" title="'._JSHOP_PUBLISH.'" ></a>') : ('<a href="javascript:void(0)" onclick="return listItemTask(\'cb' . $i . '\', \'publish\')"><img title="'._JSHOP_UNPUBLISH.'" src="components/com_jshopping/images/publish_x.png"></a>');
     ?>
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
<br/>
<br/>
<div class="helpbox">
    <div class="head"><?php echo _JSHOP_DEFAULT_FRONT_LANG;?></div>
    <div class="text"><?php print _JSHOP_DEFAULT_FRONT_LANG_INFO;?></div>
    <br/>
    <div class="head"><?php echo _JSHOP_DEFAULT_LANG_FOR_COPY;?></div>
    <div class="text"><?php print _JSHOP_DEFAULT_LANG_FOR_COPY_INFO;?></div>
</div>


<input type="hidden" name="task" value="" />
<input type="hidden" name="hidemainmenu" value="0" />
<input type="hidden" name="boxchecked" value="0" />
</form>