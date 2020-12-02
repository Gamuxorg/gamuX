<?php
/* Gmaux WP REST-API route settings */

//扩展 wp-json/wp/v2/post/<post_id>，添加自定义的字段
include("posts_ext.php");

//获取首页轮播图片 ，调用get_mainSlide
//route: wp-json/gamux/v1/images/mainslide/<picnum>?
include("images.php");
add_action( 'rest_api_init', function () {
	register_rest_route( 'gamux/v1', '/images/mainslide(/?)(?P<picnum>\d*)', array(
		'methods' => 'GET',
		'callback' => '\Gamux\get_mainSlide',
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
	register_rest_route( 'gamux/v1', '/oauth/weibo/\?code=(?P<code>\w+)', array(
		'methods' => 'GET',
		'callback' => '\Gamux\weibo_oauth_login_init'
	));
});

?>
