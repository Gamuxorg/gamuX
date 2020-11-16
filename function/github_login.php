<?php
	namespace Gamux;

/**
 * class Github_Oauth
 * 用于github认证和登录的类
 * 
 */
class Github_Oauth{
	const GITHUB_APPID = "";		//请填入github的appid
	const GITHUB_APPSECRET = "";	//请填入github的appsecret
	const GITHUB_STATE = "";					//用于防止CSRF的随机字符串

	public $code;		//用户登录github后重定向返回的code
	public $token;		//access_token
	public $data;		//github返回的用户信息

	public function __construct(string $code) {
		$this->code = $code;
	}

	/**
	 * 返回 request_access_token 请求完成后进行重定向的 URL
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

	/**
	 * 使用github返回的code请求access_token
	 *
	 * @return bool
	 */
	private function request_access_token() {
		//防止CSRF攻击
		if($_GET['state'] != self::GITHUB_STATE) {
			$error = new \WP_Error(403, "认证信息失败，请确保从linuxgame.cn登录并提供正确的凭据");
			wp_die($error);
		}

		$request_url = "https://github.com/login/oauth/access_token";
		$data = array(
			'client_id' => self::GITHUB_APPID,
			'client_secret' => self::GITHUB_APPSECRET,
			'code' => $this->code,
			'redirect_uri' => $this->get_redirect_url(),
			'state' => self::GITHUB_STATE
		);
		//发送POST请求
		$response = wp_remote_post($request_url, array(
			'headers' => array(
				'Accept' => 'application/json'
			),
			'body' => $data,
			'redirection' => 5,
			'blocking' => true,					
			'timeout'=> 15			            // using a shorter time to ensure user experience
		));
		if(is_wp_error($response)) {
			$error = new \WP_Error($response->get_error_code(), $response->get_error_message() . "\n Get access_token failed.");
			wp_die($error);
		}
		elseif($response['response']['code'] != 200) {				
			$err_code = $response['response']['code'];
			$error = new \WP_Error(400, "HTTP $err_code Error, getting access_token failed.");
			wp_die($error);
		}
		else 
			$output = json_decode($response['body'], true);
		
		if(empty($output["access_token"])) {
			$error_mess = $output['error'];
			$error_desc = $output['error_description'];
			wp_die("获取token失败\n" . $error_mess . "\n $error_desc");
		}
		$this->token = $output["access_token"];
		return true;
	}

	/**
	 * 使用获取的 access_token 向github请求用户信息
	 *
	 * @return bool
	 */
	private function request_user_info() {
		$url = "https://api.github.com/user";
		$token = $this->token;
		//发送GET请求
		$response = wp_remote_get( $url, array(
			'headers' => "Authorization: token $token"
		));
		if(is_wp_error($response)) {
			$error = new \WP_Error($response->get_error_code(), $response->get_error_message() . "\n Get user info failed.");
			wp_die($error);
		}
		elseif($response['response']['code'] != 200) {
			$err_code = $response['response']['code'];
			$error = new \WP_Error(400, "HTTP $err_code Error, getting user info failed.");
			wp_die($error);
		}
		else
			$data = json_decode($response['body'], true);
		
		if(empty($data['login'])) {
			$error_mess = $data['error'];
			$error_desc = $data['error_description'];
			wp_die("获取用户信息失败\n" . $error_mess . "\n $error_desc");
		}
		$this->data = $data;
		return true;
	}

	/**
	 * 用户首次登录，注册新的用户
	 *
	 * @param array $data 用户信息
	 * @return array $new_user
	 */
	private function register_newUser(array $data) : array {
		$github_id = $data['id'];
		$email = $data['email'];
		$name = $data['name'];
		$github_account = $data['login'];
		$prefix = 'github_';

		$random_password = wp_generate_password( $length=12, $include_standard_special_chars=false );
		$login_name = $prefix . $github_account;
		$userdata = array(
			'user_login' => $login_name,
			'display_name' => $name,
			'user_email' => $email,
			'user_pass' => $random_password,
			'nickname' => $name
		);
		$user_id = wp_insert_user( $userdata );
		if(!is_wp_error($user_id)) {
			add_user_meta($user_id, "github_id", $github_id);
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
	 * 主函数
	 *
	 * @return void
	 */
	public function github_oauth() {
		$this->request_access_token();
		$this->request_user_info();
		$data = $this->data;

		if(is_user_logged_in()) {				//用户已登录
			github_oauth_redirect();
		}
		else {
			$github_id = $data['id'];
			$github_user = get_users(array(
				"meta_key" => "github_id",
				"meta_value" => $github_id
			));
			if(count($github_user) != 0) {		//用户正常登录
				update_user_meta($github_user[0]->ID ,"nickname", $data['name']);
				wp_set_auth_cookie($github_user[0]->ID);
				github_oauth_redirect();
			}
			else {								//用户首次登录
				$new_user = $this->register_newUser($data);
				wp_signon(array(
					"user_login" => $new_user['user_login'], 
					"user_password" => $new_user['user_pass']
				),false);
				github_oauth_redirect();
			}
		}
	}
}//class Github_Oauth

	//完成所有步骤后，重定向回主页
	function github_oauth_redirect() {
		wp_redirect(site_url());		//记得改回wp_safe_redirect  HTTPS
	}

	/**
	 * 返回前端所需的登录GITHUB URL
	 *
	 * @return string $url
	 */
	function github_login_url() : string {
		$url = 'https://github.com/login/oauth/authorize?client_id=' . Github_Oauth::GITHUB_APPID . '&scope=user&state=' . Github_Oauth::GITHUB_STATE . '&redirect_uri='. site_url();
		return $url;
	}

	//用户已登录，返回前端所需的github头像和用户名
	function get_avatar_info() {
		$current_user = get_currentuserinfo();
		$id = $current_user->ID;
		$github_id = get_user_meta($id, 'github_id', true);
		$avatar = 'https://kr.linuxgame.cn:8088/git_avatar.php?id='. $github_id;
		$name = $current_user->nickname;
		return array($name, $avatar);
	}

	//用户已完成认证，github 带上code授权码重定向回本站
	function github_oauth_login_init(){
		if (isset($_GET['code'])) {
			if(session_status() == PHP_SESSION_NONE)
				session_start();
			$github_oauth = new Github_Oauth($_GET['code']);
			$github_oauth->github_oauth();
		}
	}
	add_action('init','\Gamux\github_oauth_login_init');		//init 是否合理