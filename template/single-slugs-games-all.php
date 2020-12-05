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
    <section class="post-commit">
      <div class="commit-head">评论</div>
      <el-card v-for="comment in comments" :key="comment.id">
        <el-row class="comment-card" :gutter="10">
          <el-col :md="3">
            <div class="comment-author-avatar">
              <el-image class="comment-author-avatar-img" fit="fit" src="https://avatars3.githubusercontent.com/u/4121607"></el-image>
            </div>
          </el-col>
          <el-col :md="21">
            <div class="comment-main">
              <div class="comment-author-reply" v-html="comment.content.rendered"></div>
              <div class="comment-reply-card">
                <div class="comment-reply-avatar"></div>
                <div class="comment-reply-text">
                  <time class="time">123</time>
                  <el-button type="text" class="button">操作按钮</el-button>
                </div>
              </div>
            </div>
          </el-col>
        </el-row>
      </el-card>      
    </section>
  </el-col>

  <el-col id="post-sidebar" :xs="0" :sm="8" :md="8" :lg="6">
    <section id="post-edit">
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
    <section id="post-rate">
      <svg class="icon" aria-hidden="true">
        <use xlink:href="#icon-dianzan-copy"></use>
      </svg>
      <svg class="icon" aria-hidden="true">
        <use xlink:href="#icon-dianzan2"></use>
      </svg>      
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