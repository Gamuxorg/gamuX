<?php
/**
 * Plugin Name:     gamux轮播图URL区块
 * Description:     Block written with ESNext standard and JSX support – build step required.
 * Version:         1.1.0
 * Author:          hyh19962008@Github
 * License:         GPL-3.0
 * License URI:     https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:     slide-url
 *
 * @package         create-block
 */

/**
 * Registers all block assets so that they can be enqueued through the block editor
 * in the corresponding context.
 *
 * @see https://developer.wordpress.org/block-editor/tutorials/block-tutorial/applying-styles-with-stylesheets/
 */
namespace Gamux;

function create_block_gamux_slideurl_block_init() {
	$dir = __DIR__;

	$script_asset_path = "$dir/build/index.asset.php";
	if ( ! file_exists( $script_asset_path ) ) {
		throw new Error(
			'You need to run `npm start` or `npm run build` for the "gamux/slideURL" block first.'
		);
	}
	$index_js     = 'build/index.js';
	$script_asset = require( $script_asset_path );
	wp_register_script(
		'gamux-slide-url-block-editor',
		plugins_url( $index_js, __FILE__ ),
		$script_asset['dependencies'],
		$script_asset['version']
	);
	// wp_set_script_translations( 'create-block-gutenpride-block-editor', 'gutenpride' );

	$editor_css = 'build/index.css';
	wp_register_style(
		'gamux-slide-url-block-editor',
		plugins_url( $editor_css, __FILE__ ),
		array(),
		filemtime( "$dir/$editor_css" )
	);

	// $style_css = 'build/style-index.css';
	// wp_register_style(
	// 	'create-block-gutenpride-block',
	// 	plugins_url( $style_css, __FILE__ ),
	// 	array(),
	// 	filemtime( "$dir/$style_css" )
	// );

	register_block_type( 'gamux/slide-url', array(
		'editor_script' => 'gamux-slide-url-block-editor',
		'editor_style'  => 'gamux-slide-url-block-editor'
	) );
}
add_action( 'init', '\Gamux\create_block_gamux_slideurl_block_init' );

/**
 * 注册文章的默认内容，在文章开头添加以下内容，并阻止用户删除
 *       ---轮播图片---
 * 
 * 		  <轮播图URL>
 * 
 *       ---文章正文---
 *
 * @return void
 */
function gamux_register_template() {
    $post_type_object = get_post_type_object( 'post' );
    $post_type_object->template = array(
		array('core/paragraph', [
			'align' => 'center',
			'content' => '<strong class="has-medium-font-size" contenteditable="false">----------------------------------轮播图片---------------------------------------</strong>'
		]),
		array('gamux/slide-url'),
		array('gamux/slide-url'),
		array('core/paragraph', [
			'align' => 'center',
			'content' => '<strong class="has-medium-font-size" contenteditable="false">-----------------------------------文章正文---------------------------------------</strong>'
		]),
		array('core/paragraph', ['placeholder' => '开始正文'])
	);
	// $post_type_object->template_lock = "insert";
}
add_action('init', '\Gamux\gamux_register_template');

/**
 * 用户文章中没有正确添加轮播分隔符，重新添加
 *
 * @param [type] $content
 * @param integer $status 错误情形状态码
 * @param integer $ext_pos 发生错误的下标
 * @param string $delimiter_content_start
 * @param string $delimiter_content_end
 * @param string $delimiter_slide_start
 * @param string $delimiter_slide_end
 * 
 * @return string $content
 */
function reorder_slides_delimiter($content, int $status, int $ext_pos = 0, string $delimiter_content_start, string $delimiter_content_end, string $delimiter_slide_start, string $delimiter_slide_end) : string {
	$encoding = "UTF-8";

	// 提取所有的轮播区块
	$slides = [];
	do {
		$last_pos = 0;
		$pos_slide_start = mb_strpos($content, $delimiter_slide_start, $last_pos, $encoding);
		$last_pos = $pos_slide_start + mb_strlen($delimiter_slide_start);
		$pos_slide_end = mb_strpos($content, $delimiter_slide_end, $last_pos, $encoding) + mb_strlen($delimiter_slide_end);
		$last_pos = $pos_slide_end;
		$slide = mb_substr($content, $pos_slide_start, $pos_slide_end - $pos_slide_start, $encoding);
		if($pos_slide_start !== false) {
			array_push($slides, $slide);
			// 删除该轮播区块
			$content_head = mb_substr($content, 0, $pos_slide_start, $encoding);
			$content_tail = mb_substr($content, $pos_slide_end, null, $encoding);
			$content = $content_head . $content_tail;
		}
	}while($pos_slide_start !== false);
	
	$slides_html = "";
	foreach($slides as $slide) {
		$slides_html = $slides_html . $slide . "\n\n";
	}

	switch($status) {
		case 0:				//缺少起始分隔
			// 删除结束分隔
			$pos = mb_strpos($content, $delimiter_content_end, 0, $encoding);
			if($pos !== false) {
				$content_head = mb_substr($content, 0, $pos, $encoding);
				$content_tail = mb_substr($content, $pos + mb_strlen($delimiter_content_end), null, $encoding);
				$content = $content_head . $content_tail;
			}
			break;
		case 1:				//起始分隔位置不正确
			// 删除起始分隔
			$content_head = mb_substr($content, 0, $ext_pos, $encoding);
			$content_tail = mb_substr($content, $ext_pos + mb_strlen($delimiter_content_start), null, $encoding);
			$content = $content_head . $content_tail;
			// 删除结束分隔
			$pos = mb_strpos($content, $delimiter_content_end, 0, $encoding);
			if($pos !== false) {
				$content_head = mb_substr($content, 0, $pos, $encoding);
				$content_tail = mb_substr($content, $pos + mb_strlen($delimiter_content_end), null, $encoding);
				$content = $content_head . $content_tail;
			}
			break;
		case 2:				//缺少结束分隔
			// 删除起始分隔
			$content = mb_substr($content, $ext_pos + mb_strlen($delimiter_content_start), null, $encoding);
			break;
	}
	$content = $delimiter_content_start . "\n\n" . $slides_html . $delimiter_content_end . "\n\n" . $content;
	str_replace("\n\n\n", "\n", $content);
	return $content;
}

/**
 * 检查用户添加的文章中是否有轮播图URL区块，如果有，则检查其是否正确添加了分隔符
 *
 * @param  $content
 * @return $content
 */
function filter_slides_delimiter($content) {
		$len = mb_strlen($content);
		$encoding = "UTF-8";
		$body = "";

		// 查找是否有插入轮播图
		$slides = [];
		$delimiter_slide_start = '<!-- wp:gamux/slide-url -->';
		$delimiter_slide_end = '<!-- /wp:gamux/slide-url -->';
		$pos_slide_start = mb_strpos($content, $delimiter_slide_start, 0, $encoding);
		$pos_slide_end = mb_strrpos($content, $delimiter_slide_end, 0, $encoding) + mb_strlen($delimiter_slide_end);
		if($pos_slide_start !== false and $pos_slide_end !== false) {		
			$delimiter_content_start = <<<doc1
<!-- wp:paragraph {"align":"center"} -->
<p class="has-text-align-center"><strong class="has-medium-font-size" contenteditable="false">----------------------------------轮播图片---------------------------------------</strong></p>
<!-- /wp:paragraph -->
doc1;
			$delimiter_content_end = <<<doc2
<!-- wp:paragraph {"align":"center"} -->
<p class="has-text-align-center"><strong class="has-medium-font-size" contenteditable="false">-----------------------------------文章正文---------------------------------------</strong></p>
<!-- /wp:paragraph -->
doc2;
			$delimiter_content_start = str_replace('"', '\"', $delimiter_content_start);
			$delimiter_content_end = str_replace('"', '\"', $delimiter_content_end);

			$pos = mb_strpos($content, $delimiter_content_start, 0, $encoding);
			if($pos === false) {		//缺少起始分隔
				$content = reorder_slides_delimiter($content, $status = 0, $pos = 0, $delimiter_content_start, $delimiter_content_end, $delimiter_slide_start, $delimiter_slide_end);
			}
			else {						//存在起始分隔
				if($pos !== 0) {		//起始分隔位置不正确
					$content = reorder_slides_delimiter($content, $status = 1, $pos, $delimiter_content_start, $delimiter_content_end, $delimiter_slide_start, $delimiter_slide_end);
				}
				else {					//起始分隔位置正确
					$pos = mb_strpos($content, $delimiter_content_end, $pos + mb_strlen($delimiter_content_start, $encoding), $encoding);
					if($pos !== false)	//存在结束分隔，排版正常，不做修改
						return $content;
					else {				//缺少结束分隔
						$content = reorder_slides_delimiter($content, $status = 2, $pos = 0, $delimiter_content_start, $delimiter_content_end, $delimiter_slide_start, $delimiter_slide_end);
					}
				}
			}
		}

		return $content;
}
add_filter('content_save_pre', '\Gamux\filter_slides_delimiter');

// 将<slide></slide>标签添加到允许使用的HTML标签中，防止被过滤
function add_allowed_tag_slide($tags) {
    $tags['slide'] = array();
    return $tags;
}
add_filter('wp_kses_allowed_html', '\Gamux\add_allowed_tag_slide');