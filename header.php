<!DOCTYPE html>
<html lang="zh_CN">
<head>
  <meta charset="utf-8">
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
	<link crossorigin="anonymous" href="https://unpkg.com/element-ui@2.13.0/lib/theme-chalk/index.css" rel="stylesheet">
  <link rel="stylesheet" href="<?php bloginfo('template_url'); ?>/style.css">
  <link href="https://fonts.font.im/css?family=Roboto:400,900" rel="stylesheet">
  <?php if(is_single()) { ?>
    <link rel="stylesheet" href="<?php bloginfo('template_url'); ?>/css/single.css">
    <style>
    #main {
      background-image: url(<?php echo get_post_meta($post->ID, 'bg')[0]; ?>);
    }
  <?php } elseif(is_home()) { ?>
    <link rel="stylesheet" href="<?php bloginfo('template_url'); ?>/css/index.css">
  <?php } elseif(is_category() or (strpos($_SERVER['REQUEST_URI'], 'gamelist') != false)){ ?>
    <link rel="stylesheet" href="<?php bloginfo('template_url'); ?>/css/category.css">
  <?php } elseif(is_search()){ ?>
    <link rel="stylesheet" href="<?php bloginfo('template_url'); ?>/css/search.css">
  <?php } else { echo ""; } ?>
	<?php wp_head(); ?>
</head>
<body id="body">
<div id="container">
<header id="header" ref="headerstyle" :style="{display:display}">
  <div id="header-div">
    <div id="site-logo">GAMUX</div>
    
    <el-row id="header-row">
      <el-col id="header-col">
        <el-menu
          id="left-menu"
          default-active="2"
          class="el-menu-vertical-demo">
          <el-menu-item index="1">
            <i class="el-icon-menu"></i>
            <span slot="title">游戏列表</span>
          </el-menu-item>
          <el-menu-item index="2">
            <i class="el-icon-news"></i>
            <span slot="title">新闻资讯</span>
          </el-menu-item>
          <el-menu-item index="3">
            <i class="el-icon-document"></i>
            <span slot="title">帮助文档</span>
          </el-menu-item>
          <el-menu-item index="4">
            <i class="el-icon-star-on"></i>
            <span slot="title">愿望清单</span>
          </el-menu-item>
          <el-menu-item index="5">
            <i class="el-icon-chat-line-square"></i>
            <span slot="title">交流面板</span>
          </el-menu-item>
          <el-menu-item index="6">
            <i class="el-icon-milk-tea"></i>
            <span slot="title">打赏本站</span>
          </el-menu-item>   
        </el-menu>
      </el-col>
    </el-row>
  
    <div id="avatar">
      <div class="block avatar-inner">点击登录</div>
    </div>
  
    <div id="techfrom">
      <span class="wplogo"><img src="<?php bloginfo('template_url'); ?>/img/wplogo.png"></span>
      <span class="vuelogo"><img src="<?php bloginfo('template_url'); ?>/img/vuelogo.png"></span>
      <span class="elementlogo"><i class="el-icon-eleme"></i></span>
    </div>
  </div>
</header>
<div id="section" :style="{ marginLeft: sectionMarginLeft + 'px' }">
