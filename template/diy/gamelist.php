<?php 
/*
 * Template Name: 游戏列表
*/
get_header(); ?>

<!--索引-->
<section id="index">
  <div id="catenamelist">
    <el-button type="text" id="games-all-button" @click="refresh">所有游戏</el-button>
    <el-button type="text" v-for="cat in CATEGORIES" :key="cat.id" :id="cat.id" @click="clickCat($event)">{{ cat.name }}</el-button>
  </div>
</section>

<el-divider></el-divider>
<!--列表-->
<section id="category" v-if="CATSHOW">
    <el-card class="card" v-for="post in POSTDATA" :key="post.id">
      <div class="pic">
      <a :href="post.link"><img :src="post.exts.thumbnail" class="image"></a>
      </div>
      <div class="game-info">
        <div class="game-name-sort">
          <div class="game-name"><a :href="post.link" v-html="post.title.rendered"></a></div>
          <div class="game-sort"><el-button type="text"><a :href="SITEURL + '/gamelist?catid=' + (post.exts.categories)[0].cat_id">{{ (post.exts.categories)[0].name }}</a></el-button></div>
        </div>
        <div class="game-version-date">
          <!-- 无版本号则返回一个默认值 -->
          <div class="game-version" v-if="post['exts']['version'] == ''"> ver1.0 </div>
          <div class="game-version" v-else>{{ post['exts']['version'] }}</div>
          <div class="game-date">{{ post.modified.split("T")[0] }}</div>  
        </div>
      </div>
    </el-card>
</section>

<el-divider></el-divider>
<!--翻页-->
<section id="pagi" v-if="PAGISHOW">
  <div class="block">
    <el-pagination
      @current-change="handleCurrentChange"
      :current-page.sync="CURRENTPAGE"
      :page-size="20"
      layout="total, prev, pager, next"
      :total="TOTAL">
    </el-pagination>
  </div>
</section>

<?php get_footer(); ?>