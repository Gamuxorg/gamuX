<?php
	namespace Gamux;
	include_once("oauth2.php");
/**
 * class Weibo_Oauth
 * 用于weibo认证和登录的类
 * 
 */
class Weibo_Oauth extends Oauth2{
	const APPID = "";
	const APPSECRET = "";
	const STATE = "";					//用于防止CSRF的随机字符串
	const REDIRECT_ROUTE = '/wp-json/gamux/v1/oauth/weibo';

	public $uid;		//weibo返回的uid
	public $email;		//weibo返回的用户email

	//完成所有步骤后，重定向回主页
	private function oauth_redirect() {
		wp_safe_redirect(site_url());
	}

	/**
	 * 使用weibo返回的code请求access_token和uid
	 *
	 * @return bool
	 */
	private function request_access_token() {
		$this->check_csrf(self::STATE);

		//发送POST请求
		$request_url = "https://api.weibo.com/oauth2/access_token?client_id=" . self::APPID . "&client_secret=" . self::APPSECRET . "&grant_type=authorization_code&redirect_uri=" . site_url() . self::REDIRECT_ROUTE . "&code=" . $this->code;
		$response = wp_remote_post($request_url);
		
		$output = $this->check_response_error($response, $target = "access_token");
		$this->check_response_data($output, $target);
		$this->token = $output["access_token"];
		$this->uid = $output["uid"];
		return true;
	}

	/**
	 * 使用获取的 access_token 向 weibo 请求 token_info中的uid
	 *
	 * @return bool
	 */
	/*
	function request_token_info() {
		$url = "https://api.weibo.com/oauth2/get_token_info";
		$token = $this->token;
		//发送POST请求
		$response = wp_remote_post($request_url, array(
			'headers' => "Authorization:OAuth2 $token",
			'redirection' => 5,
			'blocking' => true,					
			'timeout'=> 15
		));
		
		$output = $this->check_response_error($response, $target = "uid");
		$this->check_response_error($output, $target);
		$this->uid = $output["uid"];
		return true;
	}
	*/

	/**
	 * 使用获取的 access_token 向 weibo 请求用户信息
	 *
	 * @return bool
	 */
	private function request_user_info() {
		$token = $this->token;
		$uid = $this->uid;
		//发送GET请求
		$url = "https://api.weibo.com/2/users/show.json?uid={$uid}&access_token={$token}";
		$response = wp_remote_get($url);

		$output = $this->check_response_error($response, $target = "user_info");
		$this->check_response_data($output, $target = "idstr");
		$this->data = $output;
		return true;
	}

	/**
	 * 使用获取的 access_token 向 weibo 请求 email
	 *
	 * @return bool
	 */
	private function request_email() {
		$token = $this->token;
		$url = "https://api.weibo.com/2/account/profile/email.json?access_token={$token}";
		//发送GET请求
		$response = wp_remote_get($url);

		$output = $this->check_response_error($response, $target = "email");
		if(empty($output[0][$target])) {		//这里可能不正确	
			$error_mess = $output['error'];
			$error_desc = $output['error_description'];
			wp_die("获取{$target}失败\n" . $error_mess . "\n $error_desc");
		}
		$this->email = $output[0][$target];
		return true;
	}

	/**
	 * 用户首次登录，注册新的用户
	 *
	 * @param array $data 用户信息
	 * @return array $new_user
	 */
	private function register_newUser(array $data) : array {
		$weibo_id = $data['id'];
		// $email = $this->email;
		$email = $weibo_id . "@sina.com";
		$name = $data['name'];
		$avatar = $data['profile_image_url'];
		$weibo_account = $weibo_id;
		$prefix = 'weibo_';

		$random_password = wp_generate_password( $length=12, $include_standard_special_chars=false );
		$login_name = $prefix . $weibo_account;
		$userdata = array(
			'user_login' => $login_name,
			'display_name' => $name,
			'user_email' => $email,
			'user_pass' => $random_password,
			'user_nicename' => $name
		);
		$user_id = wp_insert_user( $userdata );
		if(!is_wp_error($user_id)) {
			add_user_meta($user_id, "weibo_id", $weibo_id);
			add_user_meta($user_id, "weibo_avatar", $avatar);
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
	 * 更新用户头像链接，因为会过期
	 *
	 * @return bool
	 */
	private function update_avatar($weibo_id) {
		$prefix = 'weibo_';
		$user_id = get_user_by("login", $prefix . $weibo_id)->ID;
		$avatar = $this->data['profile_image_url'];
		update_user_meta($user_id, "weibo_avatar", $avatar);
		return true;
	}

	/**
	 * 用户正常登录
	 *
	 * @return void
	 */
	private function login() {
		$data = $this->data;
		$weibo_id = $data['id'];
		$weibo_user = get_users(array(
			"meta_key" => "weibo_id",
			"meta_value" => $weibo_id
		));
		if(count($weibo_user) != 0) {		//用户正常登录
			$this->update_avatar($weibo_id);
			wp_set_auth_cookie($weibo_user[0]->ID);
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
	}

	/**
	 * 主函数
	 *
	 * @return void
	 */
	public function weibo_oauth() {
		$this->request_access_token();
		$this->request_user_info();
		// $this->request_email();				//暂时拿不到权限

		if(is_user_logged_in()) {				//用户已登录
			$this->oauth_redirect();
		}
		else {
			$this->login();
		}
	}
}//class Weibo_Oauth

	// 返回前端所需的登录URL
	function weibo_login_url() {
		$url = "https://api.weibo.com/oauth2/authorize?client_id=" . Weibo_Oauth::APPID . "&state=" . Weibo_Oauth::STATE . "&response_type=code&redirect_uri=" . site_url() . Weibo_Oauth::REDIRECT_ROUTE;
		return $url;
	}

	//用户已完成认证，weibo 带上code授权码重定向回本站
	function weibo_oauth_login_init(\WP_REST_Request $request) {
		$code = $request->get_param('code');
		if (!empty($code)) {
			if(session_status() == PHP_SESSION_NONE)
				session_start();
			$weibo_oauth = new Weibo_Oauth($code);
			$weibo_oauth->weibo_oauth();
		}
	}

?>