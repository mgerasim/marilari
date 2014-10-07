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
<div class="jshop vendordetailinfo">
    <?php if ($this->header){?>
    <h1><?php print $this->header ?></h1>
    <?php }?>
    
    <div class="vendor_logo">
        <?php if ($this->vendor->logo!=""){?>
        <img src="<?php print $this->vendor->logo?>" alt="<?php print htmlspecialchars($this->vendor->shop_name);?>" />
        <?php }?>
    </div>
    <table>
        <tr>
          <td class="name">
            <?php print _JSHOP_F_NAME ?>: 
          </td>
          <td>
            <?php print $this->vendor->f_name ?>
          </td>
        </tr>
        
        <tr>
          <td class="name">
            <?php print _JSHOP_L_NAME ?>:
          </td>
          <td>
            <?php print $this->vendor->l_name ?>
          </td>
        </tr>        
        <tr>
          <td class="name">
            <?php print _JSHOP_FIRMA_NAME ?>:
          </td>
          <td>
            <?php print $this->vendor->company_name ?>
          </td>
        </tr>
        
        <tr>
          <td class="name">
            <?php print _JSHOP_EMAIL ?>:
          </td>
          <td>
            <?php print $this->vendor->email ?>
          </td>
        </tr>        
        <tr>
          <td  class="name">
            <?php print _JSHOP_STREET_NR ?>:
          </td>
          <td>
            <?php print $this->vendor->adress ?>
          </td>
        </tr>
        
        <tr>
          <td class="name">
            <?php print _JSHOP_ZIP ?>:
          </td>
          <td>
            <?php print $this->vendor->zip ?>
          </td>
        </tr>        
        <tr>
          <td class="name">
            <?php print _JSHOP_CITY ?>:
          </td>
          <td>
            <?php print $this->vendor->city ?>
          </td>
        </tr>        
        <tr>
          <td class="name">
            <?php print _JSHOP_STATE ?>:
          </td>
          <td>
            <?php print $this->vendor->state ?>
          </td>
        </tr>
        
        <tr>
          <td class="name">
            <?php print _JSHOP_COUNTRY ?>:
          </td>
          <td>
            <?php print $this->vendor->country ?>
          </td>
        </tr>
        
        <tr>
          <td class="name">
            <?php print _JSHOP_TELEFON ?>:
          </td>
          <td>
            <?php print $this->vendor->phone ?>
          </td>
        </tr>
        
        <tr>
          <td class="name">
            <?php print _JSHOP_FAX ?>:
          </td>
          <td>
            <?php print $this->vendor->fax ?>
          </td>
        </tr>
    </table>
</div>    