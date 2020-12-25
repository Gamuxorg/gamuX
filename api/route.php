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

add_action('rest_api_init', function () {
	register_rest_route( 'gamux/v1', '/test', array(
		'methods' => 'GET',
		'callback' => function(WP_REST_Request $request){
			
			wp_die(new \WP_Error(500, "致命错误"));
			// return 0;
		},
		'permission_callback' => "__return_true"
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


?>
