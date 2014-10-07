<?php
/**
* @version      4.3.1 13.08.2013
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die('Restricted access');

$usergroup=$this->usergroup;
JHTML::_('behavior.tooltip');
?>
<form action="index.php?option=com_jshopping&controller=usergroups" method="post" name="adminForm" id="adminForm">

<div class="col100">
<fieldset class="adminform">
    <table class="admintable" width="100%">
	<tr>
		<td class="key" width="20%">
			<?php echo _JSHOP_TITLE;?>*	
		</td>
		<td>
			<input class="inputbox" type="text" name="usergroup_name" value="<?php echo $usergroup->usergroup_name;?>" />
		</td>
	</tr>
    <tr>
        <td class="key">
            <?php echo _JSHOP_USERGROUP_IS_DEFAULT;?>                
        </td>
        <td>
            <input type="checkbox" name="usergroup_is_default" <?php if ($usergroup->usergroup_is_default) echo 'checked="checked"';?> value="1" />
            <?php echo JHTML::tooltip(_JSHOP_USERGROUP_IS_DEFAULT_DESCRIPTION);?>
        </td>
    </tr>
    <tr>
        <td class="key">
            <?php echo _JSHOP_USERGROUP_DISCOUNT;?>*    
        </td>
        <td>
            <input class="inputbox" type="text" name="usergroup_discount" value="<?php echo $usergroup->usergroup_discount;?>" /> %
        </td>
    </tr>
	<tr>
		<td class="key">
			<?php echo _JSHOP_DESCRIPTION;?>	
		</td>
		<td>
			<?php 
            $editor=JFactory::getEditor();
            print $editor->display( 'usergroup_description',  $usergroup->usergroup_description , '100%', '350', '75', '20' ) ;
            ?> 
		</td>
	</tr>
    <?php $pkey="etemplatevar";if ($this->$pkey){print $this->$pkey;}?>
</table>
</fieldset>
</div>
<div class="clr"></div>

<input type="hidden" name="task" value="save" />
<input type="hidden" name="usergroup_id" value="<?php echo $usergroup->usergroup_id;?>" />
</form>