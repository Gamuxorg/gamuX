<?php
	namespace Gamux;
/**
 * class Oauth2
 * 第三方登录的通用父类
 */
class Oauth2{
	const APPID = "";
	const APPSECRET = "";
	const STATE = "";
	
	public $code;		//用户在通过第三方授权后重定向返回的code
	public $token;		//access_token
	public $data;		//第三方返回的用户信息

	public function __construct(string $code = "") {
		$this->code = $code;
	}

	private function check_csrf() {
		if($_GET['state'] != self::STATE) {
			$error = new \WP_Error(403, "认证信息失败，请确保从linuxgame.cn登录并提供正确的凭据");
			wp_die($error);
		}
	}

	private function check_response_error($response, string $target) : array {
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

	private function check_response_data($output, $target) {
		if(empty($output[$target])) {
			$error_mess = $output['error'];
			$error_desc = $output['error_description'];
			wp_die("获取{$target}失败\n" . $error_mess . "\n $error_desc");
		}
		return true;
	}

	/**
	 * 返回到当前页面或者首页
	 *
	 * @return string $redirect_url
	 */
	private function get_redirect_url() : string {
		if( is_home() )
			$redirect_url = site_url();
		else
			$redirect_url = get_page_link();
		return $redirect_url;
	}

	private function oauth_redirect() {}

	private function request_access_token() {}

	private function request_user_info() {}

	private function register_newUser(array $data) : array {}

	private function login() {}

}

	// class b extends Oauth2{
	// 	const APPID = 333;
	// }

	// $ds = new b;
	// echo $ds::APPID;

?>