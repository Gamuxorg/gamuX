<?php
	namespace Gamux;
	/**
	 * 从Redis数据库中获取下载统计信息
	 */

	/**
	 * class TbName
	 * 保存三个表名
	 */
	class TbName {
		public $id;
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
	
	// 获取单个文章单日的下载量
	function get_daily_postsingle($day) {
		global $redis;
		global $tb;
		return (int)$redis->zScore($tb->day, $day);
	}

	// 获取单个文章所有单日的下载量
	function get_daily_postall() {
		global $redis;
		global $tb;
		$result = $redis->zRange($tb->day, 0, -1, $withscores = true);
		krsort($result, SORT_NATURAL);
		return $result;
	}

	// 获取所有文章某一天的下载量
	function get_daily_all_singleDay($date) {
		global $redis;
		$overall = get_overall_all();
		krsort($overall, SORT_NATURAL);
		$result = [];
		foreach($overall as $id => $value) {
			$count = $redis->zScore((new TbName($id))->day, $date);
			if($count)
				$result[$id] = $count;
		}
		return $result;
	}

	// 获取单个文章单个月的下载量
	function get_monthly_postsingle($month) {
		global $redis;
		global $tb;
		return (int)$redis->zScore($tb->mon, $month);
	}

	// 获取单个文章所有月份的下载量
	function get_monthly_postall() {
		global $redis;
		global $tb;
		$result = $redis->zRange($tb->mon, 0, -1, $withscores = true);
		krsort($result, SORT_NATURAL);
		return $result;
	}

	// 获取所有文章某个月的下载量
	function get_monthly_all_singleMon(string $yearMon) {
		global $redis;
		$overall = get_overall_all();
		krsort($overall, SORT_NATURAL);
		$result = [];
		foreach($overall as $id => $value) {
			$count = $redis->zScore((new TbName($id))->mon, $yearMon);
			if($count)
				$result[$id] = $count;
		}
		return $result;
	}

	// 按月份获取所有文章的年度下载量
	function get_yearly_all(string $year) {
		global $redis;
		$overall = get_overall_all();
		krsort($overall, SORT_NATURAL);
		$result = [];
		for($i=1; $i <= 12; $i++) {
			$mon = ($i < 10) ? '0'.$i : $i;
			$yearMon = "$year-$mon";

			// 计算单月所有文章下载量
			$monthlyCount = 0;
			foreach($overall as $id => $value) 
				$monthlyCount += $redis->zScore((new TbName($id))->mon, $yearMon);

			array_push($result, [
				"name" => $i . "月",
				"value" => $monthlyCount
			]);
		}
		return $result;
	}

	// 获取单个文章的总下载量
	function get_overall_single() {
		global $redis;
		global $tb;
		return (int)$redis->hGet($tb->all, $tb->id);
	}

	// 获取所有文章的总下载量
	function get_overall_all() {
		global $redis;
		global $tb;
		return $redis->hGetAll($tb->all);
	}

	// 获取单个文章的所有数据
	function get_post_all() {
		return [
			"overall" => get_overall_single(),
			"monthly" => get_monthly_postall(),
			"daily"   => get_daily_postall()
		];
	}

	function down_error($code, $msg) {
		return [
			"code" => $code,
			"message" => $msg
		];
	}

	header("Content-Type: application/json");
	header("Access-Control-Allow-Origin: *");
	// 接受3个查询参数：action, post_id, para
	// 其中para为可选的
	if(!isset($_GET['post_id'])) {
		echo json_encode(down_error(-1, "?post_id not provided"));
		exit(-1);
	}
	if(!isset($_GET['action'])) {
		echo json_encode(down_error(-2, "?action not provided"));
		exit(-2);
	}

	$id = (int)addslashes($_GET['post_id']);
	$action = $_GET['action'];
	if(isset($_GET['para']))
		$para = $_GET['para'];

	$redis = new \Redis;
	$redis->connect("localhost");
	$member = new Time;
	$tb = new TbName($id);

	// 判断请求动作
	switch($action) {
		case "overall":
			$result = get_overall_all();
			break;
		case "postall":
			$result = get_post_all();
			break;
		case "yearly":
			if(empty($para)) {
				echo json_encode(down_error(-3, "?para not provided"));
				exit(-3);
			}
			if($id == 0) {	
				$result = get_yearly_all($para);
			}
			break;
		case "monthly":
			if(empty($para)) {
				echo json_encode(down_error(-3, "?para not provided"));
				exit(-3);
			}
			if($id == 0) {	
				$result = get_monthly_all_singleMon($para);
			}
			break;
		case "daily":
			if(empty($para)) {
				echo json_encode(down_error(-3, "?para not provided"));
				exit(-3);
			}
			if($id == 0) {	
				$result = get_daily_all_singleDay($para);
			}
			break;
	}

	if(!empty($result)) {
		$return = down_error(0, "success");
		$return["data"] = $result;
	}
	else 
		$return = down_error(-4, "没有数据");

	$json = json_encode($return);
	echo $json;

	$redis->close();
?>