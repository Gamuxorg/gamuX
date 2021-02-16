<?php 
/*
 * Template Name: 搜索
*/
if (!defined('ABSPATH')) exit;
get_header(); ?>

<!--索引-->
<section id="index">
  <el-form :inline="true" :model="formInline" class="demo-form-inline">
    <el-form-item>
      <el-input v-model="formInline.input" placeholder="搜索一下"></el-input>
    </el-form-item>
    <el-form-item>
      <el-button type="primary" @click="onSubmit">查询</el-button>
    </el-form-item>
  </el-form>
</section>

<el-divider></el-divider>
<!--列表-->
<section id="news">
    <el-card class="card" v-for="post in postdata" :key="post.id">
      <div class="game-info">
        <div class="game-name-sort">
          <div class="game-name"><a :href="post.url">{{post.title}}</a></div>
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