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
<?php if ($this->params->get('show_page_heading') && $this->params->get('page_heading')) {?>    
<div class="shophead<?php print $this->params->get('pageclass_sfx');?>"><h1><?php print $this->params->get('page_heading')?></h1></div>
<?php }?>
<div class="jshop">
<?php print $this->manufacturer->description?>

<?php if (count($this->rows)){?>
<div class="jshop_list_manufacturer">
<table class = "jshop">
    <?php foreach($this->rows as $k=>$row){?>
        <?php if ($k%$this->count_manufacturer_to_row==0) print "<tr>";?>
        <td class = "jshop_categ" width = "<?php print (100/$this->count_manufacturer_to_row)?>%">
          <table class = "manufacturer">
             <tr>
               <td class="image">
                    <a href = "<?php print $row->link;?>"><img class = "jshop_img" src = "<?php print $this->image_manufs_live_path;?>/<?php if ($row->manufacturer_logo) print $row->manufacturer_logo; else print $this->noimage;?>" alt="<?php print htmlspecialchars($row->name);?>" /></a>
               </td>
               <td>
                   <a class = "product_link" href = "<?php print $row->link?>"><?php print $row->name?></a>
                   <p class = "manufacturer_short_description"><?php print $row->short_description?></p>
                   <?php if ($row->manufacturer_url!=""){?>
                   <div class="manufacturer_url">
                        <a target="_blank" href="<?php print $row->manufacturer_url?>"><?php print _JSHOP_MANUFACTURER_INFO?></a>
                   </div>
                   <?php }?>
               </td>
             </tr>
           </table>
        </td>    
        <?php if ($k%$this->count_manufacturer_to_row==$this->count_manufacturer_to_row-1) print "</tr>";?>
	 <?php } ?>
     <?php if ($k%$this->count_manufacturer_to_row!=$this->count_manufacturer_to_row-1) print "</tr>";?>
</table>
</div>
<?php } ?>
</div>