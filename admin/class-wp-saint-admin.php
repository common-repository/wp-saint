<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://wpsaint.com/
 * @since      1.0.0
 *
 * @package    Wp_Saint
 * @subpackage Wp_Saint/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wp_Saint
 * @subpackage Wp_Saint/admin
 * @author     Team WP Saint <support@wpsaint.com>
 */
class Wp_Saint_Admin {

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

  const WP_SAINT_ADMIN_MENU_SLUG = 'wp_saint_settings';

  const WP_SAINT_THIRD_PARTY_SLUG = 'wp_saint_third_party_scripts';

  const WP_SAINT_WEB_ANALYTICS_SLUG = 'wp_saint_web_analytics';

  const WP_SAINT_THIRD_PARTY_SCRIPTS = 'wp_saint_third_party';

  const WP_SAINT_WEB_ANALYTICS = 'wp_saint_web_analytics';

  private $options;

  /**
   * Initialize the class and set its properties.
   *
   * @since    1.0.0
   * @param      string    $plugin_name       The name of this plugin.
   * @param      string    $version    The version of this plugin.
   */
  public function __construct( $plugin_name, $version ) {

    $this->plugin_name = $plugin_name;
    $this->version = $version;

    $this->load_dependencies();
    $this->load_settings_handlers();
    $this->load_options();

  }

  /**
   * Load the required dependencies for this plugin.
   *
   * @since    1.0.0
   * @access   private
   */
  private function load_dependencies() {

    /**
     * The class reponsible for handling the third-party script settings.
     */
    require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/integration/class-wp-saint-third-party-scripts.php';

    /**
     * The class reponsible for handling the web analytics settings.
     */
    require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/integration/class-wp-saint-web-analytics.php';

  }

  /**
   * Creates new instances of the settings handler classes.
   *
   * @since    1.0.0
   * @access   private
   */
  private function load_settings_handlers() {
    
    $this->third_party_scripts = new Wp_Saint_Third_Party_Scripts( $this );
    $this->web_analytics = new Wp_Saint_Web_Analytics( $this );

  }

  /**
   * Load the options for each of the setting types from the database.
   *
   * @since    1.0.0
   * @access   private
   */
  private function load_options() {

    $this->options = array();
    $this->options[ self::WP_SAINT_THIRD_PARTY_SCRIPTS ] = $this->third_party_scripts->load_options();
    $this->options[ self::WP_SAINT_WEB_ANALYTICS ] = $this->web_analytics->load_options();

  }

  /**
   * Register the stylesheets for the admin area.
   *
   * @since    1.0.0
   */
  public function enqueue_styles() {

    /**
     * This function is provided for demonstration purposes only.
     *
     * An instance of this class should be passed to the run() function
     * defined in Wp_Saint_Loader as all of the hooks are defined
     * in that particular class.
     *
     * The Wp_Saint_Loader will then create the relationship
     * between the defined hooks and the functions defined in this
     * class.
     */

    $dev_version = time();
    // $dev_version = $this->version;

    wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wp-saint-admin.css', array(), $dev_version, 'all' );

  }

  /**
   * Register the JavaScript for the admin area.
   *
   * @since    1.0.0
   */
  public function enqueue_scripts() {

    /**
     * This function is provided for demonstration purposes only.
     *
     * An instance of this class should be passed to the run() function
     * defined in Wp_Saint_Loader as all of the hooks are defined
     * in that particular class.
     *
     * The Wp_Saint_Loader will then create the relationship
     * between the defined hooks and the functions defined in this
     * class.
     */

    wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wp-saint-admin.js', array( 'jquery' ), $this->version, false );

  }

  /**
   * Register the menus for the admin area.
   *
   * @since    1.0.0
   */
  public function admin_menu() {

    add_menu_page(
      __( 'WP Saint - Options', 'wp-saint' ),   // Page title
      __( 'WP Saint', 'wp-saint' ),   // Menu title
      'manage_options', // capability
      self::WP_SAINT_WEB_ANALYTICS_SLUG, // Menu slug
      array( $this->web_analytics, 'create_form' ),
      'dashicons-welcome-widgets-menus' // Menu icon
    );

    add_submenu_page(
      self::WP_SAINT_WEB_ANALYTICS_SLUG, // Menu slug
      __( 'WP Saint - Web Analytics', 'wp-saint' ), // Page Title
      __( 'Web Analytics', 'wp-saint' ), // Menu Title
      'manage_options', // capability
      self::WP_SAINT_WEB_ANALYTICS_SLUG, // Menu slug
      array( $this->web_analytics, 'create_form' ),
      2
    );

    add_submenu_page(
      self::WP_SAINT_WEB_ANALYTICS_SLUG, // Menu slug
      __( 'WP Saint - Third Party Scripts', 'wp-saint' ), // Page Title
      __( 'Third Party Scripts', 'wp-saint' ), // Menu Title
      'manage_options', // capability
      self::WP_SAINT_THIRD_PARTY_SLUG, // Menu slug
      array( $this->third_party_scripts, 'create_form' ),
      4
    );

  }

  public function create_admin_page() {

    // Restrict access to this page to admins.
    if( ! current_user_can( 'manage_options' ) ) {
      wp_die( __( 'You do not have sufficient permissions to access this page.', 'wp-saint' ) );
    }

    global $pagenow;

    $this->get_page_header();
    ?>
    <div class="wrap">
      <h1>WP Saint - Options</h1>

      <div class="wp-saint-body">
        <div class="wp-saint-body-content">

        </div>

        <div class="wp-saint-box-container">
          <?php $this->get_side_menu(); ?>
        </div>
      </div>
    </div>
    <?php
    $this->get_page_footer();
  }

  public function get_page_header() {
    
    include plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/header.php';

  }

  public function get_page_footer() {

    include plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/footer.php';

  }

  public function get_side_menu() {

    include plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/side-menu.php';

  }

  public function footer_scripts() {

    ?><script> postboxes.add_postbox_toggles(pagenow); </script><?php

  }
}
