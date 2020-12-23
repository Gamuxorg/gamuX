<?php 
/*
 * gameslist template
*/
get_header(); ?>

<!--索引-->
<section id="index">
  <div class="category" v-for="cat in categories" :key="cat.id">
    <button type="text" :id="cat.id" @click="clickCatJson($event)">{{ cat.name }}</button>
  </div>
</section>

<!--列表-->
<section id="category">
    <el-card class="card" v-for="post in postdata" :key="post.id">
      <div class="pic">
      <a :href="post.link"><img :src="post.exts.thumbnail" class="image"></a>
      </div>
      <div class="game-info">
        <div class="game-name-sort">
          <div class="game-name"><a :href="post.link">{{ post.title.rendered }}</a></div>
          <div class="game-sort"><el-button type="text"><a :href="siteurl + '/gamelist?catid=' + (post.exts.categories)[0].cat_id">{{ (post.exts.categories)[0].name }}</a></el-button></div>
        </div>
        <div class="game-version-date">
          <div class="game-version">13.6.2.8</div>
          <div class="game-date">{{ post.modified.split("T")[0] }}</div>  
        </div>
      </div>
    </el-card>
</section>

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