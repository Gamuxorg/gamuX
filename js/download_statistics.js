/**
 * WP后台下载统计页面脚本
 */

// 获取历史总览数据
function get_download_overall() { 
    $.get(document.location.origin + "/wp-json/gamux/v1/download?action=overall&post_id=0", "", function(response) {
        if(response.code != 0) {
            $("#download_overall_error_msg").text(response.message);
            setTimeout(()=>$("#download_overall_error_msg").text(""), 2200);
        }
        else {
            var postTitleArr = new Array;
            var postDownCountArr = new Array;
            var total = 0;
            for(id in response.data) {
                postTitleArr.push(response.data[id].title);
                postDownCountArr.push(response.data[id].count);
                total += response.data[id].count;
            }

            // clear previous echart instance
            var dom = document.getElementById("download_overall");
            if(dom.hasAttribute("_echarts_instance_")) {
                dom.removeAttribute("_echarts_instance_");
                dom.innerHTML = "";
            }

            var chart = echarts.init(dom, 'light');
            var option = {
                tooltip: {},
                xAxis: {
                    data: postTitleArr
                },
                yAxis: {},
                title: {
                    text: "总下载量: " + total,
                    left: "center"
                },
                series: [{
                    name: '下载量',
                    type: 'bar',
                    data: postDownCountArr,
                    color: '#001852',
                    barWidth: '50px',
                    barCategoryGap: '20%',
                    label: {
                        show: true,
                        position: 'top'
                    }
                }],
                dataZoom: [{
                    type: 'slider',
                    xAxisIndex: 0,
                    start: 70,
                    end: 100
                }]
            };
            chart.setOption(option);
            //清除错误信息
            $("#download_overall_error_msg").text("");
        }
    });
}

// 获取年度统计数据
function get_download_yearly() { 
    var year = document.getElementById("download_yearly_year");
    if(!year.checkValidity()) {
        $("#download_yearly_error_msg").text(year.validationMessage);
        setTimeout(()=>$("#download_yearly_error_msg").text(""), 2200);
        return;
    }
    year = year.value;

    $.get("https://kr.linuxgame.cn:8088/get_download_data.php?action=yearly&post_id=0&para=" + year, "", function(response) {
        if(response.code != 0) {
            $("#download_yearly_error_msg").text(response.message);
            setTimeout(()=>$("#download_yearly_error_msg").text(""), 2200);
        }
        else {
            var postTitleArr = new Array;
            var total = 0;
            // remove entry whose value == 0 
            for(i = 0; i < response.data.length; i++) {
                if(response.data[i].value > 0) {
                    postTitleArr.push(response.data[i].name);
                    total += response.data[i].value;
                }
                else {
                    response.data.splice(i, 1);
                    i--;
                }
            }

            // clear previous echart instance
            var dom = document.getElementById("download_yearly");
            if(dom.hasAttribute("_echarts_instance_")) {
                dom.removeAttribute("_echarts_instance_");
                dom.innerHTML = "";
            }

            var chart = echarts.init(dom, 'dark');
            var option = {
                tooltip: {},
                title: {
                    text: year + "年下载量: " + total,
                    left: "center"
                },
                legend: {
                    orient: 'vertical',
                    left: 10,
                    data: postTitleArr
                },
                series: [{
                    name: '月下载量',
                    type: 'pie',
                    radius: '58%',
                    center: ['50%', '50%'],
                    data: response.data,
                    label: {
                        position: "inner",
                        formatter: '{b}\n{c}'
                    },
                    labelLine: {
                        show: false
                    }
                }]
            };
            chart.setOption(option);
            //清除错误信息
            $("#download_yearly_error_msg").text("");
        }
        cc=response;
    });
}

// 获取月度统计数据
function get_download_monthly() {
    var year = document.getElementById("download_monthly_year");
    var mon = document.getElementById("download_monthly_mon");
    if(!year.checkValidity()) {
        $("#download_monthly_error_msg").text(year.validationMessage);
        setTimeout(()=>$("#download_monthly_error_msg").text(""), 2200);
        return;
    }
    if(!mon.checkValidity()) {
        $("#download_monthly_error_msg").text(mon.validationMessage);
        setTimeout(()=>$("#download_monthly_error_msg").text(""), 2200);
        return;
    }
    
    //月份补零
    year = year.value;
    mon = mon.value;
    if(mon < 10)
        mon = "0" + mon;

    $.get(document.location.origin + "/wp-json/gamux/v1/download?action=monthly&post_id=0&para="+ (year+"-"+mon), "", function(response) {
        if(response.code != 0) {
            $("#download_monthly_error_msg").text(response.message);
            setTimeout(()=>$("#download_monthly_error_msg").text(""), 2200);
        }
        else {
            //准备数据
            var postTitleArr = new Array;
            var postDownCountArr = new Array;
            var total = 0;
            for(id in response.data) {
                postTitleArr.push(response.data[id].title);
                postDownCountArr.push(response.data[id].count);
                total += response.data[id].count;
            }

            // clear previous echart instance
            var dom = document.getElementById("download_monthly");
            if(dom.hasAttribute("_echarts_instance_")) {
                dom.removeAttribute("_echarts_instance_");
                dom.innerHTML = "";
            }

            var chart = echarts.init(dom, 'light');
            var option = {
                title: {
                    text: (year+"-"+mon) + ": " + total,
                    left: "center"
                },
                tooltip: {},
                xAxis: {
                    data: postTitleArr
                },
                yAxis: {},
                series: [{
                    name: '下载量',
                    type: 'line',
                    data: postDownCountArr,
                    color: '#a092f1',
                    label: {
                        show: true,
                        position: 'top'
                    }
                }],
                dataZoom: [{
                    type: 'slider',
                    xAxisIndex: 0,
                    start: 70,
                    end: 100
                }]
            };
            chart.setOption(option);
            //清除错误信息
            $("#download_monthly_error_msg").text("");
        }
    });
}

// 获取单日统计数据
function get_download_daily() {
    var date = document.getElementById("download_daily_date");
    if(!date.checkValidity()) {
        $("#download_daily_error_msg").text(date.validationMessage);
        setTimeout(()=>$("#download_daily_error_msg").text(""), 2200);
        return;
    }
    date = date.value;

    $.get(document.location.origin + "/wp-json/gamux/v1/download?action=daily&post_id=0&para=" + date, "", function(response) {
        if(response.code != 0) {
            $("#download_daily_error_msg").text(response.message);
            setTimeout(()=>$("#download_daily_error_msg").text(""), 2200);
        }
        else {
            //准备数据
            var postTitleArr = new Array;
            var postDownCountArr = new Array;
            var total = 0;
            for(id in response.data) {
                postTitleArr.push(response.data[id].title);
                postDownCountArr.push(response.data[id].count);
                total += response.data[id].count;
            }

            // clear previous echart instance
            var dom = document.getElementById("download_daily");
            if(dom.hasAttribute("_echarts_instance_")) {
                dom.removeAttribute("_echarts_instance_");
                dom.innerHTML = "";
            }

            var chart = echarts.init(dom, 'light');
            var option = {
                title: {
                    text: date + ": " + total,
                    left: "center"
                },
                tooltip: {},
                xAxis: {
                    data: postTitleArr
                },
                yAxis: {},
                series: [{
                    name: '下载量',
                    type: 'scatter',
                    symbolSize: 15,
                    data: postDownCountArr,
                    color: '#38b6b6',
                    label: {
                        show: true,
                        position: 'top'
                    }
                }],
                dataZoom: [{
                    type: 'slider',
                    xAxisIndex: 0,
                    start: 0,
                    end: 100
                }]
            };
            chart.setOption(option);
            //清除错误信息
            $("#download_daily_error_msg").text("");
        }
    });
}

// 单日统计-前一日
function download_daily_prev() {
    var date = document.getElementById("download_daily_date");
    date.valueAsNumber -= 86400000;
    get_download_daily();
}

// 单日统计-后一日
function download_daily_next() {
    var date = document.getElementById("download_daily_date");
    date.valueAsNumber += 86400000;
    get_download_daily();
}

// 文章查询
function get_download_data() {
    var post_id = $("#download_post_id").val();
    if(post_id == "") {
        $("#download_data_error_msg").text("请输入文章ID");   
        setTimeout(()=>$("#download_data_error_msg").text(""), 2200);
    }
    var action = $("#download_action").val();
    
    $.get("https://kr.linuxgame.cn:8088/get_download_data.php?post_id=" + post_id + "&action=" + action, "", function(response) {
        if(response.code != 0) {
            $("#download_data_error_msg").text(response.message);
            setTimeout(()=>$("#download_data_error_msg").text(""), 2200);
        }
        else {
            if(response.data.overall == 0) {
                $("#download_stat").text("指定文章没有数据");
                setTimeout(()=>$("#download_data_error_msg").text(""), 2200);
            }
            else {
                var mons = String();
                for(var mon in response.data.monthly) {
                    mons += mon + ": " + response.data.monthly[mon] + "<br>";
                }
                var days = String();
                for(var day in response.data.daily) {
                    days += day + ": " + response.data.daily[day] + "<br>";
                }
                $("#download_stat").html(
                    "All:<br>" + response.data.overall + "<br><br>" +
                    "Monthly:<br>" + mons + "<br>" +
                    "Daily:<br>" + days + "<br>" 
                );
            }
            $("#download_data_error_msg").text("");
        }
    });
}
