<?php 
/*
 * 首页模板
*/
get_header(); ?>

<section id="cards">
    <el-card class="card" v-for="{o,index} in 8" :key="o">
      <div class="pic">
      <img src="https://media.st.dl.pinyuncloud.com/steam/apps/570/header.jpg?t=1591047995" class="image">
      </div>
      <div class="game-info">
        <div class="game-name-sort">
          <div class="game-name">刀塔2之大聪明的大聪明</div>
          <div class="game-sort"><el-button type="text">即时战略</el-button></div>
        </div>
        <div class="game-version-date">
          <div class="game-version">13.6.2.8</div>
          <div class="game-date">2016-03-25</div>      
        </div>
      </div>
    </el-card>
</section>

<section id="news">
<el-row :gutter="10">
  <el-col :xs="24" :sm="12">
    <el-card class="box-card one">
      <div slot="header" class="clearfix">
        <span>新闻</span>
        <el-button style="float: right; padding: 3px 0" type="text">操作按钮</el-button>
      </div>
      <el-collapse accordion v-model="activeName">
        <el-collapse-item title="一致性" v-for="o in 10" :key="o" name="o">
          <div>与现实生活一致：与现实生活的流程、逻辑保持一致，遵循用户习惯的语言和概念；</div>
        </el-collapse-item>
      </el-collapse>
    </el-card>
  </el-col>

  <el-col :xs="24" :sm="8">
    <el-card class="box-card one">
      <div slot="header" class="clearfix">
        <span>卡片名称</span>
        <el-button style="float: right; padding: 3px 0" type="text">操作按钮</el-button>
      </div>
      <div class="text item" v-for="o in 5" :key="o">列表1</div>
    </el-card>
    <el-card class="box-card one">
      <div slot="header" class="clearfix">
        <span>卡片名称</span>
        <el-button style="float: right; padding: 3px 0" type="text">操作按钮</el-button>
      </div>
      <div class="text item" v-for="o in 5" :key="o">列表1</div>
    </el-card>
  </el-col>

  <el-col :xs="24" :sm="4">
    <div class="images">
      <div class="image">
        <img src="https://media.st.dl.pinyuncloud.com/steam/apps/570/header.jpg?t=1591047995" class="image">
    </div>
  </el-col>
</el-row>
</section>

</section>
<?php get_footer(); ?>
