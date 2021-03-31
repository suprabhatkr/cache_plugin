<?php 
	$url = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	$short_url = str_replace("http://", "", $url);
	$cache_location = WP_CONTENT_DIR."/cache/suprabhat/".$short_url."index.html";
	if (is_file($cache_location) and empty($_GET)){
		$cache_file = fopen($cache_location,"r");
		$cache = fread($cache_file, filesize($cache_location));
		echo $cache;
		echo "Cache file by cache file";
		echo apcu_fetch($url);
		echo "cache file by apcu";
		exit();
	}
	function deleteCacheDirectory($dir) {
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
	        if (!deleteCacheDirectory($dir . DIRECTORY_SEPARATOR . $item)) {
	            return false;
	        }
	    }
	    return rmdir($dir);
	}
	if(!empty($_GET)){
		deleteCacheDirectory(WP_CONTENT_DIR."/cache/suprabhat");
	}
?>