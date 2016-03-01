<?php

class scf_Options {

	private $replacedOptions;
	private $defaultOptions;

	public $inputtedfields;
	private $options;
	private $passedOptions;


	/* 
	 * Constructor functions
	 */
	public function __construct() {

		// Default settings to use in the beginning
		$this->defaultOptions = array();

		// Default options to use in the beginning
		$this->replacedOptions = array(
	        'form_styling'      => 'full-width',                    // Is the form 'full-width' or 'narrow'
	        'return'            => false,                           // Return the contents or echo
	        'column_class'      => 'col-sm-12',                     // Split form over two columns
	        'labels'            => true,                            // Use labels
	        'placeholders'      => false,                           // Use placeholders
	        'btn_wrapper'		=> 'col-xs-12',						// Wrapper class for the button to sit in
	    );

		// Dummy field data
		$this->inputtedfields = maybe_unserialize( get_option( 'scf_table_fields', array() ) );

		// Final options to return
		$this->options = array();

	}


	/* 
	 * return the options
	 */
	public function get($passedOptions) {

		// Set the passed options in frontend or shortcodes
		$this->passedOptions = $passedOptions;

		// Set the default options from the database
		$this->setDefaults();

		// Replace any defaults with passed options
		$this->replaceWithPassed();

		// Merge the options
		$this->options = $this->replacedOptions + $this->defaultOptions;

		// Return the options
		return $this->options;

	}


	/* 
	 * return the options
	 */
	private function setDefaults() {

		$arr = array();

		// Use a form?
		$arr['form'] = (boolean) get_option('scf_form', '0');

		// Location for the form to be sent to
		$arr['send_to_url'] = get_option('scf_send_to', get_permalink());

		// Get the form title
		$arr['form_title'] = get_option('scf_form_title', '<h2>Enquire now!</h2>');

		// Get the email subject
		$arr['email_subject'] = get_option('scf_email_subject', 'Website Enquiry');

		// Get the email recipients
		$arr['email_recipients'] = get_option('scf_email_recipients', get_bloginfo('admin_email'));

		// What form styling should be used?
		$arr['form_styling'] = get_option('scf_form_styling', 'bootstrap');

		// Does the Bootstrap CDN need to be included?
		$arr['include_bootstrap'] = (boolean) get_option('scf_include_bootstrap', '0' );

		// Does the FontAwesome CDN need to be included?
		$arr['include_fontawesome'] = (boolean) get_option('scf_include_fontawesome', '0' );

		// Get the extra class for the submit button
		$arr['submit_class'] = get_option('scf_submit_class', 'btn-primary');

		// Get the success message
		$arr['success_msg'] = get_option('scf_success_msg', 'Thanks!');

		// Use reCAPTCHA or maths test
		$arr['validation'] = get_option('scf_validation', 'recaptcha');

		// Does the reCAPTCHA script need to be loaded?
		$arr['include_recaptcha'] = (boolean) get_option('scf_include_recaptcha', '0' );

		// Use a button?
		$arr['button'] = (boolean) get_option('scf_display_button');

		// Collapse the form
		$arr['form_collapsed'] = (boolean) get_option('scf_default_collapse');

		// Get the text for the button
		$arr['btn_text'] = get_option('scf_button_text', 'Get in touch now');

		// Get the extra class for the button
		$arr['btn_class'] = get_option('scf_button_class', 'btn-primary');

		// Get the button icon side
		$arr['btn_icon_side'] = get_option('scf_button_side', 'left');

		// Get the button icon (FontAwesome)
		$arr['btn_icon_type'] = get_option('scf_button_icon', 'fa-comments');

		// Get the public key for reCAPTCHA
		$arr['public_key'] = get_option('scf_recaptcha_public', '');

		// Get the private key for reCAPTCHA
		$arr['private_key'] = get_option('scf_recaptcha_private', '');

		// Set the final default settings
		$this->defaultOptions = $arr;

	}


	/* 
	 * return the options
	 */
	private function replaceWithPassed() {

		// Use a more managable variable
		$o = $this->passedOptions;

		// Is the form styling option set
		if( isset($o['width']) ) $this->replacedOptions['form_styling'] = $o['width'];

		// Should the form be returned
		if( isset($o['return']) ) $this->replacedOptions['return'] = (boolean) $o['return'];

		// Change if a button is required
		if( isset($o['button']) ) $this->replacedOptions['button'] = (boolean) $o['button'];

		// Change if the form is collapsed
		if( isset($o['form_collapse']) ) $this->replacedOptions['form_collapsed'] = (boolean) $o['form_collapse'];

		// Change the button text if required
		if( isset($o['btn_text']) ) $this->replacedOptions['btn_text'] = $o['btn_text'];

		// Change the button wrapper
		if( isset($o['btn_wrapper_class']) ) $this->replacedOptions['btn_wrapper'] = $o['btn_wrapper_class'];

		// Change the form title if required
		if( isset($o['form_title']) ) $this->replacedOptions['form_title'] = isset($_POST['form_title']) ? $_POST['form_title'] : $o['form_title'];

		// Change the email subject
		if( isset($o['email_subject']) ) $this->replacedOptions['email_subject'] = isset($_POST['email_subject']) ? $_POST['email_subject'] : $o['email_subject'];

	}





}