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

$gmb_ajax_nonce = wp_create_nonce('gmb_ajax');

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

<script type="text/javascript">
var delBtns = document.querySelectorAll('.gmb-del-btn');
var enableBtn = document.querySelector('#gmb-enable-btn');

function post(action, data) {
    var jsonDat = {
        action: action, 
        data: data,
        _ajax_nonce: "<?php echo esc_js($gmb_ajax_nonce);?>",
    }

    jQuery.ajax({
        url: ajaxurl,
        data: jsonDat,
        type: "POST",
        dataType: "json",
        success: function(res) {
            location.href = location.href;
        }, 
    });
}

if(typeof delBtns != 'undefined' && delBtns.length > 0) {
    delBtns.forEach(function(btn, idx) {
        btn.addEventListener('click', function(ev) {
            if(confirm('Are you sure to delete?')) {
                var ele = ev.target;
                var data = ele.getAttribute('data');

                post('gmb_del', data);
            };
        });
    });
}

if(typeof enableBtn != 'undefined') {
    enableBtn.addEventListener('click', function(ev) {
        var ele = ev.target;
        var data = ele.getAttribute('data');

        post("gmb_enable", data);
    });
}
</script>

<style type="text/css">
.gmb-enable-session {
    margin: 10px;
}

.gmb-add-form {
    margin: 10px;
}

.gmb-add-form div{
    margin-bottom: 10px;
}

.gmb-rules-tb {
    margin: 10px;
    text-align: center;
    width: 90%;
}

.gmb-rules-tb tr:nth-child(1) {
    background-color: black; 
    color: white;
}

.gmb-rules-tb td, .gmb-rules-tb th {
    padding: 5px;
}

.gmb-rules-tb tr:nth-child(2n) {
    background-color: #ccc;
}
</style>
