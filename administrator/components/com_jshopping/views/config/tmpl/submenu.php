<?php 
/**
* @version      4.3.1 13.08.2013
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die('Restricted access');
$menu=getItemsConfigPanelMenu(); 
?>
<div class="jssubmenu">
    <div class="m">
        <ul id="submenu">
        <?php foreach($menu as $el){
            if (!$el[3]) continue;
            if (strpos($el[1], @$_SERVER['QUERY_STRING'] ) !== false ) {
                $class="class='active'";
            }else{
                $class="";
            }
        ?>
            <li>
                <a <?php print $class;?> href="<?php print $el[1]?>"><?php print $el[0]?></a>
            </li>
        <?php }?>        
        </ul>    
        <div class="clr"></div>
    </div>
</div>