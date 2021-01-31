<?php
	/**
	 * 调用STEAM　Api拉取文章信息
	 */

	$appid = $_GET["appid"];	
	if($appid == "") {
		echo 0;
		exit(0);
	}

	$steamKey = '';
	
	$curl = curl_init("https://store.steampowered.com/api/appdetails?appids={$appid}&key={$steamKey}&format=json&cc=cn&l=zh");
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_HTTPHEADER, [
		'Accept-Language:zh'
	]);
	header_remove("Content-Type");
	header("Content-Type : application/json");
	
	$response = curl_exec($curl);
	if(curl_errno($curl) == 0) {
		$obj = json_decode($response);
		if($obj->$appid->success == true) {
			// 删除不需要的字段
			unset($obj->$appid->data->required_age);
			unset($obj->$appid->data->is_free);
			unset($obj->$appid->data->dlc);
			unset($obj->$appid->data->supported_languages);
			unset($obj->$appid->data->legal_notice);
			unset($obj->$appid->data->drm_notice);
			unset($obj->$appid->data->developers);
			unset($obj->$appid->data->publishers);
			unset($obj->$appid->data->packages);
			unset($obj->$appid->data->package_groups);
			unset($obj->$appid->data->metacritic);
			unset($obj->$appid->data->recommendations);
			unset($obj->$appid->data->achievements);
			unset($obj->$appid->data->support_info);
			unset($obj->$appid->data->content_descriptors);
			echo json_encode($obj);
		}
		else
			echo -1;
			
	}
	else {
		echo curl_error;
	}

	curl_close($curl);
?>
