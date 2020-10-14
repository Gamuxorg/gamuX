<?php 
/*
 * games-all template
*/
get_header(); ?>

<div class="breadcrumb">
  <el-page-header :title="catname" @back="goBack" :content="postname">
</div>
<el-row id="post" :gutter="10">
  <el-col id="post-main" :xs="24" :sm="16" :md="16" :lg="18">
    <section class="game-content">
      <div id="post-title">
        {{ postname }}
      </div>
      <div id="post-intro" v-html="postcontent"></div>
    </section>
    <section class="post-download">
    <a :href="siteurl">下载地址</a>
    </section>    
    <section class="post-commit">
    游戏评论
    </section>
  </el-col>

  <el-col id="post-sidebar" :xs="0" :sm="8" :md="8" :lg="6">
    <section id="post-edit">
      <el-button type="primary" icon="el-icon-edit">编辑</el-button>
      <el-button type="primary" icon="el-icon-notebook-2">投稿</el-button>
    </section>
    <section id="post-thumb">
      <el-image src="https://media.st.dl.pinyuncloud.com/steam/apps/458710/header.jpg?t=1601048208" fit="fill"></el-image>
    </section>
    <section id="post-buy">
      <el-button type="primary" icon="el-icon-shopping-cart-2">steam购买</el-button>
      <el-button type="primary" icon="el-icon-shopping-cart-2">GOG购买</el-button>
    </section>
    <section id="post-rate">
    </section>
  </el-col>  
</el-row>
<?php get_footer(); ?>