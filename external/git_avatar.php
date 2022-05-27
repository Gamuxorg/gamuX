<?php
    /* git_avatar.php
       根据请求中的id 下载github头像，并保存到redis中
       下次请求时从redis中取出
    */
    
    define("WEEK",604800);  //设置失效时间为一周
    $github_id = $_GET['id'];
    $url = 'https://avatars2.githubusercontent.com/u/'. $github_id .'?v=3';
    if(is_null($github_id) or $github_id=="" ){
        header("Status : 404",true);
        return;
    }

    $redis = new Redis();
    $redis->connect('/var/run/redis/redis-server.sock');
    
    if($pic = $redis->get($github_id)){         //redis中已存在，更新过期时间
        header('Content-Type : image/png');
        $redis->expire($github_id, WEEK);
        echo $pic;
    }
    else{
        $pic = file_get_contents($url);          //redis中不存在，从github获取头像
        if($pic){
            $redis->setex($github_id, WEEK, $pic);
            header('Content-Type : image/png');
            echo $pic;
        }
        else{
            header("Status : 404",true);
            return;
        }
    }
?>