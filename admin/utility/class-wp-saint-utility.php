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
class Wp_Saint_Utility {

  /**
   * The admin plugin.
   *
   * @since    1.0.0
   * @access   private
   * @var      object    $admin    The admin plugin.
   */
  private $admin;

  /**
   * Initialize the class and set its properties.
   *
   * @since    1.0.0
   * @param    string    $admin       The parent admin class.
   */
  public function __construct( $admin ) {
    $this->admin = $admin;
  }

  /**
   * Get the option value for an input field. Used to show the existing value in the form field.
   *
   * @since    1.0.0
   */
  public static function get_option_value( $options, $field_name ) {
    
    return isset( $options[ $field_name ] ) ? trim( $options[ $field_name ] ) : '';

  }

  /**
   * Get the option value for an array field. Used to show the existing value in the form field.
   *
   * @since    1.0.0
   */
  public static function get_array_value( $options, $field_name ) {
    
    return isset( $options[ $field_name ] ) ? $options[ $field_name ] : array();

  }

  /**
   * Process and clean the user input for text fields.
   *
   * @since    1.0.0
   */
  public static function process_textfield_input( $input, $field_name ) {
    
    if( isset( $input[ $field_name ] ) ) {
      return sanitize_text_field( $input[ $field_name ] );
    }

    return '';

  }

  /**
   * Process and clean the user input for numeric text fields.
   *
   * @since    1.0.0
   */
  public static function process_numeric_input( $input, $field_name ) {
    
    if( ! empty( $input[ $field_name ] ) && isset( $input[ $field_name ] ) && 
      strlen( trim( $input[ $field_name ] ) > 0 ) ) {
      $value = sanitize_text_field( $input[ $field_name ] );

      if( is_numeric( $value ) ) {
        return $value;
      }
    }

    return '';

  }

  /**
   * Validate the input string for a valid Google Analytics Tracking ID.
   *
   * @return   true if valid
   * @since    1.0.0
   */
  public static function validate_google_analytics_id( $input_str ) {

    return preg_match( '/^ua-\d{4,9}-\d{1,4}$/i', strval( $input_str ) );

  }

  /**
   * Validate the input string for a valid tag or cookie name.
   *
   * Only alpha numeric and underscore ( _ ) are allowed.
   *
   * @return   true if valid
   * @since    1.0.0
   */
  public static function validate_tag_name( $input_str ) {

    // _ is allowed. If it exists, remove the char and then validate for alphanumeric.
    if( strpos( $input_str, '_' ) !== FALSE ) {
      $alpha_str = str_replace( '_', '', $input_str );
      return ctype_alnum( $alpha_str );
    } else {
      return ctype_alnum( $input_str );
    }

    return false;

  }

  /**
   * Process and clean the user input for multiple selection selects.
   *
   * @since    1.0.0
   */
  public static function process_multiple_input( $input, $field_name ) {
    
    if( ! empty( $input[ $field_name ] ) ) {
      $ret_values = array();
      foreach( $input[ $field_name ] as $ix => $value ) {
        $ret_values[] = sanitize_text_field( $value );
      }
      return $ret_values;
    }

    return '';

  }

  /**
   * Process and clean the user input for roles selection selects.
   *
   * @since    1.0.0
   */
  public static function process_roles_input( $input, $field_name ) {
    
    if( ! empty( $input[ $field_name ] ) ) {
      $ret_values = array();
      foreach( $input[ $field_name ] as $name => $value ) {
        $ret_values[] = sanitize_text_field( $name );
      }
      return $ret_values;
    }

    return '';

  }

  /**
   * @param string $domain Pass $_SERVER['SERVER_NAME'] here
   * @param bool $debug
   *
   * @debug bool $debug
   * @return string
   */
  public static function get_domain($domain, $debug = false) {
    $original = $domain = strtolower($domain);

    if (filter_var($domain, FILTER_VALIDATE_IP)) { return $domain; }

    $debug ? print('<strong style="color:green">&raquo;</strong> Parsing: '.$original) : false;

    $arr = array_slice(array_filter(explode('.', $domain, 4), function($value){
      return $value !== 'www';
    }), 0); //rebuild array indexes

    if (count($arr) > 2)
    {
      $count = count($arr);
      $_sub = explode('.', $count === 4 ? $arr[3] : $arr[2]);

      $debug ? print(" (parts count: {$count})") : false;

      if (count($_sub) === 2) // two level TLD
      {
        $removed = array_shift($arr);
        if ($count === 4) // got a subdomain acting as a domain
        {
          $removed = array_shift($arr);
        }
        $debug ? print("<br>\n" . '[*] Two level TLD: <strong>' . join('.', $_sub) . '</strong> ') : false;
      }
      elseif (count($_sub) === 1) // one level TLD
      {
        $removed = array_shift($arr); //remove the subdomain

        if (strlen($_sub[0]) === 2 && $count === 3) // TLD domain must be 2 letters
        {
          array_unshift($arr, $removed);
        }
        else
        {
          // non country TLD according to IANA
          $tlds = array(
            'aero',
            'arpa',
            'asia',
            'biz',
            'cat',
            'com',
            'coop',
            'edu',
            'gov',
            'info',
            'jobs',
            'mil',
            'mobi',
            'museum',
            'name',
            'net',
            'org',
            'post',
            'pro',
            'tel',
            'travel',
            'xxx',
          );

          if (count($arr) > 2 && in_array($_sub[0], $tlds) !== false) //special TLD don't have a country
          {
            array_shift($arr);
          }
        }
        $debug ? print("<br>\n" .'[*] One level TLD: <strong>'.join('.', $_sub).'</strong> ') : false;
      }
      else // more than 3 levels, something is wrong
      {
        for ($i = count($_sub); $i > 1; $i--)
        {
          $removed = array_shift($arr);
        }
        $debug ? print("<br>\n" . '[*] Three level TLD: <strong>' . join('.', $_sub) . '</strong> ') : false;
      }
    }
    elseif (count($arr) === 2)
    {
      $arr0 = array_shift($arr);

      if (strpos(join('.', $arr), '.') === false
        && in_array($arr[0], array('localhost','test','invalid')) === false) // not a reserved domain
      {
        $debug ? print("<br>\n" .'Seems invalid domain: <strong>'.join('.', $arr).'</strong> re-adding: <strong>'.$arr0.'</strong> ') : false;
        // seems invalid domain, restore it
        array_unshift($arr, $arr0);
      }
    }

    $debug ? print("<br>\n".'<strong style="color:gray">&laquo;</strong> Done parsing: <span style="color:red">' . $original . '</span> as <span style="color:blue">'. join('.', $arr) ."</span><br>\n") : false;

    return join('.', $arr);
  }

}
