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
      <div id="post-intro" v-html="postcontent"></div>
    </section>
   
    <!--评论-->
    <el-divider></el-divider>
    <section class="post-commit"  v-if="comnum > 0">
      <el-card class="comment-div" v-for="comment in comments" :key="comment.id">
        <div class="comment-card" :id="'comment-main-' + comment.id">
          <div class="comment-left">
            <div class="comment-author-avatar">
              <el-image class="comment-author-avatar-img" fit="fit" :src="comment.author_avatar"></el-image>
            </div>
            <div class="comment-author-name">{{ comment.author_name }}</div>
          </div>
          <div class="comment-right">
            <div class="comment-main">
              <div class="comment-main-content" v-html="comment.content"></div>
              <div class="comment-main-info">
                <div class="comment-main-info-main">
                  <span>{{ comment.date }}</span>
                  <span class="comment-reply-span">
                    <el-button v-if="islogin == 1" type="text" icon="el-icon-edit" @click="commentReply($event)">回复</el-button>
                    <el-button v-else type="text" icon="el-icon-edit" @click="dialogloginVisible">回复</el-button>
                  </span>
                </div>
              </div>
            </div>
            <div class="comment-reply-card" v-if="comment.children" v-for="reply in comment.children" :key="reply.id" :id="'comment-reply-' + comment.id + '-' + reply.id">
              <div class="comment-left">
                <div class="comment-author-avatar">
                  <el-image class="comment-author-avatar-img" fit="fit" :src="reply.author_avatar"></el-image>
                </div>
                <div class="comment-author-name">{{ reply.author_name }}</div>
              </div>
              <div class="comment-right">
                <div class="comment-main">
                  <div class="comment-main-content" v-html="reply.content"></div>
                  <div class="comment-main-info">
                    <div class="comment-main-info-main">
                      <span>{{ reply.date }}</span>
                      <span class="comment-replay-span">
                        <el-button v-if="islogin == 1" type="text" icon="el-icon-edit" @click="commentReply($event)">回复</el-button>
                        <el-button v-else type="text" icon="el-icon-edit" @click="dialogloginVisible">回复</el-button>
                      </span>                      
                    </div>
                  </div>
                </div>
              </div>             
            </div>
          </div>
        </div>
      </el-card>
      <!--回复框-->
      <div id="reply" class="onlyreply" rel="reply" style="display:none;">
        <el-input
          type="textarea"
          :autosize="{ minRows: 2}"
          placeholder="请输入内容"
          v-model="replyTextarea">
        </el-input>
        <div class="reply-button-aria">
          <el-button type="text" icon="el-icon-edit" @click="commentReplyCancle()">取消回复</el-button>
          <el-button id="replyButton" type="text" icon="el-icon-edit" @click="onEditorButtonClicked($event)">发送回复</el-button>
        </div>
      </div>
    </section>
    <section class="post-commit" v-else>
      <div class="comment-head">评论</div>
      <div class="nocomment">还没有评论，试试发布一个吧~</div>
    </section>

    <!--评论框-->
    <section id="report-commit">
      <div id="commit-editor">
        <quill-editor
          ref="myQuillEditor"
          v-model="editorContent"
          :options="editorOption"
          @change="onEditorChange($event)"
        />
      </div>
      <div class="report-button">
        <el-button v-if="islogin == 1" id="reportButton" type="type" icon="el-icon-edit" @click="onEditorButtonClicked($event)">发布评论</el-button>
        <el-button v-else id="reportButton" type="type" icon="el-icon-edit" @click="dialogloginVisible">发布评论</el-button>
      </div>
    </section>
  </el-col>

  <el-col id="post-sidebar" :xs="0" :sm="8" :md="8" :lg="6">
    <!--下载-->
    <section id="post-edit" v-if="islogin == 1">
      <a :href="editurl"><i class="el-icon-edit"></i>编辑</a>
      <a :href="contributeurl"><i class="el-icon-notebook-2"></i>投稿</a>
    </section>
    <section id="post-edit" v-else>
      <el-button type="text" icon="el-icon-edit" @click="dialogloginVisible">编辑</el-button>
      <el-button type="text" icon="el-icon-notebook-2" @click="dialogloginVisible">投稿</el-button>
      <div class="clear"></div>
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