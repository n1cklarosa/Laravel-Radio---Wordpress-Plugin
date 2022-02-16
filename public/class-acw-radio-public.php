<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://allclassweb.com
 * @since      1.0.0
 *
 * @package    Acw_Radio
 * @subpackage Acw_Radio/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Acw_Radio
 * @subpackage Acw_Radio/public
 * @author     Nick La Rosa <nick@allclassweb.com>
 */
class Acw_Radio_Public
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
     * @param      string    $plugin_name       The name of the plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version)
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
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

        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/acw-radio-public.css', array(), $this->version, 'all');
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
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
        $settings['offset'] = 6;
        wp_register_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/acw-radio-public.js', array( 'jquery' ), $this->version, true);
        $dataToBePassed = $settings;
        wp_localize_script($this->plugin_name, 'station_vars', $dataToBePassed);
        wp_enqueue_script($this->plugin_name);
    }

        
    public function shortcode_function($atts)
    {
        $args = shortcode_atts(
            array(
                'arg1'   => 'arg1',
                'arg2'   => 'arg2',
            ),
            $atts
        );
        $offset = 6;
        $weekdays = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];


        $time_divs = "<div class='time-slots'> <div class='height_60'></div>";

        for ($i=0; $i < 24; $i++) {
            $altered = $i - $offset;
            if($altered < 0)
                $altered = $altered + 24;

            $pm = $altered >= 12 ? "am" : "pm";
            // $pm = $altered >= 12 ? "pm" : "am";
            $newTime = $altered >= 12 ? $altered - 12 : $altered;
            if($newTime == 0)
                $newTime = 12;
            $time_divs = $time_divs . "<div class='time_slot height_60 hour_$i'>$newTime$pm</div>";
            
        }

        $time_divs = $time_divs . "</div>";

        $weekDays = "";

        for ($i=0; $i < 7; $i++) {
            $slot_divs = "<div class='slot-slots'>";
            for ($j=0; $j < 24; $j++) {
                $slot_divs = $slot_divs . "<div class='slot_slot height_30 ${i}_hour_".$j."_0'></div>";
                $slot_divs = $slot_divs . "<div class='slot_slot height_30 ${i}_hour_".$j."_30'> </div>";
            }
            $slot_divs = $slot_divs . "</div>";

            $weekDays = $weekDays . "<div class='grid-weekday'><div class='weekday-heading height_60'>".$weekdays[$i]."</div><div class='weekday-container weekday_$i'>$slot_divs</div></div>";
        }


        $var = "<div class='programguide loading'><span class='load'>Loading Program Guide</span>
            <div class='desktop-program-grid'>
            ".$time_divs."
            ".$weekDays."
            </div>
            <div class='mobile-program-grid'></div>
        </div>";
        return $var;
    }
}
