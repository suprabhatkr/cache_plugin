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
	function compress(string $src,int $height = null,$width= null){
        $abspath = str_replace("wp-content/plugins/caching_plugin","",__DIR__);
		if ($height!=null and $width!=null){
			$filepath = str_replace(HOME_URL,$abspath,$src);
			$arr=image_make_intermediate_size($filepath,$height,$width);
			if (gettype($arr)=="array"){
                		$compressedUrl = str_replace(basename($src),$arr['file'],$src);
			}else{
				$compressedUrl = $src;
			}
		}else{
			$compressedUrl=$src;
		}
                return $compressedUrl;
	}
    function to_webp(string $src){
        $base = dirname($src);
        $filename = basename($src);
        $abspath = str_replace("wp-content/plugins/caching_plugin","",__DIR__);
        $baseDir = str_replace(HOME_URL,$abspath,$base);
        if(preg_match("@\.jpg@", $src)){
            $newfilename = str_replace("jpg","webp",$filename);
            $webPfile = $baseDir."/".$newfilename;
            $im = imagecreatefromjpeg($src);
            imagewebp($im, $webPfile);
            $webPurl = str_replace($abspath,HOME_URL,$webPfile);
            return $webPurl;
        }
        if(preg_match("@\.png@", $src)){
            $newfilename = str_replace("png","webp",$filename);
            $webPfile = $baseDir."/".$newfilename;
            $im = imagecreatefrompng($src);
            imagewebp($im, $webPfile);
            $webPurl = str_replace($abspath,HOME_URL,$webPfile);
            return $webPurl;
        }
        return null;
    }
    function compress_image(int $post_id){
        global $wpdb;
        $abspath = str_replace("wp-content/plugins/caching_plugin","",__DIR__);
        if(!isset($wpdb))
        {
                require_once($abspath.'wp-config.php');
                require_once($abspath.'wp-includes/wp-db.php');
        }
        global $wpdp;
        $posts = $wpdb->get_results("select * from wp_posts where id=".$post_id);
        foreach ($posts as $post){
            $post_content = $post->post_content;
            $new_post_content = $post_content;
            $pagedom = new DOMDocument('1.0', 'UTF-8');
            $container_height= null;
            $container_width = null;
            if (!empty($post_content) and preg_match("@img@",$post_content)===1){
                $new_post_content = $post_content;
                preg_match( '<img.*>' , $post_content, $post_content_match );
                @$pagedom->loadHTML($post_content);
                $anchors = $pagedom -> getElementsByTagName('img');
                $to_compress_this=false;
                foreach($anchors as $element){
                    $to_compress_this = true;
                    $container_height = $element->getAttribute('height');
                    $container_width = $element->getAttribute('width');
                    $imageClasses = $element->getAttribute('class');
                    $src = $element->getAttribute('src');
                    foreach(explode(" ",$imageClasses) as $class){
                        if (preg_match("@wp-image.*@",$class,$match)){
                            $imageIDclass = array_pop($match);
                            break;
                        }
                    }
                    if($imageIDclass!=null){
                                            // echo $imageIDclass;
                        $imageID = explode("-",$imageIDclass)[2];
                        $imageData = $wpdb->get_results("select * from wp_postmeta where post_id=".$imageID." and meta_key='_wp_attachment_metadata'");
                        $imageDetails = array_pop($imageData);
                        if (gettype($imageDetails)!='NULL'){
                            $meta_id = $imageDetails->meta_id;
                            $imageDetails = $imageDetails->meta_value;
                            foreach(unserialize($imageDetails) as $key => $value){
                                if (gettype($value)!="array"){
                                    if(($key)=='height')$container_height=$value;
                                    if(($key)=='width')$container_width=$value;
                                }//else if($key=="sizes"){
                                //     foreach($value as $sizetype=>$sizevalue){
                                //         if (!empty($sizevalue)){
                                //             foreach ($sizevalue as $image_property=>$property_value){
                                //                 if ($image_property == "height" and $property_value==$container_height){
                                //                     $to_compress_this=false;
                                //                     $compressedImage = $sizevalue;
                                //                 }
                                //                 if ($image_property == "width" and $property_value==$container_width){
                                //                     $to_compress_this=false;
                                //                     $compressedImage = $sizevalue;
                                //                 }
                                //             }
                                //         }
                                //     }
                                // }
                            }
                        }
                        else{
                            $to_compress_this=false;
                        }
                        if($to_compress_this){
                            $filepath = str_replace(HOME_URL,$abspath,$src);
                            $compressedImage = image_make_intermediate_size($filepath,$container_width,$container_height);
                            $updated_imagedetails = unserialize($imageDetails);
                            $updated_sizeArr = $updated_imagedetails['sizes'];
                            $num = 0;
                            foreach($updated_sizeArr as $size=>$sizedata){
                            $name = explode("-",$imageIDclass);
                                if ($name[0]=="customeSize" and number_format($name[1])>=$num){
                                    $num=number_format($name[1])+1;
                                }
                            }
                            if (!empty($compressedImage)){
                                $updated_sizeArr['customeSize-'.$num] = $compressedImage;
                            }
                            $updated_imagedetails['sizes']=$updated_sizeArr;
                            $wpdb->query($wpdb->prepare("update wp_postmeta set meta_value=%s where meta_id=%s;",serialize($updated_imagedetails),$meta_id)); 
                        }
                        if(gettype($compressedImage)!='boolean'){
                        $compressedUrl = str_replace(basename($src), $compressedImage['file'], $src);
                        $webPurl = to_webp($compressedUrl);
                        $to_webp=true;
                        if ($to_webp and !preg_match('<picture.*>', $new_post_content)){
                            $match_content = DOMinnerHTML($element);
                            $image_tag = '<img.*>';
                            foreach ($post_content_match as $img){
                                if (preg_match("@".$match_content."@", $img)){
                                    $image_tag = "<".$img;
                                    $image_tag = str_replace("</figure>", "", $image_tag);
                                    break;
                                }
                            }
                            $picture_tag = "<picture><source srcset=\"".$webPurl."\" type=\"image/webp\"><source srcset=\"".$compressedUrl."\" type=\"image/jpeg\">".$image_tag."</picture>";
                            $new_post_content=str_replace($image_tag, $picture_tag, $new_post_content);
                        }
                        }
                    }
                }
                // echo $post_content;
                // echo $new_post_content;
                $wpdb->query($wpdb->prepare("update wp_posts set post_content=%s where post_content=%s;",$new_post_content,$post_content)); 
            }
        }
    }
    for ($i=1;$i<=149;$i++){
    compress_image($i);}
    $option_page = HOME_URL."/wp-admin/options-general.php?page=cp-cache-plugin";
    wp_redirect($option_page);