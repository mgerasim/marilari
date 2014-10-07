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
$in_row = $this->config->product_count_related_in_row;
?>
<?php if (count($this->related_prod)){?>    
    <div class="related_header"><?php print _JSHOP_RELATED_PRODUCTS?></div>
    <div class="jshop_list_product">
    <table class = "jshop list_related">
        <?php foreach($this->related_prod as $k=>$product){?>  
            <?php if ($k%$in_row==0) print "<tr>";?>
            <td width="<?php print 100/$in_row?>%" class="jshop_related">
                <?php include(dirname(__FILE__)."/../".$this->folder_list_products."/".$product->template_block_product);?>
            </td>
            <?php if ($k%$in_row==$in_row-1) print "</tr>";?>   
        <?php }?>
        <?php if ($k%$in_row!=$in_row-1) print "</tr>";?>
    </table>
    </div> 
<?php }?>