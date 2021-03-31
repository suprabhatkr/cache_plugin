<?php 
	function cp_add_settings_page() {
		#cp_render_plugin_settings_page();
	    add_options_page( 'Cache plugin page', 'Cache Plugin Menu', 'manage_options', 'cp-cache-plugin', 'cp_render_plugin_settings_page' );
	}
	add_action( 'admin_menu', 'cp_add_settings_page' );
	function post_setting_temp(){
		print_r($_POST);
	}
	function cp_render_plugin_settings_page() {
	    ?>
	    <h2>Cache Plugin Settings</h2>
	    <!-- <form action="options.php" method="post"> -->
	    <form action="http://127.0.0.1/wordpress_blog/wp-content/plugins/caching_plugin/save_option.php" method="post">
	        <?php 
	        settings_fields( 'cache_plugin_options' );
	        do_settings_sections( 'cache_plugin' ); 
	        submit_button();?>
	    </form>
	    <form action="http://127.0.0.1/wordpress_blog/wp-content/plugins/caching_plugin/image_func.php" method="post">
	        <?php 
	        submit_button();?>
	    </form>
	    <?php
	}
	function cp_register_settings() {
		register_setting('cache_plugin_options','cache_plugin_options','cache_plugin_options_validate');
	    add_settings_section( 'cache_plugin_options', 'API Settings', 'cache_plugin_section_text', 'cache_plugin' );

	    add_settings_field( 'cache_plugin_setting_cache_html', 'Cache Page', 'cache_plugin_setting_cache_html', 'cache_plugin', 'cache_plugin_options' );
	    add_settings_field( 'cache_plugin_setting_cache_css', 'Cache CSS', 'cache_plugin_setting_cache_css', 'cache_plugin', 'cache_plugin_options' );
	    add_settings_field( 'cache_plugin_setting_cache_js', 'Cache JavaScript', 'cache_plugin_setting_cache_js', 'cache_plugin', 'cache_plugin_options' );
	    add_settings_field( 'cache_plugin_setting_aggregate_js', 'Aggregate JavaScript', 'cache_plugin_setting_aggregate_js', 'cache_plugin', 'cache_plugin_options' );
	    add_settings_field( 'cache_plugin_setting_aggregate_css', 'Aggregate CSS', 'cache_plugin_setting_aggregate_css', 'cache_plugin', 'cache_plugin_options' );
	    add_settings_field( 'cache_plugin_setting_defer_js', 'Defer JavaScript', 'cache_plugin_setting_defer_js', 'cache_plugin', 'cache_plugin_options' );
	    add_settings_field( 'cache_plugin_setting_defer_css', 'Defer CSS', 'cache_plugin_setting_defer_css', 'cache_plugin', 'cache_plugin_options' );
	    add_settings_field( 'cache_plugin_setting_compress_image', 'Compress Image', 'cache_plugin_setting_compress_image', 'cache_plugin', 'cache_plugin_options' );
	    add_settings_field( 'cache_plugin_setting_webp_image', 'Convert to WebP', 'cache_plugin_setting_webp_image', 'cache_plugin', 'cache_plugin_options' );
	}
	add_action( 'admin_init', 'cp_register_settings' );

	function cache_plugin_options_validate( $input ) {
	    return true;
	}
	function cache_plugin_section_text() {
	    echo '<p>Here you can set all the options</p>';
	}

	function cache_plugin_setting_cache_html() {
	    $option = get_option( 'sup_cache' );
	    echo "<input type=\"checkbox\" id=\"sup_cache\" name=\"sup_cache\" value='Y' ".($option==true ? 'checked' : '')."/> ";
	}
	function cache_plugin_setting_cache_css() {
	    $option = get_option( 'sup_cache_css' );
	    echo "<input type=\"checkbox\" id=\"sup_cache_css\" name=\"sup_cache_css\" value='Y' ".($option==true ? 'checked' : '')."/>";
	}
	function cache_plugin_setting_cache_js() {
	    $option = get_option( 'sup_cache_js' );
	    echo "<input type=\"checkbox\" id=\"sup_cache_js\" name=\"sup_cache_js\" value='Y' ".($option==true ? 'checked' : '')."/> ";
	}
	function cache_plugin_setting_aggregate_js() {
	    $option = get_option( 'sup_aggregate' );
	    echo "<input type=\"checkbox\" id=\"sup_aggregate\" name=\"sup_aggregate\" value='" . esc_attr( $option ) . "' ".($option==true ? 'checked' : '')."/> ";
	}
	function cache_plugin_setting_aggregate_css() {
	    $option = get_option( 'sup_agg_css' );
	    echo "<input type=\"checkbox\" id=\"sup_agg_css\" name=\"sup_agg_css\" value='" . esc_attr( $option ) . "' ".($option==true ? 'checked' : '')."/> ";
	}
	function cache_plugin_setting_defer_js() {
	    $option = get_option( 'sup_defer' );
	    echo "<input type=\"checkbox\" id=\"sup_defer\" name=\"sup_defer\" value='" . esc_attr( $option ) . "' ".($option==true ? 'checked' : '')."/> ";
	}
	function cache_plugin_setting_defer_css() {
	    $option = get_option( 'sup_defer_css' );
	    echo "<input type=\"checkbox\" id=\"sup_defer_css\" name=\"sup_defer_css\" value='" . esc_attr( $option ) . "' ".($option==true ? 'checked' : '')."/> ";
	}
	function cache_plugin_setting_compress_image() {
	    $option = get_option( 'sup_compress' );
	    echo "<input type=\"checkbox\" id=\"sup_compress\" name=\"sup_compress\" value='" . esc_attr( $option ) . "' ".($option==true ? 'checked' : '')."/> ";
	}
	function cache_plugin_setting_webp_image() {
	    $option = get_option( 'sup_webp' );
	    echo "<input type=\"checkbox\" id=\"sup_webp\" name=\"sup_webp\" value='".esc_attr($option)."' ".($option==true ? 'checked' : '')."/> ";
	}
?>