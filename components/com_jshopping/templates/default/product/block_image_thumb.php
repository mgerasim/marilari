<?php if ( (count($this->images)>1) || (count($this->videos) && count($this->images)) ) {?>
    <?php foreach($this->images as $k=>$image){?>
        <img class="jshop_img_thumb" src="<?php print $this->image_product_path?>/<?php print $image->image_thumb?>" alt="<?php print htmlspecialchars($image->_title)?>" title="<?php print htmlspecialchars($image->_title)?>" onclick="showImage(<?php print $image->image_id?>)" />
    <?php }?>
<?php }?>