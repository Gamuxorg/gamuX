<?php
	namespace Gamux;

	/**
	 * 获取首页的轮播图片，按照上个月下载量排名
	 * 目前暂时先返回随机的图片
	 * 
	 */
	class MainSlidePic {
		private $postTitles;		//post ID和title
		public $posts;				//返回 postLink + src

		public function __construct() {
			$this->posts = array();
			$this->postTitles = get_post_titles();
		}

		/**
		 * 返回一个不重复的随机的文档ID
		 *
		 * @return mixed $id || false 
		 */
		private function randID() : int {
			if(count($this->postTitles) > 0)
				$index = random_int(0, count($this->postTitles) -1);
			else
				return false;

			$id = $this->postTitles[$index]->ID;
			array_splice($this->postTitles, $index, 1);
			return $id;
		}

		/**
		 * 随机获取一个游戏内的所有图片
		 *
		 * @return mixed $post0 || false
		 */
		private function newImages() {
			do {
				if(($id = $this->randID()) === false) {
					continue;
				}
				$post = get_posts([
					'include' => [$id],
					'numberposts'=> 1,
					'category'=> [
						256, 112					//只获取游戏类别
					]
				]);
			}while(empty($post));					//如果id是非游戏类别时会返回空

			$post0 = new \stdClass();
			$post0->id = $post[0]->ID;
			$post0->imgs = [];

			$content = $post[0]->post_content;
			$imgCount = preg_match_all('/<slide>.+?<\/slide>/', $content, $imgs);
			if($imgCount > 0) {
				$post0->imgs = $imgs[0];
			}

			if(isset($post0) and count($post0->imgs) > 0)
				return $post0;
			else
				return false;			//有些游戏没有图片
		}
		
		/**
		 * 获取一个游戏内的一张随机的图片的URL
		 *
		 * @param array $imgs
		 * @return mixed $tmp || false
		 */
		private function imageSrc(array $imgs) {
			$arrLen = count($imgs);
			$i = random_int(0, $arrLen-1);
			
			$count = preg_match_all('/http.+</', $imgs[$i], $tmp);
			if($count > 0) {
				$tmp = substr($tmp[0][0], 0, -1);		//删除<
				return $tmp;
			}
			else
				return false;				//有些img标签里没src属性
		}

		/**
		 * 获取指定数量的图片的链接src及其文章链接
		 *
		 * @param int $picNum
		 * @return array $posts || false
		 */
		public function getImageSrcs($picNum = 4) {
			$i = 0;
			while($i < $picNum) {
				// 随机获取一个游戏内的所有图片
				do {
					$post = $this->newImages();
				}while($post === false);

				// 从上面获取的游戏内随机选取一张的图片的URL
				$src = $this->imageSrc($post->imgs);
				if($src === false) 
					continue;

				array_push($this->posts, array(
					"id" => $post->id,
					"postLink" => get_permalink($post->id),
					"imageSrc" => $src
				));
				$i++;
			}
			return $this->posts;
		}
	} //class

	//获取首页的轮播图片，在route.php中调用
	function get_mainSlide(\WP_REST_Request $request) {
		$picNum = (int)$request->get_param('picnum');
		$slides = new MainSlidePic();
		if(!empty($picNum)) 
			$imgSrcs = $slides->getImageSrcs($picNum);
		else
			$imgSrcs = $slides->getImageSrcs();	
		
		if(!is_array($imgSrcs)) {
			if($imgSrcs === false)
				return [
					"status" => -1,
					"message" => "无法获取足够数量的图片，请尝试减少请求数"
				];
			else
				return [
					"status" => -2,
					"message" => "未知错误"
				];
		}
		else
			return [
				"status" => 0,
				"message" => "成功",
				"data" => $imgSrcs
			];
	}
?>
