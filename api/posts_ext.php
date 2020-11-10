<?php
	namespace Gamux;
	/**
	 * 为post文章添加额外的REST-API字段exts
	 * 
	 */

	/**
	 * 获取所有下载链接
	 * @TODO： 获取文件大小fileSize的速度比较慢，有时候会卡在这里，建议调整到前端进行
	 *
	 * @param $id 文章的id
	 * @return array $downloadList 返回下载链接数组
	 */
	function get_downloadList($id) {
		$downloadList = array();
		$count = gamux_down_count();
		$gamux_down = gamux_down_var();

		if($count > 0) {
			for($i = 0; $i < $count; $i++) {
				$listItem = array(
					"version" => get_post_meta($id, $gamux_down['title'].'_'.$i)[0],
					"downloadCount" => 0,
					"date" => get_post_meta($id, $gamux_down['date'].'_'.$i)[0],
					// "fileSize" => remote_file_size(get_post_meta($id, $gamux_down['durl'].'_'.$i)[0]),
					"link" => get_post_meta($id, $gamux_down['durl'].'_'.$i)[0]
				);
				array_push($downloadList, $listItem);
			}
		}
	
		return $downloadList;
	}

	/**
	 * 添加和注册各个字段
	 * 
	 * 添加 thumbnail 字段，返回缩略图链接
	 * 添加 images 字段，返回所有图片
	 * 添加 downloadList 字段，返回所有的下载链接
	 * 添加 INT postViews 字段，返回文章阅读数
	 * 添加 buyUrl 字段，返回购买链接
	 * 添加 authorName 字段，返回作者
	 * 添加 modAuthorName 字段，返回修改作者
	 * 添加 tagList 字段，返回文章标签列表
	 * 添加 sysRequirements 字段，返回配置信息
	 * 添加 BOOL isUserLogin 字段，判断用户是否登陆
	 * @return void
	 */
	function add_post_ext() {
		register_rest_field("post", "exts", array(
			"get_callback" => function($args) {
				// setPostViews($args['id']);		//更新文章阅读数
				update_post_caches($posts);
				return array(
					"thumbnail" => get_thumbnail_url($args['id']),
					"images" => get_all_imgs($args['content']['raw']),
					"downloadList" => get_downloadList($args["id"]),
					"postViews" => (int)getPostViews($args['id']),
					"buyUrl" => get_post_meta($args['id'], 'buy_url')[0],
					"authorName" => get_the_author(),
					"modAuthorName" => get_the_modified_author(),
					"tagList" => get_the_tag_list(),
					"sysRequirements" => get_post_meta($args['id'], 'peizhi')[0],
					"isUserLogin" => is_user_logged_in()
				);
			}
		));
	}

	add_action("rest_api_init", '\Gamux\add_post_ext');

?>