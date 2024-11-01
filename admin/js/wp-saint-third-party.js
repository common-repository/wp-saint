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
    var options = {
      lineNumbers: true,
      mode: 'htmlmixed'
    };
    var editor1 = CodeMirror.fromTextArea(document.getElementById('wp_saint_third_party_scripts_header'), options);
    var editor2 = CodeMirror.fromTextArea(document.getElementById('wp_saint_third_party_scripts_footer'), options);

    $('#submit').on('click', function() {
      
      // Copy over the textarea values to the hidden fields so that they are sent in the form submission.
      $('input[name="wp_saint_third_party[wp_saint_third_party_scripts_header]"]').val(editor1.getValue());
      $('input[name="wp_saint_third_party[wp_saint_third_party_scripts_footer]"]').val(editor2.getValue());

      return true;
    });

  });

})( jQuery );
