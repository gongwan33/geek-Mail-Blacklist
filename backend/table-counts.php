<?php 
function gmb_counts_table($monitor_counts) {
?>
<table class="gmb-attemps-tb">
    <tr>
        <th>Uername</th>
        <th>Email</th>
        <th>Result</th>
        <th>Count</th>
    </tr>
    <?php if(!empty($monitor_counts)):?>
    <?php foreach($monitor_counts as $record):?>
    <tr>
        <td><?php echo esc_html($record['username']);?></td>
        <td><?php echo esc_html($record['email']);?></td>
        <?php $result = $record['result'] == 1?'Success':'Failed';?>
        <td style="font-weight:bold;color:<?php echo esc_attr($record['result'] == 1?'green':'red')?>"><?php echo esc_html($result);?></td>
        <td><?php echo esc_html($record['count']);?></td>
    </tr>
    <?php endforeach;?>
    <?php endif;?>
</table>
<?php
}
