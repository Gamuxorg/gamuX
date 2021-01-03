<?php
	namespace Gamux;
	/**
	 * 为users 用户信息添加额外的REST-API字段avatar
	 * 
	 */	

	/**
	 * 根据用户登录名，判断用户使用哪种登录方式
	 *
	 * @param [type] $uid
	 * @return string
	 */
	function get_user_login_type($uid) : string {
		$login_name = get_userdata($uid)->get("user_login");
		$type_prefix = [
			"github" => "/^github_/",
			"weibo" => "/^weibo_/",
			"qq" => "/^qq_/"
		];
		foreach($type_prefix as $type => $prefix) {
			if(preg_match($prefix, $login_name))
				return $type;
		}
		return "null";			//no match
	}

	/**
	 * 获取用户的头像链接
	 *
	 * @param [type] $uid
	 * @return string $avatar
	 */
	function get_user_avatar($uid) : string {
		$type = get_user_login_type($uid);
		switch($type) {
			case "github":
				$github_id = get_user_meta($uid, 'github_id', true);
				$avatar = "https://kr.linuxgame.cn:8088/git_avatar.php?id=$github_id";
				break;
			case "weibo":
				$avatar = get_user_meta($uid, "weibo_avatar", true);
				break;
			case "qq":
				$avatar = get_user_meta($uid, "qq_avatar", true);
				break;
			default:
				$avatar = "";
		}
		return $avatar;
	}

	/**
	 * 为users 用户信息添加的REST-API字段avatar，和退出登陆链接logout_url
	 *
	 * @return string $link
	 */
	function add_user_ext() {
		register_rest_field("user", "avatar", array(
			"get_callback" => function($args) {
				$link = get_user_avatar($args['id']);
				return $link;
			}
		));
		register_rest_field("user", "logout_url", array(
			"get_callback" => function($args) {
				return html_entity_decode(wp_logout_url(home_url()));
			}
		));
	}

	add_action("rest_api_init", '\Gamux\add_user_ext');