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
    <section class="post-commit" v-if="comnum > 0">
      <div class="comment-head">评论</div>
      <el-card class="comment-card" v-for="comment in comments" :key="comment.id">
        <el-row class="comment-card" :gutter="10">
          <el-col class="comment-left" :xs="24" :sm="4" :md="3" :lg="3">
            <div class="comment-author-avatar">
              <el-image class="comment-author-avatar-img" fit="fit" src="https://avatars3.githubusercontent.com/u/4121607"></el-image>
            </div>
            <div class="comment-author-name">{{ comment.author_name }}</div>
          </el-col>
          <el-col class="comment-right" :xs="24" :sm="20" :md="21" :lg="21">
            <div class="comment-main">
              <div class="comment-main-content" v-html="comment.content"></div>
              <div class="comment-main-info">
                <div class="comment-main-info-main">
                  <span>{{ comment.date }}</span>
                  <span><el-button type="text" icon="el-icon-edit">回复</el-button></span>
                </div>
              </div>
            </div>
            <el-row class="comment-reply-card" :gutter="10" v-if="comment.children" v-for="reply in comment.children" :key="reply.id">
              <el-col class="comment-left" :xs="24" :sm="3" :md="2" :lg="1">
                <div class="comment-author-avatar">
                  <el-image class="comment-author-avatar-img" fit="fit" src="https://avatars3.githubusercontent.com/u/4121607"></el-image>
                </div>
                <div class="comment-author-name">{{ reply.author_name }}</div>
              </el-col>
              <el-col class="comment-right" :xs="24" :sm="21" :md="22" :lg="23">
                <div class="comment-main-content" v-html="reply.content"></div>
                <div class="comment-main-info">
                  <div class="comment-main-info-main">
                    <span>{{ reply.date }}</span>
                    <span><el-button type="text" icon="el-icon-edit">回复</el-button></span>
                  </div>
                </div>
              </el-col>             
            </el-row>
          </el-col>
        </el-row>
      </el-card>
    </section>
    <section class="post-commit" v-else>
      <div class="comment-head">评论</div>
      <div class="nocomment">还没有评论，试试发布一个吧~</div>
    </section>
    <section id="report-commit">
      <div id="commit-editor">
        <quill-editor
          ref="myQuillEditor"
          v-model="editorContent"
          :options="editorOption"
          @blur="onEditorBlur($event)"
          @focus="onEditorFocus($event)"
          @ready="onEditorReady($event)"
          @change="onEditorChange($event)"
        />
      </div>
      <div class="report-button">
        <el-button type="type" icon="el-icon-edit" @click="onEditorButtonClicked">发布评论</el-button>
      </div>
    </section>
  </el-col>

  <el-col id="post-sidebar" :xs="0" :sm="8" :md="8" :lg="6">
    <section id="post-edit" v-if="islogin == 1">
    <a :href="editurl"><el-button type="primary" icon="el-icon-edit">编辑</el-button></a>
    <a :href="contributeurl"><el-button type="primary" icon="el-icon-notebook-2">投稿</el-button></a>
    </section>
    <section id="post-edit" v-else>
      <el-button type="primary" icon="el-icon-edit" @click="dialogloginVisible">编辑</el-button>
      <el-button type="primary" icon="el-icon-notebook-2" @click="dialogloginVisible">投稿</el-button>
      <div class="clear"></div>
    </section>
       
    <section id="post-thumb">
      <div id="post-thumb-div">
        <img :src="thumbnail" intrinsicsize="2x1">
      </div>
    </section>
    <section id="post-buy">
      <div class="post-buy-div" v-for="buy in buyurls">
        <a :href="buy.url" target="_blank">
          <svg class="aliicon" aria-hidden="true">
            <use xlink:href="#icon-gamux-buy"></use>
          </svg>
          购买·{{ buy.text }}
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