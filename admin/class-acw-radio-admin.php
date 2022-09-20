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

        wp_register_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/acw-radio-admin.js', array('jquery'), $this->version, false);
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
        <div id="mrloading"></div>
        <div id="mrresults"></div>
<?php
    }

    public function acw_register_settings()
    {
        register_setting('acw_plugin_options', 'acw_plugin_options', [$this, 'acw_plugin_options_validate']);
        add_settings_section('api_settings', 'API Settings', [$this, 'acw_plugin_section_text'], 'acw_plugin');

        add_settings_field('acw_plugin_setting_api_key', 'API Key', [$this, 'acw_plugin_setting_api_key'], 'acw_plugin', 'api_settings');
        add_settings_field('acw_plugin_setting_hls', 'HLS Slug', [$this, 'acw_plugin_setting_hls'], 'acw_plugin', 'api_settings');
        add_settings_field('acw_plugin_setting_icecast', 'Icecast URL', [$this, 'acw_plugin_setting_icecast'], 'acw_plugin', 'api_settings');
        add_settings_field('acw_plugin_setting_player', 'Show Player', [$this, 'acw_plugin_setting_player'], 'acw_plugin', 'api_settings');
        add_settings_field('acw_plugin_setting_pgstart', 'Grid Start Hour', [$this, 'acw_plugin_setting_pgstart'], 'acw_plugin', 'api_settings');
        add_settings_field('acw_plugin_setting_program_type', 'Program Page Type', [$this, 'acw_plugin_setting_program_type'], 'acw_plugin', 'api_settings');
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

    public function acw_plugin_setting_hls()
    {
        $options = get_option('acw_plugin_options');
        echo "<input id='acw_plugin_setting_hls' name='acw_plugin_options[hls]' type='text' value='" . esc_attr($options['hls']) . "' />";
    }


    public function acw_plugin_setting_icecast()
    {
        $options = get_option('acw_plugin_options');
        echo "<input id='acw_plugin_setting_icecast' name='acw_plugin_options[icecast]' type='text' value='" . esc_attr($options['icecast']) . "' />";
    }


    public function acw_plugin_setting_pgstart()
    {
        $options = get_option('acw_plugin_options');
        $value = isset($options['pgstart']) ? $options['pgstart'] : 6;
        echo "<input id='acw_plugin_setting_icecast' name='acw_plugin_options[pgstart]' min=\"0\" max=\"10\" type='number' value='" . esc_attr($value) . "' /> am
            <p>Choose your start hour for the program grid. It is best to pick a time where you have a program starting at that time every day. (eg. 6). As it stands, the grid can only begin</p>
        ";
    }

    public function acw_plugin_setting_player()
    {
        $options = get_option('acw_plugin_options');
        $yes = "";
        $no = "selected";

        if (isset($options['player'])) :
            if ($options['player'] == "yes") {
                $yes = 'selected';
                $no = "";
            } else {
                $no = 'selected';
            }
        endif;
        echo "<select id='acw_plugin_setting_player' name='acw_plugin_options[player]' type='text'  >
        <option value=\"yes\" $yes>Yes</option>
        <option value=\"no\" $no>No</option></select>";
    }

    public function acw_plugin_setting_program_type()
    {
        $options = get_option('acw_plugin_options');

        if (isset($options['program_type'])) :
            if ($options['program_type'] == "local") {
                $local = 'selected';
                $iframe = "";
                $js = "";
            } elseif ($options['program_type'] == "iframe") {
                $local = '';
                $iframe = "selected";
                $js = "";
            } else {
                $local = '';
                $iframe = "";
                $js = "selected";
            }
        else :
            $local = '';
            $iframe = "selected";
            $js = "";
        endif;
        echo "<select id='acw_plugin_setting_program_type' name='acw_plugin_options[program_type]' type='text'  >
        <option value=\"iframe\" $iframe>Iframe</option>
        <option value=\"js\" $js>Imported Data</option>
        <option value=\"local\" $local>Let me edit them</option></select>";
    }



    public function ajax_first()
    {
        /**
         * Do not forget to check your nonce for security!
         *
         * @link https://codex.wordpress.org/Function_Reference/wp_verify_nonce
         */
        if (!wp_verify_nonce($_POST['_nonce'], 'any_value_here')) {
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
        if (!check_ajax_referer('any_value_here', '_nonce', false)) {
            wp_send_json_error('Invalid security token sent.');
            die();
        }

        $settings = get_option('acw_plugin_options');
        $response = wp_remote_get('https://app.myradio.click/api/public/station/' . $settings['api_key'], ['timeout' => 100]);
        if (is_wp_error($response)) {
            error_log("error fetching programs from api " . json_encode($response->errors));
            wp_send_json_error('error fetching programs from api');
        }
        $decoded = json_decode($response['body']);
        $progams = $decoded->data->programs;
        $type = $settings['program_type'] ?: 'iframe';
        $finished = [];
        $new = [];
        $att = [];
        $all = [];
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
            $content = null;
            if ($type == 'js') {
                $content = '[show_program slug="' . $value->slug . '"]';
            } elseif ($type == 'iframe') {
                $content = '<iframe src="https://programpage.myradio.click?station=' . $settings['api_key'] . '&program=' . $value->id . '&name=false" class="mr-iframe" id="program-iframe" ></iframe>';
            }

            if (count($existingPrograms) > 0) {
                $finished[] = $existingPrograms[0]->ID;
                $id = $existingPrograms[0]->ID;
                $new[] = $existingPrograms[0]->ID;
                $all[] = $existingPrograms[0]->ID;
                update_post_meta($id, 'external_id', $value->id);
                update_post_meta($id, 'mr_description', $value->bio);
                update_post_meta($id, 'mr_presenters', $value->presenter_string);
                update_post_meta($id, 'mr_slots', json_encode($value->slots));
                if ($content != null) {
                    $data = array(
                        'ID' => $id,
                        'post_content' => $content,
                    );
                    wp_update_post(
                        $data
                    );
                }
            } else {
                if ($content !== null) {
                    $id = wp_insert_post(array(
                        'post_title' => $value->name,
                        'post_type' => 'program',
                        'post_name' => $value->slug,
                        'post_content' => $content ?: "",
                        'post_status' => 'publish',
                        'post_excerpt' => $value->introduction === null ? "" : $value->introduction
                    ));
                } else {
                    $id = wp_insert_post(array(
                        'post_title' => $value->name,
                        'post_type' => 'program',
                        'post_name' => $value->slug,
                        'post_status' => 'publish',
                    ));
                }
                add_post_meta($id, 'external_id', $value->id, true);
                add_post_meta($id, 'mr_description', $value->bio, true);
                add_post_meta($id, 'mr_presenters', $value->presenter_string, true);
                add_post_meta($id, 'mr_slots', json_encode($value->slots), true);
                $finished[] = $id;
                $all[] = $id;
                if ($value->image != null) {
                    $pos = strpos($value->image->url, 'webp');
                    $pos2 = strpos($value->image->url, 'svg');
                    if ($pos !== false) {
                    } else {
                        if ($pos !== false) {
                        } else {
                            $att[] = $this->remote_image_as_featured_image($id, $value->image->url);
                        }
                    }
                }
            }
        }

        $redundant = get_posts(
            array(
                'post_type' => 'program',
                'post__not_in' => $all,
                'posts_per_page' => 500
            )
        );

        foreach ($redundant as $key => $value) {
            wp_delete_post($value->ID, false);
        }

        die(json_encode(['success' => true, "data" => $decoded, "junk" => [$finished, $att, $new, $decoded, $redundant, $all]]));
    }

    public function remote_image_as_featured_image($post_id, $url, $attachment_data = array())
    {
        $download_remote_image = new Acw_Radio_Media($url, $attachment_data);
        $attachment_id         = $download_remote_image->download();

        if (!$attachment_id) {
            return false;
        }

        return set_post_thumbnail($post_id, $attachment_id);
    }
}
