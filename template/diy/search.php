<?php 
/*
 * Template Name: 搜索
*/
if (!defined('ABSPATH')) exit;
get_header(); ?>

<!--列表-->
<section id="news">
    <el-card class="card" v-for="post in postdata" :key="post.id">
      <div class="game-info">
        <div class="game-name-sort">
          <div class="game-name"><a :href="post.url">{{ post.title }}</a></div>
          <div class="game-sort">
            <span v-if="(post.exts.cats)[0]">{{ (post.exts.cats)[0].name }}</span>
            <span>{{ post.exts.modified }}</span>
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
      :page-size="10"
      layout="total, prev, pager, next"
      :total="total">
    </el-pagination>
  </div>
</section>

<?php get_footer(); ?>