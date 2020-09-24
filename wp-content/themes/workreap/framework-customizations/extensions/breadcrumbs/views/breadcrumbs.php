<?php
if (!defined('FW')) {
    die('Forbidden');
}

//Custom Titles
if( is_singular( 'projects' ) ){
	$title = esc_html__('Job detail', 'workreap');
} else if( is_singular( 'employers' ) ){
	$title = esc_html__('Company detail', 'workreap');
} else if( is_singular( 'post' ) ){
	$title = esc_html__('Article detail', 'workreap');
} else if( is_singular( 'proposals' ) ){
	$title = esc_html__('Job proposal', 'workreap');
}else if( is_singular( 'micro-services' ) ){
	$title = esc_html__('Service detail', 'workreap');
}
?>
<?php if (!empty($items)) : ?>
       <ol class="wt-breadcrumb">
            <?php for ($i = 0; $i < workreap_count_items($items); $i ++) :?>
                <?php if ($i == ( workreap_count_items($items) - 1 )) : 
		   				if( !empty($title) ){?>
                    <li class="last-item"><?php echo esc_attr($title); ?></li>
                    <?php } else{?>
                    	<li class="last-item"><?php echo esc_attr($items[$i]['name']); ?></li>
                    <?php }?>
                <?php elseif ($i == 0) : ?>
                    <li class="first-item">
                        <?php if (isset($items[$i]['url'])) : ?>
                            <a href="<?php echo esc_url($items[$i]['url']); ?>"><?php echo esc_html($items[$i]['name']); ?></a></li>
                    <?php
                    else : echo esc_html($items[$i]['name']);
                    endif
                    ?>
                <?php else :
                    ?>
                    <li class="<?php echo intval( $i - 1 ) ?>-item">
                    <?php if (isset($items[$i]['url'])) : ?>
                            <a href="<?php echo esc_url($items[$i]['url']); ?>"><?php echo esc_html($items[$i]['name']); ?></a></li>
                    <?php
                    	else : echo esc_html($items[$i]['name']);
                    endif
                    ?>
            <?php endif ?>
        <?php endfor ?>
        </ol>
<?php endif ?>