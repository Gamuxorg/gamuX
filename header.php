<!DOCTYPE html>
<html lang="en_US">
<head>
  <meta charset="utf-8">
  <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="keywords" content="Linux游戏站是全世界唯一全面介绍Linux游戏及相关工具的中文社区，本站秉承简洁而不简单的办站原则，为广大Linux用户娱乐性而奋斗！">
  <meta name="revised" content="gamux, 2020/01/26/">
  <meta http-equiv="refresh" content="21600">
  <meta name="description" content="Linux游戏, Linux game, Linuxgame, gamux, steam">
	<title> <?php echo get_bloginfo('name'); ?> | 
		<?php 
      if (is_home() || is_front_page())
        echo '为Linux用户的娱乐性而奋斗！';
      elseif (is_single())
        echo get_the_title().' for Linux';
      else
        echo '你好，gamux！';
		?>
	</title>
	<link rel="shortcut icon" href="<?php bloginfo('template_url'); ?>/logo.ico">
  <link crossorigin="anonymous" integrity="sha512-WYzwsHxfl8+qirF4V9ZyFULDBwxvm1kuCHlSd1F57JwALyQMxeq7j1blj4Y7pmP9r0tVMk7GYUI5xLDB7QIZCA==" href="//lib.baomitu.com/element-ui/2.15.3/theme-chalk/index.min.css" rel="stylesheet">
  <link rel="stylesheet" href="<?php bloginfo('template_url'); ?>/style.css">
  <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
  <?php if(is_single()) { ?>
    <link rel="stylesheet" href="<?php bloginfo('template_url'); ?>/css/single.css">
    <link crossorigin="anonymous" integrity="sha384-Cr4NirNGPwhXoUPml2HA5PmMExeUuxM/oxUMDhMdSzUi9udHL+hdgDZZpq/2rOrp" href="//lib.baomitu.com/quill/1.3.7/quill.snow.min.css" rel="stylesheet">
  <?php } elseif(is_home()) { ?>
    <link rel="stylesheet" href="<?php bloginfo('template_url'); ?>/css/index.css">
  <?php } elseif(is_category() or (strpos($_SERVER['REQUEST_URI'], 'gamelist') != false)){ ?>
    <link rel="stylesheet" href="<?php bloginfo('template_url'); ?>/css/category.css">
  <?php } elseif(is_category() or (strpos($_SERVER['REQUEST_URI'], 'newslist') != false) or (strpos($_SERVER['REQUEST_URI'], 'search') != false)){ ?>
    <link rel="stylesheet" href="<?php bloginfo('template_url'); ?>/css/news.css">
  <?php } elseif(is_search()){ ?>
    <link rel="stylesheet" href="<?php bloginfo('template_url'); ?>/css/search.css">
  <?php } elseif(is_single()) { ?>
    <link rel="stylesheet" href="<?php bloginfo('template_url'); ?>/css/single.css">
  <?php } else { echo ""; } ?>
	<?php wp_head(); ?>
</head>
<body id="body">
<div id="container">
<header id="header">
  <div id="header-div">
    <div id="site-logo"><a href="<?php echo esc_url( home_url( '/' ) ); ?>">GAMUX</a></div>
    <div>
      <el-form action="search" method="get" class="demo-form-inline">
          <input type="text" placeholder="请输入搜索内容" id="header-search" name="s" size="small">
      </el-form>
    </div>

    <el-row id="header-row">
      <el-col id="header-col">
        <el-menu
          id="left-menu"
          class="el-menu-vertical-demo">
          <el-menu-item index="1">
            <i class="el-icon-menu"></i>
            <span slot="title"><a :href="siteurl + '/gamelist'">游戏列表</a></span>
          </el-menu-item>
          <el-menu-item index="2">
            <i class="el-icon-news"></i>
            <span slot="title"><a :href="siteurl + '/newslist'">新闻资讯</a></span>
          </el-menu-item>
          <el-menu-item index="3">
            <i class="el-icon-document"></i>
            <span slot="title"><a href="https://doc.appimage.cn/docs/home/">帮助文档</a></span>
          </el-menu-item>
          <el-menu-item index="4">
            <i class="el-icon-star-on"></i>
            <span slot="title"><a href="https://github.com/Gamuxorg/bbs/issues/new/choose" target="_blank">愿望清单</a></span>
          </el-menu-item>
          <el-menu-item index="5">
            <i class="el-icon-chat-line-square"></i>
            <span slot="title"><a href="https://github.com/Gamuxorg/bbs/issues" target="_blank">交流面板</a></span>
          </el-menu-item>
          <el-menu-item index="6">
            <i class="el-icon-milk-tea"></i>
            <span slot="title">打赏本站</span>
          </el-menu-item>   
        </el-menu>
      </el-col>
    </el-row>
  
    <div id="avatar" v-if="islogin == 0">
      <div class="block avatar-inner">
        <el-button class="login-button" type="text" ref="login" @click="dialogloginVisible = true">点击登录</el-button>
      </div>
    </div>
    <div id="avatar" v-else>
      <div class="header-avatar">
        <el-image :src="useravatar"></el-image>
        <div class="username">
          <a :href="siteurl + '/wp-admin'">{{ username }}</a> | <a :href="logout">退出</a>
        </div>
      </div>      
    </div>
    <!--弹窗登录-->
    <el-dialog title="网站登录" class="login-dialog" :visible.sync="dialogloginVisible">
      <div class="github-login login-dialog-list">        
        <a href="<?php echo \Gamux\github_login_url(); ?>">
          <el-button type="primary">
          <svg class="aliicon" aria-hidden="true">
            <use xlink:href="#icon-gamux-github2"></use>
          </svg>GitHub账号登录
          </el-button>
        </a>
      </div>
      <div class="weibo-login login-dialog-list">
        <a href="<?php echo \Gamux\weibo_login_url(); ?>">
        <el-button type="danger">
          <svg class="aliicon" aria-hidden="true">
            <use xlink:href="#icon-weibo"></use>
          </svg>新浪微博账号登录
        </el-button>
        </a>
      </div>
      <div class="qq-login login-dialog-list">
        <a href="<?php echo \Gamux\qq_login_url(); ?>">
        <el-button type="success">
          <svg class="aliicon" aria-hidden="true">
            <use xlink:href="#icon-QQ"></use>
          </svg>腾讯QQ账号登录
        </el-button>
        </a>
      </div>
    </el-dialog>

    <div id="beian">
      <a href="https://beian.miit.gov.cn/" target="_blank"><?php echo get_option( 'zh_cn_l10n_icp_num' );?></a>
    </div>
    <div id="techfrom">
      <span class="wplogo"><img src="<?php bloginfo('template_url'); ?>/img/wplogo.png"></span>
      <span class="vuelogo"><img src="<?php bloginfo('template_url'); ?>/img/vuelogo.png"></span>
      <span class="elementlogo"><i class="el-icon-eleme"></i></span>
    </div>
  </div>
</header>
<div id="section">
  <div id="mobile-header">

  </div>
  <div id="inner-section">
