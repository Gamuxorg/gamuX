<?php
/* Gmaux WP REST-API route settings */

include("images.php");

//获取首页轮播图片 ，调用get_mainSlide
add_action( 'rest_api_init', function () {
	register_rest_route( 'gamux/v1', '/images/mainslide/(?P<picnum>\d*)', array(
		'methods' => 'GET',
		'callback' => '\Gamux\get_mainSlide',
		'args' => array(
			'picnum' => array(
				'default' => 4,
				'validate_callback' => function($param, $request, $key) {
					return is_numeric($param);
				}
			)
		)
	));
});

?>
