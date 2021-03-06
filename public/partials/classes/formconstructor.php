<?php

class scf_FormConstructor {

	protected $options;
	private $fields;
	public $emailContents;
	private $valid;
	private $verified;
	private $errors;
	private $completed;


	/* 
	 * Constructor functions
	 */
	public function __construct() {

		$this->options = array();
		$this->fields = array();
		$this->emailContents = '';
		$this->valid = false;
		$this->verified = false;
		$this->errors = array();
		$this->completed = false;

	}



	/* 
	 * Create the form
	 */
	public function init($passedOptions = array()) { 

		// Add all the files required
		require_once 'content.php';
		require_once 'email.php';
		require_once 'fields.php';
		require_once 'formvalidation.php';
		require_once 'options.php';

		global $multiple_forms;

		// Make the passed options an array if it's empty. It sometimes comes through as an empty string.
		if(!$passedOptions) $passedOptions = array();


		/*
		 * 1. Replace success message placeholders
		 * 2. Cycle through each field, noting their properties in an array
		 * 3. Add the JS validation script to the contents
		 * 4. Check if the form has been completed
		 * 5. If not completed, determine if button or form should be shown, and create the classes
		 * 6. If completed, determine if it's valid (recaptcha, contents, etc)
		 * 7. Show errors if form is not valid
		 * 8. Set the 'from' details
		 * 9. Send email if form is valid
		 */

		// Set the classes required
		$contentClass = new scf_Content();
		$emailClass = new scf_Email();
		$optionClass = new scf_Options();

		// Set the options and settings
		$this->options = $optionClass->get($passedOptions);

		// Require the 
		if( $this->options['validation'] === 'recaptcha' && $this->options['include_recaptcha'] && !isset($multiple_forms)) require_once 'recaptchalib.php';

		// Return false if there is no form to show. This is if no settings have been completed.
		if( !$this->options['form'] ) return false;

		// Replace placeholder text in the success message
		$this->replacePlaceholders();

		// Create array of all fields
		$this->fields = scf_Fields::getSCFFields($optionClass->inputtedfields, $this->options, false);

		// Don't show the form if there aren't any fields set
		if( count($this->fields) < ($this->options['validation'] !== 'recaptcha' ? 2 : 1 ) ) return false;

		// Check if the form has been completed
		$this->completed = scf_FormValidation::isFormCompleted($this->fields);

		// Check if the form values are valid been completed. Pass errors if not.
		$this->valid = scf_FormValidation::isFormValid($this->fields, $this->completed);
		if(is_array($this->valid) && $this->completed) $this->addError($this->valid);

		// Check if the form verification has passed successfully. Pass errors if not.
		$this->verified = scf_FormValidation::isFormVerified($this->options, $this->fields, $this->completed);
		if($this->verified !== true && $this->completed) $this->addError($this->verified);

		// Form is completed. Now check if it's valid and verified
		if( $this->completed === true && $this->valid === true && $this->verified === true ) {

			// Send the email
			$emailClass->sendEmail($this->fields, $this->options);

			// Reset the values from the fields
			$this->fields = array();
			$this->fields = scf_Fields::getSCFFields($optionClass->inputtedfields, $this->options, true);

			// Redirect to another page after completing the submission and force the success message (if there is one)
			if( $this->options['send_to_url'] === '' ) $this->options['send_to_url'] = strtok($_SERVER["REQUEST_URI"],'?');
			$parameters = !empty($_GET) ? http_build_query(array_merge($_GET, array('scf_success'=>'true'))) : 'scf_success=true';

			// Fallback on Javascript because the headers have already been sent
			$string = '<script type="text/javascript">';
		    $string .= 'window.location = "' . $this->options['send_to_url'] . '?' . $parameters . '"';
		    $string .= '</script>';
		    $contentClass->addToPageContent($string);

		} elseif( isset($_GET['scf_success']) && $_GET['scf_success'] === 'true') {

			// Create the success message
			$contentClass->addSuccessMessage($this->options);

		}

		// Add the form or button as required and output the content with errors (if there are any).
		if( $this->options['button'] == true ) $contentClass->addButton($this->options);
        if( $this->options['form'] == true ) $contentClass->addForm($this->options, $this->fields, $this->errors, $this->verified, $this->completed);

		// Add the js script to the form content
		$contentClass->addValidationScript($this->fields);

		// Set that there are multiple forms
		$multiple_forms = true;

		// Echo or return the final page content
		if( !$this->options['return'] ) echo $contentClass->getPageContent();
		return $contentClass->getPageContent();

	}



	/* 
	 * Replace the placeholders from the success message
	 */
	private function replacePlaceholders() {

		// Replace the {{PREVPAGE}} placeholder
	    if( !empty($_REQUEST['prevpage']) ) {
	        $this->options['success_msg'] = str_replace('{{PREVPAGE}}', $_REQUEST['prevpage'], $this->options['success_msg']);
	    } else {
	        $this->options['success_msg'] = str_replace('{{PREVPAGE}}', get_permalink(), $this->options['success_msg']);
	    }

		// Replace the {{HOMEPAGE}} placeholder
	    $this->options['success_msg'] = str_replace('{{HOMEPAGE}}', get_bloginfo('url'), $this->options['success_msg']);

		// Replace the {{HOMEPAGE}} placeholder
	    $this->options['success_msg'] = str_replace('{{PAGETITLE}}', get_the_title(), $this->options['success_msg']);

	}



	/* 
	 * Check if the form values are valid
	 */
	public function addError($message) {

		// Add error to the array
		if( is_array($message) ) {

			// If it's an array being passed, pass the new messages to it
			$this->errors = array_merge($message);

		} else {

			// Otherwise pass the new message
			$this->errors[] = $message;

		}

	}



}

?>