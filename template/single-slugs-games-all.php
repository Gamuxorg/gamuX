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
   
    <!--评论-->
    <el-divider></el-divider>
    <section class="post-commit" v-if="comnum > 0">
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
    <!--下载-->
    <section id="post-edit" v-if="islogin == 1">
      <a :href="editurl"><i class="el-icon-edit"></i>编辑</a>
      <a :href="contributeurl"><i class="el-icon-notebook-2"></i>投稿</a>
      <el-button type="text" icon="el-icon-download" @click="dialogdownload = true">下载游戏</el-button>
    </section>
    <section id="post-edit" v-else>
      <el-button type="text" icon="el-icon-edit" @click="dialogloginVisible">编辑</el-button>
      <el-button type="text" icon="el-icon-notebook-2" @click="dialogloginVisible">投稿</el-button>
      <el-button type="text" icon="el-icon-download" @click="dialogloginVisible">登录下载</el-button>
      <div class="clear"></div>
    </section>
    <el-dialog id="download" :title="`下载链接·${downloadlist['message']}`" :visible.sync="dialogdownload">
      <el-table :data="downloadlist.downloadList" :default-sort="{prop: 'version', order: 'descending'}" max-height="70vh">
        <el-table-column
          prop="version"
          label="版本"
          sortable>
        </el-table-column>        
        <el-table-column
          prop="date"
          label="日期"
          sortable>
        </el-table-column>
        <el-table-column
          prop="downloadCount"
          label="下载量"
          sortable>
        </el-table-column>
        <el-table-column
          prop="volume"
          label="容量"
          sortable>
        </el-table-column>
        <el-table-column
          prop="link"
          label="下载">
          <template slot-scope="scope">
            <el-link :href="scope.row.link" type="primary" target="_blank">下载</el-link>
          </template>
        </el-table-column>
        <el-table-column
          prop="comment"
          label="备注">
        </el-table-column>
      </el-table>
    </el-dialog>
    
    <!--特色图片-->
    <section id="post-thumb">
      <div id="post-thumb-div">
        <img :src="thumbnail" intrinsicsize="2x1">
      </div>
    </section>

    <!--购买链接-->
    <section id="post-buy">
      <div class="post-buy-div" v-for="buy in buyurls">
        <a :href="buy.link" target="_blank">
          <svg class="aliicon" aria-hidden="true">
            <use xlink:href="#icon-gamux-buy"></use>
          </svg>
          {{ buy.store }}
        </a>
      </div>
    </section>

    <!--标签-->
    <section id="post-tag">
      <el-divider class="tag-title">本文标签</el-divider>
      <div class="tag-content" v-html="taglist"></div>
    </section>

    <!--文章修改记录-->
    <section id="timeline">
    <el-divider class="tag-title">修订记录</el-divider>
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