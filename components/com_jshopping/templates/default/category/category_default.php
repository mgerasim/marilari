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
<div class="jshop">
<h1><?php print $this->category->name?></h1>
<?php print $this->category->description?>

<div class="jshop_list_category">
<?php if (count($this->categories)){ ?>
<table class = "jshop list_category">
    <?php foreach($this->categories as $k=>$category){?>
        <?php if ($k%$this->count_category_to_row==0) print "<tr>"; ?>
        <td class="jshop_categ" width="<?php print (100/$this->count_category_to_row)?>%">
          <table class = "category">
            <tr>
            <td class="image">
                <a href = "<?php print $category->category_link;?>"><img class="jshop_img" src="<?php print $this->image_category_path;?>/<?php if ($category->category_image) print $category->category_image; else print $this->noimage;?>" alt="<?php print htmlspecialchars($category->name)?>" title="<?php print htmlspecialchars($category->name)?>" /></a>
            </td>
            <td>
               <a class = "product_link" href = "<?php print $category->category_link?>"><?php print $category->name?></a>
               <p class = "category_short_description"><?php print $category->short_description?></p>
            </td>
            </tr>
           </table>
        </td>    
        <?php if ($k%$this->count_category_to_row==$this->count_category_to_row-1) print '</tr>'; ?>
    <?php } ?>
        <?php if ($k%$this->count_category_to_row!=$this->count_category_to_row-1) print '</tr>'; ?>
</table>
<?php }?>
</div>
<?php include(dirname(__FILE__)."/products.php");?>
</div>