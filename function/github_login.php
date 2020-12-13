<?php
	namespace Gamux;
	include_once("oauth2.php");
/**
 * class Github_Oauth
 * 用于github认证和登录的类
 * 
 */
class Github_Oauth extends Oauth2{
	const APPID = "";		//请填入github的appid
	const APPSECRET = "";	//请填入github的appsecret
	const STATE = "ggggaaaa";					//用于防止CSRF的随机字符串
	const REDIRECT_ROUTE = '/wp-json/gamux/v1/oauth/github';

	//完成所有步骤后，重定向回主页
	private function oauth_redirect() {
		wp_redirect(site_url());		//记得改回wp_safe_redirect  HTTPS
	}

	/**
	 * 使用github返回的code请求access_token
	 *
	 * @return bool
	 */
	private function request_access_token() {
		//防止CSRF攻击
		$this->check_csrf(self::STATE);

		$request_url = "https://github.com/login/oauth/access_token";
		$data = array(
			'client_id' => self::APPID,
			'client_secret' => self::APPSECRET,
			'code' => $this->code,
			'redirect_uri' => site_url() . self::REDIRECT_ROUTE,
			'state' => self::STATE
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
		$output = $this->check_response_error($response, $target = "access_token");

		$this->check_response_data($output, $target);
		$this->token = $output[$target];
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
		$response = wp_remote_get($url, array(
			'headers' => "Authorization: token $token"
		));
		$output = $this->check_response_error($response, $target = "user_info");
		
		$this->check_response_data($output, $target = "login");
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
			'user_nicename' => $name
		);
		$user_id = wp_insert_user( $userdata );
		if(!is_wp_error($user_id)) {
			add_user_meta($user_id, "github_id", $github_id);
			update_user_meta($user_id, "nickname", $name);
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
		$github_id = $data['id'];
		$github_user = get_users(array(
			"meta_key" => "github_id",
			"meta_value" => $github_id
		));
		if(count($github_user) != 0) {		//用户正常登录
			wp_set_auth_cookie($github_user[0]->ID);
			$this->oauth_redirect();
		}
		else {								//用户首次登录
			$new_user = $this->register_newUser($data);
			wp_signon(array(
				"user_login" => $new_user['user_login'], 
				"user_password" => $new_user['user_pass']
			), false);
			$this->oauth_redirect();
		}
		return true;
	}

	/**
	 * 主函数
	 *
	 * @return void
	 */
	public function github_oauth() {
		$this->request_access_token();
		$this->request_user_info();

		if(is_user_logged_in()) {				//用户已登录
			$this->oauth_redirect();
		}
		else {
			$this->login();
		}
	}
}//class Github_Oauth

	/**
	 * 返回前端所需的登录GITHUB URL
	 *
	 * @return string $url
	 */
	function github_login_url() : string {
		$url = 'https://github.com/login/oauth/authorize?client_id=' . Github_Oauth::APPID . '&scope=user&state=' . Github_Oauth::STATE . '&redirect_uri='. site_url() . Github_Oauth::REDIRECT_ROUTE;
		return $url;
	}

	//用户已登录，返回前端所需的github头像和用户名
	function get_avatar_info() {
		$current_user = get_currentuserinfo();
		$id = $current_user->ID;
		$github_id = get_user_meta($id, 'github_id', true);
		$avatar = 'https://kr.linuxgame.cn:8088/git_avatar.php?id='. $github_id;
		$name = $current_user->display_name;
		return array($name, $avatar);
	}

	//用户已完成认证，github 带上code授权码重定向回本站
	function github_oauth_login_init(\WP_REST_Request $request){
		$code = $request->get_param('code');
		if(!empty($code)) {
			if(session_status() == PHP_SESSION_NONE)
				session_start();
			$github_oauth = new Github_Oauth($_GET['code']);
			$github_oauth->github_oauth();
		}
	}