<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://allclassweb.com
 * @since      1.0.0
 *
 * @package    Acw_Radio
 * @subpackage Acw_Radio/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Acw_Radio
 * @subpackage Acw_Radio/includes
 * @author     Nick La Rosa <nick@allclassweb.com>
 */
class Acw_Radio
{

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      Acw_Radio_Loader    $loader    Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $plugin_name    The string used to uniquely identify this plugin.
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $version    The current version of the plugin.
     */
    protected $version;

    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function __construct()
    {
        if (defined('ACW_RADIO_VERSION')) {
            $this->version = ACW_RADIO_VERSION;
        } else {
            $this->version = '1.0.0';
        }
        $this->plugin_name = 'acw-radio';

        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();
    }

    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     *
     * - Acw_Radio_Loader. Orchestrates the hooks of the plugin.
     * - Acw_Radio_i18n. Defines internationalization functionality.
     * - Acw_Radio_Admin. Defines all hooks for the admin area.
     * - Acw_Radio_Public. Defines all hooks for the public side of the site.
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies()
    {

        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-acw-radio-loader.php';

        /**
         * The class responsible for defining internationalization functionality
         * of the plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-acw-radio-i18n.php';

        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-acw-radio-media.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-acw-radio-widgets.php';

        /**
         * The class responsible for defining all actions that occur in the admin area.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-acw-radio-admin.php';

        /**
         * The class responsible for defining all actions that occur in the public-facing
         * side of the site.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-acw-radio-public.php';


        /**
         * Custom Post Types
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes//class-acw-radio-post-types.php';

        $this->loader = new Acw_Radio_Loader();
    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the Acw_Radio_i18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function set_locale()
    {
        $plugin_i18n = new Acw_Radio_i18n();

        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_admin_hooks()
    {
        $plugin_admin = new Acw_Radio_Admin($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
        $this->loader->add_action('admin_menu', $plugin_admin, 'acw_add_settings_page');
        $this->loader->add_action('admin_init', $plugin_admin, 'acw_register_settings');

        $plugin_post_types = new Acw_Radio_Post_Types();

        /**
         * The problem with the initial activation code is that when the activation hook runs, it's after the init hook has run,
         * so hooking into init from the activation hook won't do anything.
         * You don't need to register the CPT within the activation function unless you need rewrite rules to be added
         * via flush_rewrite_rules() on activation. In that case, you'll want to register the CPT normally, via the
         * loader on the init hook, and also re-register it within the activation function and
         * call flush_rewrite_rules() to add the CPT rewrite rules.
         *
         * @link https://github.com/DevinVinson/WordPress-Plugin-Boilerplate/issues/261
         */
        $this->loader->add_action('init', $plugin_post_types, 'create_custom_post_type', 999);

        /**
         * The wp_ajax_ is telling wordpress to use ajax and the prefix_ajax_first is the hook name to use in JavaScript or in URL.
         *
         * Call AJAX function via URL: https://www.yourwebsite.com/wp-admin/admin-ajax.php?action=prefix_ajax_first&post_id=23&other_param=something
         *
         * The ajax_first is the callback function.
         * wp_ajax_ is for authenticated users
         * wp_ajax_nopriv_ is for NOT authenticated users
         */
        $this->loader->add_action('wp_ajax_prefix_ajax_first', $plugin_admin, 'ajax_first');
    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_public_hooks()
    {
        $plugin_public = new Acw_Radio_Public($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');

        /**
         * Register shortcode via loader
         *
         * Use: [short-code-name args]
         *
         * @link https://github.com/DevinVinson/WordPress-Plugin-Boilerplate/issues/262
         */
        $this->loader->add_shortcode("programguide", $plugin_public, "shortcode_function", $priority = 10);
        $this->loader->add_shortcode("programlist", $plugin_public, "episode_list_shortcode", $priority = 10);
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    1.0.0
     */
    public function run()
    {
        $this->loader->run();
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since     1.0.0
     * @return    string    The name of the plugin.
     */
    public function get_plugin_name()
    {
        return $this->plugin_name;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @since     1.0.0
     * @return    Acw_Radio_Loader    Orchestrates the hooks of the plugin.
     */
    public function get_loader()
    {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @since     1.0.0
     * @return    string    The version number of the plugin.
     */
    public function get_version()
    {
        return $this->version;
    }
}
