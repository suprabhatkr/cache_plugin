<?php 
	$txt= ob_get_contents();
	require __DIR__."/js_and_css.php";
	$txt=compress_css_js($txt);
	$to_cache=get_option('sup_cache');
	if($to_cache){
		$Site_name = basename(ABSPATH);
		$domain_name = str_replace("/".$Site_name, "", HOME_URL);
		$url = $domain_name.$_SERVER['REQUEST_URI'];
		$path=$url;
		$path = str_replace("http://", "", $path);
		$path = str_replace("https://", "", $path);
		$cache_path = WP_CONTENT_DIR."/cache/suprabhat/";
		$cache_page_path = $cache_path;
		$path=explode("/", $path);
		foreach($path as $each_dir){
			$cache_page_path.=($each_dir."/");
			if (!is_dir($cache_page_path)) mkdir($cache_page_path, 0777, true);
		}
		@wp_cache_set($url,$txt);
		#echo wp_cache_get($url);
		apcu_store($url,$txt);
		$cache_file = fopen($cache_page_path."index.html","w");
		fwrite($cache_file,$txt);
		fclose($cache_file);
	}