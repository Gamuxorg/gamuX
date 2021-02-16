<?php
	namespace Gamux;

	/**
	 * 获取所有文章的ID和title
	 *
	 * @return array
	 */
	function get_post_titles() {
		global $wpdb;
		$sql = "SELECT ID, post_title FROM " . $wpdb->prefix . "posts WHERE post_type = 'post' AND post_status = 'publish' ORDER BY ID;";
		return $wpdb->get_results($sql);		
	}

	/**
	 * API函数，获取下载数据，添加文章title，并进行排序
	 *
	 * @return array $response
	 */
	function get_download_count() {
		$response = json_decode(file_get_contents("https://kr.linuxgame.cn:8088/get_download_data.php?" . $_SERVER["QUERY_STRING"]), true);
		if($response['code'] == 0) {
			$data = array();
			ksort($response['data']);
			$i = 0;
			$posts_title = \Gamux\get_post_titles();
			// mapping id and post_title
			foreach($response['data'] as $key => $value) {
				while($i < count($posts_title) && $key > $posts_title[$i]->ID) {
					$i++;
				}
				if($i < count($posts_title) && $key == $posts_title[$i]->ID) {
					array_push($data, [
						"ID" => $posts_title[$i]->ID,
						"title" => $posts_title[$i]->post_title,
						"count" => (int)$value
					]);
				}
			}

			// insert sort by count
			$max = count($data);
			$i = 1;
			while($i < $max) {
				if($data[$i-1]['count'] > $data[$i]['count']) {
					$tmp = $data[$i];
					$k = $i-1;
					while($k >= 0 && $data[$k]['count'] > $tmp['count']) {
						$data[$k+1] = $data[$k];
						$k--;
					}
					$data[$k+1] = $tmp;
				}
				$i++;
			}

			$response['data'] = $data;
		}
		
		return $response;
	}
	
?>
