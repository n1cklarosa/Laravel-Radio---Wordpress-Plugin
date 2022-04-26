<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://allclassweb.com
 * @since             1.0.0
 * @package           Acw_Radio
 *
 * @wordpress-plugin
 * Plugin Name:       All Class Web Radio Functions
 * Plugin URI:        https://allclassweb.com
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.4
 * Author:            Nick La Rosa
 * Author URI:        https://allclassweb.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       acw-radio
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (! defined('WPINC')) {
    die;
}

define('MR_API_URL', 'https://app.myradio.click/api');
define('MR_HLS_URL', 'https://hls-server.nicklarosa.net/public/endpoints/ondemand/duration');
define('MR_DATE_FORMAT', 'l, j F, Y');

if (!defined('MR_REACT_PLAYER')):
    define('MR_REACT_PLAYER', true);
endif;

if (!defined('MR_GRID_DROPDOWN')):
    define('MR_GRID_DROPDOWN', false);
endif;

global $mr_episode_data;
/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define('ACW_RADIO_VERSION', '1.0.4');

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-acw-radio-activator.php
 */
function activate_acw_radio()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-acw-radio-activator.php';
    Acw_Radio_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-acw-radio-deactivator.php
 */
function deactivate_acw_radio()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-acw-radio-deactivator.php';
    Acw_Radio_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_acw_radio');
register_deactivation_hook(__FILE__, 'deactivate_acw_radio');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-acw-radio.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_acw_radio()
{
    $plugin = new Acw_Radio();
    $plugin->run();
}
run_acw_radio();


function gutenberg_program_list_register_block()
{
    register_block_type(__DIR__ ."/blocks/program-list");
    register_block_type(__DIR__ ."/blocks/episode-page");
}
add_action('init', 'gutenberg_program_list_register_block');


// // add_theme_support('editor-styles');
// add_editor_style( './public/css/acw-radio-public.css' );



// Load our styles from the css folder
function plugin_mce_css($mce_css)
{
    if (! empty($mce_css)) {
        $mce_css .= ',';
    }

    $mce_css .= plugins_url('public/css/acw-radio-public.css', __FILE__);
    return $mce_css;
}

add_filter('mce_css', 'plugin_mce_css');

function myradio_program_list_theme_add_editor_styles()
{
    wp_enqueue_style('myradio-click-editor-styles', plugins_url('public/css/acw-radio-public.css', __FILE__));
}
add_action('enqueue_block_editor_assets', 'myradio_program_list_theme_add_editor_styles');


function check_if_episodes_page()
{
    if (is_page('Episodes')) {
        return 'Updated Content';
    } else {
        return false;
    }
}
function add_code_before_content($content)
{
    global $mr_episode_data;
    $acuity_page = check_if_episodes_page();
    $settings = get_option('acw_plugin_options');
    
    if (!$settings || !isset($settings['api_key'])) {
        return "Station Slug Not Set in config";
    }

    if (defined('MR_HLS')):
        $slug = MR_HLS;
    else:
        $slug = $settings['api_key'];
    endif;
    $var = '';
    if ($acuity_page != false) {
        if (!$mr_episode_data) {
            $mr_episode_data = get_episode_data();
        }

        if ($mr_episode_data === false) {
            ob_start();
            echo $slug;
            $results = get_latest_episodes();
            if (isset($results->data)) {
                $episodes = $results->data;
            } else {
                return "No Episodes Found";
            } ?>
				 <div class='latest-episodes'> 
					 <?php
                     $base_link = get_page_permalink_from_name("Episodes");
            $offset =  isset($_GET['offset']) ? sanitize_text_field($_GET['offset']) : 1;
            foreach ($episodes as $key => $ep) {
                $image = $ep->program->image ? $ep->program->image->url : null;
                $date = get_date_from_gmt(date('Y-m-d H:i:s', $ep->timestamp), MR_DATE_FORMAT) ; ?>
						<div class="mr-episode-row"> 
							<button class="mr-play-audio" 
							<?php if ($image !==null):?> data-image="<?php echo $image; ?>" <?php endif; ?> 
							data-title="<?php echo $ep->program->name . " ". $date; ?>" 
							data-url="https://hls-server.nicklarosa.net/public/endpoints/ondemand/duration/<?php echo $slug; ?>/aac_96/<?php echo $ep->local; ?>/<?php echo $ep->duration; ?>/playlist.m3u8?unique=website" aria-label="Play <?php echo $ep->readable; ?>">
								
							</button>
                            <a href="<?php echo $base_link."?date=".$ep->timestamp; ?>"><p><?php echo $ep->program->name; ?></p><p><?php echo $ep->readable; ?></p></a>
						</div>
					 <?php
            } ?>
				 </div>
				<?php if (($offset != '1') && ($offset != 1)) { ?>
					 <a href="<?php echo $base_link."?offset=".($offset - 1); ?>">  Previous</a>
				<?php } ?>
			<?php if ($offset."" !== $results->meta->last_page.""): ?>
				<a href="<?php echo $base_link."?offset=".($offset + 1); ?>">  Next</a>
				<?php endif; ?>
			<?php $var = ob_get_clean();
        } else {
            $ep = $mr_episode_data->data;
            ob_start();
            $image = $ep->program->image ? $ep->program->image->url : null;
            $date = get_date_from_gmt(date('Y-m-d H:i:s', $ep->timestamp), MR_DATE_FORMAT) ;
            $currentTimestap = current_time('timestamp', true);
            $readableName = $ep->program->name . " ". $date;
         
            // https://hls-server.nicklarosa.net/public/endpoints/ondemand/duration/2ser/aac_96/2022-03-13T10:00:00+11:00/1800/playlist.m3u8?unique=website
            // var_dump($mr_episode_data);?>

		<?php if ($currentTimestap >= $ep->timestamp) : ?>
			<button class="mr-play-audio" data-title="<?php echo $readableName; ?>" 
				<?php if ($image !==null):?> data-image="<?php echo $image; ?>" <?php endif; ?> 
					data-url="<?php echo MR_HLS_URL."/".$ep->station->hls_stream."/aac_96/".$ep->local."/".$ep->duration."/playlist.m3u8?unique=website" ?>">Play!</button>
		<?php endif; ?>
		<div class="episode-details">
			<?php foreach ($mr_episode_data->data->logs as $key => $log) {?>
				<p><?php echo $log->artist; ?></p>
			<?php } ?>
		</div> 
		<?php
        $var = ob_get_clean();
        }
        return $var;
    }
    return $content;
}
add_filter('the_content', 'add_code_before_content');


function get_page_permalink_from_name($page_name)
{
    global $post;
    global $wpdb;
    $pageid_name = $wpdb->get_var("SELECT ID FROM $wpdb->posts WHERE post_title = '" . $page_name . "' LIMIT 0, 1");
    return get_permalink($pageid_name);
}

function change_title_for_episodes($query)
{
    if (!$query->is_main_query()) {
        return;
    }
    // if ($query->is_nav_menu()) {
    //     return;
    // }
    global $mr_episode_data;
    $ep_page = check_if_episodes_page();
    $data = "";
    // echo $acuity_page;
    if ($ep_page != false) {
        if (!$mr_episode_data) {
            $mr_episode_data = get_episode_data();
        }
        if ($mr_episode_data !== false) {
            add_filter('the_title', function ($title, $id = null) use ($mr_episode_data) {
                if ($title == 'Episodes') {
                    return $mr_episode_data->data->program->name . " - ". get_date_from_gmt(date('Y-m-d H:i:s', $mr_episode_data->data->timestamp), MR_DATE_FORMAT) ;
                } else {
                    return $title;
                }
            });
            add_filter('pre_get_document_title', function () use ($mr_episode_data) {
                if ($mr_episode_data) {
                    return $mr_episode_data->data->program->name . " " . get_date_from_gmt(date('Y-m-d H:i:s', $mr_episode_data->data->timestamp), MR_DATE_FORMAT);
                } else {
                    return "All Episodes";
                }
            });
        }
    }
}
 
// This is the crux of it – we want to try hooking our function to the wp_title
// filter as soon as your `slug` variable is set to the WP_Query; not all the way down in the template.
add_action('parse_query', 'change_title_for_episodes');


function get_episode_data()
{
    if (isset($_GET['date'])) {
        $date = sanitize_text_field($_GET['date']);
        $settings = get_option('acw_plugin_options');
        if (!$settings || !isset($settings['api_key'])) {
            return false;
        }
 
        $api_key = $settings['api_key'];
        $response = wp_remote_get('https://app.myradio.click/api/public/station/'.$api_key.'/episode/'.$date);
        if (is_array($response) && ! is_wp_error($response)) {
            $headers = $response['headers']; // array of http header lines
            $body    = $response['body']; // use the content
        }
        return json_decode($body);
    } else {
        return false;
    }
}


function get_latest_episodes()
{
    $offset =  isset($_GET['offset']) ? sanitize_text_field($_GET['offset']) : 1;
    $settings = get_option('acw_plugin_options');
    if (!$settings || !isset($settings['api_key'])) {
        return false;
    }
    $api_key = $settings['api_key'];
    $response = wp_remote_get('https://app.myradio.click/api/public/station/'.$api_key.'/recent?page='.$offset);
    if (is_array($response) && ! is_wp_error($response)) {
        $headers = $response['headers']; // array of http header lines
        $body    = $response['body']; // use the content
    }
    return json_decode($body);
}

if (MR_REACT_PLAYER == true):
        
    add_action('wp_body_open', 'wpdoc_add_custom_body_open_code');
    
    function wpdoc_add_custom_body_open_code()
    {
        echo '<div class="mr-site-wrapper"><div class="mr-site-content-wrapper">';
    }

    function prefix_footer_code()
    {
        echo '</div><div class="root-mr" id="root-mr"></div></div>';
    }
    add_action('wp_footer', 'prefix_footer_code');
endif;
