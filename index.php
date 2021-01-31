<?php 
/*
 * 首页模板
*/
get_header(); ?>
<section id="carousel" ref="car">
<el-row :gutter="10">

  <!--首页轮播-->
  <el-col class="car-col-1" ref="carcol1" :xs="24" :sm="12" :md="12" :lg="10">
   <el-carousel class="car-car" :interval="5000" arrow="always" indicator-position="none">
    <el-carousel-item v-for="item in slidedata" :key="item.id">
      <a :href="item.postLink" target="_blank"><img :src="item.imageSrc"></a>
    </el-carousel-item>
   </el-carousel>
  </el-col>

  <el-col class="car-col-2" ref="carcol2" :xs="24" :sm="12" :md="12" :lg="9" :style="{height: carHeight + 'px'}">

  <?php 
    query_posts("showposts=14&category_name=news"); if (have_posts()) : while (have_posts()) : the_post();
  ?> 
    <div class="car-col2-second"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></div>
  <?php  endwhile; endif; ?>
  <?php wp_reset_query(); ?>
  </el-col>

  <el-col class="car-col-3" :xs="0" :sm="0" :md="0"  :lg="5">
  123
  </el-col>

</el-row>
</section>

<div class="underline">
  <div class="ul-left">游戏上新</div>
</div>

<section id="cards">
    <el-card class="card" v-for="post in postdata" :key="post.id">
      <div class="pic">
      <a :href="post.link"><img :src="post.exts.thumbnail" class="image"></a>
      </div>
      <div class="game-info">
        <div class="game-name-sort">
          <div class="game-name"><a :href="post.link" v-html="post.title.rendered"></a></div>
          <div class="game-sort"><el-button type="text"><a :href="'/gamelist?catid=' + (post.exts.categories)[0].cat_id">{{ (post.exts.categories)[0].name }}</a></el-button></div>
        </div>
        <div class="game-version-date">
          <!-- 无版本号则返回一个默认值 -->
          <div class="game-version" v-if="post['exts']['version'] == ''"> ver1.0 </div>
          <div class="game-version" v-else>{{ post['exts']['version'] }}</div>
          <div class="game-date">{{ post.modified.split("T")[0] }}</div>
        </div>
      </div>
    </el-card>
</section>

<div class="underline">
  <div class="ul-left">杂货铺</div>
  <div class="ul-right"></div>
</div>

<section id="grocery">
<el-row :gutter="10">

  <el-col class="gro-col-1" ref="grocol1" :xs="24" :sm="12" :md="12" :lg="10">
    <el-card class="comment-card box-card">
      <div slot="header" class="commment-header clearfix">
        <span>游戏评论</span>
        <el-button style="float: right; padding: 3px 0" type="text"><a href='<?php echo site_url("/wp-admin/edit-comments.php") ?>'>更多评论</a></el-button>
      </div>
      <div class="comment-list text item">
      <?php
      $comments = get_comments('status=approve&number=10');
      foreach($comments as $comment) :
      ?>
        <div class="comment-text">
          <div class="reply-game"><el-tag>
            <?php $tmpTitle = get_post($comment->comment_post_ID)->post_title; 
                  if(mb_strlen($tmpTitle) > 15)
                    $tmpTitle = mb_substr($tmpTitle, 0, 14) . "...";
                  echo $tmpTitle;
                  unset($tmpTitle);
            ?>
          </el-tag></div>
          <el-badge :value=<?php echo get_comments_number($comment->comment_post_ID) ?> :max="9" class="comment-reply item">
            <span class="reply-text">回复</span>
          </el-badge>
          <a href="<?php echo esc_url( get_comment_link($comment->comment_ID) ); ?>">
            <?php echo  mb_strimwidth(strip_tags($comment->comment_content), 0, 50, '...'); ?>
          </a>
        </div>
      <?php endforeach;?>
      </div>
    </el-card>
  </el-col>

  <el-col class="gro-col-2" ref="grocol2" :xs="24" :sm="12" :md="12" :lg="10">
    <el-card class="wish-card box-card">
      <div slot="header" class="wish-header clearfix">
        <span>愿望清单</span>
        <el-button style="float: right; padding: 3px 0" type="text" >更多评论</el-button>
      </div>
      <div class="wish-lists text item">
        <div class="wish-text" v-for="wish in wishlist">
          <div class="wish-title"><a :href="wish.html_url" target="_blank">{{ wish.title }}</a></div>
        </div>
      </div>
    </el-card>
  </el-col>

</el-row>
</section>

</section>
<?php get_footer(); ?>
