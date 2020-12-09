<?php 
/*
 * games-all template
*/
get_header(); ?>

<div class="breadcrumb">
  <el-page-header :title="catname" @back="goBack" :content="postdate">
</div>

<el-row id="post" :gutter="10">
  <el-col id="post-main" :xs="24" :sm="16" :md="16" :lg="18">
    <section class="game-content">
      <div id="post-title">
        {{ postname }}
      </div>
      <el-carousel :interval="4000" :type="imgtype" arrow="never">
        <el-carousel-item v-for="item in 4" :key="item">          
          <el-image fit="fit" src="https://media.st.dl.pinyuncloud.com/steam/apps/812140/ss_0ef33c0f230da6ebac94f5959f0e0a8bbc48cf8a.600x338.jpg?t=1602601042"></el-image>
        </el-carousel-item>
      </el-carousel>
      <div id="post-intro" v-html="postcontent"></div>
    </section>
    <section class="post-download">
      <div class="download-head">下载地址</div>
      <div class="download-main">
        <el-table :data="tableData">
        <el-table-column
            prop="version"
            label="版本">
          </el-table-column>        
          <el-table-column
            prop="date"
            label="日期">
          </el-table-column>
          <el-table-column
            prop="quantity"
            label="下载量">
          </el-table-column>
          <el-table-column
            prop="volume"
            label="容量">
          </el-table-column>
          <el-table-column
            prop="link"
            label="下载地址">
          </el-table-column>
          <el-table-column
            prop="remark"
            label="备注">
            
          </el-table-column>
        </el-table>
      </div>
    </section>    
    <?php comments_template(); ?>
  </el-col>

  <el-col id="post-sidebar" :xs="0" :sm="8" :md="8" :lg="6">
    <section id="post-edit" v-if="islogin == true">
    <a :href="editurl"><el-button type="primary" icon="el-icon-edit">编辑</el-button></a>
    <a :href="contributeurl"><el-button type="primary" icon="el-icon-notebook-2">投稿</el-button></a>
    </section>
    <section id="post-edit" v-else>
      <el-button type="primary" icon="el-icon-edit">编辑</el-button>
      <el-button type="primary" icon="el-icon-notebook-2">投稿</el-button>
    </section>
       
    <section id="post-thumb">
      <el-image :src="thumbnail" fit="fill"></el-image>
    </section>
    <section id="post-buy">
      <div class="post-buy-div" v-for="buy in buyurls">
        <a :href="buy.url" target="_blank">
          <svg class="icon" aria-hidden="true">
            <use xlink:href="#icon-gamux-buy"></use>
          </svg>
          {{ buy.text }}
        </a>
      </div>
    </section>
    <section id="timeline">
      <el-timeline>
        <el-timeline-item
          v-for="(activity, index) in activities"
          :key="index"
          :icon="activity.icon"
          :type="activity.type"
          :size="activity.size"
          :timestamp="activity.timestamp">
          {{activity.author}}{{activity.content}}
        </el-timeline-item>
      </el-timeline>
    </section>
  </el-col>  
</el-row>
<?php get_footer(); ?>