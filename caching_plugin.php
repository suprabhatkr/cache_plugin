<?php
	/**
	* @package Supra_plugin
	*/
	/*
	Plugin Name: Cache_plugin
	Plugin URI: http://127.0.0.1
	Description: This is my first attemp on writing plugin
	Version: 1.0.0
	Author: Operations
	Author URI: http://127.0.0.1/wordpress_blog/wp-content/plugins/image_compress/button.php
	License: GPLv2 or later
	Text Domain: Supra Plugin
	*/
	if(!defined('ABSPATH')){
		die;
	}
	defined('ABSPATH') or die("You can't access");
	if (! function_exists('add_action')){
		echo "You can't access";
	}
	if(!function_exists('wp_get_current_user')) {
    		include(ABSPATH . "wp-includes/pluggable.php");
	}
	define('HOME_URL',get_option('home'));
	$Site_name = basename(HOME_URL);
	add_filter('plugin_action_links_'.plugin_basename(__FILE__), 'salcode_add_plugin_page_settings_link');
	function salcode_add_plugin_page_settings_link( $links ) {
		$links[] = '<a href="'.HOME_URL.'/wp-admin/options-general.php?page=cp-cache-plugin">optimise </a>';
		return $links;
	}

	function update_wp_config(){
		if ( file_exists( ABSPATH . 'wp-config.php') ) {
			$global_config_file = ABSPATH . 'wp-config.php';
		} else {
			$global_config_file = dirname( ABSPATH ) . '/wp-config.php';
		}
		$line = 'if(!defined(\'WP_CACHE\'))define(\'WP_CACHE\', true);';
		$early_line = 'define( \'DB_NAME\'';
		if ($global_config_file){
			$config_file = fopen($global_config_file,"r");
			$content = fread($config_file, filesize($global_config_file));
			if (!(preg_match('@WP_CACHE@', $content))){
				fclose($config_file);
				$config_file = fopen($global_config_file,"w");
				$updated_content = str_replace($early_line, $line."\n".$early_line, $content);
				fwrite($config_file,$updated_content);
			}
			fclose($config_file);
		}
	}
	function reverse_wp_config(){
		if ( file_exists( ABSPATH . 'wp-config.php') ) {
			$global_config_file = ABSPATH . 'wp-config.php';
		} else {
			$global_config_file = dirname( ABSPATH ) . '/wp-config.php';
		}
		$line = 'if(!defined(\'WP_CACHE\'))define(\'WP_CACHE\', true);';
		if ($global_config_file){
			$config_file = fopen($global_config_file,"r");
			$content = fread($config_file, filesize($global_config_file));
			if ((preg_match('@WP_CACHE@', $content))){
				fclose($config_file);
				$config_file = fopen($global_config_file,"w");
				$updated_content = str_replace( $line."\n","", $content);
				fwrite($config_file,$updated_content);
			}
			fclose($config_file);
		}
	}

	function update_advance_cache(){
		$advanced_cache = fopen(WP_CONTENT_DIR."/advanced-cache.php","w+");
		$temp_advanced_cache = fopen(WP_CONTENT_DIR."/plugins/caching_plugin/advanced_cache.php", 'r');
		$content = fread($temp_advanced_cache, filesize(WP_CONTENT_DIR."/plugins/caching_plugin/advanced_cache.php"));
		fwrite($advanced_cache, $content);
		fclose($temp_advanced_cache);
		fclose($advanced_cache);
	}
	function reverse_advance_cache(){
		unlink(WP_CONTENT_DIR."/advanced-cache.php");
	}
	function update_wp_comment(){
		if ( file_exists( ABSPATH . 'wp-comments-post.php') ) {
			$global_comments_file = ABSPATH . 'wp-comments-post.php';
		} else {
			$global_comments_file = dirname( ABSPATH ) . '/wp-comments-post.php';
		}
		$temp_comments = fopen(WP_CONTENT_DIR."/plugins/caching_plugin/wp-comments-post.php", 'r');
		$line = fread($temp_comments, filesize(WP_CONTENT_DIR."/plugins/caching_plugin/wp-comments-post.php"));
		fclose($temp_comments);
		$early_line = 'wp_get_current_user();';
		if ($line){
			$comment_file = fopen($global_comments_file,"r");
			$content = fread($comment_file, filesize($global_comments_file));
			if (!(preg_match('@'.$line.'@', $content))){
				fclose($comment_file);
				$comment_file = fopen($global_comments_file,"w");
				$updated_content = str_replace($early_line, $early_line."\n".$line, $content);
				fwrite($comment_file,$updated_content);
			}
			fclose($comment_file);
		}
	}
	function reverse_wp_comment(){
		if ( file_exists( ABSPATH . 'wp-comments-post.php') ) {
			$global_comments_file = ABSPATH . 'wp-comments-post.php';
		} else {
			$global_comments_file = dirname( ABSPATH ) . '/wp-comments-post.php';
		}
		$temp_comments = fopen(WP_CONTENT_DIR."/plugins/caching_plugin/wp-comments-post.php", 'r');
		$line = fread($temp_comments, filesize(WP_CONTENT_DIR."/plugins/caching_plugin/wp-comments-post.php"));
		if ($global_comments_file){
			$comment_file = fopen($global_comments_file,"r");
			$content = fread($comment_file, filesize($global_comments_file));
			if ((preg_match('@suprabhat@', $content))){
				fclose($comment_file);
				$comment_file = fopen($global_comments_file,"w");
				$updated_content = str_replace("\n".$line, "", $content);
				fwrite($comment_file,$updated_content);
			}
			fclose($comment_file);
		}
		fclose($temp_comments);
	}
	function make_cache_file(){
		$cache_page_path = WP_CONTENT_DIR;
		$path = "/cache/suprabhat";
		$path=explode("/", $path);
		foreach($path as $each_dir){
			$cache_page_path.=($each_dir."/");
			if (!is_dir($cache_page_path)) mkdir($cache_page_path, 0777, true);
		}
	}
	function deleteDirectory($dir) {
	    if (!file_exists($dir)) {
	        return true;
	    }
	    if (!is_dir($dir)) {
	        return unlink($dir);
	    }
	    foreach (scandir($dir) as $item) {
	        if ($item == '.' || $item == '..') {
	            continue;
	        }
	        if (!deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) {
	            return false;
	        }
	    }
	    return rmdir($dir);
	}
	function remove_cache_file(){
		deleteDirectory(WP_CONTENT_DIR."/cache/suprabhat");
	}
	function update_blog_header(){
		$line = 'require_once WP_CONTENT_DIR.\'/plugins/caching_plugin/opt2.php\';';
		$blog_header = fopen(ABSPATH."/wp-blog-header.php",'a');
		fwrite($blog_header, $line);
		fclose($blog_header);
	}
	function reverse_blog_header(){
		$line = 'require_once WP_CONTENT_DIR.\'/plugins/caching_plugin/opt2.php\';';
		$blog_header = fopen(ABSPATH."/wp-blog-header.php",'r');
		$content = fread($blog_header, filesize(ABSPATH."/wp-blog-header.php"));
		fclose($blog_header);
		$blog_header = fopen(ABSPATH."/wp-blog-header.php",'w');
		$new_content = str_replace($line, "", $content);
		fwrite($blog_header, $new_content);
		fclose($blog_header);
	}
	global $wpdb;
	function activate_img_plugin(){
		global $wpdb;
		$wpdb->query("insert into wp_options(option_name,option_value) values ('sup_webp',false);");
		$wpdb->query("insert into wp_options(option_name,option_value) values ('sup_compress',false);");
		$wpdb->query("insert into wp_options(option_name,option_value) values ('sup_defer',false);");
		$wpdb->query("insert into wp_options(option_name,option_value) values ('sup_aggregate',false);");
		$wpdb->query("insert into wp_options(option_name,option_value) values ('sup_cache_js',true);");
		$wpdb->query("insert into wp_options(option_name,option_value) values ('sup_cache_css',true);");
		$wpdb->query("insert into wp_options(option_name,option_value) values ('sup_agg_css',false);");
		$wpdb->query("insert into wp_options(option_name,option_value) values ('sup_defer_css',false);");
		$wpdb->query("insert into wp_options(option_name,option_value) values ('sup_cache',true);");
		update_blog_header();
		update_wp_config();
		update_advance_cache();
		update_wp_comment();
		make_cache_file();
	}
	function deactivate_img_plugin(){
		global $wpdb;
		$wpdb->query("delete from wp_options where option_name='sup_webp';");
		$wpdb->query("delete from wp_options where option_name='sup_compress';");
		$wpdb->query("delete from wp_options where option_name='sup_defer';");
		$wpdb->query("delete from wp_options where option_name='sup_aggregate';");
		$wpdb->query("delete from wp_options where option_name='sup_cache_js';");
		$wpdb->query("delete from wp_options where option_name='sup_cache_css';");
		$wpdb->query("delete from wp_options where option_name='sup_defer_css';");
		$wpdb->query("delete from wp_options where option_name='sup_agg_css';");
		$wpdb->query("delete from wp_options where option_name='sup_cache';");
		reverse_blog_header();
		reverse_wp_comment();
		reverse_advance_cache();
		reverse_wp_config();
		remove_cache_file();
	}
	register_activation_hook(__FILE__,'activate_img_plugin');
	register_deactivation_hook(__FILE__,'deactivate_img_plugin');
	function get_data($plugin_opt){
		global $wpdb;
		$option = $wpdb->get_results("select * from wp_options where option_name='".$plugin_opt."';");
		return get_option($plugin_opt);
	}
	function replace_attribute(string $url){
		$new_url = "";
		$url = str_split($url);
		foreach($url as $c){
			if ($c=='?'){
				return $new_url;
			}
			$new_url.=$c;
		}
		return $new_url;
	}
	function replace_with_minified( $tag, $handle, $src ) {
		if (preg_match("@fonts.google.com@", $src))
		{
			$tag = str_replace(basename($src), "google_font.css", $tag);
		}
		else{
	        $cache_src = str_replace(dirname($src), HOME_URL."/wp-content/cache/suprabhat", $src);
	        $cache_file = str_replace(dirname($src), ABSPATH."wp-content/cache/suprabhat", $src);
	        $cache_file = replace_attribute($cache_file);
	        $cdn_src=str_replace('http://', 'http://localhost:3000/api/v1/articles/', $src);
	        #echo $cache_file;
			if(true)
				$tag = str_replace($src,$cdn_src, $tag);
	    }
	    return $tag;
	}
	function remove_js_tags($tag,$handle,$src){
		if (!empty($src)  and !preg_match("@jquery@", $src)){
			$tag = "";
		}
		return $tag;
	}
	function remove_css_tags($tag,$handle,$src){
		if (!empty($src)  and !preg_match("@admin-bar@", $src) and preg_match("@".$Site_name."@", $src)){
			$tag = "";
		}
		return $tag;
	}
	function replace_with_minify($tag,$handle,$src){
		$cache_src = str_replace(dirname($src), HOME_URL."/wp-content/cache/suprabhat", $src);
		$cache_file = str_replace(dirname($src), ABSPATH."wp-content/cache/suprabhat", $src);
		$cache_file = replace_attribute($cache_file);
		$cdn_src=str_replace('http://', 'http://localhost:3000/api/v1/articles/', $src);
		if(true)
			$tag = str_replace($src,$cdn_src, $tag);
		return $tag;
	}
	function add_defer_to_script( $tag, $handle, $src ) {
	    $tag = str_replace("<script", "<script defer", $tag);
	    return $tag;
	}
	function add_defer_to_css( $html, $handle, $href, $media ) {
		if(!preg_match("@wp-admin@", $href))
	    $html = str_replace("'stylesheet'", "'preload' as='style' onload=\"this.rel='stylesheet'\"", $html);
	    return $html;
	}
	function add_aggregate_file(){
		echo "<script defer src=\"".ABSPATH."/wp-content/cache/suprabhat/aggregateSuprabhat.js\"></script>";	
	}
	function add_aggregate_css(){
		echo "<link rel='preload' as='style' onload=\"this.rel='stylesheet'\"id='aggregate-css'  href='".HOME_URL."/wp-content/cache/suprabhat/aggregateCSS.css' type='text/css' media='all' />";	
	}
	function add_critical_css(){
		echo "<link rel='stylesheet' id='critical-css'  href='".HOME_URL."/wp-content/cache/suprabhat/criticalCSS.css' type='text/css' media='all' />";	
	}
	$to_defer = get_data("sup_defer");
	$to_minify = get_data("sup_cache_js");
	$to_aggregate = get_data("sup_aggregate");
	$to_aggregate_css = get_data("sup_agg_css");
	$to_minify_css = get_data("sup_cache_css");
	$to_defer_css = get_data("sup_defer_css");
	#echo $to_minify.$to_defer.$to_minify_css.$to_minify_css;
	if($to_defer){
		add_filter( 'script_loader_tag', 'add_defer_to_script', 20, 3 );
	}
	if (true){
		add_filter( 'script_loader_tag', 'replace_with_minify',20,3);
	}
	if ($to_aggregate){
		add_filter('script_loader_tag','remove_js_tags',20,3);
	}
	if ($to_aggregate_css){
		add_filter('style_loader_tag','remove_css_tags',20,3);
		add_action('wp_footer','add_aggregate_css');
	}
	if (true){
		add_filter( 'style_loader_tag', 'replace_with_minified', 20, 3 );
	}
	if ($to_defer_css){
		add_filter( 'style_loader_tag', 'add_defer_to_css', 10, 4 );
	}
	require 'options_page.php';
	@ob_start();
?>


