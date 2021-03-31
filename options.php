<!DOCTYPE html>
<html>
<body>
<?php
	global $wpdb;
	if(!isset($wpdb))
	{
    		require_once(ABSPATH.'/wp-config.php');
    		require_once(ABSPATH.'/wp-includes/wp-db.php');
	}
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		$to_cache_page=false;
		if (!empty($_POST["cache_page"])) {
			$to_cache_page=true;
		}
		$to_cache_css=false;
		if (!empty($_POST["cache_css"])) {
			$to_cache_css=true;
		}
		$to_cache_js=false;
		if (!empty($_POST["cache_js"])) {
			$to_cache_js=true;
		}
		$to_cache_font=false;
		if (!empty($_POST["cache_font"])) {
			$to_cache_font=true;
		}
		$wpdb->query($wpdb->prepare("update wp_options set option_value=%d where option_name=%s",$to_cache_page,"sup_page_cache"));
		$wpdb->query($wpdb->prepare("update wp_options set option_value=%d where option_name=%s",$to_cache_css,"sup_css_cache"));
		$wpdb->query($wpdb->prepare("update wp_options set option_value=%d where option_name=%s",$to_cache_js,"sup_js_cache"));
		$wpdb->query($wpdb->prepare("update wp_options set option_value=%d where option_name=%s",$to_cache_font,"sup_font_cache"));
	}
	function test_input($data) {
 		$data = trim($data);
  		$data = stripslashes($data);
  		$data = htmlspecialchars($data);
  		return $data;
	}
	$to_cache_page = get_data("sup_page_cache");
	$to_cache_css = get_data("sup_css_cache");
	$to_cache_js = get_data("sup_js_cache");
	$to_cache_font = get_data("sup_font_cache");
?>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
	<div><label for="to_cache_page">Compress The Picture</label>
	<input type="checkbox" id="to_cache_page" name="to_cache_page" value="Y" <?php echo ($to_cache_page==1 ? 'checked' : '');?>></div>
	<div><label for="to_cache_css">Convert to WebP </label>
	<input type="checkbox" id="to_cache_css" name="to_cache_css" value="Y" <?php echo ($to_cache_css==1 ? 'checked' : '');?>></div>
	<div><label for="to_cache_js">Defer JS </label>
	<input type="checkbox" id="to_cache_js" name="to_cache_js" value="Y" <?php echo ($to_cache_js==1 ? 'checked' : '');?>></div>
	<div><label for="to_cache_font">Aggregate JS </label>
	<input type="checkbox" id="to_cache_font" name="to_cache_font" value="Y" ></div>
	<div><input type="submit" name="submit" value="Submit"></div>
</form>
<div>
	<form method="post" action="<?php echo ABSPATH ?>/wp-admin/plugins.php">
		<input type="submit" name="plugin Page" value="Return to home page">
	</form>
</div>
</body>
</html>