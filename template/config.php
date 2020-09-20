<?php

TEMPLATE_DIR = "/template";
SINGLE_COMMON_NAME = "single-";

//定义模板文件存放目录
 define(SINGLE_PATH, TEMPLATEPATH . $TEMPLATE_DIR);
 //自动选择模板的函数
 function gamux_single_template($single) {
  global $wp_query, $post;
  //通过分类别名或ID选择模板文件
  foreach((array)get_the_category() as $cat) :
    if(file_exists(SINGLE_PATH . $SINGLE_COMMON_NAME . $cat->slug . '.php'))
      return SINGLE_PATH . $SINGLE_COMMON_NAME . $cat->slug . '.php';
 }
 //通过 single_template 钩子挂载函数
 add_filter('single_template', 'mobantu_single_template');
 ?>