<?php 
/*
 *增加后台更多输入框
*/
//1 添加上传模块
function gamux_down_var() {
	$gamux_down = array(      
		"durl"   => "downurl",
		"title"  => "dtitle",
		"date"   => "ddate"
	);
	return $gamux_down;
}

//获取可用的下载链接数量
function gamux_down_count() : int {
	global $wpdb,$post;
	$sql = "SELECT COUNT(meta_id) AS count FROM ".$wpdb->prefix."postmeta WHERE post_id = ".$post->ID." AND meta_key REGEXP 'downurl.'";
	return $wpdb->get_var($sql);
}

//2获取远程文件大小，需打开allow_url_fopen
function remote_file_size($url) {
	if($url == '' || $url == null){
		return 'null';
	}
	else {
		$header_array = get_headers($url, true);
		$size = $header_array['Content-Length'];
		if($size == 0 || $size == null) {
			return 'null';
		}
		else if($size < 1024) {
			return $size.'B';
		}
		else if($size < 1048576) {
			return round($size/1024, 2).'K';
		}
		else if($size < 1073741824) {
			return round($size/1048576, 2).'M';
		}
		else{
			return round($size/1073741824, 2).'G';
		}
	}
}

function output_downlist() {
	global $post;
	$output = '';
	for($i = gamux_down_count()-1; $i >= 0; $i--) {
		$output .=  "<tr class='d-body-tr'><th scope='col' class='d-version'>".get_post_meta($post->ID, gamux_down_var()['title'].'_'.$i)[0]."</th><td></td><td class='d-date'>".get_post_meta($post->ID, gamux_down_var()['date'].'_'.$i)[0]."</td><td class='d-size'>".remote_file_size(get_post_meta($post->ID, gamux_down_var()['durl'].'_'.$i)[0])."</td><td class='d-url'><a  href=".get_post_meta($post->ID, gamux_down_var()['durl'].'_'.$i)[0].">下载</a></td></tr>";
	}
	return $output;
}

//3显示当前文章的最新一个下载信息
function downlist_array() {
	global $post;
	$i = gamux_down_count()-1;
	$output = array(
		'version' => get_post_meta($post->ID, gamux_down_var()['title'].'_'.$i)[0],
		'downurl' => get_post_meta($post->ID, gamux_down_var()['durl'].'_'.$i)[0],
		'date'    => get_post_meta($post->ID, gamux_down_var()['date'].'_'.$i)[0]  
	);
	return $output;		
}

//4 激活缩略图
add_theme_support( 'post-thumbnails', array( 'post' ) );
set_post_thumbnail_size( 85, 85 );