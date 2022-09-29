<?php

if (!function_exists('bader_enqueue_admin_styles')) {
	function bader_enqueue_admin_styles()
	{
		wp_enqueue_style('bader-dashboard-widget-css', plugin_dir_url(__FILE__) . 'assets/css/style.css');
	}
}
add_action('admin_enqueue_scripts', 'bader_enqueue_admin_styles');

add_action('wp_dashboard_setup', 'bader_add_dashboard_widget');

if (!function_exists('bader_add_dashboard_widget')) {
	function bader_add_dashboard_widget()
	{
		wp_add_dashboard_widget(
			'bader_dashboard_widget', // widget ID
			'Baders Weather Widget', // widget title
			'bader_dashboard_widget', // callback #1 to display it
			'bader_process_my_dashboard_widget' // callback #2 for settings
		);
	}
}
if (!function_exists('bader_dashboard_widget')) {
	/*
 	* Callback #1 function
 	* Displays widget content
 	*/
	function bader_dashboard_widget()
	{
		//default lat and long to brookvale
		$lat = (get_option('_weather_lat') !== false && !empty(get_option('_weather_lat'))) ? get_option('_weather_lat') : -33.7628854;
		$long = (get_option('_weather_long') !== false && !empty(get_option('_weather_long'))) ? get_option('_weather_long') : 151.2707024;
		$weather = BadersWeather::getWeather($lat, $long);
		if ($weather !== 'fail') {
			$last_update = intval((time() - get_option('_cached_timestamp')) / 60);
			ob_start(); ?>
			<div id="bader-weather-widget">
				<div class="bader-weather-widget-icon">
					<img src="http://openweathermap.org/img/w/<?php echo $weather->icon; ?>.png" alt="<?php echo $weather->main; ?>">
					<p><?php echo $weather->main; ?></p>
				</div>
				<div class="bader-weather-widget-content">
					<p>The current weather in <?php echo $weather->location; ?> is <strong><?php echo $weather->weather; ?>℃</strong>, <?php echo $weather->desc; ?>.</p>
					<p> Updated <?php echo $last_update; ?> minute<?php echo ($last_update !== 1) ? 's' : ''; ?> ago.</p>
				</div>
			</div>
		<?php echo ob_get_clean();
		} else echo '<p>please enter correct lat and long!</p>';
	}
}

if (!function_exists('bader_process_my_dashboard_widget')) {
	/*
 	* Callback #2 function
 	* Displays widget settings
 	*/
	function bader_process_my_dashboard_widget()
	{
		if ('POST' == $_SERVER['REQUEST_METHOD'] && isset($_POST['_weather_lat']) && isset($_POST['_weather_long'])) {
			update_option('_weather_lat', sanitize_text_field($_POST['_weather_lat']));
			update_option('_weather_long', sanitize_text_field($_POST['_weather_long']));
			BadersWeather::clearTransient();
		}
		ob_start();
		?>
		<p><label for="_weather_lat">Latitude</label><br>
			<input type="text" name="_weather_lat" placeholder="Enter Latitude" value="<?php echo get_option('_weather_lat'); ?>" />
		<p>
		<p><label for="_weather_lat">Longitude</label><br>
			<input type="text" name="_weather_long" placeholder="Enter Longitude" value="<?php echo get_option('_weather_long'); ?>" />
		<p>
	<?php echo ob_get_clean();
	}
}
