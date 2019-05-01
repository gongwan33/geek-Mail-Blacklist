(function() {
    var enableBtn = document.querySelector('#gmb-enable-btn');
    var delRecordsBtn = document.querySelector('#gmb-del-records-btn');

    function post(action, data, dataType = 'json', cb = null) {
        var jsonDat = {
            action: action, 
            data: data,
            _ajax_nonce: ajaxobject.ajaxnonce,
        }

        jQuery.ajax({
            url: ajaxobject.ajaxurl,
            data: jsonDat,
            type: "POST",
            dataType: dataType,
            success: function(res) {
                if(cb) {
                    cb(res);
                } else {
                     location.href = location.href;
                }
            }, 
        });
    }

    function setDelBtnListener() {
        var delBtns = document.querySelectorAll('.gmb-del-btn');
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
    }

    setDelBtnListener();

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

    function findDateLine(chartData, dateStr, result) {
        for(var i = 0; i < chartData.length; i++) {
            var line = chartData[i];
            if(line.date == dateStr && line.result == result) {
                return line.num;
            }
        }

        return 0;
    }

    //configuring chart
    var chartData = ajaxobject.monitorchartdata;
    var todayArray = ajaxobject.today.split('-');
    var tYear = parseInt(todayArray[0]);
    var tMonth = parseInt(todayArray[1]) - 1;
    var tDay = parseInt(todayArray[2]);
    var date = new Date(tYear, tMonth, tDay);
    date = new Date(date.setDate(date.getDate() + 1));

    var chartData = ajaxobject.monitorchartdata;
    var ctx = document.getElementById('status-chart').getContext('2d');
    var chartX = [];
    var chartSucY = [];
    var chartFailY = [];

    for(var i = 0; i < 30; i++) {
        date = new Date(date.setDate(date.getDate() - 1));
        var dateStr = date.getFullYear() + '-' + (date.getMonth() + 1) + '-' + date.getDate();
        chartX.push(dateStr);
        chartSucY.push(findDateLine(chartData, dateStr, 1));
        chartFailY.push(findDateLine(chartData, dateStr, 0));
    }

    var failColor = '#f92f2f';
    var sucColor = '#239fff';

    var statusChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: chartX.reverse(),
            datasets: [
                {
                    label: 'Successful Login Attempts',
                    borderColor: sucColor,
                    backgroundColor: sucColor,
                    data: chartSucY.reverse(),
                    fill: false,
                    yAxisID: 'y-axis-1',
                },
                {
                    label: 'Failed Login Attempts',
                    borderColor: failColor,
                    backgroundColor: failColor,
                    data: chartFailY.reverse(),
                    fill: false,
                    yAxisID: 'y-axis-2',
                },
            ]
        },
        options: {
            responsive: true,
            hoverMode: 'index',
            stacked: false,
            title: {
                display: true,
                text: 'Login Attempts'
            },
            scales: {
                yAxes: [{
                    type: 'linear', 
                    display: true,
                    position: 'left',
                    id: 'y-axis-1',
                }, {
                    type: 'linear', 
                    display: true,
                    position: 'right',
                    id: 'y-axis-2',
                    gridLines: {
                        drawOnChartArea: false, 
                    },
                }],
            }
        }
    });

    var selects = document.querySelectorAll('.gmb-page-select');
    selects.forEach(function(select, idx) {
        select.addEventListener('change', function(ev) {
            var act = ev.target.getAttribute('data');
            var containerName = ev.target.getAttribute('container');
            var container = document.getElementById(containerName);
            container.innerHTML = '<div style="padding:5rem;text-align:center;">Loading...</div>';

            post(act, this.value, 'html', function(res) {
                container.innerHTML = res;
                setDelBtnListener();
            });
        }); 
    });
})();
