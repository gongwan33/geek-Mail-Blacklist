<?php
if ( ! defined( 'ABSPATH' ) ) exit;

if(!GMB::isUserValid()) {
    echo "<h3>Only Administor can access this page.</h3>";
}

if(!empty($_POST['gmb-bl-rule'])) {
    $add_res = GMBActions::addRule();
}

$gmb_rules = GMBActions::getRules();
$GMB_enabled = get_option('gmb-enabled');

if($GMB_enabled === 'no') {
    $GMB_enable_btn_str = 'Enable Blacklist';
    $GMB_enable_btn_data = '1';
    $GMB_enable_btn_color = "red";
} else if($GMB_enabled === 'yes') {
    $GMB_enable_btn_str = 'Disable Blacklist';
    $GMB_enable_btn_data = '0';
    $GMB_enable_btn_color = "green";
} else {
    $GMB_enable_btn_str = 'Error Status';
}

$monitor_counts = GMBMonitor::getCounts();
$monitor_records = GMBMonitor::getRecords();
?>
<form method="post" class="gmb-add-form">
<div>
    <h3>Emails to Block(Support Regular Expression):</h3>
    <label><strong>Instruction</strong>: when adding regular expressions, please wrap it with symbol '/'. For example: /.*@a.com/ means filter all emails with the domain a.com. Any rule without wrapping by '/' will be regarded as a full match rule.</label>
    <br/>
    <label style="color:red"><strong>Warning</strong>: this blacklist function relys on the default WordPress registration process. So if you are using any customized registration pages, please make sure they follow the WordPress standard registration functions and process.</label>
</div>
<div>
    <input type="text" name="gmb-bl-rule" placeholder="One rule at a time" style="width: 500px"/>
    <?php wp_nonce_field( 'gmb_form', 'gmb-form-nonce' ); ?>
    <input type="submit" value="Add"/>
    <?php if(!$add_res['res']):?>
    <div style="color:red">
        <strong><?php echo esc_html($add_res['info']);?></strong>
    </div>
    <?php endif;?>
</div>
</form>

<div class="gmb-enable-session" style="font-size:large;">
    <span style="font-weight: bold;">Blacklist Enabled:</span>
    <span style="background-color:<?php echo esc_attr($GMB_enable_btn_color);?>;padding:2px;color:white;" ><?php echo esc_html(strtoupper($GMB_enabled));?></span>
    <button data="<?php echo esc_attr($GMB_enable_btn_data);?>" style="font-size:medium;padding:5px" id="gmb-enable-btn"><?php echo esc_html($GMB_enable_btn_str);?></button>
</div>

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

<div>
<br/>
<hr/>
<br/>
</div>

<div class="attempts-block">
<div>
    <h3>Login Attempts</h3>    
</div>

<div>
    <button id="gmb-del-records-btn" style="font-size:medium;padding:5px;">Clear Records</button>
</div>

<h4>Overall Status</h4>
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

<h4>Detailed Records</h4>
<table class="gmb-attemps-tb">
    <tr>
        <th>Uername</th>
        <th>Email</th>
        <th>Last Attemt Time</th>
        <th>Result</th>
        <th>Info</th>
    </tr>
    <?php if(!empty($monitor_records)):?>
    <?php foreach($monitor_records as $record):?>
    <tr>
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
</div>


