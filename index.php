<?php 
/*
 * 首页模板
*/
get_header(); ?>

<section id="cards">
    <el-card :body-style="{ padding: '0px' }" class="card" v-for="{o,index} in 12" :key="o">
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
  <template>
    <el-table
      :data="tableData"
      stripe
      style="width: 100%">
      <el-table-column
        prop="date"
        label="游戏"
        width="180">
      </el-table-column>
      <el-table-column
        prop="name"
        label="用户"
        width="180">
      </el-table-column>
      <el-table-column
        prop="address"
        label="评论内容">
      </el-table-column>
    </el-table>
  </template>
</section>
<section id="last-commit">

</section>
<?php get_footer(); ?>
