<?php
	namespace Gamux;
	include_once("oauth2.php");
/**
 * class QQ_Oauth
 * 用于qq认证和登录的类
 * 
 */
class QQ_Oauth extends Oauth2{
	const APPID = "";
	const APPSECRET = "";
	const STATE = "ggggaaaa";					//用于防止CSRF的随机字符串
	const REDIRECT_ROUTE = '/wp-json/gamux/v1/oauth/qq';

	public $openid;		//qq返回的openid

	//完成所有步骤后，重定向回主页
	private function oauth_redirect() {
		wp_safe_redirect(site_url());
	}

	/**
	 * 使用qq返回的code请求access_token
	 *
	 * @return bool
	 */
	private function request_access_token() {
		$this->check_csrf();

		$request_url = "https://graph.qq.com/oauth2.0/token?client_id=" . self::APPID . "&client_secret=" . self::APPSECRET . "&grant_type=authorization_code&redirect_uri=" . site_url() . self::REDIRECT_ROUTE . "&code=" . $this->code;
		//发送GET请求
		$response = wp_remote_get($request_url);
		
		$output = $this->check_response_error($response, $target = "access_token");
		$this->check_response_error($output, $target);
		$this->token = $output["access_token"];
		return true;
	}

	/**
	 * 使用获取的 access_token 向 qq 请求 openid
	 *
	 * @return bool
	 */
	function request_token_info() {
		$url = "https://graph.qq.com/oauth2.0/me?access_token=";
		$token = $this->token;
		//发送POST请求
		$response = wp_remote_get($url . $token);
		
		$output = $this->check_response_error($response, $target = "openid");
		$this->check_response_error($output, $target);
		$this->openid = $output["openid"];
		return true;
	}

	/**
	 * 使用获取的 access_token 向 qq 请求用户信息
	 *
	 * @return bool
	 */
	private function request_user_info() {
		$token = $this->token;
		$openid = $this->openid;
		$url = "https://graph.qq.com/user/get_user_info?access_token={$token}&oauth_consumer_key=" . self::APPID . "&openid={$openid}";
		//发送GET请求
		$response = wp_remote_get($url);
		
		$output = $this->check_response_error($response, $target = "user_info");
		if(isset($output['ret'])) {
			$error_mess = $output['error'];
			$error_desc = $output['error_description'];
			wp_die("获取{$target}失败\n" . $error_mess . "\n $error_desc");
		}
		$this->data = $output;
		return true;
	}

	/**
	 * 用户首次登录，注册新的用户
	 *
	 * @param array $data 用户信息
	 * @return array $new_user
	 */
	private function register_newUser(array $data) : array {
		$qq_id = $data['id'];
		$email = $this->email;
		$name = $data['name'];
		$nickname = $data['screen_name'];
		$avatar = $data['profile_image_url'];
		$qq_account = $data['domain'];
		$prefix = 'qq_';

		$random_password = wp_generate_password( $length=12, $include_standard_special_chars=false );
		$login_name = $prefix . $qq_account;
		$userdata = array(
			'user_login' => $login_name,
			'display_name' => $name,
			'user_email' => $email,
			'user_pass' => $random_password,
			'nickname' => $nickname
		);
		$user_id = wp_insert_user( $userdata );
		if(!is_wp_error($user_id)) {
			add_user_meta($user_id, "qq_id", $qq_id);
			add_user_meta($user_id, "qq_avatar", $avatar);
			$new_user = array(
				"user_login" => $login_name,
				'user_pass' => $random_password
			);
			return $new_user;
		}
		else {
			wp_die($user_id);
		}
	}

	/**
	 * 用户正常登录
	 *
	 * @return bool
	 */
	private function login() {
		$data = $this->data;
		$qq_id = $data['id'];
		$qq_user = get_users(array(
			"meta_key" => "qq_id",
			"meta_value" => $qq_id
		));
		if(count($qq_user) != 0) {		//用户正常登录
			update_user_meta($qq_user[0]->ID , "nickname", $data['name']);
			wp_set_auth_cookie($qq_user[0]->ID);
			oauth_redirect();
		}
		else {								//用户首次登录
			$new_user = $this->register_newUser($data);
			wp_signon(array(
				"user_login" => $new_user['user_login'], 
				"user_password" => $new_user['user_pass']
			), false);
			oauth_redirect();
		}
	}

	/**
	 * 主函数
	 *
	 * @return void
	 */
	public function qq_oauth() {
		$this->request_access_token();
		$this->request_user_info();
		$this->request_email();

		if(is_user_logged_in()) {				//用户已登录
			$this->oauth_redirect();
		}
		else {
			$this->login();
		}
	}
}//class QQ_Oauth

	// 返回前端所需的登录URL
	function qq_login_url() {
		$url = "https://graph.qq.com/oauth2.0/authorize?client_id=" . QQ_Oauth::APPID . "&state=" . QQ_Oauth::STATE . "&response_type=code&redirect_uri=" . site_url() . QQ_Oauth::REDIRECT_ROUTE . "&scope=";
		return $url;
	}

	//用户已完成认证，qq 带上code授权码重定向回本站
	function qq_oauth_login_init(\WP_REST_Request $request) {
		$code = $request->get_param('code');
		if (!empty($code)) {
			if(session_status() == PHP_SESSION_NONE)
				session_start();
			$qq_oauth = new QQ_Oauth($code);
			$qq_oauth->qq_oauth();
		}
	}
	// add_action('init','\Gamux\qq_oauth_login_init');	


?>