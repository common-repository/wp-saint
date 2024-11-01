<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://wpsaint.com/
 * @since      1.0.0
 *
 * @package    Wp_Saint
 * @subpackage Wp_Saint/admin/integration
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wp_Saint
 * @subpackage Wp_Saint/admin/integration
 * @author     Team WP Saint <support@wpsaint.com>
 */
class Wp_Saint_Third_Party_Scripts {

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
  const THIRD_PARTY_SCRIPTS_GROUP = 'wp_saint_third_party_group';

  /**
   * The WP options section name.
   *
   * @since    1.0.0
   * @access   public
   */
  const THIRD_PARTY_SCRIPTS_SECTION = 'wp_saint_third_party_section';

  /**
   * The WP options slug name.
   *
   * @since    1.0.0
   * @access   public
   */
  const THIRD_PARTY_SCRIPTS_SLUG = 'wp_saint_third_party_slug';

  /**
   * The ID of the header script setting.
   *
   * @since    1.0.0
   * @access   public
   */
  const THIRD_PARTY_SCRIPTS_HEADER = 'wp_saint_third_party_scripts_header';

  /**
   * The ID of the footer script setting.
   *
   * @since    1.0.0
   * @access   public
   */
  const THIRD_PARTY_SCRIPTS_FOOTER = 'wp_saint_third_party_scripts_footer';

  /*
   * The options for this module.
   *
   * @since    1.0.0
   * @access   private
   */
  private $options;

  /**
   * Initialize the class and set its properties.
   *
   * @since    1.0.0
   * @param    string    $admin       The parent admin class.
   */
  public function __construct( $admin ) {

    $this->admin = $admin;

    $this->load_dependencies();

    add_action( 'admin_init', array( $this, 'init_settings' ) );

    // Register the action to output the header script.
    add_action( 'wp_head', array( $this, 'output_header_script' ), 11 );

    // Register the action to output the footer script.
    add_action( 'wp_footer', array( $this, 'output_footer_script' ) );

    // Register the action to enqueue styles and scripts.
    add_action( 'admin_enqueue_scripts', array( $this, 'register_scripts' ) );

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
    
    // Various Codemirror dependencies.
    wp_register_style( 'codemirror-css',
      'https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.48.4/codemirror.min.css',
      array(),
      '5.48.4',
      'all'
    );

    wp_register_style( 'codemirror-theme-css',
      'https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.48.4/theme/neo.min.css',
      array(),
      '5.48.4',
      'all'
    );

    wp_register_script( 'codemirror-mode-js',
      'https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.48.4/mode/htmlmixed/htmlmixed.min.js',
      array(),
      '5.48.4',
      true
    );

    wp_register_script( 'codemirror-css-js',
      'https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.48.4/mode/css/css.min.js',
      array(),
      '5.48.4',
      true
    );

    wp_register_script( 'codemirror-js-js',
      'https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.48.4/mode/javascript/javascript.min.js',
      array(),
      '5.48.4',
      true
    );

    wp_register_script( 'codemirror-xml-js',
      'https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.48.4/mode/xml/xml.min.js',
      array(),
      '5.48.4',
      true
    );

    wp_register_script( 'codemirror-js',
      'https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.48.4/codemirror.min.js',
      array(),
      '5.48.4',
      true
    );

    // Custom Javascript to configure and enable the Codemirror functionality for the textareas.
    wp_register_script( 'wp-saint-third-js',
      plugins_url( 'js/wp-saint-third-party.js', dirname( __FILE__ ) ),
      array( 'codemirror-js' ),
      '1.0.0',
      true
    );

  }

  /**
   * Load the options for this module from the database.
   *
   * @since    1.0.0
   */
  public function load_options() {

    $this->options = get_option( Wp_Saint_Admin::WP_SAINT_THIRD_PARTY_SCRIPTS );

    return $this->options;

  }

  /**
   * Create and output the form for adding the third party scripts.
   *
   * @since    1.0.0
   */
  public function create_form() {

    // Include the stylesheets and scripts only on this page.
    $this->enqueue_scripts();

    $this->admin->get_page_header();
    ?>
    <div class="wrap">
      <h1>WP Saint - Third Party Scripts</h1>

      <div class="wp-saint-body">
        <div class="wp-saint-body-content">
        <form method="post" action="options.php">
        <?php
          settings_fields( self::THIRD_PARTY_SCRIPTS_GROUP );
          do_settings_sections( self::THIRD_PARTY_SCRIPTS_SLUG );
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
  }

  /**
   * Create and output the form for adding the third party scripts.
   *
   * @since    1.0.0
   */
  private function enqueue_scripts() {
    
    wp_enqueue_style( 'codemirror-css' );
    wp_enqueue_style( 'codemirror-theme-css' );

    wp_enqueue_script( 'codemirror-js' );
    wp_enqueue_script( 'codemirror-mode-js' );
    wp_enqueue_script( 'codemirror-css-js' );
    wp_enqueue_script( 'codemirror-js-js' );
    wp_enqueue_script( 'codemirror-xml-js' );

    wp_enqueue_script( 'wp-saint-third-js' );

  }

  /**
   * Configure the settings input fields.
   *
   * @since    1.0.0
   */
  public function init_settings() {
    
    register_setting(
      self::THIRD_PARTY_SCRIPTS_GROUP, // Option group
      Wp_Saint_Admin::WP_SAINT_THIRD_PARTY_SCRIPTS, // Option name
      array( $this, 'sanitize' )
    );

    add_settings_section(
      self::THIRD_PARTY_SCRIPTS_SECTION,   // Section ID
      'Header and Footer Scripts', // Title
      array( $this, 'section_info' ),   // Callback
      self::THIRD_PARTY_SCRIPTS_SLUG // Slug
    );

    add_settings_field(
      self::THIRD_PARTY_SCRIPTS_HEADER,  // ID
      __( 'Header Scripts', 'wp-saint' ),  // Title
      array( $this, 'header_script_field_callback' ), // Callback
      self::THIRD_PARTY_SCRIPTS_SLUG, // Slug
      self::THIRD_PARTY_SCRIPTS_SECTION   // Section ID
    );

    add_settings_field(
      self::THIRD_PARTY_SCRIPTS_FOOTER,  // ID
      __( 'Footer Scripts', 'wp-saint' ),  // Title
      array( $this, 'footer_script_field_callback' ), // Callback
      self::THIRD_PARTY_SCRIPTS_SLUG, // Slug
      self::THIRD_PARTY_SCRIPTS_SECTION   // Section ID
    );

  }

  /**
   * Output the info text for the scripts fields section.
   *
   * @since    1.0.0
   */
  public function section_info() {

    print '<p>Enter your scripts which needs to be inserted in either header or footer section on all pages. You can insert JavaScript and CSS codes here. Do not forget to wrap your code inside the proper &lt;HTML&gt; or &lt;script&gt; tags.</p>';

    print "<p><strong>Note</strong>: PHP code is not supported.</p>";

  }

  /**
   * Process the input for a textarea. Used to clean and filter the input as needed.
   *
   * @since    1.0.0
   */
  private function process_textarea_input( $input, $field_name ) {

    if( isset( $input[ $field_name ] ) ) {
      return $input[ $field_name ];
    }

  }

  /**
   * Process the user input for the textareas.
   *
   * @since    1.0.0
   */
  public function sanitize( $input ) {

    $new_input = array();

    $field_name = self::THIRD_PARTY_SCRIPTS_HEADER;
    $new_input[ $field_name ] = $this->process_textarea_input( $input, $field_name );

    $field_name = self::THIRD_PARTY_SCRIPTS_FOOTER;
    $new_input[ $field_name ] = $this->process_textarea_input( $input, $field_name );

    return $new_input;

  }

  /**
   * Output the HTML for the header script form field.
   *
   * @since    1.0.0
   */
  public function header_script_field_callback() {

    $field_name = self::THIRD_PARTY_SCRIPTS_HEADER;

    printf(
      '<textarea class="widefat" rows="8" id="' . $field_name . '" name="' .  Wp_Saint_Admin::WP_SAINT_THIRD_PARTY_SCRIPTS . '[' .  $field_name . '_temp]">%s</textarea>', 
      Wp_Saint_Utility::get_option_value( $this->options, $field_name )
    );
    echo '<div class="description">These scripts will be included in the <code>&lt;head&gt;</code> section of the page.</div>';
    echo '<input type="hidden" name="' .  Wp_Saint_Admin::WP_SAINT_THIRD_PARTY_SCRIPTS . '[' .  $field_name . ']" />';

  }

  /**
   * Output the HTML for the footer script form field.
   *
   * @since    1.0.0
   */
  public function footer_script_field_callback() {

    $field_name = self::THIRD_PARTY_SCRIPTS_FOOTER;

    printf(
      '<textarea class="widefat" rows="8" id="' . $field_name . '" name="' .  Wp_Saint_Admin::WP_SAINT_THIRD_PARTY_SCRIPTS . '[' .  $field_name . '_temp]">%s</textarea>', 
      Wp_Saint_Utility::get_option_value( $this->options, $field_name )
    );

    echo '<div class="description">These scripts will be included in the page footer above the <code>&lt;/body&gt;</code> tag.</div>';
    echo '<input type="hidden" name="' .  Wp_Saint_Admin::WP_SAINT_THIRD_PARTY_SCRIPTS . '[' .  $field_name . ']" />';

  }

  /**
   * Output the HTML for the footer script form field.
   *
   * @since    1.0.0
   */
  private function send_output( $field_name ) {

    // Skip admin pages, feed, robots and trackbacks.
    if( is_admin() || is_feed() || is_robots() || is_trackback() ) {
      return;
    }

    // Get the value for this field.
    $value = Wp_Saint_Utility::get_option_value( $this->options, $field_name );
    if( empty( $value ) ) {
      return;
    }

    if( strlen( trim( $value ) ) == 0 ) {
      return;
    }

    // Wrap the content in comments and output it.
    echo $this->get_output_prefix() . wp_unslash( $value ) . $this->get_output_suffix();

  }

  /**
   * Output the header script contents.
   *
   * @since    1.0.0
   */
  public function output_header_script() {

    $this->send_output( self::THIRD_PARTY_SCRIPTS_HEADER );

  }

  /**
   * Output the footer script contents.
   *
   * @since    1.0.0
   */
  public function output_footer_script() {

    $this->send_output( self::THIRD_PARTY_SCRIPTS_FOOTER );

  }

  /**
   * Output the leading content for the script.
   *
   * @since    1.0.0
   */
  private function get_output_prefix() {
    
    return '<!--START: Scripts via @WPSaint - wpsaint.com -->';

  }

  /**
   * Output the trailing content for the script.
   *
   * @since    1.0.0
   */
  private function get_output_suffix() {
    
    return '<!--END: Scripts via @WPSaint - wpsaint.com -->
';

  }

}
