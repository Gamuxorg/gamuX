<?php 
/*
 * gameslist template
*/
get_header(); ?>

<!--索引-->
<section id="index">
  <div id="catenamelist">
    <el-button type="text" id="games-all-button" @click="refresh">所有游戏</el-button>
    <el-button type="text" v-for="cat in categories" :key="cat.id" :id="cat.id" @click="clickCat($event)">{{ cat.name }}</el-button>
  </div>
</section>

<el-divider></el-divider>
<!--列表-->
<section id="category">
    <el-card class="card" v-for="post in postdata" :key="post.id">
      <div class="pic">
      <a :href="post.link"><img :src="post.exts.thumbnail" class="image"></a>
      </div>
      <div class="game-info">
        <div class="game-name-sort">
          <div class="game-name"><a :href="post.link" v-html="post.title.rendered"></a></div>
          <div class="game-sort"><el-button type="text"><a :href="siteurl + '/gamelist?catid=' + (post.exts.categories)[0].cat_id">{{ (post.exts.categories)[0].name }}</a></el-button></div>
        </div>
        <div class="game-version-date">
          <div class="game-version" v-if="post['exts']['downloadList']['status'] != 1"> 版本信息获取异常 </div>
          <div class="game-version" v-else>{{ post['exts']['downloadList']['downloadList'][0]['version'] }}</div>
          <div class="game-date">{{ post.modified.split("T")[0] }}</div>  
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
      :page-size="20"
      layout="total, prev, pager, next"
      :total="total">
    </el-pagination>
  </div>
</section>

<?php get_footer(); ?>