<?php 
function gmb_records_table($monitor_records) {
?>
<table class="gmb-attemps-tb">
    <tr>
        <th>IP</th>
        <th>Uername</th>
        <th>Email</th>
        <th>Last Attemt Time</th>
        <th>Result</th>
        <th>Info</th>
    </tr>
    <?php if(!empty($monitor_records)):?>
    <?php foreach($monitor_records as $record):?>
    <tr>
        <td><?php echo esc_html($record['ip']);?></td>
        <td><?php echo esc_html($record['username']);?></td>
        <td><?php echo esc_html($record['email']);?></td>
        <td><?php echo esc_html($record['time']);?></td>
        <?php $result = $record['result'] == 1?'Success':'Failed';?>
        <td style="font-weight:bold;color:<?php echo esc_attr($record['result'] == 1?'green':'red')?>"><?php echo esc_html($result);?></td>
        <td><?php echo esc_html($record['info']);?></td>
    </tr>
    <?php endforeach;?>
    <?php endif;?>
</table>
<?php
}
