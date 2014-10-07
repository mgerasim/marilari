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
    <h1><?php print _JSHOP_LOGIN ?></h1>
    
    <?php if ($this->config->shop_user_guest && $this->show_pay_without_reg) {?>
      <span class="text_pay_without_reg"><?php print _JSHOP_ORDER_WITHOUT_REGISTER_CLICK?> <a href="<?php print SEFLink('index.php?option=com_jshopping&controller=checkout&task=step2',1,0, $this->config->use_ssl);?>"><?php print _JSHOP_HERE?></a></span>
    <?php } ?>
    
    <table width="100%">
    <tr>
        <td width="50%" valign="top" class="login_block">
              <span class="small_header"><?php print _JSHOP_HAVE_ACCOUNT ?></span>
              <span><?php print _JSHOP_PL_LOGIN ?></span>
              <form method = "post" action="<?php print SEFLink('index.php?option=com_jshopping&controller=user&task=loginsave', 1,0, $this->config->use_ssl)?>" name = "jlogin">
                <table style="margin-top:3px;">
                <tr>
                    <td><?php print _JSHOP_USERNAME ?>: </td>
                    <td><input type = "text" name = "username" value = "" class = "inputbox" /></td>
                </tr>
                <tr>
                    <td><?php print _JSHOP_PASSWORT ?>: </td>
                    <td><input type = "password" name = "passwd" value = "" class = "inputbox" /></td>
                </tr>
                <tr>
                    <td colspan="2" align="right">
                        <label for = "remember_me"><?php print _JSHOP_REMEMBER_ME ?></label><input type = "checkbox" name = "remember" id = "remember_me" value = "yes" /><br />
                        <input type="submit" class="button" value="<?php print _JSHOP_LOGIN ?>" /><br />                        
                        <a href = "<?php print $this->href_lost_pass ?>"><?php print _JSHOP_LOST_PASSWORD ?></a>
                    </td>
                </tr>
                </table>
                <input type = "hidden" name = "return" value = "<?php print $this->return ?>" />
                <?php echo JHtml::_('form.token');?>
              </form>   
        </td>
        
        <td width="50%" valign="top" class="register_block">
            <span class="small_header"><?php print _JSHOP_HAVE_NOT_ACCOUNT ?></span>
            <span><?php print _JSHOP_REGISTER ?></span>
            <?php if (!$this->config->show_registerform_in_logintemplate){?>
                <div style="padding-top:3px;"><input type="button" class="button" value="<?php print _JSHOP_REGISTRATION ?>" onclick="location.href='<?php print $this->href_register ?>';" /></div>
            <?php }else{?>
                <?php $hideheaderh1 = 1; include(dirname(__FILE__)."/register.php"); ?>
            <?php }?>
        </td>        
    </tr>
    </table>
</div>    