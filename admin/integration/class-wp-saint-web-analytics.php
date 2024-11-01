<?php

/**
 * The web analytics functionality of the plugin.
 *
 * @link       http://wpsaint.com/
 * @since      1.0.0
 *
 * @package    Wp_Saint
 * @subpackage Wp_Saint/admin/integration
 */

/**
 * The web analytics functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the web analytics stylesheet and JavaScript.
 *
 * @package    Wp_Saint
 * @subpackage Wp_Saint/admin/integration
 * @author     Team WP Saint <support@wpsaint.com>
 */
class Wp_Saint_Web_Analytics {

  /**
   * The admin plugin.
   *
   * @since    1.0.0
   * @access   private
   * @var      object    $admin    The admin plugin.
   */
  private $admin;

  /**
   * The WP options group name.
   *
   * @since    1.0.0
   * @access   public
   */
  const WEB_ANALYTICS_GROUP = 'wp_saint_web_analytics_group';

  /**
   * The WP options section name.
   *
   * @since    1.0.0
   * @access   public
   */
  const WEB_ANALYTICS_BASIC_SECTION = 'wp_saint_weba_basic_section';

  const WEB_ANALYTICS_ADVANCED_SECTION = 'wp_saint_weba_advanced_section';

  const WEB_ANALYTICS_COOKIES_SECTION = 'wp_saint_weba_cookies_section';

  const WEB_ANALYTICS_ENHANCED_LINK_SECTION = 'wp_saint_weba_enhanced_link_section';

  const WEB_ANALYTICS_TRACKING_SECTION = 'wp_saint_weba_tracking_section';

  /**
   * The WP options slug name.
   *
   * @since    1.0.0
   * @access   public
   */
  const WEB_ANALYTICS_SLUG = 'wp_saint_web_analytics_slug';

  /**
   * The ID of the header script setting.
   *
   * @since    1.0.0
   * @access   public
   */
  const WEB_ANALYTICS_TRACKING_ID = 'wp_saint_weba_ga_id';


  const WEB_ANALYTICS_ADVANCED_SETTINGS = 'wp_saint_weba_advanced';

  const WEB_ANALYTICS_GLOBAL_GTAG_RENAME = 'wp_saint_weba_global_gtag_rename';

  const WEB_ANALYTICS_DISABLE_ADVERTISING = 'wp_saint_weba_disable_advertising';

  const WEB_ANALYTICS_ANONYMIZE_IP = 'wp_saint_weba_anonymize_ip';

  const WEB_ANALYTICS_SET_USER_ID = 'wp_saint_weba_set_user_id';


  const WEB_ANALYTICS_COOKIE_DOMAIN = 'wp_saint_weba_cookie_domain';

  const WEB_ANALYTICS_COOKIE_EXPIRES = 'wp_saint_weba_cookie_expires';

  const WEB_ANALYTICS_COOKIE_PREFIX = 'wp_saint_weba_cookie_prefix';

  const WEB_ANALYTICS_COOKIE_UPDATE = 'wp_saint_weba_cookie_update';


  const WEB_ANALYTICS_ENHANCED_LINK_ENABLE = 'wp_saint_weba_elink_enable';

  const WEB_ANALYTICS_ENHANCED_LINK_COOKIE_NAME = 'wp_saint_weba_elink_cookie_name';

  const WEB_ANALYTICS_ENHANCED_LINK_COOKIE_EXPIRES = 'wp_saint_weba_elink_cookie_expires';

  const WEB_ANALYTICS_ENHANCED_LINK_LEVELS = 'wp_saint_weba_elink_levels';


  const WEB_ANALYTICS_DISABLE_TRACKING_FOR_ROLES = 'wp_saint_weba_disable_tracking';


  /**
   * The default values for these settings.
   */
  const WEB_ANALYTICS_GLOBAL_GTAG_RENAME_DEFAULT_VALUE = 'gtag';

  const WEB_ANALYTICS_COOKIE_EXPIRES_DEFAULT_VALUE = '730';

  const WEB_ANALYTICS_ENHANCED_LINK_COOKIE_NAME_DEFAULT_VALUE = '_gali';

  const WEB_ANALYTICS_ENHANCED_LINK_COOKIE_EXPIRES_DEFAULT_VALUE = '30';

  const WEB_ANALYTICS_ENHANCED_LINK_LEVELS_DEFAULT_VALUE = '3';

  /**
   * The ID of the footer script setting.
   *
   * @since    1.0.0
   * @access   public
   */
  const WEB_ANALYTICS_FOOTER = 'wp_saint_web_analytics_scripts_footer';

  const WEB_ANALYTICS_VALIDATION_ERRORS = 'wp_saint_web_analytics_errors';

  const WEB_ANALYTICS_VALIDATION_ERROR_VALUES = 'wp_saint_web_analytics_error_values';

  /*
   * The options for this module.
   *
   * @since    1.0.0
   * @access   private
   */
  private $options;

  private $roles_list;

  private $errors;
  
  private $error_values;

  /**
   * Initialize the class and set its properties.
   *
   * @since    1.0.0
   * @param    string    $admin       The parent admin class.
   */
  public function __construct( $admin ) {

    if( ! session_id() ) {
      session_start();
    }

    $this->admin = $admin;

    $this->load_dependencies();
    $this->load_options();

    add_action( 'admin_init', array( $this, 'init_settings' ) );

    // Register the action to output the header script.
    add_action( 'wp_head', array( $this, 'output_header_script' ), 10 );

    // Register the action to enqueue styles and scripts.
    add_action( 'admin_enqueue_scripts', array( $this, 'register_scripts' ) );

    add_action( 'updated_option', array( $this, 'updated_option' ), 10, 3 );

    $this->roles_list = self::get_editable_roles();

    $this->check_for_errors();

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
    require_once plugin_dir_path( dirname( __FILE__ ) ) . '/utility/class-wp-saint-utility.php';

  }


  /**
   * Registers the stylesheets and scripts for the admin form.
   *
   * @since    1.0.0
   */
  public function register_scripts() {

    $dev_version = time();
    // $dev_version = $this->version;

    // jQuery UI is needed for the spinner text field.
    wp_register_style( 'jquery-ui-css',
      plugins_url( 'css/jquery-ui.min.css', dirname( __FILE__ ) ),
      array(),
      '1.12.1',
      'all'
    );

    wp_register_style( 'jquery-ui-structure-css',
      plugins_url( 'css/jquery-ui.structure.min.css', dirname( __FILE__ ) ),
      array(),
      '1.12.1',
      'all'
    );

    wp_register_style( 'jquery-ui-theme-css',
      plugins_url( 'css/jquery-ui.theme.min.css', dirname( __FILE__ ) ),
      array(),
      '1.12.1',
      'all'
    );

    wp_register_style( 'switcher-css',
      plugins_url( 'css/clean-switch.css', dirname( __FILE__ ) ),
      array(),
      '1.0.0',
      'all'
    );

    wp_register_script( 'jquery-ui-js',
      plugins_url( 'js/jquery-ui.min.js', dirname( __FILE__ ) ),
      array( 'jquery' ),
      '1.12.1',
      true
    );

    wp_register_script( 'jquery-validation',
      'https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.1/jquery.validate.min.js',
      array(),
      '1.19.1',
      true
    ); 

    // Custom Javascript to configure and enable the Codemirror functionality for the textareas.
    wp_register_script( 'wp-saint-web-analytics-js',
      plugins_url( 'js/wp-saint-web-analytics.js', dirname( __FILE__ ) ),
      array( 'jquery-validation' ),
      $dev_version,
      true
    );

  }

  /**
   * Load the options for this module from the database.
   *
   * @since    1.0.0
   */
  public function load_options() {

    $this->options = get_option( Wp_Saint_Admin::WP_SAINT_WEB_ANALYTICS );

    return $this->options;

  }

  /**
   * Create and output the form for adding the third party scripts.
   *
   * @since    1.0.0
   */
  public function create_form() {

    error_log( '-------create form-------' );
    $this->check_for_errors();

    // Include the stylesheets and scripts only on this page.
    $this->enqueue_scripts();

    $this->admin->get_page_header();
    ?>
    <div class="wrap">
      <h1>WP Saint - Web Analytics</h1>

      <div class="wp-saint-body">
        <div class="wp-saint-body-content">
        <?php $this->display_errors(); ?>
        <form id="wp-saint-web-analytics-form" method="post" action="options.php">
        <?php
          settings_fields( self::WEB_ANALYTICS_GROUP );
          do_settings_sections( self::WEB_ANALYTICS_SLUG );
          submit_button();
        ?>
        </form>
        </div>

        <div class="wp-saint-box-container">
          <?php $this->admin->get_side_menu(); ?>
        </div>
      </div>
    </div>
    <?php
    $this->admin->get_page_footer();
    $this->clear_errors();
  }

  /**
   * Create and output the form for adding the third party scripts.
   *
   * @since    1.0.0
   */
  private function enqueue_scripts() {
    
    wp_enqueue_style( 'jquery-ui-css' );
    wp_enqueue_style( 'jquery-ui-structure-css' );
    wp_enqueue_style( 'jquery-ui-theme-css' );

    wp_enqueue_style( 'switcher-css' );

    wp_enqueue_script( 'wp-saint-web-analytics-js' );
    wp_enqueue_script( 'jquery-ui-js' );

  }

  /**
   * Configure the settings input fields.
   *
   * @since    1.0.0
   */
  public function init_settings() {
    
    register_setting(
      self::WEB_ANALYTICS_GROUP, // Option group
      Wp_Saint_Admin::WP_SAINT_WEB_ANALYTICS, // Option name
      array( $this, 'sanitize' )
    );

    add_settings_section(
      self::WEB_ANALYTICS_BASIC_SECTION,   // Section ID
      '', // Title
      array( $this, 'basic_section_info' ),   // Callback
      self::WEB_ANALYTICS_SLUG // Slug
    );

    add_settings_field(
      self::WEB_ANALYTICS_TRACKING_ID,  // ID
      __( 'Google Analytics Tracking ID', 'wp-saint' ),  // Title
      array( $this, 'ga_tracking_id_callback' ), // Callback
      self::WEB_ANALYTICS_SLUG, // Slug
      self::WEB_ANALYTICS_BASIC_SECTION   // Section ID
    );

    // The advanced settings section.
    add_settings_section(
      self::WEB_ANALYTICS_ADVANCED_SECTION,   // Section ID
      '', // Title
      array( $this, 'advanced_section_info' ),   // Callback
      self::WEB_ANALYTICS_SLUG // Slug
    );

    add_settings_field(
      self::WEB_ANALYTICS_TRACKING_ID,  // ID
      __( 'Show Advanced Settings', 'wp-saint' ),  // Title
      array( $this, 'enable_advanced_settings_callback' ), // Callback
      self::WEB_ANALYTICS_SLUG, // Slug
      self::WEB_ANALYTICS_ADVANCED_SECTION   // Section ID
    );

    add_settings_field(
      self::WEB_ANALYTICS_GLOBAL_GTAG_RENAME,  // ID
      __( 'Rename the global gtag() object', 'wp-saint' ),  // Title
      array( $this, 'global_gtag_rename_callback' ), // Callback
      self::WEB_ANALYTICS_SLUG, // Slug
      self::WEB_ANALYTICS_ADVANCED_SECTION   // Section ID
    );

    add_settings_field(
      self::WEB_ANALYTICS_DISABLE_ADVERTISING,  // ID
      __( 'Disable advertising feature', 'wp-saint' ),  // Title
      array( $this, 'disable_advertising_callback' ), // Callback
      self::WEB_ANALYTICS_SLUG, // Slug
      self::WEB_ANALYTICS_ADVANCED_SECTION   // Section ID
    );

    add_settings_field(
      self::WEB_ANALYTICS_ANONYMIZE_IP,  // ID
      __( 'Anonymize IP address', 'wp-saint' ),  // Title
      array( $this, 'anonymize_ip_callback' ), // Callback
      self::WEB_ANALYTICS_SLUG, // Slug
      self::WEB_ANALYTICS_ADVANCED_SECTION   // Section ID
    );

    add_settings_field(
      self::WEB_ANALYTICS_SET_USER_ID,  // ID
      __( 'Capture and set User ID', 'wp-saint' ),  // Title
      array( $this, 'set_user_id_callback' ), // Callback
      self::WEB_ANALYTICS_SLUG, // Slug
      self::WEB_ANALYTICS_ADVANCED_SECTION   // Section ID
    );

    // The cookie settings section.
    add_settings_section(
      self::WEB_ANALYTICS_COOKIES_SECTION,   // Section ID
      '', // Title
      array( $this, 'cookies_section_info' ),   // Callback
      self::WEB_ANALYTICS_SLUG // Slug
    );

    add_settings_field(
      self::WEB_ANALYTICS_COOKIE_DOMAIN,  // ID
      __( 'Cookie Domain', 'wp-saint' ),  // Title
      array( $this, 'cookie_domain_callback' ), // Callback
      self::WEB_ANALYTICS_SLUG, // Slug
      self::WEB_ANALYTICS_COOKIES_SECTION   // Section ID
    );

    add_settings_field(
      self::WEB_ANALYTICS_COOKIE_EXPIRES,  // ID
      __( 'Cookie Expires', 'wp-saint' ),  // Title
      array( $this, 'cookie_expires_callback' ), // Callback
      self::WEB_ANALYTICS_SLUG, // Slug
      self::WEB_ANALYTICS_COOKIES_SECTION   // Section ID
    );

    add_settings_field(
      self::WEB_ANALYTICS_COOKIE_PREFIX,  // ID
      __( 'Cookie Prefix', 'wp-saint' ),  // Title
      array( $this, 'cookie_prefix_callback' ), // Callback
      self::WEB_ANALYTICS_SLUG, // Slug
      self::WEB_ANALYTICS_COOKIES_SECTION   // Section ID
    );

    add_settings_field(
      self::WEB_ANALYTICS_COOKIE_UPDATE,  // ID
      __( 'Cookie Update', 'wp-saint' ),  // Title
      array( $this, 'cookie_update_callback' ), // Callback
      self::WEB_ANALYTICS_SLUG, // Slug
      self::WEB_ANALYTICS_COOKIES_SECTION   // Section ID
    );


    // The enhanced link attribution settings section.
    add_settings_section(
      self::WEB_ANALYTICS_ENHANCED_LINK_SECTION,   // Section ID
      '', // Title
      array( $this, 'elink_section_info' ),   // Callback
      self::WEB_ANALYTICS_SLUG // Slug
    );

    add_settings_field(
      self::WEB_ANALYTICS_ENHANCED_LINK_ENABLE,  // ID
      __( 'Enable Enhanced Link Attribution Settings', 'wp-saint' ),  // Title
      array( $this, 'enable_elink_settings_callback' ), // Callback
      self::WEB_ANALYTICS_SLUG, // Slug
      self::WEB_ANALYTICS_ENHANCED_LINK_SECTION   // Section ID
    );

    add_settings_field(
      self::WEB_ANALYTICS_ENHANCED_LINK_COOKIE_NAME,  // ID
      __( 'Cookie Name', 'wp-saint' ),  // Title
      array( $this, 'elink_cookie_name_callback' ), // Callback
      self::WEB_ANALYTICS_SLUG, // Slug
      self::WEB_ANALYTICS_ENHANCED_LINK_SECTION   // Section ID
    );

    add_settings_field(
      self::WEB_ANALYTICS_ENHANCED_LINK_COOKIE_EXPIRES,  // ID
      __( 'Cookie Expires', 'wp-saint' ),  // Title
      array( $this, 'elink_cookie_expires_callback' ), // Callback
      self::WEB_ANALYTICS_SLUG, // Slug
      self::WEB_ANALYTICS_ENHANCED_LINK_SECTION   // Section ID
    );

    add_settings_field(
      self::WEB_ANALYTICS_ENHANCED_LINK_LEVELS,  // ID
      __( 'Levels', 'wp-saint' ),  // Title
      array( $this, 'elink_levels_callback' ), // Callback
      self::WEB_ANALYTICS_SLUG, // Slug
      self::WEB_ANALYTICS_ENHANCED_LINK_SECTION   // Section ID
    );

    // The enhanced link attribution settings section.
    add_settings_section(
      self::WEB_ANALYTICS_TRACKING_SECTION,   // Section ID
      __( '', 'wp-saint' ),  // Title
      array( $this, 'tracking_section_info' ),   // Callback
      self::WEB_ANALYTICS_SLUG // Slug
    );
    
    add_settings_field(
      self::WEB_ANALYTICS_DISABLE_TRACKING_FOR_ROLES,  // ID
      __( 'Disable tracking for', 'wp-saint' ),  // Title
      array( $this, 'disable_tracking_callback' ), // Callback
      self::WEB_ANALYTICS_SLUG, // Slug
      self::WEB_ANALYTICS_TRACKING_SECTION   // Section ID
    );

  }

  /**
   * Output the info text for the scripts fields section.
   *
   * @since    1.0.0
   */
  public function basic_section_info() {

  }

  /**
   * Output the info text for the scripts fields section.
   *
   * @since    1.0.0
   */
  public function advanced_section_info() {

    print "<hr />";
    print "<h3>Advanced Settings</h3>";

  }

  /**
   * Output the info text for the cookies fields section.
   *
   * @since    1.0.0
   */
  public function cookies_section_info() {

    print '<div id="wp_saint_cookies_header">';
    print "<hr />";
    print '<h3>Cookies</h3>';
    print '</div>';

  }

  /**
   * Output the info text for the enchanced link attribution fields section.
   *
   * @since    1.0.0
   */
  public function elink_section_info() {
    
    print '<div id="wp_saint_elink_header">';
    print "<hr />";
    print '<h3>Enhanced Link Attribution</h3>';
    print '</div>';

  }

  /**
   * Output the info text for the scripts fields section.
   *
   * @since    1.0.0
   */
  public function tracking_section_info() {

    print '<div id="wp_saint_roles_header">';
    print "<hr />";
    print '<h3>Disable Google Analytics Tracking</h3>';
    print '</div>';

  }

  /**
   * Process the user input for the textareas.
   *
   * @since    1.0.0
   */
  public function sanitize( $input ) {

    // error_log( print_r( $_POST, TRUE ) );
    // error_log( print_r( $input, TRUE ) );

    $new_input = array();

    $field_name = self::WEB_ANALYTICS_TRACKING_ID;
    $tracking_id = Wp_Saint_Utility::process_textfield_input( $input, $field_name );
    if( Wp_Saint_Utility::validate_google_analytics_id( $tracking_id ) ) {
      $new_input[ $field_name ] = $tracking_id;
    } else {
      $this->add_error( $field_name, __( 'The Google Tracking ID must be in a valid format.' ) );
      $this->add_error_value( $field_name, $tracking_id );
    }

    $field_name = self::WEB_ANALYTICS_ADVANCED_SETTINGS;
    $new_input[ $field_name ] = Wp_Saint_Utility::process_textfield_input( $input, $field_name );

    $field_name = self::WEB_ANALYTICS_GLOBAL_GTAG_RENAME;
    $field_value = Wp_Saint_Utility::process_textfield_input( $input, $field_name );
    if( strlen( $field_value ) == 0 ) {
      $field_value = self::WEB_ANALYTICS_GLOBAL_GTAG_RENAME_DEFAULT_VALUE;
    } else {
      if( Wp_Saint_Utility::validate_tag_name( $field_value ) ) {
        $new_input[ $field_name ] = $field_value;
      } else {
        $this->add_error( $field_name, __( 'Only letters and numbers are allowed in the global gtag() object rename field.' ) );
        $this->add_error_value( $field_name, $field_value );
      }
    }

    $field_name = self::WEB_ANALYTICS_DISABLE_ADVERTISING;
    $new_input[ $field_name ] = Wp_Saint_Utility::process_textfield_input( $input, $field_name );

    $field_name = self::WEB_ANALYTICS_ANONYMIZE_IP;
    $new_input[ $field_name ] = Wp_Saint_Utility::process_textfield_input( $input, $field_name );

    $field_name = self::WEB_ANALYTICS_SET_USER_ID;
    $new_input[ $field_name ] = Wp_Saint_Utility::process_textfield_input( $input, $field_name );


    $field_name = self::WEB_ANALYTICS_COOKIE_DOMAIN;
    $new_input[ $field_name ] = Wp_Saint_Utility::process_textfield_input( $input, $field_name );

    $field_name = self::WEB_ANALYTICS_COOKIE_EXPIRES;
    $new_input[ $field_name ] = Wp_Saint_Utility::process_numeric_input( $input, $field_name );

    $field_name = self::WEB_ANALYTICS_COOKIE_PREFIX;
    $field_value = Wp_Saint_Utility::process_textfield_input( $input, $field_name );
    if( strlen( $field_value ) > 0 ) {
      if( Wp_Saint_Utility::validate_tag_name( $field_value ) ) {
        $new_input[ $field_name ] = $field_value;
      } else {
        $this->add_error( $field_name, __( 'Only letters and numbers are allowed in the Cookie Prefix field.' ) );
        $this->add_error_value( $field_name, $field_value );
      }
    }

    $field_name = self::WEB_ANALYTICS_COOKIE_UPDATE;
    $new_input[ $field_name ] = Wp_Saint_Utility::process_textfield_input( $input, $field_name );


    $field_name = self::WEB_ANALYTICS_ENHANCED_LINK_ENABLE;
    $new_input[ $field_name ] = Wp_Saint_Utility::process_textfield_input( $input, $field_name );

    $field_name = self::WEB_ANALYTICS_ENHANCED_LINK_COOKIE_NAME;
    $field_value = Wp_Saint_Utility::process_textfield_input( $input, $field_name );

    // Validate if there is an input value.
    if( strlen( $field_value ) > 0 ) {

      // Validate only if the user has entered a value different from the default value.
      if( $field_value !== self::WEB_ANALYTICS_ENHANCED_LINK_COOKIE_NAME_DEFAULT_VALUE ) {

        // Save the value if the value has only alphanumeric characters.
        if( Wp_Saint_Utility::validate_tag_name( $field_value ) ) {
          $new_input[ $field_name ] = $field_value;
        } else {
          $this->add_error( $field_name, __( 'Only letters and numbers are allowed in the Enhanced Link Attribution Cookie Name field.' ) );
          $this->add_error_value( $field_name, $field_value );
        }
      }
    }

    $field_name = self::WEB_ANALYTICS_ENHANCED_LINK_COOKIE_EXPIRES;
    $new_input[ $field_name ] = Wp_Saint_Utility::process_numeric_input( $input, $field_name );

    $field_name = self::WEB_ANALYTICS_ENHANCED_LINK_LEVELS;
    $level = Wp_Saint_Utility::process_numeric_input( $input, $field_name );
    if( ! empty( $level ) ) {
      $level_int = intval( $level );
      if( $level >= 1 && $level <= 5 ) {
        $new_input[ $field_name ] = $level;
      } else {
        $this->add_error( $field_name, __( 'The Level must have a value from 1 to 5.' ) );
      }
    }

    $field_name = self::WEB_ANALYTICS_DISABLE_TRACKING_FOR_ROLES;
    $new_input[ $field_name ] = Wp_Saint_Utility::process_roles_input( $input, $field_name );

    // error_log( 'errors: ' . print_r( $this->errors, TRUE ) );
    // error_log( 'error.vals: ' . print_r( $this->error_values, TRUE ) );
    if( ! empty( $this->errors ) ) {
      $this->save_errors();
    }

    // error_log( 'new_input: ' . print_r( $new_input, TRUE ) );

    return $new_input;

  }

  /**
   * Output the HTML for the header script form field.
   *
   * @since    1.0.0
   */
  public function ga_tracking_id_callback() {

    $field_name = self::WEB_ANALYTICS_TRACKING_ID;
    $field_value = Wp_Saint_Utility::get_option_value( $this->options, $field_name );

    $classes = '';
    if( $this->field_has_error( $field_name ) ) {
      $classes = 'wp-saint-error-field';
      $field_value = $this->get_error_value( $field_name );
    }

    printf(
      '<input type="text" size="20" maxlength="40" class="%s" id="' . $field_name . '" name="' .  Wp_Saint_Admin::WP_SAINT_WEB_ANALYTICS . '[' .  $field_name . ']" value="%s" />', 
      $classes,
      $field_value
    );
    echo '<div class="description">Your GA tracking ID that starts with <code>UA</code>. Like UA-1231231-25</div>';

  }

  /**
   * Output the HTML for the header script form field.
   *
   * @since    1.0.0
   */
  public function enable_advanced_settings_callback() {

    $field_name = self::WEB_ANALYTICS_ADVANCED_SETTINGS;

    echo '<label class="cl-switch">';
    echo '<input type="checkbox" id="' . $field_name . '" name="' .  Wp_Saint_Admin::WP_SAINT_WEB_ANALYTICS . '[' .  $field_name . ']" value="enabled" ';

    $field_value = "off";
    if( $this->is_advanced_settings_enabled() ) {
      echo 'checked="checked" ';
      $field_value = "on";
    }
    
    echo '/>';
    echo '<span class="switcher"></span>';
    echo '</label>';

    echo '<input type="hidden" id="' . $field_name . '_value" name="' . $field_name . '_value" value="' . $field_value . '" />';

    echo '<p id="wp_saint_adv_warning" class="wp-saint-adv-warning">Warning: please make changes only if you know what you are doing, turn off this feature to use default Google Analytics gtag.js code.</p>';
  }

  /**
   * Output the HTML for the header script form field.
   *
   * @since    1.0.0
   */
  public function global_gtag_rename_callback() {

    $field_name = self::WEB_ANALYTICS_GLOBAL_GTAG_RENAME;

    $field_value = Wp_Saint_Utility::get_option_value( $this->options, $field_name );
    if( empty( $field_value ) ) {
      // The default value.
      $field_value = self::WEB_ANALYTICS_GLOBAL_GTAG_RENAME_DEFAULT_VALUE;
    }

    $classes = '';
    if( $this->field_has_error( $field_name ) ) {
      $classes = 'wp-saint-error-field';
      $field_value = $this->get_error_value( $field_name );
    }

    printf(
      '<input type="text" size="10" maxlength="10" class="%s" id="' . $field_name . '" name="' .  Wp_Saint_Admin::WP_SAINT_WEB_ANALYTICS . '[' .  $field_name . ']" value="%s" />', 
      $classes,
      $field_value
    );
    echo '<div class="description">Rename the global <code>gtag</code> object to this value.</div>';

  }

  /**
   * Output the HTML for the advertising disable toggle.
   *
   * @since    1.0.0
   */
  public function disable_advertising_callback() {

    $field_name = self::WEB_ANALYTICS_DISABLE_ADVERTISING;

    echo '<label class="cl-switch">';
    echo '<input type="checkbox" style="display: none;" id="' . $field_name . '" name="' .  Wp_Saint_Admin::WP_SAINT_WEB_ANALYTICS . '[' .  $field_name . ']" value="yes" ';

    if( Wp_Saint_Utility::get_option_value( $this->options, $field_name ) === 'yes' ) {
      echo 'checked="checked" ';
    }
    
    echo '/>';
    echo '<span class="switcher"></span>';
    echo '</label>';
    echo '<div class="description">Turn on to disable advertising feature.</div>';

  }

  /**
   * Output the HTML for the advertising disable toggle.
   *
   * @since    1.0.0
   */
  public function anonymize_ip_callback() {

    $field_name = self::WEB_ANALYTICS_ANONYMIZE_IP;

    echo '<label class="cl-switch">';
    echo '<input type="checkbox" style="display: none;" id="' . $field_name . '" name="' .  Wp_Saint_Admin::WP_SAINT_WEB_ANALYTICS . '[' .  $field_name . ']" value="yes" ';

    if( Wp_Saint_Utility::get_option_value( $this->options, $field_name ) === 'yes' ) {
      echo 'checked="checked" ';
    }
    
    echo '/>';
    echo '<span class="switcher"></span>';
    echo '</label>';
    echo '<div class="description">Turn on to anonymize user IP address.</div>';

  }

  /**
   * Output the HTML for the advertising disable toggle.
   *
   * @since    1.0.0
   */
  public function set_user_id_callback() {

    $field_name = self::WEB_ANALYTICS_SET_USER_ID;

    echo '<label class="cl-switch">';
    echo '<input type="checkbox" style="display: none;" id="' . $field_name . '" name="' .  Wp_Saint_Admin::WP_SAINT_WEB_ANALYTICS . '[' .  $field_name . ']" value="yes" ';

    if( Wp_Saint_Utility::get_option_value( $this->options, $field_name ) === 'yes' ) {
      echo 'checked="checked" ';
    }
    
    echo '/>';
    echo '<span class="switcher"></span>';
    echo '</label>';
    echo '<div class="description">Captures and sets user ids for logged-in users only.</div>';

  }

  /**
   * Output the HTML for the cookie domain form field.
   *
   * @since    1.0.0
   */
  public function cookie_domain_callback() {

    $field_name = self::WEB_ANALYTICS_COOKIE_DOMAIN;
    $field_value = Wp_Saint_Utility::get_option_value( $this->options, $field_name );
    if( empty( $field_value ) ) {
      $field_value = $this->get_default_cookie_domain();
    }

    printf(
      '<input type="text" size="20" class="" id="' . $field_name . '" name="' .  Wp_Saint_Admin::WP_SAINT_WEB_ANALYTICS . '[' .  $field_name . ']" value="%s" />', 
      $field_value
    );
    echo '<div class="description">The cookie_domain must be an ancestor of the current domain. Setting an incorrect cookie domain will result in no hits being sent to Google Analytics.</div>';

  }


  /**
   * Output the HTML for the cookie expires form field.
   *
   * @since    1.0.0
   */
  public function cookie_expires_callback() {

    $field_name = self::WEB_ANALYTICS_COOKIE_EXPIRES;
    $field_value = Wp_Saint_Utility::get_option_value( $this->options, $field_name );
    if( empty( $field_value ) ) {
      $field_value = "730";
    }

    printf(
      '<input type="text" size="5" maxlength="4" class="ui-spinner-input wp-saint-spinner" id="' . $field_name . '" name="' .  Wp_Saint_Admin::WP_SAINT_WEB_ANALYTICS . '[' .  $field_name . ']" value="%s" />', 
      $field_value
    );
    echo '<div class="description">The number of days in which the GA cookie expire.  If you set the cookie_expires value to 0 (zero) seconds, the cookie turns into a session based cookie and expires once the current browser session ends.</div>';

  }

  /**
   * Output the HTML for the cookie prefix form field.
   *
   * @since    1.0.0
   */
  public function cookie_prefix_callback() {

    $field_name = self::WEB_ANALYTICS_COOKIE_PREFIX;

    $field_value = Wp_Saint_Utility::get_option_value( $this->options, $field_name );

    $classes = '';
    if( $this->field_has_error( $field_name ) ) {
      $classes = 'wp-saint-error-field';
      $field_value = $this->get_error_value( $field_name );
    }

    printf(
      '<input type="text" size="20" maxlength="10" class="%s" id="' . $field_name . '" name="' .  Wp_Saint_Admin::WP_SAINT_WEB_ANALYTICS . '[' .  $field_name . ']" value="%s" />', 
      $classes,
      $field_value
    );
    echo '<div class="description">To avoid conflicts with other cookies, you can change the cookie prefix, which will be prepended to cookies set by gtag.js.</div>';

  }


  /**
   * Output the HTML for the cookie update form field.
   *
   * @since    1.0.0
   */
  public function cookie_update_callback() {

    $field_name = self::WEB_ANALYTICS_COOKIE_UPDATE;

    $field_value = Wp_Saint_Utility::get_option_value( $this->options, $field_name );

    echo '<select id="' . $field_name . '" name="' . Wp_Saint_Admin::WP_SAINT_WEB_ANALYTICS . '[' .  $field_name . ']">';
    echo '<option value="true" ' . ( ( $field_value === 'true' ) ? 'selected="selected" ' : '' ) . '>True</option>';
    echo '<option value="false" ' . ( ( $field_value === 'false' ) ? 'selected="selected" ' : '' ) . '>False</option>';
    echo '</select>';

    echo '<div class="description">When set to false, cookies are not updated on each page load. This has the effect of cookie expiration being relative to the first time a user visited the site.</div>';

  }

  /**
   * Output the HTML for the elink settings form field.
   *
   * @since    1.0.0
   */
  public function enable_elink_settings_callback() {

    $field_name = self::WEB_ANALYTICS_ENHANCED_LINK_ENABLE;

    echo '<label class="cl-switch">';
    echo '<input type="checkbox" style="display: none;" id="' . $field_name . '" name="' .  Wp_Saint_Admin::WP_SAINT_WEB_ANALYTICS . '[' .  $field_name . ']" value="enabled" ';

    $field_value = "off";
    if( $this->is_elink_settings_enabled() ) {
      echo 'checked="checked" ';
      $field_value = "on";
    }
    
    echo '/>';
    echo '<span class="switcher"></span>';
    echo '</label>';
    echo '<input type="hidden" id="' . $field_name . '_value" name="' . $field_name . '_value" value="' . $field_value . '" />';

  }


  /**
   * Output the HTML for the elink cookie name field.
   *
   * @since    1.0.0
   */
  public function elink_cookie_name_callback() {

    $field_name = self::WEB_ANALYTICS_ENHANCED_LINK_COOKIE_NAME;
    $field_value = Wp_Saint_Utility::get_option_value( $this->options, $field_name );
    if( empty( $field_value ) ) {
      $field_value = self::WEB_ANALYTICS_ENHANCED_LINK_COOKIE_NAME_DEFAULT_VALUE;
    }

    $classes = '';
    if( $this->field_has_error( $field_name ) ) {
      $classes = 'wp-saint-error-field';
      $field_value = $this->get_error_value( $field_name );
    }

    printf(
      '<input type="text" size="10" maxlength="10" class="%s" id="' . $field_name . '" name="' .  Wp_Saint_Admin::WP_SAINT_WEB_ANALYTICS . '[' .  $field_name . ']" value="%s" />', 
      $classes,
      $field_value
    );
    echo '<div class="description">The name of the link attribution cookie name. Defaults to <code>_gali</code>.</div>';

  }

  /**
   * Output the HTML for the elink cookie expires form field.
   *
   * @since    1.0.0
   */
  public function elink_cookie_expires_callback() {

    $field_name = self::WEB_ANALYTICS_ENHANCED_LINK_COOKIE_EXPIRES;
    $field_value = Wp_Saint_Utility::get_option_value( $this->options, $field_name );
    if( empty( $field_value ) ) {
      $field_value = self::WEB_ANALYTICS_ENHANCED_LINK_COOKIE_EXPIRES_DEFAULT_VALUE;
    }

    printf(
      '<input type="text" size="5" maxlength="4" class="ui-spinner-input wp-saint-spinner" id="' . $field_name . '" name="' .  Wp_Saint_Admin::WP_SAINT_WEB_ANALYTICS . '[' .  $field_name . ']" value="%s" /> seconds', 
      $field_value
    );
    echo '<div class="description">The maximum duration (in seconds) the enhanced link attribution cookie should be saved for.</div>';

  }

  /**
   * Output the HTML for the elink levels form field.
   *
   * @since    1.0.0
   */
  public function elink_levels_callback() {

    $field_name = self::WEB_ANALYTICS_ENHANCED_LINK_LEVELS;
    $field_value = Wp_Saint_Utility::get_option_value( $this->options, $field_name );
    if( empty( $field_value ) ) {
      $field_value = self::WEB_ANALYTICS_ENHANCED_LINK_LEVELS_DEFAULT_VALUE;
    }

    printf(
      '<input type="text" size="5" maxlength="4" class="ui-spinner-input wp-saint-spinner" id="' . $field_name . '" name="' .  Wp_Saint_Admin::WP_SAINT_WEB_ANALYTICS . '[' .  $field_name . ']" value="%s" />', 
      $field_value
    );
    echo '<div class="description">The maximum number of levels in the DOM to look for an existing ID.</div>';

  }


  /**
   * Output the HTML for the elink levels form field.
   *
   * @since    1.0.0
   */
  public function disable_tracking_callback() {

    $field_name = self::WEB_ANALYTICS_DISABLE_TRACKING_FOR_ROLES;

    $selected_roles = Wp_Saint_Utility::get_array_value( $this->options, $field_name );
    if( ! isset( $selected_roles ) || empty( $selected_roles ) ) {
      $selected_roles = array();
    }

    echo '<div id="wp_saint_roles_container" class="wp-saint-cb-container">';

    $this->roles_list = self::get_editable_roles();

    foreach( $this->roles_list as $role_name => $role ) {
      echo '<div class="switcher-row">';
      echo '<label class="cl-switch">';
      echo '<input type="checkbox"  value="enabled" ';
      echo 'name="' . Wp_Saint_Admin::WP_SAINT_WEB_ANALYTICS . '[' .  $field_name . '][' . $role_name . ']"';

      if( ! empty( $selected_roles ) && in_array( $role_name, $selected_roles ) ) {
        echo ' checked="checked"';
      }
      
      echo '>';
      echo '<span class="switcher"></span>';
      echo '<span class="label">' . $role['name'] . '</label>';
      echo '</label>';
      echo '</div>';
    } // foreach

    echo '</div>';

    echo '<div class="description">Google Analytics tracking will be disabled for the above user types.</div>';

  }


  /**
   * Output the header script contents.
   *
   * @since    1.0.0
   */
  public function output_header_script() {

    // Skip admin pages, feed, robots and trackbacks.
    if( is_admin() || is_feed() || is_robots() || is_trackback() ) {
      return;
    }

    // Get the value for this field.
    $ga_code = $this->get_ga_code();
    if( empty( $ga_code ) ) {
      return;
    }

    if( strlen( trim( $ga_code ) ) == 0 ) {
      return;
    }

    // Wrap the content in comments and output it.
    echo $this->get_output_prefix() . wp_unslash( $ga_code ) . $this->get_output_suffix();

  }

  /**
   * Output the leading content for the script.
   *
   * @since    1.0.0
   */
  private function get_output_prefix() {
    
    return '
<!-- START: Google Analytics Using - WP Saint www.wpsaint.com -->';

  }

  /**
   * Output the trailing content for the script.
   *
   * @since    1.0.0
   */
  private function get_output_suffix() {
    
    return '<!-- END: Google Analytics Using - WP Saint www.wpsaint.com -->
';

  }

  /**
   * Returns the advanced setting value.
   *
   * @since    1.0.0
   * @return   Boolean the setting value
   */
  private function is_advanced_settings_enabled() {

    $advanced_setting = Wp_Saint_Utility::get_option_value( $this->options, self::WEB_ANALYTICS_ADVANCED_SETTINGS );

    return ( $advanced_setting === 'enabled' );

  }

  /**
   * Returns the elink setting value.
   *
   * @since    1.0.0
   * @return   Boolean the setting value
   */
  private function is_elink_settings_enabled() {

    $elink_setting = Wp_Saint_Utility::get_option_value( $this->options, self::WEB_ANALYTICS_ENHANCED_LINK_ENABLE );

    return ( $elink_setting === 'enabled' );

  }

  /**
   * Checks if the input string is a valid Google Analytics ID.
   *
   * @since    1.0.0
   * @return   true if the string is valid
   */
  private function isAnalytics($str){

    return preg_match( '/^ua-\d{4,9}-\d{1,4}$/i', strtolower( strval( $str ) ) ) ? true : false;

  }

  private function get_default_cookie_domain() {

    return Wp_Saint_Utility::get_domain( $_SERVER['SERVER_NAME'] );

  }

  private static function get_editable_roles() {

    global $wp_roles;

    $editable_roles = array();

    if( isset( $wp_roles ) ) {
      $all_roles = $wp_roles->roles;
      $editable_roles = apply_filters( 'editable_roles', $all_roles );
    }

    return $editable_roles;

  }

  private function is_ga_disabled_for_user() {

    // Get the list of roles for which tracking is disabled.
    $disabled_roles = Wp_Saint_Utility::get_array_value( $this->options, self::WEB_ANALYTICS_DISABLE_TRACKING_FOR_ROLES );
    if( ! empty( $disabled_roles ) ) {

      $user = wp_get_current_user();

      $has_disabled_roles = array_intersect( (array) $user->roles, $disabled_roles );
      if( ! empty( $has_disabled_roles ) ) {
        return true;
      }

    }

    return false;

  }

  /**
   * Generate the Google Analytics script code.
   *
   * @since    1.0.0
   * @return   the script tags.
   */
  private function get_ga_code() {

    // The GA tracking ID is required to output the script tags.
    $ga_tracking_id = Wp_Saint_Utility::get_option_value( $this->options, self::WEB_ANALYTICS_TRACKING_ID );
    if( isset( $ga_tracking_id ) && ! empty( $ga_tracking_id ) && strlen( $ga_tracking_id ) > 0 ) {
    } else {
      // If there is no tracking ID, return an empty string.
      return '';
    }

    // Start with the script tag to load the gtag js file.
    $template =<<< EOT

<script async src="https://www.googletagmanager.com/gtag/js?id=GA_TRACKING_ID"></script>
<script>

EOT;

    // This must be done before any calls to gtag().
    // Google Analytics can be disabled for certain user roles.
    if( $this->is_ga_disabled_for_user() ) {
      $disable_template =<<< EOT
  window['ga-disable-GA_TRACKING_ID'] = true;


EOT;
      $template .= $disable_template;
    }


    // These tags will always be present regardless of other configuration.
    $data_layer_template =<<< EOT
window.dataLayer = window.dataLayer || [];    
function {GTAG}(){dataLayer.push(arguments);}
{GTAG}('js', new Date());

EOT;
    $template .= $data_layer_template;

    if( $this->is_advanced_settings_enabled() ) {

      // Additional configuration settings.
      $config = array();

      // Disable advertising.
      if( Wp_Saint_Utility::get_option_value( $this->options, self::WEB_ANALYTICS_DISABLE_ADVERTISING ) === 'yes' ) {
        $config['allow_ad_personalization_signals'] = false;
      }

      // Anonymize IP address.
      if( Wp_Saint_Utility::get_option_value( $this->options, self::WEB_ANALYTICS_ANONYMIZE_IP ) === 'yes' ) {
        $config['anonymize_ip'] = true;
      }

      // Capture User ID.
      if( Wp_Saint_Utility::get_option_value( $this->options, self::WEB_ANALYTICS_SET_USER_ID ) === 'yes' ) {
        if( is_user_logged_in() ) {
          // Add the username only if the user is logged in.
          $user = wp_get_current_user();
          $config['user_id'] = $user->ID;
        }
      }

      // Cookies.
      $cookie_prefix = Wp_Saint_Utility::get_option_value( $this->options, self::WEB_ANALYTICS_COOKIE_PREFIX );
      if( isset( $cookie_prefix ) && ! empty( $cookie_prefix ) && strlen( $cookie_prefix ) > 0 ) {
        $config['cookie_prefix'] = $cookie_prefix;
      }

      // Cookie Domain.
      $cookie_domain = Wp_Saint_Utility::get_option_value( $this->options, self::WEB_ANALYTICS_COOKIE_DOMAIN );
      // Include the cookie domain entry only if different from the default cookie domain.
      if( isset( $cookie_domain ) && ! empty( $cookie_domain ) && strlen( $cookie_domain ) > 0 && $cookie_domain !== $this->get_default_cookie_domain() ) {
        $config['cookie_domain'] = $cookie_domain;
      }

      // Cookie Expires.
      $cookie_expires = Wp_Saint_Utility::get_option_value( $this->options, self::WEB_ANALYTICS_COOKIE_EXPIRES );
      if( isset( $cookie_expires ) && ! empty( $cookie_expires ) && strlen( $cookie_expires ) > 0 ) {
        if( intval( $cookie_expires ) !== intval( self::WEB_ANALYTICS_COOKIE_EXPIRES_DEFAULT_VALUE ) ) {
          $config['cookie_expires'] = intval( $cookie_expires ) * DAY_IN_SECONDS;
        }
      }

      // Cookie Update
      $cookie_update = Wp_Saint_Utility::get_option_value( $this->options, self::WEB_ANALYTICS_COOKIE_UPDATE );
      if( isset( $cookie_update ) && ! empty( $cookie_update ) && strlen( $cookie_update ) > 0 ) {
        if( $cookie_update !== "true" ) {
          $config['cookie_update'] = ( $cookie_update !== "true" );
        }
      }

      $elink_config = array();

      if( $this->is_elink_settings_enabled() ) {

        $elink_cookie_name = Wp_Saint_Utility::get_option_value( $this->options, self::WEB_ANALYTICS_ENHANCED_LINK_COOKIE_NAME );
        if( isset( $elink_cookie_name ) && ! empty( $elink_cookie_name ) && strlen( $elink_cookie_name ) > 0 ) {
          if( $elink_cookie_name === self::WEB_ANALYTICS_ENHANCED_LINK_COOKIE_NAME_DEFAULT_VALUE ) { 
          } else {
            $elink_config['cookie_name'] = $elink_cookie_name;
          }
        }

        $elink_cookie_expires = Wp_Saint_Utility::get_option_value( $this->options, self::WEB_ANALYTICS_ENHANCED_LINK_COOKIE_EXPIRES );
        if( isset( $elink_cookie_expires ) && ! empty( $elink_cookie_expires ) && strlen( $elink_cookie_expires ) > 0 ) {
          $cookie_expires_value = intval( $elink_cookie_expires );
          if( $cookie_expires_value !== intval( self::WEB_ANALYTICS_ENHANCED_LINK_COOKIE_EXPIRES_DEFAULT_VALUE ) ) {
            $elink_config['cookie_expires'] = $cookie_expires_value;
          }
        }

        $elink_cookie_levels = Wp_Saint_Utility::get_option_value( $this->options, self::WEB_ANALYTICS_ENHANCED_LINK_LEVELS );
        if( isset( $elink_cookie_levels ) && ! empty( $elink_cookie_levels ) && strlen( $elink_cookie_levels ) > 0 ) {
          if( intval( $elink_cookie_levels ) !== intval( self::WEB_ANALYTICS_ENHANCED_LINK_LEVELS_DEFAULT_VALUE ) ) {
            $elink_config['levels'] = $elink_cookie_levels;
          }
        }

      } // elink settings enabled.

      if( empty( $elink_config ) ) {
        // If using only the default settings, show the value as 'true'.
        $config['link_attribution'] = 'true';
      } else {
        $config['link_attribution'] = $elink_config;
      }
    } // advanced_settings_enabled?

    // error_log( print_r( $config, TRUE ) );

    if( ! empty( $config ) ) {
      // If there are config settings, encode them and include in the config section.
      $config_json = json_encode( $config, JSON_PRETTY_PRINT );
      $gtag_config =<<< EOT

{GTAG}('config', 'GA_TRACKING_ID', {$config_json});

EOT;
      $template .= $gtag_config;

    } else {
      // If there are no config settings, output the simple form of the config tag.

      $gtag_config =<<< EOT

{GTAG}('config', 'GA_TRACKING_ID');

EOT;
      $template .= $gtag_config;

    } // if empty config.


      /*
      'send_page_view': false,        // only if disable pageview is set to true
      'allow_display_features': false,    //Disable display features is set to yes
      'optimize_id': 'OPT_CONTAINER_ID'   //LATER PURPOSE. Deploy Google Optimize container ID
      */

    $template_end =<<< EOT
</script>

EOT;
    $template .= $template_end;

    // error_log( $template );

    // Substitute the gtag name.
    $gtag_name = Wp_Saint_Utility::get_option_value( $this->options, self::WEB_ANALYTICS_GLOBAL_GTAG_RENAME );
    if( isset( $gtag_name ) && ! empty( $gtag_name ) && strlen( $gtag_name ) > 0 ) {
    } else {
      $gtag_name = 'gtag';
    }
    $template = str_replace( '{GTAG}', $gtag_name, $template );

    // Substitute the tracking id value.
    $template = str_replace( 'GA_TRACKING_ID', $ga_tracking_id, $template );

    return $template;

  }

  public function updated_option( $option_name, $old_value, $option_value ) {

    if( $option_name === Wp_Saint_Admin::WP_SAINT_WEB_ANALYTICS ) {
      // error_log( 'opt.name: ' . $option_name );
    }

  }

  private function add_error( $field_name, $error_message ) {

    $this->errors[ $field_name ] = $error_message;

  }

  private function add_error_value( $field_name, $error_field_value ) {

    $this->error_values[ $field_name ] = $error_field_value;

  }

  private function display_errors() {

    if( ! empty( $this->errors ) ) {
      ?><div class="error wp-saint-error-msg">

        <?php foreach( $this->errors as $error ) {
          ?><li><?php echo $error; ?></li><?php
        } ?>
      </div><?php
    }

  }

  private function save_errors() {

    if( ! empty( $this->errors ) ) {
      $_SESSION[ self::WEB_ANALYTICS_VALIDATION_ERRORS ] = $this->errors;
      $_SESSION[ self::WEB_ANALYTICS_VALIDATION_ERROR_VALUES ] = $this->error_values;
    }

  }

  private function clear_errors() {

    if( isset( $_SESSION[ self::WEB_ANALYTICS_VALIDATION_ERRORS ] ) ) {
      unset( $_SESSION[ self::WEB_ANALYTICS_VALIDATION_ERRORS ] );
    }
    $this->errors = array();

    if( isset( $_SESSION[ self::WEB_ANALYTICS_VALIDATION_ERROR_VALUES ] ) ) {
      unset( $_SESSION[ self::WEB_ANALYTICS_VALIDATION_ERROR_VALUES ] );
    }
    $this->error_values = array();

  }

  private function field_has_error( $field_name ) {

    return isset( $this->errors[ $field_name ] );

  }

  private function get_error_value( $field_name ) {
    
    if( isset( $this->errors[ $field_name ] ) ) {
      // Verify that this field has an error.
      if( isset( $this->error_values[ $field_name ] ) ) {
        return $this->error_values[ $field_name ];
      }
    }

    return '';

  }

  private function check_for_errors() {
    
    if( isset( $_SESSION[ self::WEB_ANALYTICS_VALIDATION_ERRORS ] ) ) {
      $this->errors = $_SESSION[ self::WEB_ANALYTICS_VALIDATION_ERRORS ];
      $this->error_values = $_SESSION[ self::WEB_ANALYTICS_VALIDATION_ERROR_VALUES ];
    } else {
      $this->errors = array();
      $this->error_values = array();
    }

  }

}
