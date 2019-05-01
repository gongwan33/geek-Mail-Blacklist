<?php
function gmb_pagination($num, $action, $container) {
    $pages = ceil($num/GMB_DEFAULT_LIMIT);
?>
<div class="gmb-pagination">
    <span>Page:</span>
    <select class="gmb-page-select" data="<?php echo esc_attr($action);?>" container="<?php echo esc_attr($container);?>">
    <?php for($i = 1; $i <= $pages; $i++):?>
        <option value="<?php echo esc_attr($i);?>"><?php echo esc_html($i);?></option>
    <?php endfor;?>
    </select>
</div>
<?php
}

