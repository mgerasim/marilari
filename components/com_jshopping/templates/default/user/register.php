<?php 
/**
* @version      4.3.2 18.08.2013
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die('Restricted access');
?>
<?php
$config_fields = $this->config_fields;
include(dirname(__FILE__)."/register.js.php");
?>
<div class="jshop">
    <?php if (!isset($hideheaderh1)){?>
    <h1><?php print _JSHOP_REGISTRATION;?></h1>
    <?php } ?>
    
    <form action = "<?php print SEFLink('index.php?option=com_jshopping&controller=user&task=registersave',1,0, $this->config->use_ssl)?>" method = "post" name = "loginForm" onsubmit = "return validateRegistrationForm('<?php print $this->urlcheckdata?>', this.name)" autocomplete="off">
    <?php echo $this->_tmpl_register_html_1?>
    <div class = "jshop_register" style="padding-top:0px;">
      <table>
        <?php if ($config_fields['title']['display']){?>
        <tr>
          <td class="name">
            <?php print _JSHOP_REG_TITLE?> <?php if ($config_fields['title']['require']){?><span>*</span><?php } ?>
          </td>
          <td>
            <?php print $this->select_titles ?>
          </td>
        </tr>        
        <?php } ?>
        <?php if ($config_fields['f_name']['display']){?>
        <tr>
          <td class="name">
            <?php print _JSHOP_F_NAME ?> <?php if ($config_fields['f_name']['require']){?><span>*</span><?php } ?>
          </td>
          <td>
            <input type = "text" name = "f_name" id = "f_name" value = "" class = "inputbox" />
          </td>
        </tr>
        <?php } ?>
        <?php if ($config_fields['l_name']['display']){?>
        <tr>
          <td class="name">
            <?php print _JSHOP_L_NAME ?> <?php if ($config_fields['l_name']['require']){?><span>*</span><?php } ?>
          </td>
          <td>
            <input type = "text" name = "l_name" id = "l_name" value = "" class = "inputbox" />
          </td>
        </tr>
        <?php } ?>
        <?php if ($config_fields['m_name']['display']){?>
        <tr>
          <td class="name">
            <?php print _JSHOP_M_NAME ?> <?php if ($config_fields['m_name']['require']){?><span>*</span><?php } ?>
          </td>
          <td>
            <input type = "text" name = "m_name" id = "m_name" value = "" class = "inputbox" />
          </td>
        </tr>
        <?php } ?>
        <?php if ($config_fields['firma_name']['display']){?>
        <tr>
          <td class="name">
            <?php print _JSHOP_FIRMA_NAME ?> <?php if ($config_fields['firma_name']['require']){?><span>*</span><?php } ?>
          </td>
          <td>
            <input type = "text" name = "firma_name" id = "firma_name" value = "" class = "inputbox" />
          </td>
        </tr>
        <?php } ?>
        <?php if ($config_fields['client_type']['display']){?>
        <tr>
          <td class="name">
            <?php print _JSHOP_CLIENT_TYPE ?> <?php if ($config_fields['client_type']['require']){?><span>*</span><?php } ?>
          </td>
          <td>
            <?php print $this->select_client_types;?>
          </td>
        </tr>
        <?php } ?>
        <?php if ($config_fields['firma_code']['display']){?>
        <tr id='tr_field_firma_code' <?php if ($config_fields['client_type']['display']){?>style="display:none;"<?php }?>>
          <td class="name">
            <?php print _JSHOP_FIRMA_CODE ?> <?php if ($config_fields['firma_code']['require']){?><span>*</span><?php } ?>
          </td>
          <td>
            <input type = "text" name = "firma_code" id = "firma_code" value = "" class = "inputbox" />
          </td>
        </tr>
        <?php } ?>
        <?php if ($config_fields['tax_number']['display']){?>
        <tr id='tr_field_tax_number' <?php if ($config_fields['client_type']['display']){?>style="display:none;"<?php }?>>
          <td class="name">
            <?php print _JSHOP_VAT_NUMBER ?> <?php if ($config_fields['tax_number']['require']){?><span>*</span><?php } ?>
          </td>
          <td>
            <input type = "text" name = "tax_number" id = "tax_number" value = "" class = "inputbox" />
          </td>
        </tr>
        <?php } ?>
        <?php if ($config_fields['email']['display']){?>
        <tr>
          <td class="name">
            <?php print _JSHOP_EMAIL ?> <?php if ($config_fields['email']['require']){?><span>*</span><?php } ?>
          </td>
          <td>
            <input type = "text" name = "email" id = "email" value = "" class = "inputbox" />
          </td>
        </tr>
        <?php } ?>
        <?php if ($config_fields['email2']['display']){?>
        <tr>
          <td class="name">
            <?php print _JSHOP_EMAIL2 ?> <?php if ($config_fields['email2']['require']){?><span>*</span><?php } ?>
          </td>
          <td>
            <input type = "text" name = "email2" id = "email2" value = "" class = "inputbox" />
          </td>
        </tr>
        <?php } ?>
        <?php if ($config_fields['birthday']['display']){?>
        <tr>
          <td class="name">
            <?php print _JSHOP_BIRTHDAY?> <?php if ($config_fields['birthday']['require']){?><span>*</span><?php } ?>
          </td>
          <td>            
            <?php echo JHTML::_('calendar', '', 'birthday', 'birthday', $this->config->field_birthday_format, array('class'=>'inputbox', 'size'=>'25', 'maxlength'=>'19'));?>
          </td>
        </tr>
        <?php } ?>
      </table>
    </div>
    <?php echo $this->_tmpl_register_html_2?>
    <div class = "jshop_register">
      <table>
        <?php if ($config_fields['home']['display']){?>
        <tr>
          <td  class="name">
            <?php print _JSHOP_HOME ?> <?php if ($config_fields['home']['require']){?><span>*</span><?php } ?>
          </td>
          <td>
            <input type = "text" name = "home" id = "home" value = "" class = "inputbox" />
          </td>
        </tr>
        <?php } ?>
        <?php if ($config_fields['apartment']['display']){?>
        <tr>
          <td  class="name">
            <?php print _JSHOP_APARTMENT ?> <?php if ($config_fields['apartment']['require']){?><span>*</span><?php } ?>
          </td>
          <td>
            <input type = "text" name = "apartment" id = "apartment" value = "" class = "inputbox" />
          </td>
        </tr>
        <?php } ?>
        <?php if ($config_fields['street']['display']){?>
        <tr>
          <td  class="name">
            <?php print _JSHOP_STREET_NR ?> <?php if ($config_fields['street']['require']){?><span>*</span><?php } ?>
          </td>
          <td>
            <input type = "text" name = "street" id = "street" value = "" class = "inputbox" />
            <?php if ($config_fields['street_nr']['display']){?>
            <input type="text" name="street_nr" id="street_nr" value="" class="inputbox" />
            <?php }?>
          </td>
        </tr>
        <?php } ?>
        <?php if ($config_fields['zip']['display']){?>
        <tr>
          <td class="name">
            <?php print _JSHOP_ZIP ?> <?php if ($config_fields['zip']['require']){?><span>*</span><?php } ?>
          </td>
          <td>
            <input type = "text" name = "zip" id = "zip" value = "" class = "inputbox" />
          </td>
        </tr>
        <?php } ?>
        <?php if ($config_fields['city']['display']){?>
        <tr>
          <td class="name">
            <?php print _JSHOP_CITY ?> <?php if ($config_fields['city']['require']){?><span>*</span><?php } ?>
          </td>
          <td>
            <input type = "text" name = "city" id = "city" value = "" class = "inputbox" />
          </td>
        </tr>
        <?php } ?>
        <?php if ($config_fields['state']['display']){?>
        <tr>
          <td class="name">
            <?php print _JSHOP_STATE ?> <?php if ($config_fields['state']['require']){?><span>*</span><?php } ?>
          </td>
          <td>
            <input type = "text" name = "state" id = "state" value = "" class = "inputbox" />
          </td>
        </tr>
        <?php } ?>
        <?php if ($config_fields['country']['display']){?>
        <tr>
          <td class="name">
            <?php print _JSHOP_COUNTRY ?> <?php if ($config_fields['country']['require']){?><span>*</span><?php } ?>
          </td>
          <td>
            <?php print $this->select_countries ?>
          </td>
        </tr>
        <?php } ?>        
      </table>
    </div>
    <?php echo $this->_tmpl_register_html_3?>
    <div class = "jshop_register">
      <table>
        <?php if ($config_fields['phone']['display']){?>
        <tr>
          <td class="name">
            <?php print _JSHOP_TELEFON ?> <?php if ($config_fields['phone']['require']){?><span>*</span><?php } ?>
          </td>
          <td>
            <input type = "text" name = "phone" id = "phone" value = "" class = "inputbox" />
          </td>
        </tr>
        <?php } ?>
        <?php if ($config_fields['mobil_phone']['display']){?>
        <tr>
          <td class="name">
            <?php print _JSHOP_MOBIL_PHONE ?> <?php if ($config_fields['mobil_phone']['require']){?><span>*</span><?php } ?>
          </td>
          <td>
            <input type = "text" name = "mobil_phone" id = "mobil_phone" value = "" class = "inputbox" />
          </td>
        </tr>
        <?php } ?>
        <?php if ($config_fields['fax']['display']){?>
        <tr>
          <td class="name">
            <?php print _JSHOP_FAX ?> <?php if ($config_fields['fax']['require']){?><span>*</span><?php } ?>
          </td>
          <td>
            <input type = "text" name = "fax" id = "fax" value = "" class = "inputbox" />
          </td>
        </tr>
        <?php } ?>
        
        <?php if ($config_fields['ext_field_1']['display']){?>
        <tr>
          <td class="name">
            <?php print _JSHOP_EXT_FIELD_1 ?> <?php if ($config_fields['ext_field_1']['require']){?><span>*</span><?php } ?>
          </td>
          <td>
            <input type = "text" name = "ext_field_1" id = "ext_field_1" value = "" class = "inputbox" />
          </td>
        </tr>
        <?php } ?>
        <?php if ($config_fields['ext_field_2']['display']){?>
        <tr>
          <td class="name">
            <?php print _JSHOP_EXT_FIELD_2 ?> <?php if ($config_fields['ext_field_2']['require']){?><span>*</span><?php } ?>
          </td>
          <td>
            <input type = "text" name = "ext_field_2" id = "ext_field_2" value = "" class = "inputbox" />
          </td>
        </tr>
        <?php } ?>
        <?php if ($config_fields['ext_field_3']['display']){?>
        <tr>
          <td class="name">
            <?php print _JSHOP_EXT_FIELD_3 ?> <?php if ($config_fields['ext_field_3']['require']){?><span>*</span><?php } ?>
          </td>
          <td>
            <input type = "text" name = "ext_field_3" id = "ext_field_3" value = "" class = "inputbox" />
          </td>
        </tr>
        <?php } ?>
        
      </table>
    </div>
    <?php echo $this->_tmpl_register_html_4?>
    <div class = "jshop_register">
      <table>
        <?php if ($config_fields['u_name']['display']){?>
        <tr>
          <td class="name">
            <?php print _JSHOP_USERNAME ?> <?php if ($config_fields['u_name']['require']){?><span>*</span><?php } ?>
          </td>
          <td>
            <input type = "text" name = "u_name" id = "u_name" value = "" class = "inputbox" />
          </td>
        </tr>
        <?php } ?>
        <?php if ($config_fields['password']['display']){?>
        <tr>
          <td class="name">
            <?php print _JSHOP_PASSWORD ?> <?php if ($config_fields['password']['require']){?><span>*</span><?php } ?>
          </td>
          <td>
            <input type = "password" name = "password" id = "password" value = "" class = "inputbox" />
          </td>
        </tr>
        <?php } ?>
        <?php if ($config_fields['password_2']['display']){?>
        <tr>
          <td class="name">
            <?php print _JSHOP_PASSWORD_2 ?> <?php if ($config_fields['password_2']['require']){?><span>*</span><?php } ?>
          </td>
          <td>
            <input type = "password" name = "password_2" id = "password_2" value = "" class = "inputbox" />
          </td>
        </tr>
        <?php } ?>
        <?php if ($config_fields['privacy_statement']['display']){?>
        <tr>
          <td class="name">
            <a class="privacy_statement" href="#" onclick="window.open('<?php print SEFLink('index.php?option=com_jshopping&controller=content&task=view&page=privacy_statement&tmpl=component', 1);?>','window','width=800, height=600, scrollbars=yes, status=no, toolbar=no, menubar=no, resizable=yes, location=no');return false;">
            <?php print _JSHOP_PRIVACY_STATEMENT?> <?php if ($config_fields['privacy_statement']['require']){?><span>*</span><?php } ?>
            </a>            
          </td>
          <td>
            <input type="checkbox" name="privacy_statement" id="privacy_statement" value="1" />
          </td>
        </tr>
        <?php } ?>        
      </table>
    </div>
    <?php echo $this->_tmpl_register_html_5?>
    <div class="requiredtext">* <?php print _JSHOP_REQUIRED?></div>
    <?php echo $this->_tmpl_register_html_6?>
    <?php echo JHtml::_('form.token');?>
    <input type = "submit" value = "<?php print _JSHOP_SEND_REGISTRATION ?>" class = "button" />
    </form>
</div>