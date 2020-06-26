<?php 
/*
 * 首页模板
*/
get_header(); ?>
<div>
<input><button>搜寻</button>
</div>
<section id="cards">
  <div class="card" v-for="{o,index} in 24" :key="o">
    <el-card :body-style="{ padding: '0px' }" class="inner-card">
      <div class="pic">
      <img src="https://shadow.elemecdn.com/app/element/hamburger.9cf7b091-55e9-11e9-a976-7f4d0b07eef6.png" class="image">
      </div>
      <div style="padding: 14px;">
        <span>好吃的汉堡</span>
        <div class="bottom clearfix">
          <time class="time">121212</time>
          <el-button type="text" class="button">操作按钮</el-button>
        </div>
      </div>
    </el-card>
  </div>
</section>
<?php get_footer(); ?>
