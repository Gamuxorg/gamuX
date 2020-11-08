<?php
	namespace Gamux;
	/**
	 * 为post文章添加额外的REST-API字段
	 * 
	 */

	/**
	 * 获取所有下载链接
	 * TODO： 好像性能不太好，查询时间要2、3秒
	 *
	 * @param $id 文章的id
	 * @return array $downloadList 返回下载链接数组
	 */
	function get_downloadList($id) {
		$downloadList = array();
		$count = (int)gamux_down_count();
		
		for($i = 0; $i < $count; $i++) {
			$listItem = array(
				"version" => get_post_meta($id, gamux_down_var()['title'].'_'.$i)[0],
				"downloadCount" => 0,
				"date" => get_post_meta($id, gamux_down_var()['date'].'_'.$i)[0],
				"fileSize" => remote_file_size(get_post_meta($id, gamux_down_var()['durl'].'_'.$i)[0]),
				"link" => get_post_meta($id, gamux_down_var()['durl'].'_'.$i)[0]
			);
			array_push($downloadList, $listItem);
		}
	
		return $downloadList;
	}

	//添加和注册各个字段
	function add_post_ext() {
		//添加 thumbnail 字段
		register_rest_field("post", 'thumbnail', array(
			"get_callback" => function($args) {
				return get_thumbnail_url($args['id']);
			}
		));

		//添加 images 字段，返回所有图片
		register_rest_field("post", 'images', array(
			"get_callback" => function($args) {
				return get_all_imgs($args['content']['raw']);
			}
		));

		//添加 downloadList 字段，返回所有的下载链接
		register_rest_field("post", 'downloadList', array(
			"get_callback" => function($args) {
				return get_downloadList($args["id"]);
			}
		));

		//一些其他工作
		register_rest_field("post", 'dummyWorks', array(
			"get_callback" => function($args) {
				// setPostViews($args['id']);		//更新文章阅读数
				return 0;
			}
		));
	}

	add_action("rest_api_init", '\Gamux\add_post_ext');

?>