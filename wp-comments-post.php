$url = $_SERVER['HTTP_REFERER'];
$url = str_replace("https://", "", $url);
$url = str_replace("http://", "", $url);
$cache_location = __DIR__."/wp-content/cache/suprabhat/".$url."index.html";
unlink($cache_location);