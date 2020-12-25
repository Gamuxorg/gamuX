<?php
	namespace Gamux;
/**
 * class Oauth2
 * 第三方登录的通用父类
 */
class Oauth2{
	public $code;		//用户在通过第三方授权后重定向返回的code
	public $token;		//access_token
	public $data;		//第三方返回的用户信息

	public function __construct(string $code = "") {
		$this->code = $code;
	}

	/**
	 * 检查带回的state字段是否和本站保留的一致，防止CSRF攻击
	 *
	 * @param string $myState
	 * @return void
	 */
	protected function check_csrf(string $myState) {
		if($_GET['state'] != $myState) {
			$error = new \WP_Error(403, "认证信息失败，请确保从linuxgame.cn登录并提供正确的凭据");
			wp_die($error);
		}
		return true;
	}

	/**
	 * 检查wp_remote_request()操作是否正确获取到了远程资源
	 *
	 * @param array|WP_Error $response
	 * @param string $target 请求的信息简介
	 * @return $output
	 */
	protected function check_response_error($response, string $target) {
		if(is_wp_error($response)) {
			$error = new \WP_Error($response->get_error_code(), $response->get_error_message() . "\n Get $target failed.");
			wp_die($error);
		}
		elseif($response['response']['code'] != 200) {				
			$err_code = $response['response']['code'];
			$error = new \WP_Error(400, "HTTP $err_code Error, Get $target failed.");
			wp_die($error);
		}
		else 
			$output = json_decode($response['body'], true);

		return $output;
	}

	/**
	 * 检查远程返回的JSON数据是否正确
	 *
	 * @param $output JSON解析后的数据
	 * @param string $target 检查的字段
	 * @return mixed
	 */
	protected function check_response_data($output, string $target) {
		if(empty($output[$target])) {
			$error_mess = $output['error'];
			$error_desc = $output['error_description'];
			wp_die("获取{$target}失败\n" . $error_mess . "\n $error_desc");
		}
		return true;
	}

	private function oauth_redirect() {}

	private function request_access_token() {}

	private function request_user_info() {}

	private function register_newUser(array $data) : array {}

	private function login() {}

}

?>