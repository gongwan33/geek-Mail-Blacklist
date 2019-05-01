<?php 
function gmb_rules_table($gmb_rules) {
?>
<table class="gmb-rules-tb">
<tr>
    <th>Rules</th>
    <th>Created Time</th>
    <th>By</th>
    <th>Action</th>
</tr>

<?php if(!empty($gmb_rules)):?>
<?php foreach($gmb_rules as $rule):?>
<tr>
    <td><?php echo esc_html($rule['expression']);?></td>
    <td><?php echo esc_html($rule['time']);?></td>
    <?php $user = get_user_by('id', $rule['userid']);?>
    <td><?php echo esc_html($user->display_name);?></td>
    <td><button class="gmb-del-btn" data="<?php echo esc_attr($rule['id']);?>">Delete</button></td>
</tr>
<?php endforeach;?>
<?php endif;?>
</table>
<?php
}
