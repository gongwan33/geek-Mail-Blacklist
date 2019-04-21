<?php
if(!GMB::isUserValid()) {
    echo "<h3>Only Administor can access this page.</h3>";
}

function gmb_get_rules() {
    global $wpdb;

    $table_name = $wpdb->prefix . GMB_DB_NAME; 
    $sql = "SELECT * FROM $table_name ORDER BY time DESC";
    $res = $wpdb->get_results($sql, ARRAY_A);
    return $res;
}

if(!empty($_POST['gmb-bl-rule'])) {
    global $wpdb;
    $cur_user = wp_get_current_user();
    $userid = $cur_user->ID;
    $time = current_time('mysql');
    $exp = $_POST['gmb-bl-rule'];
    $table_name = $wpdb->prefix . GMB_DB_NAME; 

    $sql = $wpdb->prepare("SELECT id FROM $table_name WHERE expression=%s", $exp);
    $res = $wpdb->get_row($sql);

    if(empty($res)) {
        $sql = $wpdb->prepare("INSERT INTO $table_name (expression, time, userid) VALUES (%s, %s, %s)", array($exp, $time, $userid));

        $wpdb->query($sql);
    }

}

$gmb_rules = gmb_get_rules();

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
    <input type="submit" value="Add"/>
</div>
</form>

<div class="gmb-enable-session" style="font-size:large;">
    <span style="font-weight: bold;">Blacklist Enabled:</span>
    <span style="background-color:<?php echo $GMB_enable_btn_color;?>;padding:2px;color:white;" ><?php echo strtoupper($GMB_enabled);?></span>
    <button data="<?php echo $GMB_enable_btn_data;?>" style="font-size:medium;padding:5px" id="gmb-enable-btn"><?php echo $GMB_enable_btn_str;?></button>
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
    <td><?php echo $rule['expression'];?></td>
    <td><?php echo $rule['time'];?></td>
    <?php $user = get_user_by('id', $rule['userid']);?>
    <td><?php echo $user->display_name;?></td>
    <td><button class="gmb-del-btn" data="<?php echo $rule['id'];?>">Delete</button></td>
</tr>
<?php endforeach;?>
<?php endif;?>
</table>

<script type="text/javascript">
var delBtns = document.querySelectorAll('.gmb-del-btn');
var enableBtn = document.querySelector('#gmb-enable-btn');

function post(url, data) {
    var params = {
        action: data
    }

    const searchParams = Object.keys(params).map((key) => {
    return encodeURIComponent(key) + '=' + encodeURIComponent(params[key]);
    }).join('&');
    
    fetch(url, {
        method: "POST",
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8'
        },
        body: searchParams
    }).then(res => {
        location.href = location.href;
    });

}

if(typeof delBtns != 'undefined' && delBtns.length > 0) {
    delBtns.forEach(function(btn, idx) {
        btn.addEventListener('click', function(ev) {
            if(confirm('Are you sure to delete?')) {
                var ele = ev.target;
                var data = "del-" + ele.getAttribute('data');

                post("<?php echo GMB_URL.'/backend/actions.php'; ?>", data);
            };
        });
    });
}

if(typeof enableBtn != 'undefined') {
    enableBtn.addEventListener('click', function(ev) {
        var ele = ev.target;
        var data = "enable-" + ele.getAttribute('data');

        post("<?php echo GMB_URL.'/backend/actions.php'; ?>", data);
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
