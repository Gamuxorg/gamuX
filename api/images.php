<?php
	namespace Gamux;
	/**
	 * 获取首页的轮播图片，按照上个月下载量排名
	 * 目前暂时先返回随机的图片
	 * 
	 */
	class MainSlidePic {
		const MaxRetryTime = 1000;	//防止无限循环

		private $postMaxId;
		private $post_idUsedCount;
		public $post_idUsed;		//已经使用或判定无效的文章id
		public $srcs;
		public $post_ids;			//最终使用的文章id
		public $posts;				//返回 postLink + src

		public function __construct() {
			$this->postMaxId = wp_get_recent_posts([
				'numberposts' => 1, 
				'post_status' => 'publish'
			])[0]['ID'];							//获取文章中最大的ID
			$this->post_idUsedCount = 0;
			$this->post_idUsed = array();
			$this->srcs = array();
			$this->post_ids = array();
			$this->posts = array();
		}

		/**
		 * 返回一个不重复的随机的文档ID
		 *
		 * @return mixed $id || false 
		 */
		private function randID() : int {
			$retry = 0;
			do {
				$id = rand(1, $this->postMaxId);
				if($retry > self::MaxRetryTime)				//防止无限循环
					return false;
			}while(in_array($id, $this->post_idUsed));
			
			array_push($this->post_idUsed, $id);
			$this->post_idUsedCount++;
			return $id;
		}

		/**
		 * 获取一个游戏内的所有图片
		 *
		 * @return mixed $post || false
		 */
		private function newImages() {
			do {
				if(($id = $this->randID()) === false) {
					return false;
				}
				while(\get_post_type($id) != 'post')
					$id = $this->randID();
				$post = get_posts([
					'numberposts'=> 1,
					'offset'=> $id,
					'category'=> [
						1, 256					//只获取游戏类别
					]
				]);
			}while(empty($post));			//如果offset是非游戏类别时会返回空
			
			$content = $post[0]->post_content;
			$imgCount = preg_match_all('/<img.*\/>/', $content, $imgs);
			if($imgCount > 0) {
				$post = new \stdClass();
				$post->id = $id;
				$post->imgs = $imgs[0];
				return $post;
			}
			else
				return NULL;			//有些游戏没有图片
		}
		
		/**
		 * 获取一个游戏内的一张随机的图片的URL
		 *
		 * @param Array $imgs
		 * @return mixed $tmp || false
		 */
		private function imageSrc(Array $imgs) {
			$arrLen = count($imgs);
			$i = rand(0, $arrLen-1);
			
			$count = preg_match_all('/src=\".*?\"/', $imgs[$i], $tmp);
			if($count > 0){
				$tmp = substr($tmp[0][0], 5);				//删除src="
				$tmp = substr($tmp, 0, strlen($tmp)-1);		//删除最后的"
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
		public function getImageSrcs($picNum = 4) : array {
			for($i = 0; $i < $picNum; $i++) {
				do {
					$post = $this->newImages();
					if(!is_array($post))
						if($post === false)
							return false;
				}while(is_null($post));
				array_push($this->post_ids, $post->id);

				do {
					$src = $this->imageSrc($post->imgs);
				}while($src === false);
				array_push($this->srcs, $src);

				array_push($this->posts, array(
					"postLink" => get_permalink($this->post_ids[$i]),
					"imageSrc" => $this->srcs[$i]
				));
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
