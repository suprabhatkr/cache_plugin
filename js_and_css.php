<?php
	function DOMinnerHTML(DOMNode $element) 
	{ 
	    $innerHTML = ""; 
	    $children  = $element->childNodes;
	    foreach ($children as $child) 
	    { 
	        $innerHTML .= $element->ownerDocument->saveHTML($child);
	    }
	    return $innerHTML; 
	} 
function compress_css_js(string $page_content){
	$abspath = str_replace("wp-content/plugins/caching_plugin","",__DIR__);
	$cache_dir = $abspath."wp-content/cache/suprabhat";
	if (!is_dir($cache_dir)){
		mkdir($cache_dir,0777,true);
	}
	global $wpdb;
	include "minifyjs.php";
    include "minifycss.php";
	$new_page_content = $page_content;
	$to_cache_js = get_option('sup_cache_js');
	$to_cache_css = get_option('sup_cache_css');
	$dom = new DOMDocument('1.0', 'UTF-8');
	@$dom->loadHTML($page_content);
	if($to_cache_js){
		$scripts = $dom -> getElementsByTagName('script');
		foreach ($scripts as $script){
			$src = $script->getAttribute('src');
			if(!empty($src)){
				$url_components = parse_url($src); 
				if(!empty($url_components['query'])){
					parse_str($url_components['query'], $params); 
					$version = $params['ver'];
					$src = str_replace("?ver=".$version,"" , $src);
				}
				$srcfile = str_replace(HOME_URL,ABSPATH , $src);
				$myfile = fopen($srcfile, "r") or die("Unable to open file!");
				$samplejs = fread($myfile,filesize($srcfile));
				$minifiedJS = JSMin::minify($samplejs);
				$outfile = fopen(WP_CONTENT_DIR."/cache/suprabhat/".basename($src),'w') or die("Unable to open output file");
				fwrite($outfile,$minifiedJS);
				fclose($myfile);
				fclose($outfile);
				$aggregatefile = fopen(WP_CONTENT_DIR."/cache/suprabhat/aggregateSuprabhat.js","a");
				fwrite($aggregatefile, $minifiedJS);
				fclose($aggregatefile);
				$new_page_content = str_replace($src, HOME_URL."/wp-content/cache/suprabhat/".basename($src), $new_page_content);
			}
		}
	}
	if($to_cache_css){
		$links = $dom -> getElementsByTagName('link');
		foreach ($links as $link){
			$src=null;
			$rel=null;
			if($link->hasAttribute('href') and $link->hasAttribute('rel')){
				$src = $link->getAttribute('href');
				$rel = $link->getAttribute('rel');
			}
			if(!empty($src) and !empty($rel) and $rel=='stylesheet'){
				$url_components = parse_url($src); 
				if(!empty($url_components['query'])){
					#$src=str_replace("?", "", $src);
					parse_str($url_components['query'], $params); 
					foreach($params as $paramKey=>$paramValue){
						if($paramKey=='ver'){
							$version = $params['ver'];
							$src = str_replace("?ver=".$paramValue,"" , $src);
						}
					}
				}
				$srcfile = str_replace(HOME_URL,ABSPATH , $src);
				if (!preg_match("@fonts.google.com@", $src)){
					$outfile = str_replace(dirname($src),WP_CONTENT_DIR."/cache/suprabhat/" , $src);
				}
				else{
					$outfile = WP_CONTENT_DIR."/cache/suprabhat/google_font.css";
				}
				if(!(is_file($outfile))){
					if(preg_match("@\.css@", $srcfile)){
						$myfile = fopen($srcfile, "r") or die("Unable to open file!");
						$fsize = filesize($srcfile);
						if($fsize!=0){
							$samplecss = fread($myfile,$fsize);
						}
						fclose($myfile);
					}
					else{
						$samplecss = "";
						$fontDom = new DOMDocument('1.0', 'UTF-8');
						if (!(preg_match("@http@", $srcfile))){
							$srcfile = str_replace("//", "https://", $srcfile);
						}
						@$fontDom->loadHTMLFile($srcfile);
						$pres = $fontDom -> getElementsByTagName('body');
						foreach ($pres as $prekey => $prevalue) {
							if($prevalue!=null){
								$samplecss.=DOMinnerHTML($prevalue);
							}
						}
					}
					if($samplecss){
						$minifiedCSS = minify_css($samplecss);
						$output = fopen($outfile,'w') or die("Unable to open output file");
						fwrite($output,$minifiedCSS);
						fclose($output);
						$aggregatefile = fopen(WP_CONTENT_DIR."/cache/suprabhat/aggregateCSS.css","a");
						fwrite($aggregatefile, $minifiedCSS);
						fclose($aggregatefile);
						// if(!(preg_match($srcfile, $cssfiles))){
						// 	echo $srcfile;
						// 	$cssfiles.=$srcfile;
						// 	$wpdb->query("update wp_options set option_value='".$cssfiles."' where option_name='sp_cr_array'");
						// 	$penthouseDom = new DOMDocument('1.0', 'UTF-8');
						// 	@$penthouseDom->loadHTMLFile("http://127.0.0.1:8081/(fromwp,".$all_pages[$i].",".$srcfile);
						// 	$pres = $penthouseDom -> getElementsByTagName('p');
						// 	foreach ($pres as $prekey => $prevalue) {
						// 		if($prevalue!=null){
						// 			$criticalfile = fopen(WP_CONTENT_DIR."/cache/suprabhat/criticalCSS.css","a");
						// 			fwrite($criticalfile,DOMinnerHTML($prevalue));
						// 			fclose($criticalfile);
						// 		}
						// 	}
						// }
						$outsrc = str_replace(ABSPATH, HOME_URL."/", $outfile);
						$new_page_content = str_replace($src, $outsrc, $new_page_content);
					}
				}
			}
		}
	}
	return $new_page_content;
}
?>