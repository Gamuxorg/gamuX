<?php
	namespace Gamux;

	require_once $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php';
	wp();

	/**
	 * 获取首页的轮播图片，按照上个月下载量排名
	 * 目前暂时先返回随机的图片
	 * 
	 */
	class MainSlidePic {
		private $postTotalCount;
		private $postOffsetArr;
		public $srcs;

		public function __construct() {
			$this->postTotalCount = wp_count_posts()->publish;
			$this->postOffsetArr = array();
			$this->srcs = array();
		}

		/**
		 * 返回一个不重复的随机的文档下标
		 *
		 * @return int $offset
		 */
		private function randOffset() : int {
			do {
				$offset = rand(0, $this->postTotalCount - 1);
			}while(in_array($offset, $this->postOffsetArr));
			
			array_push($this->postOffsetArr, $offset);
			return $offset;
		}

		/**
		 * 获取一个游戏内的所有图片
		 *
		 * @return mixed $imgs || NULL
		 */
		private function newImages() {
			do {
				$offset = $this->randOffset();
				$post = get_posts([
					'numberposts'=>1,
					'offset'=>$offset,
					'category'=> [
						256				//只获取游戏类别
					]
				]);
			}while(empty($post));		//如果offset是非游戏类别时会返回空

			$content = $post[0]->post_content;
			$imgCount = preg_match_all('/<img.*\/>/', $content, $imgs);
			if($imgCount>0){
				return $imgs[0];
			}
			else
				return NULL;			//有些游戏没有图片
		}
		
		/**
		 * 获取一个游戏内的一张随机的图片的URL
		 *
		 * @param Array $imgs
		 * @return bool
		 */
		private function imageSrc(Array $imgs) {
			$arrLen = count($imgs);
			$i = rand(0, $arrLen-1);
			
			$count = preg_match_all('/src=\".*?\"/', $imgs[$i], $tmp);
			if($count > 0){
				$tmp = substr($tmp[0][0], 5);				//删除src="
				$tmp = substr($tmp, 0, strlen($tmp)-1);		//删除最后的"
				array_push($this->srcs, $tmp);
				return true;
			}
			else
				return false;				//有些img标签里没src属性
		}

		/**
		 * 获取制定数量的图片的链接src
		 *
		 * @param int $picNum
		 * @return Array $srcs
		 */
		public function getImageSrcs($picNum = 4) {
			for($i = 0; $i < $picNum; $i++) {
				do {
					$imgs = $this->newImages();
				}while(is_null($imgs));

				do {
					$src = $this->imageSrc($imgs);
				}while(!$src);
			}
			return $this->srcs;
		}
	} //class

	if(isset($_REQUEST['action'])) {
		if(!empty($_REQUEST['action'])) {
			switch($_REQUEST['action']) {
				case "mainslidepic":
					$slides = new MainSlidePic();
					if(!empty($_REQUEST['mainslidepicnum'])) {
						if(is_numeric((int)$_REQUEST['mainslidepicnum'])) {
							$picNum = (int)$_REQUEST['mainslidepicnum'];
							$imgSrcs = $slides->getImageSrcs($picNum);
						}
						else
							$imgSrcs = $slides->getImageSrcs();
					}
					else
						$imgSrcs = $slides->getImageSrcs();
					echo json_encode($imgSrcs);
					break;
				default:
					break;
			}
		}
	}
	else
		echo 0;
?>
