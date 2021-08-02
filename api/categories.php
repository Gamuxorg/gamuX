<?php
	namespace Gamux;

class Categories {
	const news_all_term_taxonomy_id = 255;
	const games_all_term_taxonomy_id = 256;

	public $actual_count;

	public function __construct() {
		$this->actual_count = array(
			self::news_all_term_taxonomy_id => 0,
			self::games_all_term_taxonomy_id => 0
		);
	}

	/**
	 * 获取所有文章的ID和term_taxonomy_id
	 *
	 * @return array
	 */
	private function get_categories() {
		global $wpdb;
		$post_ids = "SELECT ID FROM " . $wpdb->prefix . "posts WHERE post_type = 'post' AND post_status = 'publish' ORDER BY ID";
		$sql = "SELECT object_id AS ID, term_taxonomy_id FROM wp_term_relationships WHERE object_id IN ($post_ids) ORDER BY ID";
		return $wpdb->get_results($sql);		
	}

	/**
	 * 返回一个 cat_id => parent_id 的数组
	 *
	 * @return array $catParents
	 */
	private function get_catParents() : array {
		$categories = \get_categories();
		$catParents = array();

		foreach($categories as $cat) {
			$catParents[$cat->term_id] = $cat->parent;
		}
		
		return $catParents;
	}

	/**
	 * 计算 news_all 和 games_all 两个父母分类下所有文章的数量
	 *
	 * @return void
	 */
	private function calc_parent_type(array $posts) {
		$catParents = $this->get_catParents();

		foreach($posts as $post) {
			foreach($post as $cat_id) {
				// 只要有一个分类命中就提前结束，不再检查其他分类
				if($cat_id == self::news_all_term_taxonomy_id) {
					$this->actual_count[self::news_all_term_taxonomy_id]++;
					break;
				}
				elseif($cat_id == self::games_all_term_taxonomy_id) {
					$this->actual_count[self::games_all_term_taxonomy_id]++;
					break;
				}
				elseif(isset($catParents[$cat_id])) {					// 检查父母分类
					if($catParents[$cat_id] == self::games_all_term_taxonomy_id) {
						$this->actual_count[self::games_all_term_taxonomy_id]++;
						break;
					}
					elseif($catParents[$cat_id] == self::news_all_term_taxonomy_id) {
						$this->actual_count[self::news_all_term_taxonomy_id]++;
						break;
					}
				}
			}
		}
	}

	/**
	 * 主调函数，返回正确的news_all 和 games_all文章数量
	 *
	 * @return array $actual_count
	 */
	public function get_actual_count() : array {
		$term_taxonomy_ids = $this->get_categories();

		// 将属于同一个post的 term_taxonomy_id 合并到同一个数组成员
		$posts = array();
		foreach($term_taxonomy_ids as $entry) {
			if(!isset($posts[$entry->ID]))
				$posts[$entry->ID] = array();
			array_push($posts[$entry->ID], $entry->term_taxonomy_id);
		}

		$this->calc_parent_type($posts);
		return $this->actual_count;
	}

	/**
	 * 返回一个以 category_id 为下标的数组，
	 * 数组成员为逗号分隔的该分类下的所有文章id
	 *
	 * @return array $cat_poist_ids[int] = string
	 */
	public function get_category_posts() :array {
		$term_taxonomy_ids = $this->get_categories();

		$cat_post_ids = [];
		foreach($term_taxonomy_ids as $item) {
			if(!isset($cat_post_ids[$item->term_taxonomy_id]))
				$cat_post_ids[$item->term_taxonomy_id] = "";
			$cat_post_ids[$item->term_taxonomy_id] .= $item->ID . ",";
		}
		return $cat_post_ids;
	}
}

	// 设置正确的父母类文章数，并为每个分类增加 posts 字段，在route.php中调用
	function get_the_categories() {
		$cat = new Categories();
		$actual_count = $cat->get_actual_count();
		$cat_post_ids = $cat->get_category_posts();

		$cats = json_decode(file_get_contents('http://localhost/wp-json/wp/v2/categories?' . $_SERVER["QUERY_STRING"]));
		$len = count($cats);
		for($i = 0; $i < $len; $i++) {
			// 设置正确的news_all 和 games_all文章数量
			if($cats[$i]->id == $cat::news_all_term_taxonomy_id) 
				$cats[$i]->count = $actual_count[$cats[$i]->id];
			elseif($cats[$i]->id == $cat::games_all_term_taxonomy_id)
				$cats[$i]->count = $actual_count[$cats[$i]->id];

			// 为每个分类增加 posts 字段，其值为逗号分隔的该分类下的所有文章id
			if(isset($cat_post_ids[$cats[$i]->id]))
				$cats[$i]->posts = $cat_post_ids[$cats[$i]->id];
		}
		
		return $cats;
	}