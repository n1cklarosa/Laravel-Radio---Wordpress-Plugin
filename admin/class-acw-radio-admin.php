<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://allclassweb.com
 * @since      1.0.0
 *
 * @package    Acw_Radio
 * @subpackage Acw_Radio/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Acw_Radio
 * @subpackage Acw_Radio/admin
 * @author     Nick La Rosa <nick@allclassweb.com>
 */
class Acw_Radio_Admin
{

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of this plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version)
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Acw_Radio_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Acw_Radio_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/acw-radio-admin.css', array(), $this->version, 'all');
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Acw_Radio_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Acw_Radio_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        $settings = get_option('acw_plugin_options');

        wp_register_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/acw-radio-admin.js', array( 'jquery' ), $this->version, false);
        wp_enqueue_script($this->plugin_name);
        /**
         *  In backend there is global ajaxurl variable defined by WordPress itself.
         *
         * This variable is not created by WP in frontend. It means that if you want to use AJAX calls in frontend, then you have to define such variable by yourself.
         * Good way to do this is to use wp_localize_script.
         *
         * @link http://wordpress.stackexchange.com/a/190299/90212
         *
         * You could also pass this datas with the "data" attribute somewhere in your form.
         */
        wp_localize_script($this->plugin_name, 'wp_ajax', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        /**
         * Create nonce for security.
         *
         * @link https://codex.wordpress.org/Function_Reference/wp_create_nonce
         */
        '_nonce' => wp_create_nonce('any_value_here'),

    ));
    }

    public function acw_add_settings_page()
    {
        add_options_page('Radio Settings', 'Radio Settings', 'manage_options', 'acw-plugin', [$this, 'acw_render_plugin_settings_page']);
    }

    public function acw_render_plugin_settings_page()
    {
        ?>
		<h2>Radio Plugin Settings</h2>
		<form action="options.php" method="post">
			<?php
            settings_fields('acw_plugin_options');
        do_settings_sections('acw_plugin'); ?>
			<input name="submit" class="button button-primary" type="submit" value="<?php esc_attr_e('Save'); ?>" />
		</form>
		<button style="margin-top:40px;" class='reload-programs button button-primar'>Reload Programs from API</button>
		<?php
    }

    public function acw_register_settings()
    {
        register_setting('acw_plugin_options', 'acw_plugin_options', [$this, 'acw_plugin_options_validate']);
        add_settings_section('api_settings', 'API Settings', [$this, 'acw_plugin_section_text'], 'acw_plugin');
    
        add_settings_field('acw_plugin_setting_api_key', 'API Key', [$this, 'acw_plugin_setting_api_key'], 'acw_plugin', 'api_settings');
    }

    public function acw_plugin_options_validate($input)
    {
        // $newinput['api_key'] = trim( $input['api_key'] );
        // if ( ! preg_match( '/^[a-z0-9]{32}$/i', $newinput['api_key'] ) ) {
        // 	$newinput['api_key'] = '';
        // }
    
        return $input;
    }


    public function acw_plugin_section_text()
    {
        echo '<p>Here you can set all the options for using the API</p>';
    }

    public function acw_plugin_setting_api_key()
    {
        $options = get_option('acw_plugin_options');
        echo "<input id='acw_plugin_setting_api_key' name='acw_plugin_options[api_key]' type='text' value='" . esc_attr($options['api_key']) . "' />";
    }

    // public function acw_plugin_setting_results_limit() {
    // 	$options = get_option( 'acw_plugin_options' );
    // 	echo "<input id='acw_plugin_setting_results_limit' name='acw_plugin_options[results_limit]' type='text' value='" . esc_attr( $options['results_limit'] ) . "' />";
    // }

    // public function acw_plugin_setting_start_date() {
    // 	$options = get_option( 'acw_plugin_options' );
    // 	echo "<input id='acw_plugin_setting_start_date' name='acw_plugin_options[start_date]' type='text' value='" . esc_attr( $options['start_date'] ) . "' />";
    // }


    public function ajax_first()
    {
        /**
         * Do not forget to check your nonce for security!
         *
         * @link https://codex.wordpress.org/Function_Reference/wp_verify_nonce
         */
        if (! wp_verify_nonce($_POST['_nonce'], 'any_value_here')) {
            wp_send_json_error();
            die();
        }
        /**
         * OR you can use check_ajax_referer
         *
         * @link https://codex.wordpress.org/Function_Reference/check_ajax_referer
         * @link https://tommcfarlin.com/secure-ajax-requests-in-wordpress/
         * @link https://wordpress.stackexchange.com/questions/48110/wp-verify-nonce-vs-check-admin-referer
         */
        if (! check_ajax_referer('any_value_here', '_nonce', false)) {
            wp_send_json_error('Invalid security token sent.');
            die();
        }
        $settings = get_option('acw_plugin_options');
        $response = wp_remote_get('https://app.myradio.click/api/public/station/'.$settings['api_key']);
        $decoded = json_decode($response['body']);
        $progams = $decoded->data->programs;
        $finished = [];
        $new = []; 
        $att = []; 
        foreach ($progams as $key => $value) {
            $existingPrograms = get_posts(
                array(
                        'post_type' => 'program',
                        'orderby' => 'title',
                        'order' => 'ASC',
                        'meta_query' => array(
                            array('key' => 'external_id', 'value' => $value->id)
                         )
                    )
            );
            if (count($existingPrograms) > 0) {
                $finished[] = $existingPrograms[0]->ID;
                $new[] = $existingPrograms[0]->ID;
            } else {
                $id = wp_insert_post(array(
                    'post_title' => $value->name,
                    'post_type'=>'program',
                    'post_content'=>'testing',
                    'post_status' => 'publish',
                    'post_excerpt' => $value->introduction
                ));
                add_post_meta($id, 'external_id', $value->id, true);
                $finished[] = $id;

                if ($value->image != null) {
                    $att[] = $this->remote_image_as_featured_image($id, $value->image->url ) ;
                }
            }
        }

        die(json_encode([$finished, $att, $new, $decoded]));
    }
     
    public function remote_image_as_featured_image($post_id, $url, $attachment_data = array())
    {
        $download_remote_image = new Acw_Radio_Media($url, $attachment_data);
        $attachment_id         = $download_remote_image->download();
  
        if (! $attachment_id) {
            return false;
        }
  
        return set_post_thumbnail($post_id, $attachment_id);
    }
}
