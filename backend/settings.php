<?php
if ( ! defined( 'ABSPATH' ) ) exit;

if(!GMB::isUserValid()) {
    echo "<h3>Only Administor can access this page.</h3>";
}

if(!empty($_POST['gmb-bl-rule'])) {
    $add_res = GMBActions::addRule();
}

$gmb_rules = GMBActions::getRules();
$gmb_rules_num = GMBActions::getRuleNum();
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

$gmb_monitor_counts = GMBMonitor::getCounts();
$gmb_monitor_counts_num = GMBMonitor::getCountsNum();
$gmb_monitor_records = GMBMonitor::getRecords();
$gmb_monitor_records_num = GMBMonitor::getRecordNum();
?>
<form method="post" class="gmb-add-form">
<div>
    <h3>Emails to Block(Support Regular Expression):</h3>
    <h4>Block certain Emails from registration.</h4>
    <div class="gmb-instruct">
        <strong>Instruction</strong>: 
        <ul>
            <li>When adding regular expressions, please wrap it with symbol '/'. For example: /.*@a.com/ means filter all the Emails with the domain a.com. Any rule without wrapping by '/' will be regarded as a full match rule.</li>
        <br/>
        <li>This blacklist function relys on the default WordPress registration process. So if you are using any customized registration pages, please make sure they follow the WordPress standard registration functions and process.</li>
        </ul>
    </div>
</div>
<br/>
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

<div id="gmb-rules-tb-container">
<?php gmb_rules_table($gmb_rules);?>
</div>
<?php gmb_pagination($gmb_rules_num, 'gmb_get_rules_page', 'gmb-rules-tb-container');?>

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

<div class="gmb-chart-container">
    <canvas id="status-chart"></canvas>
</div>

<div id="gmb-counts-tb-container">
<?php gmb_counts_table($gmb_monitor_counts);?>
</div>
<?php gmb_pagination($gmb_monitor_counts_num, 'gmb_get_monitor_counts_page', 'gmb-counts-tb-container');?>

<h4>Detailed Records</h4>
<div id="gmb-records-tb-container">
<?php gmb_records_table($gmb_monitor_records);?>
</div>
<?php gmb_pagination($gmb_monitor_records_num, 'gmb_get_monitor_records_page', 'gmb-records-tb-container');?>
</div>


