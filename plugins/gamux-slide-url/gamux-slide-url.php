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
add_action( 'init', 'create_block_gamux_slideurl_block_init' );

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

add_action( 'init', 'gamux_register_template' );