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
?>
<form action="index.php?option=com_jshopping&controller=addons" method="post" name="adminForm" id="adminForm">
<table class="table table-striped">
<thead>
  <tr>
    <th class="title" width="10">#</th>
    <th align="left">
      <?php echo _JSHOP_TITLE?>
    </th>
    <th width="120">
        <?php echo _JSHOP_VERSION?>
    </th>
    <th width="60">
        <?php echo _JSHOP_KEY?>
    </th>
    <th width="60">
        <?php echo _JSHOP_CONFIG?>
    </th>
    <th width="50">
        <?php echo _JSHOP_DELETE?>
    </th>
    <th width="40">
        <?php echo _JSHOP_ID?>
    </th>
  </tr>
</thead>  
<?php foreach($rows as $row){?>
  <tr class="row<?php echo $i % 2;?>">
   <td>
     <?php echo $i+1;?>
   </td>
   <td>
     <?php echo $row->name;?>
   </td>
   <td>
    <?php echo $row->version;?>
   </td>
   <td align="center">
   <?php if ($row->usekey){?>
    <a href='index.php?option=com_jshopping&controller=licensekeyaddon&alias=<?php print $row->alias?>&back=<?php print $this->back64?>'><img src='components/com_jshopping/images/icon-16-edit.png'></a>
   <?php }?>
   </td>
   <td align="center">
   <?php if ($row->config_file_exist){?>
    <a href='index.php?option=com_jshopping&controller=addons&task=edit&id=<?php print $row->id?>'><img src='components/com_jshopping/images/icon-16-edit.png'></a>
    <?php }?>
   </td>
   <td align="center">
    <a href='index.php?option=com_jshopping&controller=addons&task=remove&id=<?php print $row->id?>' onclick="return confirm('<?php print _JSHOP_DELETE_ALL_DATA?>')"><img src='components/com_jshopping/images/publish_r.png'></a>
   </td>
   <td align="center">
    <?php print $row->id;?>
   </td>
  </tr>
<?php $i++;}?>
</table>

<input type="hidden" name="task" value="" />
<input type="hidden" name="hidemainmenu" value="0" />
<input type="hidden" name="boxchecked" value="0" />
</form>