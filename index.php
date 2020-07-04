<?php 
/*
 * 首页模板
*/
get_header(); ?>

<section id="cards">
  <div class="card" v-for="{o,index} in 12" :key="o">
    <el-card :body-style="{ padding: '0px' }" class="inner-card">
      <div class="pic">
      <img src="https://media.st.dl.pinyuncloud.com/steam/apps/570/header.jpg?t=1591047995" class="image">
      </div>
      <div class="game-info">
        <div class="game-name">刀塔-dota2是打发士大夫撒旦法师的法师打发第三方</div>
        <div class="bottom clearfix game-version">1.35啊实打实地方撒旦法师的法师打发斯蒂芬</div>
      </div>
    </el-card>
  </div>
</section>

<section id="news">
  <template>
    <el-table
      :data="tableData"
      stripe
      style="width: 100%">
      <el-table-column
        prop="date"
        label="日期"
        width="180">
      </el-table-column>
      <el-table-column
        prop="name"
        label="姓名"
        width="180">
      </el-table-column>
      <el-table-column
        prop="address"
        label="地址">
      </el-table-column>
    </el-table>
  </template>
</section>
<section id="last-commit">

</section>
<?php get_footer(); ?>
