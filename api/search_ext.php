<?php
	namespace Gamux;
	/**
	 * 为search搜索结果添加额外的REST-API字段exts
	 * 
	 */

	/**
	 * 添加和注册各个字段
	 * 
	 * 添加 thumbnail 字段，返回缩略图链接
	 * 添加 categories 字段，返回缩略图链接
	 * 添加 version 字段，返回缩略图链接
	 * @return void
	 */
	function search_post($args) {
		// 调用内置的REST API, /wp-json/wp/v2/search
		$request = new \WP_REST_Request('GET', '/wp/v2/search');
		$request->set_query_params($_GET);
		$response = rest_do_request($request);
		$data = $response->get_data();

		// 给每个搜索结果添加exts字段
		foreach($data as &$post) {
			$post["exts"] = array(
				"thumbnail" => get_thumbnail_url($post["id"]),
				"cats" => get_categories(wp_get_post_categories($post["id"])),
				"version" => get_versionInfo($post["id"]),
				"modified" => get_post($post["id"])->post_modified
			);	
		}

		$response->set_data([
			"data" => $data,
			"count" => count($data)
		]);
		return ($response);
	}

?>
