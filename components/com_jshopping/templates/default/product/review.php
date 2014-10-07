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
<?php if ($this->allow_review){?>
    <div class="review_header"><?php print _JSHOP_REVIEWS?></div>
    <?php foreach($this->reviews as $curr){?>
        <div class="review_item">
        <div><span class="review_user"><?php print $curr->user_name?></span>, <span class='review_time'><?php print formatdate($curr->time);?></span></div>
        <div class="review_text"><?php print nl2br($curr->review)?></div>
        <?php if ($curr->mark) {?>
            <div class="review_mark"><?php print showMarkStar($curr->mark);?></div>
        <?php } ?> 
        </div>
    <?php }?>
    <?php if ($this->display_pagination){?>
    <table class="jshop_pagination">
    <tr>
        <td><div class="pagination"><?php print $this->pagination?></div></td>
    </tr>
    </table>
    <?php }?>
    <?php if ($this->allow_review > 0){?>
        <?php JHTML::_('behavior.formvalidation'); ?> 
        <span class="review"><?php print _JSHOP_ADD_REVIEW_PRODUCT?></span>
        <form action="<?php print SEFLink('index.php?option=com_jshopping&controller=product&task=reviewsave');?>" name="add_review" method="post" onsubmit="return validateReviewForm(this.name)">
        <input type="hidden" name="product_id" value="<?php print $this->product->product_id?>" />
        <input type="hidden" name="back_link" value="<?php print jsFilterUrl($_SERVER['REQUEST_URI'])?>" />
		<?php echo JHtml::_('form.token');?>
        <table id="jshop_review_write" >
            <tr>
                <td>
                    <?php print _JSHOP_REVIEW_USER_NAME?>
                </td>
                <td>
                    <input type="text" name="user_name" id="review_user_name" class="inputbox" value="<?php print $this->user->username?>"/>
                </td>
            </tr>
            <tr>
                <td>
                    <?php print _JSHOP_REVIEW_USER_EMAIL?>
                </td>
                <td>
                    <input type="text" name="user_email" id="review_user_email" class="inputbox" value="<?php print $this->user->email?>" />
                </td>
            </tr>
            <tr>
                <td>
                    <?php print _JSHOP_REVIEW_REVIEW?>
                </td>
                <td>
                    <textarea name="review" id="review_review" rows="4" cols="40" class="jshop inputbox" style="width:320px;"></textarea>
                </td>
            </tr>
            <tr>
                <td>
                    <?php print _JSHOP_REVIEW_MARK_PRODUCT?>
                </td>
                <td>
                    <?php for($i=1; $i<=$this->stars_count*$this->parts_count; $i++){?>
                        <input name="mark" type="radio" class="star {split:<?php print $this->parts_count?>}" value="<?php print $i?>" <?php if ($i==$this->stars_count*$this->parts_count){?>checked="checked"<?php }?>/>
                    <?php } ?>
                </td>
            </tr>
            <?php print $this->_tmp_product_review_before_submit;?>
            <tr>
                <td></td>
                <td>
                    <input type="submit" class="button validate" value="<?php print _JSHOP_REVIEW_SUBMIT?>" />
                </td>
            </tr>
        </table>
        </form>
    <?php }else{?>
        <div class="review_text_not_login"><?php print $this->text_review?></div>
    <?php } ?>
<?php }?>