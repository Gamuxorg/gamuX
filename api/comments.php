<?php
	namespace Gamux;

	/**
	 * REST 依据post_id获取评论
	 */

	/**
	 * class Comment_Result
	 * 对返回结果的描述
	 */
	class Comment_Result {
		public $id;
		public $post;
		public $author_id;
		public $author_name;
		public $author_avatar;
		public $date;
		public $content;
		public $parent;
		public $children;
	}

class Comments {
	private $post_id;

	public function __construct($post_id) {
		settype($post_id, 'int');
		$this->post_id = $post_id;
	}

	/**
	 * 将结果进行过滤,去除不必要的字段,并递归添加children
	 *
	 * @param \WP_Comment $comment
	 * @return Comment_Result $newComment
	 */
	private function filter_result(\WP_Comment $comment) {
		$newComment = new Comment_Result;
		$newComment->id = $comment->comment_ID;
		$newComment->post = $comment->comment_post_ID;
		$newComment->author = $comment->user_id;
		$newComment->author_name = $comment->comment_author;
		$newComment->author_avatar = get_user_avatar($comment->user_id);
		$newComment->date = $comment->comment_date;
		$newComment->content = $comment->comment_content;
		$newComment->parent = $comment->comment_parent;
		$newComment->children = array();
		if(count($comment->get_children()) > 0) {			//children是私有成员,需使用函数获取
			$children = $comment->get_children();
			$num = count($children);
			foreach($children as $child) {
				array_push($newComment->children, $this->filter_result($child));
			}
		}
		return $newComment;
	}

	/**
	 * 依据post_id获取评论数组
	 *
	 * @return array $newComments
	 */
	public function retrieve_comments() {
		$post_id = $this->post_id;
		$newComments = array();
		$comments = \get_comments([
			'post_id' => $post_id,
			'hierarchical' => 'threaded',
		]);
		
		if(count($comments) > 0) {
			foreach($comments as $comment) {
				$newComment = $this->filter_result($comment);
				array_push($newComments, $newComment);
			}
		}
		return $newComments;
	}

}//class

	/**
	 * rest 获取评论调用函数
	 *
	 * @param [type] $args
	 * @return array $data
	 */
	function get_comments($args) {
		$post_id = $args->get_param('post');
		$commentObj = new Comments($post_id);
		$data = $commentObj->retrieve_comments();
		return $data;
	}

?>