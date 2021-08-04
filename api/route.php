<?php
/* Gmaux WP REST-API route settings */

//扩展 wp-json/wp/v2/posts/<post_id>，添加自定义的字段
include("posts_ext.php");

//扩展 wp-json/wp/v2/users/<user_id>，添加未阅读文章数
include("user_comment.php");

//获取首页轮播图片 ，调用get_mainSlide
//route: wp-json/gamux/v1/images/mainslide/<picnum>?
include("images.php");
add_action( 'rest_api_init', function () {
	register_rest_route( 'gamux/v1', '/images/mainslide(/?)(?P<picnum>\d*)', array(
		'methods' => 'GET',
		'callback' => '\Gamux\get_mainSlide',
		'permission_callback' => "__return_true",
		'args' => array(
			'picnum' => array(
				'default' => 4,
				'validate_callback' => function($param, $request, $key) {
					if(!empty($param))
						return is_numeric($param);
					else
						return true;
				}
			)
		)
	));
});

//获取评论，输入post_id，调用get_comments
//route: wp-json/gamux/v1/comments/(?P<post>\d+)
include("comments.php");
add_action( 'rest_api_init', function () {
	register_rest_route( 'gamux/v1', '/comments/(?P<post>\d+)', array(
		'methods' => 'GET',
		'callback' => '\Gamux\get_comments',
		'permission_callback' => "__return_true",
		'args' => array(
			'post' => array(
				'validate_callback' => function($param, $request, $key) {
					if(!empty($param))
						return is_numeric($param);
					else
						return true;
				}
			)
		)
	));
});

//获取用户未读评论数，输入user_id
// route: wp-json/gamux/v1/comments/unread/(?P<user>\d+)
include_once("user_comment.php");
add_action( 'rest_api_init', function () {
	register_rest_route( 'gamux/v1', '/comments/unread/(?P<user>\d+)', array(
		'methods' => 'GET',
		'callback' => '\Gamux\get_user_unread',
		'permission_callback' => "__return_true",
		'args' => array(
			'user' => array(
				'validate_callback' => function($param, $request, $key) {
					if(!empty($param))
						return is_numeric($param);
					else
						return true;
				}
			)
		)
	));
});

// 根据Steam appid 拉取Steam游戏信息，创建一篇文章，带上GET查询参数?post_id时更新对应文章
// route: wp-json/gamux/v1/steam/(?P<appid>\d+)?post_id=\d*
include("steam_post.php");
add_action('rest_api_init', function () {
	register_rest_route( 'gamux/v1', '/steam/(?P<appid>\d+)', array(
		'methods' => 'GET',
		'callback' => '\Gamux\steam_init',
		'permission_callback' => "__return_true",
		'args' => array(
			'appid' => array(
				'validate_callback' => function($param, $request, $key) {
					if(!empty($param))
						return is_numeric($param);
					else
						return true;
				}
			)
		)
	));	
});

// 搜索API
// route: wp-json/gamux/v1/search
// 查询参数请参考WP REST-API search部分文档
include("search_ext.php");
add_action('rest_api_init', function () {
	register_rest_route( 'gamux/v1', '/search(/?)', array(
		'methods' => 'GET',
		'callback' => '\Gamux\search_post',
		'permission_callback' => "__return_true"
	));	
});

// 下载统计信息API
// route: wp-json/gamux/v1/download
include("get_download_count.php");
add_action('rest_api_init', function () {
	register_rest_route( 'gamux/v1', '/download(/?)', array(
		'methods' => 'GET',
		'callback' => '\Gamux\get_download_count',
		'permission_callback' => function() {		//需要限制请求
			return true;
			// return current_user_can("manage_options");
		}
	));	
});

// 分类信息API，扩展的内置的API
// route: wp-json/gamux/v1/categories(/?P<catid>\d+)
// 查询参数请参考WP REST-API categories部分文档
include("categories.php");
add_action('rest_api_init', function () {
	register_rest_route( 'gamux/v1', '/categories(/?|/(?P<catid>\d+))', array(
		'methods' => 'GET',
		'callback' => '\Gamux\get_the_categories',
		'permission_callback' => function() {
			return true;
		},
		'args' => array(
			'catid' => array(
				'validate_callback' => function($param, $request, $key) {
					if(!empty($param))
						return is_numeric($param);
					else
						return true;
				}
			)
		)
	));	
});

?>
