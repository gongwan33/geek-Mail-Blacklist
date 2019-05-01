var delBtns = document.querySelectorAll('.gmb-del-btn');
var enableBtn = document.querySelector('#gmb-enable-btn');
var delRecordsBtn = document.querySelector('#gmb-del-records-btn');

function post(action, data) {
    var jsonDat = {
        action: action, 
        data: data,
        _ajax_nonce: ajaxobject.ajaxnonce,
    }

    jQuery.ajax({
        url: ajaxobject.ajaxurl,
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

if(typeof delRecordsBtn != 'undefined') {
    delRecordsBtn.addEventListener('click', function(ev) {
        if(confirm("Are you sure to delete all the records?")) {
            var ele = ev.target;
            var data = null;

            post("gmb_del_records", data);
        }
    });
}
