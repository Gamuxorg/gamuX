<?php 
/*
 * Template Name: 新闻列表
*/
get_header(); ?>

<!--索引-->
<section id="index">
  <div id="catenamelist">
    <el-button type="text" id="games-all-button" @click="refresh">所有文章</el-button>
    <el-button type="text" v-for="cat in categories" :key="cat.id" :id="cat.id" @click="clickCat($event)">{{ cat.name }}</el-button>
  </div>
</section>

<el-divider></el-divider>
<!--列表-->
<section id="news">
    <el-card class="card" v-for="post in postdata" :key="post.id">
      <div class="game-info">
        <div class="game-name-sort">
          <div class="game-name"><a :href="post.link">{{ post.title.rendered }}</a></div>
          <div class="game-content" v-html="post.excerpt.rendered"></div>
          <div class="game-sort">
            <span><a :href="siteurl + '/gamelist?catid=' + (post.exts.categories)[0].cat_id">{{ (post.exts.categories)[0].name }}</a></span>
            <span>{{ post.date.split("T")[0] }}</span>
          </div>
        </div>
      </div>
    </el-card>
</section>

<el-divider></el-divider>
<!--翻页-->
<section id="pagi">
  <div class="block">
    <el-pagination
      @current-change="handleCurrentChange"
      :current-page.sync="currentpage"
      :page-size="5"
      layout="total, prev, pager, next"
      :total="total">
    </el-pagination>
  </div>
</section>

<?php get_footer(); ?>