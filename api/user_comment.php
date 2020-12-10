<?php
	namespace Gamux;

	/**
	 * 返回未读评论数及评论阅读地址
	 *
	 * @return array
	 */
	function get_user_unread($args) {
		$uid = $args->get_param('user');
		$unreadCount = get_user_meta($uid, "unread_comment", true);
		if($unreadCount == '')
			$unreadCount = 0;
		settype($unreadCount, "int");
		$redirect_url = site_url() . "/wp-admin/edit-comments.php?comment_status=mine&user_id=$uid";
		return [
			"status" => 0,
			"uid" => $uid,
			"unread" => $unreadCount,
			"redirect_url" => $redirect_url
		];
	}

?>