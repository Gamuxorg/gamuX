<?php
	namespace Gamux;

	// 添加特色图片的库
	include('generate_featured_image.php');

/**
 * Class Steam
 * 用于调用Steam 文章API拉取文章信息自动创建新文章的类
 * 
 */
class Steam{
	const delimiter_start = <<<'str'
<!-- wp:paragraph {"align":"center"} -->
<p class="has-text-align-center"><strong class="has-medium-font-size" contenteditable="false">----------------------------------轮播图片---------------------------------------</strong></p>
<!-- /wp:paragraph -->
str;
	const delimiter_end = <<<'str'
<!-- wp:paragraph {"align":"center"} -->
<p class="has-text-align-center"><strong class="has-medium-font-size" contenteditable="false">-----------------------------------文章正文---------------------------------------</strong></p>
<!-- /wp:paragraph -->
str;
	const slide_template = <<<'str'
<!-- wp:gamux/slide-url -->
<slide>@placeholder</slide>
<!-- /wp:gamux/slide-url -->
str;
	const image_template = <<<'str'
<!-- wp:image -->
<figure class="wp-block-image size-large"><img src="@placeholder" alt="" /></figure>
<!-- /wp:image -->
str;
	const video_template = <<<'str'
<!-- wp:video -->
<figure class="wp-block-video"><video controls src="@placeholder"></video></figure>
<!-- /wp:video -->
str;
	const p_template = <<<'str'
<!-- wp:paragraph -->
<p>@placeholder</p>
<!-- /wp:paragraph -->
str;
	const STEAM_LINK = 'https://store.steampowered.com/app/';

	public $appid;
	public $postID;
	public $siteCats;

	public function __construct($appid, $postID) {
		$this->appid = $appid;
		$this->postID = $postID;
		$this->siteCats = [						//这里的name更多的是为了能够match到Steam的分类，和数据库中的不一致
			[ "id" => 256, "name" => "游戏" ],
			[ "id" => 3, "name" => "角色扮演" ],
			[ "id" => 4, "name" => "射击" ],
			[ "id" => 5, "name" => "即时战略" ],
			[ "id" => 5, "name" => "即时战术" ],
			[ "id" => 15, "name" => "模拟" ],
			[ "id" => 15, "name" => "时间管理" ],
			[ "id" => 15, "name" => "建造" ],
			[ "id" => 19, "name" => "视觉小说" ],
			[ "id" => 19, "name" => "互动小说" ],
			[ "id" => 19, "name" => "文字" ],
			[ "id" => 40, "name" => "模拟器" ],
			[ "id" => 43, "name" => "像素" ],
			[ "id" => 44, "name" => "恐怖" ],
			[ "id" => 44, "name" => "僵尸" ],
			[ "id" => 45, "name" => "解谜" ],
			[ "id" => 45, "name" => "编程" ],
			[ "id" => 45, "name" => "探索" ],
			[ "id" => 46, "name" => "沙盒" ],
			[ "id" => 47, "name" => "生存" ],
			[ "id" => 54, "name" => "记叙" ],
			[ "id" => 54, "name" => "叙事" ],
			[ "id" => 54, "name" => "故事" ],
			[ "id" => 54, "name" => "剧情" ],
			[ "id" => 54, "name" => "电影" ],
			[ "id" => 56, "name" => "街机" ],
			[ "id" => 159, "name" => "Rogue" ],
			[ "id" => 193, "name" => "动作" ],
			[ "id" => 223, "name" => "益智" ],
			[ "id" => 239, "name" => "竞速" ],
			[ "id" => 239, "name" => "驾驶" ],
			[ "id" => 242, "name" => "回合" ],
			[ "id" => 336, "name" => "wine" ],
			[ "id" => 365, "name" => "休闲" ],
			[ "id" => 383, "name" => "体育" ],
			[ "id" => 383, "name" => "球" ],
			[ "id" => 383, "name" => "高尔夫" ]
		];
	}

	/**
	 * 向STEAM API请求指定游戏的内容
	 *
	 * @param $appid
	 * @return json
	 */
	private function request_steam_content($appid) {
		return file_get_contents("https://next.linuxgame.cn/steam_header.php?appid=$appid");
	}

	/**
	 * 添加轮播图、视频，格式化正文
	 *
	 * @param object $steamData
	 * @return string
	 */
	private function fix_content($steamData) : string {
		$slides = self::delimiter_start . "\n\n";
		foreach($steamData->screenshots as $imageData) {
			$url = $imageData->path_thumbnail;
			$slides = $slides . str_replace('@placeholder', $url, self::slide_template) . "\n\n";
		}
		$slides = $slides . self::delimiter_end . "\n\n";

		$content = wp_kses_post(wpautop($steamData->detailed_description)) . "\n\n";

		$movies = "";
		foreach($steamData->movies as $movieData) {
			foreach($movieData->webm as $url) {
				$movies = $movies . str_replace('@placeholder', $url, self::video_template) . "\n\n";
				break;		//只获取第一个
			}
		}
		return $slides . $content . $movies;
	}

	/**
	 * 修正配置字段中的HTML标签
	 *
	 * @param string $minimum
	 * @param string $recommended
	 * @return $str3
	 */
	private function fix_peizhi(string $minimum, string $recommended) {
		$str = $minimum . "\n\n" . $recommended;
		$str2 = str_replace("<br>", "\n", $str);
		$str3 = sanitize_textarea_field($str2);
		return $str3;
	}

	/**
	 * 将Steam分类标签映射到站内分类
	 *
	 * @return array $cats 分类的id数组
	 */
	private function map_category($steamCats) : array {
		$cats = [];
		foreach($steamCats as $steamCat) {
			foreach($this->siteCats as $siteCat) {
				if(strpos($steamCat->description, $siteCat['name']) !== false)
					array_push($siteCat['id']);
			}
		}

		if(empty($cats))
			array_push($cats, $this->siteCats[0]['id']);		//默认游戏类别
		array_unique($cats);
		return $cats;
	}

	/**
	 * 下载并添加特色图片
	 *
	 * @param [type] $url 图片链接
	 * @param [type] $postID
	 * @return string
	 */
	private function add_feature_image($url, $postID) {
		// 将链接更换为http
		$imageURL = str_replace("https", "http", $url);
	
		// 添加特色图片
		$attid = generate_featured_image($imageURL, $postID);
		if(is_wp_error($attid))
			return $attid->get_error_messages();
		else
			return "true";
	}

	private function exceptions(int $code, string $message) {
		return [
			'code' => $code,
			'message' => $message
		];
	}

	/**
	 * 主函数，根据Steam appid创建一篇草稿
	 *
	 * @return void
	 */
	public function new_post() {
		$appid = $this->appid;

		$json = $this->request_steam_content($appid);
		if($json != -1) {
			$steamData = json_decode($json)->$appid->data;
		}
		else
			return $this->exceptions(-1, "Failed to fetch Steam post content, please try again later.");

		if($steamData->platforms->linux == false)
			return $this->exceptions(-2, "The Game doesn't not support Linux platform.");

		$content = $this->fix_content($steamData, $appid);
		
		// 分类目录
		$cats = $this->map_category($steamData->categories);

		// 添加购买链接 - Steam
		$buy_json = json_encode([
			'buy_url' => self::STEAM_LINK . $appid . '/',
			'buy_store' => "Steam"
		]);
		$buy_json = str_replace("\\u", "\\\\u", $buy_json);		//防止wp写入数据库时删掉了UTF8转义符号

		// 配置信息
		$minimum = isset($steamData->linux_requirements->minimum) ? $steamData->linux_requirements->minimum : "";
		$recommended = isset($steamData->linux_requirements->recommended) ? $steamData->linux_requirements->recommended : "";
		$peizhi = $this->fix_peizhi($minimum, $recommended);

		// 移除轮播修正钩子
		remove_filter('content_save_pre', '\Gamux\filter_slides_delimiter');

		$postID = wp_insert_post([
			'ID'					=> $this->postID,		//当提供了ID时更新现有文章
			'post_author'           => get_current_user_id(),
			'post_content'          => $content,
			'post_title'            => $steamData->name,
			'post_status'           => empty($this->postID) ? 'draft' : 'publish',
			'post_type'             => 'post',
			'post_category '		=> $cats,
			'meta_input'			=> [
				'buy_url_0' => $buy_json,
				'peizhi' 	=> $peizhi,
				'bg'		=> $steamData->background,
			]
		]);

		if(is_wp_error($postID))
			return $this->exceptions(-3, $postID->get_error_message);

		// 添加购买链接 - 官网
		if(!empty($steamData->website)) {
			$buy_json2 = json_encode([
				'buy_url' => $steamData->website,
				'buy_store' => "官网"
			]);
			$buy_json2 = str_replace("\\u", "\\\\u", $buy_json2);
			update_post_meta($postID, 'buy_url_1', $buy_json2);
		}
		
		// 添加缩略图
		$ret = $this->add_feature_image($steamData->header_image, $postID);

		return $postID;
	}
}

	/**
	 * API 调用函数，调用Steam API创建文章，成功时重定向到新建的页面，失败时返回 [code, message]
	 *
	 * @param $args
	 * @return void | array $ret
	 */
	function steam_init($args) {
		$post_id = isset($_GET['post_id']) ? $_GET['post_id'] : '';
		$steam = new Steam($args->get_param('appid'), $post_id);
		$ret = $steam->new_post();

		if(is_numeric($ret)) {
			//重定向
			$postID = $ret;
			wp_redirect(site_url("wp-admin/post.php?post={$postID}&action=edit"));
		}
		else
			return $ret;
	}