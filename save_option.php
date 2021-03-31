<?php
	global $wpdb;
	$abspath = str_replace("wp-content/plugins/caching_plugin","",__DIR__);
	if(!isset($wpdb))
	{
    		require_once($abspath.'wp-config.php');
    		require_once($abspath.'wp-includes/wp-db.php');
	}
	function set_option($key,$value){
		global $wpdb;
		$wpdb->query($wpdb->prepare("update wp_options set option_value=%d where option_name=%s",$value,$key));
	}
	$all_opt_arr = array('sup_webp','sup_compress','sup_defer','sup_aggregate','sup_cache_js','sup_agg_css','sup_cache_css','sup_defer_css','sup_cache','sup_nothing');
	foreach($all_opt_arr as $opt){
		if(!empty($_POST[$opt])){
			set_option($opt,true);
		}else{
			set_option($opt,false);
		}
	}
	#print_r($_POST);
	$cache_dir = $abspath."wp-content/cache/suprabhat";
	deleteCacheDirectory($cache_dir);
	// $location = 'http://127.0.0.1/wordpress_blog/wp-admin/options-general.php?page=cp-cache-plugin&settings-updated=true';
	$option_location = HOME_URL.'/wp-admin/options.php';
	wp_redirect($option_location,307);
	die();
?>