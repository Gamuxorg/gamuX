<?php
//添加第三方登录的route

add_action('rest_api_init', function () {
	register_rest_route( 'gamux/v1', '/oauth/github', array(
		'methods' => 'GET',
		'callback' => '\Gamux\github_oauth_login_init',
		'permission_callback' => '__return_true'
	));
});

add_action('rest_api_init', function () {
	register_rest_route( 'gamux/v1', '/oauth/weibo', array(
		'methods' => 'GET',
		'callback' => '\Gamux\weibo_oauth_login_init',
		'permission_callback' => '__return_true'
	));
});

add_action('rest_api_init', function () {
	register_rest_route( 'gamux/v1', '/oauth/qq', array(
		'methods' => 'GET',
		'callback' => '\Gamux\qq_oauth_login_init',
		'permission_callback' => '__return_true'
	));
});