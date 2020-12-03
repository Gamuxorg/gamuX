<?php
/*
 * 第一部分，全局设置
**/

/*
 * 1.1 优化设置
**/
//1.1.1 去除wordpress功能
remove_action( 'wp_head', 'wp_generator' );
remove_action( 'wp_head', 'wlwmanifest_link' );
remove_action( 'wp_head', 'rsd_link' );
//define( 'WP_POST_REVISIONS', 1);//只保存最近两次的版本修订
//define( 'AUTOSAVE_INTERVAL', 30);//每30秒自动保存一次

//1.1.2关闭rss
//默认开启rss

//1.1.3 禁止从s.w.org获取表情和头像
function remove_dns_prefetch( $hints, $relation_type ) {
    if ( 'dns-prefetch' === $relation_type ) {
        return array_diff( wp_dependencies_unique_hosts(), $hints );
    }
    return $hints;
}
add_filter( 'wp_resource_hints', 'remove_dns_prefetch', 10, 2 );

//1.1.4关闭链接猜测功能
add_filter('redirect_canonical', 'stop_guessing');
function stop_guessing($url) {
	if (is_404()) {
		return false;
	}
	return $url;
}

//1.1.5去掉emoji加载
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
add_filter( 'emoji_svg_url', '__return_false' );

//1.1.6移除admin-bar
function southbase_remove_admin_bar(){
   return false;
}
add_filter( 'show_admin_bar' , 'southbase_remove_admin_bar');

//1.1.7移除仪表盘某些组件
remove_action('welcome_panel', 'wp_welcome_panel');//欢迎面板
function remove_screen_options() {//显示选项选项卡 
	return false;
}
add_filter('screen_options_show_screen', 'remove_screen_options');
function wpse50723_remove_help($old_help, $screen_id, $screen){//帮助选项卡
	$screen->remove_help_tabs();
	return $old_help;
}
add_filter( 'contextual_help', 'wpse50723_remove_help', 999, 3 );
function gamux_remove_dashboard_widgets() {   
    global $wp_meta_boxes;    
    //删除 "快速发布" 模块  
    unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_quick_press']);                         
    // 以下这一行代码将删除 "WordPress 开发日志" 模块  
    unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_primary']);     
    // 以下这一行代码将删除 "其它 WordPress 新闻" 模块  
    unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_secondary']);       
}  
add_action('wp_dashboard_setup', 'gamux_remove_dashboard_widgets' );

//1.1.8 恢复友链功能
add_filter('pre_option_link_manager_enabled','__return_true');   
/*
 * 1.2 修改wordpress功能
**/
//1.2.1修改自带jq调用规则

function my_init_method() {
  if ( !is_admin() )  // 后台不禁止
    wp_deregister_script( 'jquery' ); // 取消原有的 jquery 定义
  wp_deregister_script( 'l10n' );
}
add_action('wp_enqueue_scripts', 'my_init_method');

//1.2.2 获取当前页面url
function curPageURL()
{
    $pageURL = 'http';
    if ($_SERVER["HTTPS"] == "on")
    {
        $pageURL .= "s";
    }
    $pageURL .= "://";
    if ($_SERVER["SERVER_PORT"] != "80")
    {
        $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
    }
    else
    {
        $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
    }
    return $pageURL;
}
/*
 * 1.3 增加wordpress功能
**/
//1.3.1 增加github登陆
// get_template_part( 'function/github_login' ); 

//1.3.2 增加编辑文章扩展功能
// get_template_part( 'function/edit_extra_box' );

//1.3.3 上传文件重命名 
/*function rename_upload_file($file) {
    $time=date("Y-m-d H:i:s");
    $file['name'] = $time."".mt_rand(100,999).".".pathinfo($file['name'] , PATHINFO_EXTENSION);
    return $file;
}
add_filter('wp_handle_upload_prefilter', 'rename_upload_file');*/

//1.3.4 修改上传目录
function slider_upload_dir($uploads) {
    $siteurl = get_option( 'siteurl' );
    $uploads['path'] = WP_CONTENT_DIR . '/uploads';
    $uploads['url'] = $siteurl . '/wp-content/uploads';
    $uploads['subdir'] = '';
    $uploads['basedir'] = $uploads['path'];
    $uploads['baseurl'] = $uploads['url'];
    $uploads['error'] = false;
    return $uploads;
}
add_filter('upload_dir', 'slider_upload_dir');

//1.3.5 增加和注销可上传类型
add_filter('upload_mimes', 'custom_upload_mimes');
function custom_upload_mimes ( $existing_mimes=array() ) {
	$existing_mimes['xz'] = 'application/x-xz';
	return $existing_mimes;
}

//1.3.6 获取特色图片的地址
function get_thumbnail_url($id) {
  $thumbid = get_post_thumbnail_id($id);
  $func = wp_get_attachment_image_src($thumbid);
  if($func)
    return $func[0];
  else
    return "https://avatars3.githubusercontent.com/u/4121607";
}
if (function_exists('add_theme_support')) {
  add_theme_support('post-thumbnails');
  set_post_thumbnail_size(315, 147, true);
}

//1.3.7 自定义url
//自定义页面模板
/*function loadCustomTemplate($template) {
	global $wp_query;
	if(!file_exists($template))return;
	$wp_query->is_page = true;
	$wp_query->is_single = false;
	$wp_query->is_home = false;
	$wp_query->comments = false;
	// if we have a 404 status
	if ($wp_query->is_404) {
		// set status of 404 to false
		unset($wp_query->query["error"]);
		$wp_query->query_vars["error"]="";
		$wp_query->is_404=false;
	}
	// change the header to 200 OK
	header("HTTP/1.1 200 OK");
	//load our template
	include($template);
	exit;
}
function templateRedirect() {
	$basename = basename($_SERVER['REQUEST_URI'], '?' . $_SERVER['QUERY_STRING']);
	loadCustomTemplate(TEMPLATEPATH.'/gamux/'."/$basename.php");
}
add_action('template_redirect', 'templateRedirect');*/
//1.3.7 统计已发布文章数

/*
 * 1.4 Rest API 修改
*/
//1.4.1 增加posts API里的缩略图字段
/*
add_action( 'rest_api_init', 'slug_register_starship' );
function slug_register_starship() {
    register_rest_field( 'post', 'gmeta', array(
		'get_callback'    => 'rest_post_gmeta',
		'update_callback' => null,
		'schema'          => null
        )
    );
}
*/
function list_the_tags() {
	global $post;
	//输出tag列表
	$a = wp_get_post_tags($post->ID);
	$b = count($a);
	$c = [];
	if ($b == 0) {
		return '未设置标签';
	}
	else {
		for( $d=0; $d<$b; $d++ ) {
			$c[$d] = $a[$d]->name;
		}
		return $c;
	}
}
/*function rest_post_gmeta() {
	global $post;
	$a[0] = get_thumbnail_url($post->ID);
	$a[1] = downlist_array();
	$a[2] = get_the_category()[0]->cat_name;
  $a[3] = get_the_category()[0]->count;
	$a[4] = list_the_tags();
  $a[5] = get_the_author();
  return $a;
}*/

/*
 * 第二部分，前台
 */
//2.1 获取文章内图片
//2.1.1 获取所有图片
function get_all_imgs($content){
  $pattern = '/<img[^>]*src=\"([^\"]+)\"[^>]*\/?>/si';
  $matches = array();
  $out = '';
  if (preg_match_all($pattern, $content, $matches)) {
    if (count($matches[1]) == 1) {
      return $matches[1][0]; 
    }
    else {
      $out = array();
      for ($i = 0; $i < count($matches[1]); $i++) {        
        array_push($out, $matches[1][$i]);
      }
      return $out;
    }
  } 
  else {
     return "";
  }
}
add_action( 'after_setup_theme', 'default_attachment_display_settings' );
function default_attachment_display_settings() {
	update_option( 'image_default_align', 'center' );
	update_option( 'image_default_link_type', 'none' );
	update_option( 'image_default_size', 'full' );
}

//2.1.2 获取第一张图片
function get_first_img($content){
    $pattern = '/<img[^>]*src=\"([^\"]+)\"[^>]*\/?>/si';
    $matches = array();
    if (preg_match_all($pattern, $content, $matches)) {
        return '<div class="col-10 gamux-2-excerptimg"><img class="d-block w-100" src="' . $matches[1][0] . '" alt="1"></div>'; 
    } 
    else {
       return "";
    }
  }

//2.2 设置并获取文章阅读数
function getPostViews($postID){
    $count_key = 'post_views';
    $count = get_post_meta($postID, $count_key, true);
    if($count == '' || $count == null){
        delete_post_meta($postID, $count_key);
        add_post_meta($postID, $count_key, 0);
        return 0;
    }
    return $count;
}
function setPostViews($postID) {
    $count_key = 'post_views';
    $count = get_post_meta($postID, $count_key, true);
    if($count == '' || $count == null){
        $count = 0;
        delete_post_meta($postID, $count_key);
        add_post_meta($postID, $count_key, 0);
    }else{
        $count++;
        update_post_meta($postID, $count_key, $count);
    }
}

//2.3 设置摘要数字和结尾形式
function new_excerpt_length($length) {
    return 180;
    }
    add_filter("excerpt_length", "new_excerpt_length");

function new_excerpt_more($excerpt) {
    return "....";
    }
    add_filter("excerpt_more", "new_excerpt_more");

//2.4 增加文章模板
get_template_part( 'template/config' );

//添加 REST-API 路由
include("api/route.php");

//添加post额外字段的支持
include('function/edit_extra_box.php');

//添加第三方登录
include('function/oauth2_route.php');
// include('function/github_login.php');
include('function/weibo_login.php');
include('function/qq_login.php');
