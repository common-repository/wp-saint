(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */

  $(function() {

    var getParentRow = function (el) {
      return el.parents('tr');
    };

    var getParentTable = function (el) {
      return el.parents('table');
    };

    var getCbValue = function (active) {
      return active ? 'on' : 'off';
    };

    // Field lookups.
    var gaTrackingId = $('#wp_saint_weba_ga_id');
    var gaTrackingIdError = $('#wp_saint_ga_id_error');

    // Advanced settings toogle, checkbox and existing value.
    var advancedSettingsCb = $('#wp_saint_weba_advanced');
    var advancedSettingsValue = $('#wp_saint_weba_advanced_value');

    var formEl = $('#wp-saint-web-analytics-form');

    // Advanced settings field elements.
    var gtagRenameEl = $('#wp_saint_weba_global_gtag_rename');

    var disableAdvertisingCb = $('#wp_saint_weba_disable_advertising');

    var anonymizeIpCb = $('#wp_saint_weba_anonymize_ip');
    var setUserIdCb = $('#wp_saint_weba_set_user_id');

    // Cookies settings field elements.
    var cookiesPrefixEl = $('#wp_saint_weba_cookie_prefix');
    var cookiesExpiresEl = $('#wp_saint_weba_cookie_expires');

    // Elink settings field elements.
    var elinkSettingsCb = $('#wp_saint_weba_elink_enable');
    var elinkSettingsValue = $('#wp_saint_weba_elink_enable_value');

    var elinkCookieNameEl = $('#wp_saint_weba_elink_cookie_name');
    var elinkCookieExpiresEl = $('#wp_saint_weba_elink_cookie_expires');
    var elinkLevelsEl = $('#wp_saint_weba_elink_levels');

    // Row elements for showing and hiding.
    var advSettingsWarningRow = $('#wp_saint_adv_warning');

    var gtagRenameRow = getParentRow(gtagRenameEl);
    var disableAdvertisingRow = getParentRow(disableAdvertisingCb);
    var anonymizeIpRow = getParentRow(anonymizeIpCb);
    var setUserIdRow = getParentRow(setUserIdCb);

    var cookiesTable = getParentTable(cookiesPrefixEl);
    var cookiesHeader = $('#wp_saint_cookies_header');

    var elinkTable = getParentTable(elinkCookieNameEl);
    var elinkHeader = $('#wp_saint_elink_header');

    var elinkCookieNameRow = getParentRow(elinkCookieNameEl);
    var elinkCookieExpiresRow = getParentRow(elinkCookieExpiresEl);
    var elinkLevelsRow = getParentRow(elinkLevelsEl);

    var rolesContainer = $('#wp_saint_roles_container');
    var rolesTable = getParentTable(rolesContainer);
    var rolesHeader = $('#wp_saint_roles_header');

    var showAdvancedSettings = function (showFlag) {
      if (showFlag || (advancedSettingsValue.val() === 'on')) {
        advSettingsWarningRow.show();
        gtagRenameRow.show();
        disableAdvertisingRow.show();
        anonymizeIpRow.show();
        setUserIdRow.show();

        cookiesTable.show();
        cookiesHeader.show();

        elinkTable.show();
        elinkHeader.show();

        rolesTable.show();
        rolesHeader.show();
      } else {
        advSettingsWarningRow.hide();
        gtagRenameRow.hide();
        disableAdvertisingRow.hide();
        anonymizeIpRow.hide();
        setUserIdRow.hide();

        cookiesTable.hide();
        cookiesHeader.hide();

        elinkTable.hide();
        elinkHeader.hide();

        rolesTable.hide();
        rolesHeader.hide();
      }
    };
    showAdvancedSettings();

    var showElinkSettings = function (showFlag) {
      if (showFlag || (elinkSettingsValue.val() === 'on')) {
        elinkCookieNameRow.show();
        elinkCookieExpiresRow.show();
        elinkLevelsRow.show();
      } else {
        elinkCookieNameRow.hide();
        elinkCookieExpiresRow.hide();
        elinkLevelsRow.hide();
      }
    };
    showElinkSettings();

    $.validator.setDefaults({
        errorElement: 'div',
        errorPlacement: function (error, element) {
            var formEl = $(element);
            // Find the description element.
            var descEl = formEl.next('.description');
            if (descEl.length) {
              descEl.after(error);
            } else {
              formEl.after(error);
            }
        },
        highlight: function (element) {
            var formEl = $(element);
            if (formEl.hasClass('form-error-has-parent')) {
                formEl.parent().addClass('form-error');
            } else {
                formEl.addClass('form-error');
            }
        },
        unhighlight: function (element) {
            var formEl = $(element);
            if (formEl.hasClass('form-error-has-parent')) {
                formEl.parent().removeClass('form-error');
            } else {
                formEl.removeClass('form-error');
            }
        }
    });

    $.validator.addMethod('analyticsId', function (value, element) {
      var ret = isAnalytics(value);
      return ret;
    }, 'The tracking ID must be in the correct format.');

    $.validator.addMethod('lettersNumbersOnly', function (value, element) {
      return this.optional(element) || /^[a-z0-9_]+$/i.test(value);
    }, 'Only letters and numbers are allowed in this field.');

    // A Regex for the GA Tracking ID.
    var isAnalytics = function(str) {
      return (/^ua-\d{4,9}-\d{1,4}$/i).test(str.toString());
    };

    var validAnalyticsId = function (elem) {
      var el = $(elem);
      return isAnalytics(el.val());
    };

    // Validate the GA Tracking ID field.
    gaTrackingId.on('blur', function (event) {
      // Convert input to upper-case.
      gaTrackingId.val(function (_, val) {
        return val.toUpperCase();
      });
    });
    
    advancedSettingsCb.on('change', function(e) {
      var active = (advancedSettingsCb.is(':checked'));
      advancedSettingsValue.val(getCbValue(active));
      showAdvancedSettings(active);
    });

    elinkSettingsCb.on('change', function(e) {
      var active = (elinkSettingsCb.is(':checked'));
      elinkSettingsValue.val(getCbValue(active));
      showElinkSettings(active);
    });

    rolesContainer.find('.toggle').each(function (ix, elem) {
      var toggle = $(elem);
      var cb = toggle.next();
    });


    // Convert the input into a numeric value (for spinners) in case of a paste.
    var processKeyInput = function(event, minValue, maxValue) {
      var field = $(event.target);
      var fieldValue = parseInt(field.val(), 10);
      if(isNaN(fieldValue)) {
        // Not a number, reset to zero.
        field.val(0);
      } else {
        // Restrict to the range between min and max values.
        if (fieldValue < minValue) {
          fieldValue = minValue;
        }
        if (fieldValue > maxValue) {
          fieldValue = maxValue;
        }
        field.val(fieldValue);
      }
    };

    // Convert the input fields into spinners.
    cookiesExpiresEl.spinner({
      min: 0,
      max: 1825,
      numberFormat: "n"
    }).on('keyup', function(event) {
      processKeyInput(event, 0, 1825);
    });

    elinkCookieExpiresEl.spinner({
      min: 1,
      max: 90,
      numberFormat: "n"
    }).on('keyup', function (event) {
      processKeyInput(event, 1, 90);
    });

    elinkLevelsEl.spinner({
      min: 1,
      max: 5,
      numberFormat: "n"
    }).on('keyup', function (event) {
      processKeyInput(event, 1, 5);
    });

    var alphanumericRegex = new RegExp("^[a-zA-Z0-9]+$");

    var allowAlphanumeric = function(field) {
      field.on({
        keydown: function(e) {
          e = (e) ? e : event;
          var charCode = (e.charCode) ? e.charCode : ((e.keyCode) ? e.keyCode : ((e.which) ? e.which : 0));
          if (charCode == 8 || charCode == 46 || charCode == 37 || charCode == 39) {
            return true;
          } 

          if ((charCode < 65 || charCode > 122) && (charCode < 48 || charCode > 57)) {
            return false;
          }
        },
        change: function() {
          this.value = this.value.replace(/[^a-zA-Z0-9]/g, '');
        }
      });
    };

    // Utility function to prevent input of spaces.
    var disallowSpaces = function(field) {
      field.on({
        keydown: function(e) {
          // If the key is a space, disallow.
          if (e.which === 32) {
            return false;
          }
        },
        change: function() {
          // If the contents change (due to a paste), 
          // remove all spaces from the value.
          this.value = this.value.replace(/\s/g, '');
        }
      });
    };

    // Don't allow spaces in these fields.
    disallowSpaces(gtagRenameEl);
    disallowSpaces(cookiesPrefixEl);
    disallowSpaces(elinkCookieNameEl);

    formEl.validate({
      onfocusout: function (element) {
        this.element(element);
      },
      rules: {
        "wp_saint_web_analytics[wp_saint_weba_ga_id]": {
          analyticsId: true
        },
        "wp_saint_web_analytics[wp_saint_weba_global_gtag_rename]": {
          lettersNumbersOnly: true
        },
        "wp_saint_web_analytics[wp_saint_weba_cookie_prefix]": {
          lettersNumbersOnly: true
        },
        "wp_saint_web_analytics[wp_saint_weba_elink_cookie_name]": {
          lettersNumbersOnly: true
        },
      },
      message: {
        "wp_saint_web_analytics[wp_saint_weba_ga_id]": {
          analyticsId: 'The Google Tracking ID must be in the correct format.'
        },
        "wp_saint_web_analytics[wp_saint_weba_global_gtag_rename]": {
          lettersNumbersOnly: 'Only letters and numbers are allowed in the global gtag() object rename field.'
        },
        "wp_saint_web_analytics[wp_saint_weba_cookie_prefix]": {
          lettersNumbersOnly: 'Only letters and numbers are allowed in the cookie prefix field.'
        },
        "wp_saint_web_analytics[wp_saint_weba_elink_cookie_name]": {
          lettersNumbersOnly: 'Only letters and numbers are allowed in the cookie name field.'
        },
      }
    });

    formEl.on('submit', function () {
      // return formEl.valid();
      return true;
    });

  });

})( jQuery );

