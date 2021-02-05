<?php 
/*
 * 增加后台更多输入框
 */
	namespace Gamux;

/*--------------------------------下载链接支持函数----------------------------------------*/

// 对数据库字段的封装
function down_var() {
	$gamux_down = array(      
		"durl"   => "downurl",
		"title"  => "dtitle",
		"date"   => "ddate",
		"comment"  => "dcomment",
		"arch"   => "darch",
		"history" => "editHistorys"
	);
	return $gamux_down;
}

//获取可用的下载链接数量
function gamux_down_count($post_id = '') : int {
	global $wpdb, $post;
	if(empty($post)) {
		$post = new \stdClass;
		$post->ID = $post_id;
	}
	$sql = "SELECT COUNT(meta_id) AS count FROM ".$wpdb->prefix."postmeta WHERE post_id = ".$post->ID." AND meta_key REGEXP 'downurl'";
	return $wpdb->get_var($sql);
}

//获取可用的购买链接数量
function gamux_buyurl_count() : int {
	global $wpdb,$post;
	$sql = "SELECT COUNT(meta_id) AS count FROM ".$wpdb->prefix."postmeta WHERE post_id = ".$post->ID." AND meta_key REGEXP 'buy_url'";
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
		$output .=  "<tr class='d-body-tr'><th scope='col' class='d-version'>".get_post_meta($post->ID, down_var()['title'].'_'.$i, true)."</th><td></td><td class='d-date'>".get_post_meta($post->ID, down_var()['date'].'_'.$i, true)."</td><td class='d-size'>".remote_file_size(get_post_meta($post->ID, down_var()['durl'].'_'.$i, true))."</td><td class='d-url'><a  href=".get_post_meta($post->ID, down_var()['durl'].'_'.$i, true).">下载</a></td></tr>";
	}
	return $output;
}

//3显示当前文章的最新一个下载信息
function downlist_array() {
	global $post;
	$i = gamux_down_count()-1;
	$output = array(
		'version' => get_post_meta($post->ID, down_var()['title'].'_'.$i, true),
		'downurl' => get_post_meta($post->ID, down_var()['durl'].'_'.$i, true),
		'date'    => get_post_meta($post->ID, down_var()['date'].'_'.$i, true),
		'comment'    => get_post_meta($post->ID, down_var()['comment'].'_'.$i, true) 
	);
	return $output;		
}


/*--------------------------------添加新的输入栏-----------------------------------------*/


//4 增加下载链接输入栏
function download_box() {
	global $post;	

	$header=<<<header
	<form method="post" enctype="multipart/form-data" name="classic_form" id="gamux_upload_form">
        <div class="gamux-edit-upload-div">              
			<label class="gamux-edit-upload-label">
header;

			$body = "";
			for($i = 0; $i < gamux_down_count(); $i++) { 
				$durlName = down_var()['durl'].'_'.$i;
				$durlValue = get_post_meta($post->ID, $durlName, true);
				$verName = down_var()['title'].'_'.$i;
				$verValue = get_post_meta($post->ID, $verName, true);
				$commentName = down_var()['comment'].'_'.$i;
				$commentValue = get_post_meta($post->ID, $commentName, true);
				$archName = down_var()['arch'].'_'.$i;
				$archValue = get_post_meta($post->ID, $archName, true);

				$link=<<<link
				<div class="gamux-edit-upload-option">
					<input type="text" placeholder="此处显示下载url，可粘贴外部url" name="$durlName" value="$durlValue" class="gamux-up-input" />
					<input type="text" placeholder="版本说明"  name="$verName" value="$verValue" class="gamux-text-input" />
					<input type="text" placeholder="备注"  name="$commentName" value="$commentValue" class="gamux-upload-comment" />
					<select name="$archName" class="gamux-upload-arch" data="$archValue">
						<option selected disabled>CPU架构</option>
						<option>i386</option>
						<option>amd64</option>
						<option>arm64</option>
						<option>mips64el</option>
						<option>riscv64</option>
						<option>sw64</option>
					</select>
					<button type="button" class="gamux-up-button">上传</button>
					<button type="button" class="gamux-upload-delete">-</button>
				</div>
link;
				$body.=$link;
			}

			$footer=<<<footer
			</label>
			<div class="gamux-upload-add-div">
				<input class="gamux-upload-add" type="button" value="增加" />
			</div>
		</div>	
	</form>
footer;

	echo $header . $body . $footer;
}

/**
 * 处理download_box上传的数据
 *
 * @return void
 */
function save_download_box($post_id) {
	if(empty($_POST))
		return $post_id;
	if('page' == $_POST['post_type']){   
		if(current_user_can( 'edit_page', $post_id ))   
			return $post_id;
	}

	$uploadCount = 0;
	$durl = down_var()['durl'];
	foreach($_POST as $key=>$value) {				//计算上传的链接数量
		if(strpos($key, $durl) !== false)
			$uploadCount++;
	}
	$dbCount = gamux_down_count();
	$diff = $dbCount - $uploadCount;
	if($diff > 0) {									//用户删除了部分下载链接
		for($i=$dbCount-1; $i >= 0; $i--) {
			delete_post_meta( $post_id, down_var()['durl'].'_'.$i);
			delete_post_meta( $post_id, down_var()['title'].'_'.$i);
			delete_post_meta( $post_id, down_var()['comment'].'_'.$i);
			delete_post_meta( $post_id, down_var()['date'].'_'.$i);
			delete_post_meta( $post_id, down_var()['arch'].'_'.$i);
		}
	}

	for($i = 0; $i < $uploadCount; $i++) {
		$data = array(
			$_POST[down_var()['durl'].'_'.$i], 
			$_POST[down_var()['title'].'_'.$i], 
			date('y-m-d'),
			$_POST[down_var()['comment'].'_'.$i],
			$_POST[down_var()['arch'].'_'.$i]
		);
		if($data[0] != get_post_meta($post_id, down_var()['durl'].'_'.$i, true) or 
					$data[1] != get_post_meta($post_id, down_var()['title'].'_'.$i, true) or
					$data[3] != get_post_meta($post_id, down_var()['comment'].'_'.$i, true) or
					$data[4] != get_post_meta($post_id, down_var()['arch'].'_'.$i, true)) {
			update_post_meta($post_id, down_var()['durl'].'_'.$i, $data[0]);
			update_post_meta($post_id, down_var()['title'].'_'.$i, $data[1]);
			update_post_meta($post_id, down_var()['date'].'_'.$i, $data[2]);
			update_post_meta($post_id, down_var()['comment'].'_'.$i, $data[3]);
			update_post_meta($post_id, down_var()['arch'].'_'.$i, $data[4]);
		}
		//nothing changed, do nothing
	}
}
add_action( 'save_post', '\Gamux\save_download_box' );

function add_download_box() {
    add_meta_box(
      'gamux_upload_file',
      '添加下载(已有'.gamux_down_count().')',
      '\Gamux\download_box',
      'post',
      'advanced',
      'core'
    );
}
add_action( 'add_meta_boxes', '\Gamux\add_download_box' );

function add_download_box_script() {
	wp_enqueue_style('add_extra_box1', get_template_directory_uri(). '/css/add_extra_box.css');
	wp_enqueue_script('add_extra_box2', get_template_directory_uri(). '/js/add_extra_box.js');
}
add_action( 'admin_enqueue_scripts', '\Gamux\add_download_box_script' );

//5 其他信息的输入栏
function extra_meta_box($post) {
	wp_nonce_field( 'rating_nonce_action', 'rating_nonce_name' );
	$buy_key = 'buy_url';
	$peizhi_key = 'peizhi';
	$bg_key = 'bg';
	$peizhi_value = get_post_meta( $post->ID, $peizhi_key, true);
	$bg_value = get_post_meta( $post->ID, $bg_key, true);

	//获取多个购买链接
	$count = gamux_buyurl_count();	
	$link_template = '<div><input style="width: 70%;" name="buy_url[]" value="buy_value"><input name="buy_store[]" style="width:15%" placeholder="商店名, 如Steam" value="buy_place"></div>';
	$buyList = "";
	if($count > 0) {
		for($i=0; $i < $count; $i++) {
			$buyObj = json_decode(get_post_meta( $post->ID, $buy_key.'_'.$i, true));
			$tmpLink = str_replace("buy_value", $buyObj->buy_url, $link_template);
			$buyList .= str_replace("buy_place", $buyObj->buy_store, $tmpLink);
		}
	}
	else {				//新文章，添加一个空的
		$tmpLink = str_replace("buy_value", "", $link_template);
		$buyList .= str_replace("buy_place", "", $tmpLink);
	}
	
	$html =<<<str
	<div>
		<label>购买/源码地址，输入http(s)开头的url即可</label>
		<div id="gamux-buyurls" class="gamux-buyurls">	
			$buyList
		</div>
		<input type="button" value="增加" id="gamux-buyurl-add">
		<input type="button" value="删除" id="gamux-buyurl-del">
	</div>
	<label>背景图片，输入http(s)开头的url即可</label>
	<input style="width: 100%;" name="bg" id="bg" value="$bg_value">
	<label>运行配置</label>
	<textarea name="peizhi" id="peizhi" style="width: 100%;" rows = "10">$peizhi_value</textarea>
str;
	echo $html;
}

/**
 * 处理extra_meta_box上传的数据
 *
 * @param [type] $post_id
 * @return void
 */
function save_extra_meta_box( $post_id ) {
	if(empty($_POST))
		return;
	if (!isset($_POST['rating_nonce_name'])) {
		return $post_id;
	}
	$nonce = $_POST['rating_nonce_name'];
	if (!wp_verify_nonce( $nonce, 'rating_nonce_action')) {
		return $post_id;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return $post_id;
	}
	if ($_POST['post_type'] == 'post') {
		if (!current_user_can('edit_post', $post_id )) {
			return $post_id;
		}
	}

	$buy_key = 'buy_url';
	$buy_url_array = $_POST['buy_url'];
	$buy_store_array = $_POST['buy_store'];
	$peizhi_key = 'peizhi';
	$peizhi_value = $_POST['peizhi'];
	$bg_key = 'bg';
	$bg_value = $_POST['bg'];

	$uploadCount = count($buy_url_array);
	$dbCount = gamux_buyurl_count();
	$diff = $dbCount - $uploadCount;
	if($diff > 0) {									//用户删除了部分购买链接
		for($i=$dbCount-1; $i > 0; $i--)
			delete_post_meta( $post_id, $buy_key.'_'.$i);
	}
	for($i=0; $i < $uploadCount ;$i++) {
		$buy_json = json_encode([
			'buy_url' => $buy_url_array[$i],
			'buy_store' => $buy_store_array[$i]
		]);
		$buy_json = str_replace("\\u", "\\\\u", $buy_json);		//防止wp写入数据库时删掉了UTF8转义符号
		update_post_meta( $post_id, $buy_key.'_'.$i, $buy_json);
	}
	update_post_meta( $post_id, $peizhi_key, $peizhi_value );
	update_post_meta( $post_id, $bg_key, $bg_value );
}
add_action( 'save_post', '\Gamux\save_extra_meta_box' );

function add_extra_meta_box() {    
	add_meta_box(
		'extra_meta_box',
		'额外信息',
		'\Gamux\extra_meta_box',
		'post',
		'advanced',
		'default'
	);
}
add_action( 'add_meta_boxes', '\Gamux\add_extra_meta_box' );

/*--------------------------------其他功能-----------------------------------------*/

/**
 * 记录修改历史
 *
 * @param $post_id
 * @return void
 */
function save_edit_historys($post_id) {
	if(empty($_POST))
		return;
	$historys = get_post_meta($post_id, down_var()["history"], true);
	$historys = !empty($historys) ? $historys : [];

	$history = new \stdClass;
	$history->user_id = $_POST['user_ID'];
	$history->post_modified = date_create('now', new \DateTimeZone('Asia/Shanghai'))->format('Y-m-d H:i:s');	
	$history->post_modified_gmt = date_create('now', new \DateTimeZone('UTC'))->format('Y-m-d H:i:s');	
	array_push($historys, $history);
	
	update_post_meta($post_id, down_var()["history"], $historys);
}
add_action('save_post', '\Gamux\save_edit_historys');