<?php

namespace CustomRayGuns;

class FormController{
	
	public $formSubmissionVariables = array();
	
	public $userID_FormAuthor;
	
	public function __construct() {
		
		//This loads the script that adds a link to the admin area
		include_once('addCustomColum.php');

		//create the CPT 'info-form'
		include_once('InfoFormCPT.class.php');
		$CPT = new InfoFormCPTs;
		
		//Add/Update CPT on form submission
		if (isset($_POST['crg-info-form'])){
			add_action('init', array($this, 'receiveFormSubmission') );
			add_action('init', array($this, 'modifyFormSubmission') );
			add_action('init', array($this, 'updateRecord') );
		}
		
		//Form output:
		add_shortcode( 'CRG-Form', array( $this, 'returnShortcodeHTML' ) );
		
		//add_action('init', array($this, 'redirectIfNotLoggedIn') );
	}

	public function redirectIfNotLoggedIn(){
		$actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
		$linkToCheckAgainst = get_site_url() . "/info-sheet/";
		if ($actual_link == $linkToCheckAgainst){
			if ( is_user_logged_in() ){
		       	 } else {
				$location = wp_registration_url();
				wp_safe_redirect( $location);
			}
		}
	}
	
	public function returnShortcodeHTML() {
		/*
		if (!( is_user_logged_in())){
			die('error: USER SHOULD BE LOGGED IN');
		}
		*/
		include_once('getTheActualForm.php');
		$output = getTheActualForm();
		return $output;
	}
	
	public function receiveFormSubmission(){
		include_once('getFormSubmissionVariablesFromHTMLRequest.php');
		$this->formSubmissionVariables = getFormSubmissionVariablesFromHTMLRequest();
	}
	
	public function modifyFormSubmission(){
		//include_once('getFormAuthor.php');
	  	//$this->$userID_FormAuthor = getFormAuthor();
		//$x = $this->$userID_FormAuthor;
		//die ($x);
	}
	public function updateRecord(){
		$data = array();
		$data = $this->formSubmissionVariables;
		$email = $_POST['crg_login_email'];
		$user = get_user_by( 'crg_login_email', $email );
		$content = serialize($data);
		$title = $email
		$my_post = array(	'post_title'    => $title,
				'post_content'  => $content,
				'post_author'   => $user->ID,
				'post_type'   => 'InfoForm',
		);
		// Insert the post into the database
		$form_id = wp_insert_post( $my_post );
	}
}
