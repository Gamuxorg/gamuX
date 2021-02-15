<?php
	namespace Gamux;
	/**
	 * 添加后台下载统计管理选项卡
	 */

	function download_statistics() {
		$echarts = get_template_directory_uri(). '/js/echarts.min.js';
		$html=<<<str
<script type="text/javascript" src="$echarts"></script>
<h1>下载统计</h1>
<h2>历史总览</h2>
<div id='download_overall' style='width:100%;height:300px'> </div><br>
<label id="download_overall_error_msg" style="font-size:12pt;color:orange"> </label><br>
<input type='button' value='GET' onclick='get_download_overall()'>

<h2>年度统计</h2>
<div id='download_yearly' style='width:70%;height:400px'> </div><br>
<label id="download_yearly_error_msg" style="font-size:12pt;color:orange"> </label><br>
<input id="download_yearly_year" value="2020" type="number" min="2000" max="2099" step="1" required placeholder="年" style="width:7%">
<input type='button' value='GET' onclick='get_download_yearly()'>

<h2>月度统计</h2>
<div id='download_monthly' style='width:100%;height:300px'> </div><br>
<label id="download_monthly_error_msg" style="font-size:12pt;color:orange"> </label><br>
<input id="download_monthly_year" type="number" min="2000" max="2099" step="1" required placeholder="年" style="width:7%">
<input id="download_monthly_mon" type="number" min="1" max="12" step="1" required placeholder="月" style="width:5%">
<input type='button' value='GET' onclick='get_download_monthly()'>

<h2>单日统计</h2>
<div id='download_daily' style='width:100%;height:300px'> </div><br>
<label id="download_daily_error_msg" style="font-size:12pt;color:orange"> </label><br>
<input id="download_daily_date" type="date" required placeholder="yyyy-MM-dd">
<input type='button' value='前一日' onclick='download_daily_prev()'>
<input type='button' value='后一日' onclick='download_daily_next()'>
<input type='button' value='GET' onclick='get_download_daily()'>

<h2>文章查询</h2>
<div id='download_stat'> </div><br>
<label id="download_data_error_msg" style="font-size:12pt;color:orange"> </label><br>
<input id='download_post_id' type='text' value='' placeholder='请输入postID'>
<select id='download_action'><option value='postall'>单个文章</option></select>
<input type='button' value='GET' onclick='get_download_data()'>
str;
		echo $html;
	}

	function add_download_statistics_script() {
		wp_enqueue_script('download_statistics', get_template_directory_uri(). '/js/download_statistics.js');
		// wp_enqueue_script('echarts', get_template_directory_uri(). '/js/echarts.min.js');
	}
	add_action('admin_enqueue_scripts', '\Gamux\add_download_statistics_script');

	function download_statistics_options_page() {
		add_menu_page(
			'下载统计',
			'下载统计',
			'manage_options',
			'download_statistics',
			'\Gamux\download_statistics',
			'dashicons-download',
			20
		);
	}
	add_action('admin_menu', '\Gamux\download_statistics_options_page');
