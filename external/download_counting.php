<?php
	namespace Gamux;

	/**
	 * class TbName
	 * 保存三个表名
	 */
	class TbName {
		private $id;
		public $all;
		public $mon;
		public $day;

		function __construct($id) {
			$this->id = $id;
			$this->all = "download_statistics_overall";
			$this->mon = "monthly-" . $id;
			$this->day = "daily-" . $id;
		}
	}
	
	/**
	 * class Time
	 * 保存monthly和daily表所需的member值
	 */
	class Time {
		public $yearMonth;
		public $date;

		function __construct(){
			$this->yearMonth = date("Y-m");
			$this->date = date("Y-m-d");
		}
	}
	
	$id = (int)addslashes($_GET['post_id']);
	if(empty($id)) {
		echo -1;
		exit(-1);
	}

	$redis = new \Redis;
	$redis->connect("localhost");
	$member = new Time;
	$tb = new TbName($id);

	//checking if entry exists, insert all if not
	$value_all = $redis->hGet($tb->all, $id);
	if(!$value_all) {
		$redis->hSet($tb->all, $id, 1);
		$redis->zAdd($tb->mon, array(), 1, $member->yearMonth);
		$redis->zAdd($tb->day, array(), 1, $member->date);
	}
	//insert or update
	else {
		$redis->hIncrBy($tb->all, $id, 1);
		$redis->zIncrBy($tb->day, 1, $member->date);
		$redis->zIncrBy($tb->mon, 1, $member->yearMonth);
	}
	
	$redis->close();
?>