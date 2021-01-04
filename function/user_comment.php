<?php
	namespace Gamux;
	
	/**
	 * class Unread_comment_parent
	 * 对Unread_comment::parents数组成员的描述
	 */
	class Unread_comment_parent {
		public $comment_id;
		public $author_id;
	}

/**
 * class Unread_comment
 * 处理未读评论的添加和删除的class
 */
class Unread_comment {
	private $comment_id;
	private $post_id;
	private $post_author;
	private $post_editor;
	private $parent_id;
	private $parents;		//保存所有父级评论的数组

	public function __construct($comment_id) {
		$this->comment_id = $comment_id;
		$comment = get_comment($comment_id);
		$this->post_id = $comment->comment_post_ID;
		$this->parent_id = $comment->comment_parent;
		$this->parents = array();
	}

	/**
	 * 递归获取所有的父级评论，保存到　$this->parents　数组
	 *
	 * @param $parent_id 父评论id
	 * @return bool
	 */
	private function get_parents($parent_id) {
		if($parent_id == 0)
			return false;
		$parent = new Unread_comment_parent;
		$comment = get_comment($parent_id);
		$parent->comment_id = $parent_id;
		$parent->author_id = $comment->user_id;
		array_push($this->parents, $parent);
		$this->get_parents($comment->comment_parent);
		return true;
	}

	/**
	 * 向某个用户添加提醒
	 *
	 * @param $uid　用户ID
	 * @return void
	 */
	private function notify($uid) {
		$unreadCount = get_user_meta($uid, "unread_comment", true);
		if($unreadCount != "")
			$unreadCount++;
		else
			$unreadCount = 1;
		update_user_meta($uid, "unread_comment", $unreadCount);
	}

	/**
	 * 当评论者与文章的作者不同时，向文章作者添加提醒
	 *
	 * @return bool
	 */
	private function notify_postAuthor() {
		$uid = get_current_user_id();
		$post = get_post($this->post_id);
		$this->post_author = $post->post_author;
		if($post->post_author != $uid) {
			$this->notify($post->post_author);
			return true;
		}
		return false;
	}

	/**
	 * 当评论者与文章的修改者、作者不同时，向文章修改者添加提醒
	 *
	 * @return bool
	 */
	private function notify_postEditor() {
		$uid = get_current_user_id();
		$post = get_post($this->post_id);
		$this->post_editor = get_post_meta($this->post_id, "_edit_last", true);
		if(!empty($this->post_editor) and 
						$this->post_editor != $uid and 
						$this->post_editor != $this->post_author) {
			$this->notify($this->post_editor);
			return true;
		}
		return false;
	}

	/**
	 * 向所有父级评论添加提醒
	 *
	 * @return bool
	 */
	private function notify_parentCommentAuthors() {
		if($this->get_parents($this->parent_id)) {		  		//has parent
			foreach($this->parents as $parent) {
				if($parent->author_id != $this->post_author and 
								$parent->author_id != $this->post_editor)	//防止重复向文章作者、修改者提醒
					$this->notify($parent->author_id);
			}
			return true;
		}
		else
			return false;
	}

	// 添加未读评论提醒主函数
	public function add_unread() {
		$this->notify_postAuthor();
		$this->notify_postEditor();
		$this->notify_parentCommentAuthors();		
	}

	// 清除未读评论数
	static public function clear_unread() {
		$uid = get_current_user_id();
		update_user_meta($uid, "unread_comment", 0);
	}
}

add_action("wp_insert_comment", function($comment_id) {
	$unreadCom = new Unread_comment($comment_id);
	$unreadCom->add_unread();
});

// 用户打开后台评论页面时，清除未读评论数
add_action("manage_comments_nav", "\Gamux\Unread_comment::clear_unread");