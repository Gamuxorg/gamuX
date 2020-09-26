<?php 
/*
 * games-all template
*/
get_header(); ?>

<div class="breadcrumb">
  <el-page-header :title="catname" @back="goBack" :content="gamename">
</div>
<el-row :gutter="10">
  <el-col id="game-main" :xs="24" :sm="18" :md="18" :lg="19">
    <section class="game-content">
      <div id="game-title">
        {{ gamename }}
      </div>
      <div id="game-intro">
        {{ gamecontent }}
      </div>
    </section>
    <section class="game-download">
    {{ siteurl }}
    </section>    
    <section class="game-commit">
    sdfadsf
    </section>
  </el-col>

  <el-col id="game-sidebar" :xs="0" :sm="6" :md="6" :lg="5">
    <section class="game-thumb">
      <div id="game-title">
        {{ gamename }}
      </div>
      <div id="game-intro">
        123456
      </div>
    </section>
    <section class="game-commit">
    sdfadsf
    </section>
  </el-col>  
</el-row>
<?php get_footer(); ?>