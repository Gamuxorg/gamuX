<?php
	namespace Gamux;
	/**
	 * 为post文章添加额外的REST-API字段exts
	 * 
	 */

	/**
	 * 检查用户是否使用Linux平台
	 *
	 * @return bool
	 */
	function is_user_linux() {
		$agent = $_SERVER['HTTP_USER_AGENT'];
		return preg_match('/linux/i', $agent) ? true : false;
	}

	/**
	 * 检查链接是否网盘链接
	 * @TODO 完善网盘链接
	 * 
	 * @return bool
	 */
	function is_netdisk_link(string $link) {
		$netdiskHost = array(
			"baidu.com",
			"weiyun.com",
			"189.cn",
			"yandex.com",
			"jianguoyun.com",
			"onedrive.com",
			"huawei.com",
			"lanzou.com",
			"ctfile.com"
		);
		foreach($netdiskHost as $host) {
			$ret = preg_match("/$host/i", $link);
			if($ret)
				return true;
		}
		return false;
	}
	
	/**
	 * 获取所有下载链接，未登录的用户不显示，非linux用户只开放网盘链接
	 * @TODO： 获取文件大小fileSize的速度比较慢，有时候会卡在这里，建议调整到前端进行
	 *
	 * @param $id 文章的id
	 * @return array $retVal 返回status, message, downloadList下载链接数组，
	 * 					     .staus: 成功获取链接时返回1,没有下载链接时返回0，windows用户但是没有网盘链接时返回-2
	 */
	function get_downloadList($id) {
		$downloadList = array();
		$count = gamux_down_count();
		$gamux_down = down_var();

		if($count > 0) {				//有下载链接
			for($i = 0; $i < $count; $i++) {
				$listItem = array(
					"version" => get_post_meta($id, $gamux_down['title'].'_'.$i, true),
					"downloadCount" => 0,
					"date" => get_post_meta($id, $gamux_down['date'].'_'.$i, true),
					// "fileSize" => remote_file_size(get_post_meta($id, $gamux_down['durl'].'_'.$i, true)),
					"link" => get_post_meta($id, $gamux_down['durl'].'_'.$i, true),
					"comment" => get_post_meta($id, $gamux_down['comment'].'_'.$i, true)
				);
				if(is_user_linux())
					array_push($downloadList, $listItem);
				else{
					if(is_netdisk_link($listItem['link']))
						array_push($downloadList, $listItem);
				}
			}

			if(count($downloadList) == 0) {							//用户为windows，且没有网盘链接，只有直链
				$retVal = array(
					"status" => -2,
					"message" => "请使用Linux平台访问本站以获取下载链接"		
				);
			}
			else {													//用户为 win/Linux，且获取到了可用链接
				$retVal = array(
					"status" => 1,
					"message" => "成功获取下载链接",
					"downloadList" => $downloadList
				);
			}
		}
		else {
			$retVal = array(
				"status" => 0,
				"message" => "无可用下载链接"
			);
		}
		return $retVal;
	}

	/**
	 * 根据购买链接返回商店平台的类型，其他类型返回 "Etc"
	 *
	 * @param string $link
	 * @return string $store
	 */
	function buy_url_type(string $link) : string {
		$types = [
			"Steam" => "store.steampowered.com",
			"GOG" => "www.gog.com"
		];

		foreach ($types as $store => $url) {
			if(strstr($link, $url) != "")
				return $store;
		}
		return "Etc";
	}

	/**
	 * 获取购买链接数组
	 *
	 * @param $id
	 * @return array $buyList
	 */
	function get_buy_urls($id) {
		$count = gamux_buyurl_count();	
		$buyList = array();
		for($i=0; $i < $count; $i++) {
			$link = get_post_meta($id, 'buy_url_'.$i, true);
			$store = buy_url_type($link);
			array_push($buyList, [
				"store" => $store,
				"link" => $link
			]);
		}
		return $buyList;
	}

	/**
	 * 获取文章分类信息
	 *
	 * @param array $category_ids
	 * @return array $categories
	 */
	function get_categories(array $category_ids) {
		$categories = array();
		foreach($category_ids as $id) {
			$cat = \get_category($id);
			array_push($categories, [
				"cat_id" => $cat->cat_ID,
				"name" => $cat->name,
				"slug" => $cat->slug
			]);
		}
			
		return $categories;
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
					"downloadList" => get_downloadList($args['id']),
					"postViews" => (int)getPostViews($args['id']),
					"buyUrls" => get_buy_urls($args['id']),
					"categories" => get_categories($args['categories']),
					"authorName" => get_the_author(),
					"modAuthorName" => get_the_modified_author(),
					"tagList" => get_the_tag_list(),
					"sysRequirements" => get_post_meta($args['id'], 'peizhi', true),
				);
			}
		));
	}

	add_action("rest_api_init", '\Gamux\add_post_ext');

?>